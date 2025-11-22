<?php
namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\Artist;
use App\Models\MusicLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MusicController extends Controller
{
    public function player(Request $request)
    {
        $user = $request->user();

        // Eager load artist dan count likes
        $musicCollection = Music::with('artist')
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->get();

        $likesByUser = collect();
        if ($user) {
            $likesByUser = MusicLike::where('user_id', $user->id)
                ->get()
                ->keyBy('music_id');
        }

        $songs = $musicCollection->map(function ($music) use ($likesByUser) {
            $likeRecord = $likesByUser->get($music->id);

            return [
                'id'         => $music->id,
                'title'      => $music->name,
                'artist'     => $music->artist->name, // ambil dari relasi
                'src'        => $music->music_url,
                'image'      => $music->thumbnail_url
                    ?: 'https://zrnmhkofwhrptrtwbfzl.supabase.co/storage/v1/object/public/thumbnails/assets/default-song.jpg',
                'lyrics'     => $music->lyrics,
                'source'     => $music->source,
                'likesCount' => $music->likes_count,
                'isLiked'    => (bool) $likeRecord,
                'likedAt'    => $likeRecord
                    ? $likeRecord->created_at->toIso8601String()
                    : null,
            ];
        })->values();

        return Inertia::render('MainApp', [
            'songs' => $songs,
        ]);
    }

    public function index(Request $request)
    {
        $searchQuery = trim((string) $request->query('q', ''));

        $musicQuery = Music::with('artist'); // eager load artist

        if ($searchQuery !== '') {
            $musicQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhereHas('artist', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%");
                    });
            });
        }

        $musicPaginator = $musicQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('music.index', [
            'musics'       => $musicPaginator,
            'searchQuery'  => $searchQuery,
        ]);
    }

    public function create()
    {
        $artists = Artist::orderBy('name')->get();
        return view('music.create', compact('artists'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'source'      => ['required', 'in:file,youtube'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
            'lyrics'      => ['nullable', 'string'],
            'artist_type' => ['required', 'in:existing,new'],
            // Validasi kondisional
            'artist_id'       => ['required_if:artist_type,existing', 'nullable', 'exists:artists,id'],
            'new_artist_name' => ['required_if:artist_type,new', 'nullable', 'string', 'max:255'],
        ];

        // Validasi source file/youtube
        if ($request->input('source') === 'file') {
            // Max 20MB (20480 KB)
            $rules['file'] = ['required', 'mimetypes:audio/mpeg,audio/wav,audio/mp3,audio/x-wav', 'max:20480'];
        } else {
            $rules['youtube_url'] = ['required', 'url'];
        }

        $validated = $request->validate($rules);

        // Handle Artist
        if ($validated['artist_type'] === 'new') {
            $artist = Artist::firstOrCreate(
                ['name' => trim($validated['new_artist_name'])]
            );
            $artistId = $artist->id;
        } else {
            $artistId = $validated['artist_id'];
        }

        // Upload Thumbnail
        $thumbnailUrl = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailUrl = $this->uploadToSupabase(
                bucket: 'thumbnails',
                filePath: $request->file('thumbnail')->getRealPath(),
                mimeType: $request->file('thumbnail')->getMimeType(),
                ext: $request->file('thumbnail')->getClientOriginalExtension()
            );
        }

        // Upload Music / Set URL
        if ($validated['source'] === 'file') {
            $musicUrl = $this->uploadToSupabase(
                bucket: 'musics',
                filePath: $request->file('file')->getRealPath(),
                mimeType: $request->file('file')->getMimeType(),
                ext: $request->file('file')->getClientOriginalExtension()
            );
        } else {
            $musicUrl = $validated['youtube_url'];
        }

        Music::create([
            'name'          => $validated['name'],
            'artist_id'     => $artistId,
            'thumbnail_url' => $thumbnailUrl,
            'music_url'     => $musicUrl,
            'source'        => $validated['source'],
            'lyrics'        => $validated['lyrics'] ?? null,
        ]);

        return redirect()->route('music.index')->with('success', 'Music berhasil ditambahkan.');
    }

    public function edit(Music $music)
    {
        $artists = Artist::orderBy('name')->get();
        return view('music.edit', compact('music', 'artists'));
    }

    public function update(Request $request, Music $music)
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'source'      => ['required', 'in:file,youtube'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'lyrics'      => ['nullable', 'string'],
            'artist_type' => ['required', 'in:existing,new'],
            'artist_id'       => ['required_if:artist_type,existing', 'nullable', 'exists:artists,id'],
            'new_artist_name' => ['required_if:artist_type,new', 'nullable', 'string', 'max:255'],
        ];

        if ($request->input('source') === 'file') {
            // File bersifat nullable saat update, kecuali jika sebelumnya YouTube dan sekarang jadi File tapi user tidak upload
            $rules['file'] = ['nullable', 'mimetypes:audio/mpeg,audio/wav,audio/mp3,audio/x-wav', 'max:20480'];
        } else {
            $rules['youtube_url'] = ['required', 'url'];
        }

        $validated = $request->validate($rules);

        // 1. Handle Artist
        if ($validated['artist_type'] === 'new') {
            $artist = Artist::firstOrCreate(
                ['name' => trim($validated['new_artist_name'])]
            );
            $artistId = $artist->id;
        } else {
            $artistId = $validated['artist_id'];
        }

        // 2. Handle Thumbnail
        $thumbnailUrl = $music->thumbnail_url;
        if ($request->hasFile('thumbnail')) {
            $thumbnailUrl = $this->uploadToSupabase(
                bucket: 'thumbnails',
                filePath: $request->file('thumbnail')->getRealPath(),
                mimeType: $request->file('thumbnail')->getMimeType(),
                ext: $request->file('thumbnail')->getClientOriginalExtension()
            );
        }

        // 3. Handle Music Source Logic
        $newSource = $validated['source'];
        $musicUrl  = $music->music_url; // Default keep old url

        if ($newSource === 'file') {
            if ($request->hasFile('file')) {
                // User upload file baru
                $musicUrl = $this->uploadToSupabase(
                    bucket: 'musics',
                    filePath: $request->file('file')->getRealPath(),
                    mimeType: $request->file('file')->getMimeType(),
                    ext: $request->file('file')->getClientOriginalExtension()
                );
            } else {
                // User TIDAK upload file baru
                // Cek apakah sebelumnya YouTube? Jika ya, maka ERROR, karena tidak ada file audio tersimpan
                if ($music->source === 'youtube') {
                    return back()
                        ->withErrors(['file' => 'Anda harus mengunggah file audio jika mengubah sumber dari YouTube ke File.'])
                        ->withInput();
                }
                // Jika sebelumnya File, biarkan $musicUrl lama
            }
        } else {
            // Source YouTube
            $musicUrl = $validated['youtube_url'];
        }

        $music->update([
            'name'          => $validated['name'],
            'artist_id'     => $artistId,
            'thumbnail_url' => $thumbnailUrl,
            'music_url'     => $musicUrl,
            'source'        => $newSource,
            'lyrics'        => $validated['lyrics'] ?? null,
        ]);

        return redirect()->route('music.show', $music)->with('success', 'Music berhasil diperbarui.');
    }

    // ... method lainnya tetap sama
    private function uploadToSupabase(string $bucket, string $filePath, string $mimeType, string $ext): string
    {
        $baseUrl   = rtrim(config('services.supabase.url'), '/');
        $serviceKey= config('services.supabase.service_key');
        $publicBase= rtrim(config('services.supabase.public_base'), '/');

        $subdir = 'uploads/'.date('Y/m/d');
        $filename = Str::uuid()->toString().'.'.$ext;
        $objectPath = $subdir.'/'.$filename;

        $endpoint = "{$baseUrl}/storage/v1/object/{$bucket}/{$objectPath}";

        $bytes = file_get_contents($filePath);
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$serviceKey}",
            'x-upsert'      => 'true',
        ])->withBody($bytes, $mimeType ?: 'application/octet-stream')
        ->put($endpoint);

        if (!$response->successful()) {
            abort(500, 'Upload ke Supabase gagal: '.$response->body());
        }

        return "{$publicBase}/{$bucket}/{$objectPath}";
    }

    public function show(Music $music)
    {
        $music->load('artist'); // eager load
        return view('music.show', compact('music'));
    }

    public function destroy(Music $music)
    {
        $music->delete();
        return redirect()->route('music.index')->with('success', 'Music dihapus.');
    }
}
