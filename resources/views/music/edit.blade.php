<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  @vite(['resources/css/app.css'])
  <title>Edit Music - {{ $music->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    :root { --bg:#fff; --fg:#000; }
    body { background: var(--bg); color: var(--fg); margin: 0; font-family: system-ui, sans-serif; padding-bottom: 50px; }

    /* Copy style dasar dari create agar konsisten */
    .page { max-width: 720px; margin: 24px auto; padding: 0 16px; }
    .card { border: 1px solid var(--fg); border-radius: 14px; background: #fff; box-shadow: 6px 6px 0 var(--fg); overflow: hidden; }
    .card-header { padding: 14px 16px; border-bottom: 1px solid var(--fg); font-weight: 800; background: #fafafa; }
    .card-body { padding: 20px 16px; }

    .field { margin-bottom: 20px; position: relative; }
    .label { display:block; font-size:.9rem; margin-bottom:6px; font-weight:700; }
    .input, .select, .textarea, .file { width: 100%; padding: 10px; border: 1px solid var(--fg); border-radius: 8px; box-sizing: border-box; background: #fff; }
    .error { color: red; font-size: 0.85rem; margin-top: 4px; }

    .textarea {height: 400px;}

    .sr { position: absolute; opacity: 0; pointer-events: none; }

    /* Toggle Style - Fixed Selector */
    .toggle-box { border: 1px solid var(--fg); border-radius: 10px; overflow: hidden; margin-top: 5px; }
    .toggle-nav { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--fg); }
    .toggle-nav label { padding: 12px; text-align: center; font-weight: 700; cursor: pointer; background: #eee; transition: 0.2s; border-bottom: 2px solid transparent; }
    .toggle-nav label:hover { background: #ddd; }

    .toggle-content { padding: 16px; background: #fff; }
    .section-panel { display: none; }

    /* CSS Logic Penting: Input Radio harus sejajar (sibling) dengan .toggle-box */
    #artist_existing:checked ~ .artist-wrapper .toggle-nav label[for="artist_existing"],
    #artist_new:checked ~ .artist-wrapper .toggle-nav label[for="artist_new"],
    #src_file:checked ~ .source-wrapper .toggle-nav label[for="src_file"],
    #src_yt:checked ~ .source-wrapper .toggle-nav label[for="src_yt"] {
        background: var(--fg); color: #fff;
    }

    #artist_existing:checked ~ .artist-wrapper .panel-existing { display: block; }
    #artist_new:checked ~ .artist-wrapper .panel-new { display: block; }

    #src_file:checked ~ .source-wrapper .panel-file { display: block; }
    #src_yt:checked ~ .source-wrapper .panel-yt { display: block; }

    .thumb-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #000; margin-bottom: 10px; }
    .btn { background: #000; color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; }
    .btn-ghost { padding: 12px 24px; color: #000; text-decoration: none; font-weight: bold; }

    .wrap-anywhere { overflow-wrap: anywhere; word-break: break-word; }
  </style>
</head>
<body>

<div class="page">
  <div class="card">
    <div class="card-header">
      Edit Data: {{ $music->name }}
    </div>
    <div class="card-body">
      <form action="{{ route('music.update', $music->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div class="field">
          <label class="label">Nama Music</label>
          <input type="text" name="name" class="input" value="{{ old('name', $music->name) }}" required>
          @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Artist Section --}}
        <div class="field">
            <label class="label">Artist</label>

            {{-- Logic Checked: Jika ada error old('artist_type') dipakai, jika tidak cek existing data --}}
            @php
                $defaultType = old('artist_type', 'existing'); // Karena saat edit defaultnya pasti existing artist
            @endphp

            <input type="radio" name="artist_type" id="artist_existing" value="existing" class="sr" {{ $defaultType == 'existing' ? 'checked' : '' }}>
            <input type="radio" name="artist_type" id="artist_new" value="new" class="sr" {{ $defaultType == 'new' ? 'checked' : '' }}>

            <div class="artist-wrapper toggle-box">
                <div class="toggle-nav">
                    <label for="artist_existing">Pilih Artist Lama</label>
                    <label for="artist_new">Buat Artist Baru</label>
                </div>
                <div class="toggle-content">
                    {{-- Panel Existing --}}
                    <div class="section-panel panel-existing">
                        <select name="artist_id" class="select">
                            <option value="">-- Pilih Artist --</option>
                            @foreach($artists as $artist)
                                <option value="{{ $artist->id }}"
                                    {{ old('artist_id', $music->artist_id) == $artist->id ? 'selected' : '' }}>
                                    {{ $artist->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('artist_id') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    {{-- Panel New --}}
                    <div class="section-panel panel-new">
                        <input type="text" name="new_artist_name" class="input" placeholder="Nama Artist Baru" value="{{ old('new_artist_name') }}">
                        @error('new_artist_name') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Thumbnail --}}
        <div class="field">
            <label class="label">Thumbnail</label>
            @if($music->thumbnail_url)
                <img src="{{ $music->thumbnail_url }}" class="thumb-preview">
            @endif
            <input type="file" name="thumbnail" class="file">
            <small style="color:grey;">Biarkan kosong jika tidak ingin mengubah</small>
            @error('thumbnail') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Source Section --}}
        <div class="field">
            <label class="label">Sumber Audio</label>

            @php
                $defaultSource = old('source', $music->source);
            @endphp

            <input type="radio" name="source" id="src_file" value="file" class="sr" {{ $defaultSource == 'file' ? 'checked' : '' }}>
            <input type="radio" name="source" id="src_yt" value="youtube" class="sr" {{ $defaultSource == 'youtube' ? 'checked' : '' }}>

            <div class="source-wrapper toggle-box">
                <div class="toggle-nav">
                    <label for="src_file">File Audio</label>
                    <label for="src_yt">YouTube</label>
                </div>
                <div class="toggle-content">
                    <div class="section-panel panel-file">
                        @if($music->source == 'file' && $music->music_url)
                             <div style="margin-bottom:10px;">
                                 <strong>File Saat Ini:</strong>
                                 <audio controls src="{{ $music->music_url }}" style="width:100%; margin-top:5px;"></audio>
                                 <div class="wrap-anywhere" style="margin-top:6px;font-size:.9rem;">
                                    <span style="font-weight:700;">URL sekarang:</span>
                                    <a href="{{ $music->music_url }}" target="_blank" rel="noopener">{{ $music->music_url }}</a>
                                </div>
                             </div>
                        @endif
                        <label class="label">Upload File Baru (Opsional)</label>
                        <input type="file" name="file" class="file" accept="audio/*">
                        @error('file') <div class="error">{{ $message }}</div> @enderror

                    </div>

                    <div class="section-panel panel-yt">
                        <label class="label">Link YouTube</label>
                        <input type="url" name="youtube_url" class="input"
                               value="{{ old('youtube_url', $music->source == 'youtube' ? $music->music_url : '') }}">
                        @error('youtube_url') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Lirik --}}
        <div class="field">
            <label class="label">Lirik</label>
            <textarea name="lyrics" class="textarea">{{ old('lyrics', $music->lyrics) }}</textarea>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" class="btn">Update Data</button>
            <a href="{{ route('music.index') }}" class="btn-ghost">Kembali</a>
        </div>

      </form>
    </div>
  </div>
</div>

</body>
</html>
