{{-- resources/views/music/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Music List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root { --bg:#fff; --fg:#000; --muted:#00000020; }

        html, body {
            background: var(--bg); color: var(--fg);
            margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.45;
        }

        /* Header */
        .header {
            display: grid;
            grid-template-columns: 1fr auto 1fr; /* kiri | tengah | kanan */
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--fg);
            position: sticky; top: 0; background: var(--bg);
        }
        .brand { font-weight: 800; letter-spacing: .3px; }
        .header-left { justify-self: start; }
        .header-center { justify-self: center; }
        .header-right { justify-self: end; }

        .btn, .btn-ghost, .btn-outline {
            border: 1px solid var(--fg);
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .05s ease, background .1s ease, color .1s ease;
            text-decoration: none; display: inline-block;
        }
        .btn { background: var(--fg); color: var(--bg); }
        .btn-ghost { background: transparent; color: var(--fg); }
        .btn-outline { background: transparent; color: var(--fg); }
        .btn:active, .btn-ghost:active, .btn-outline:active { transform: translateY(1px); }
        .btn-ghost:hover, .btn-outline:hover { background: #00000008; }

        /* Container */
        .container { max-width: 1024px; margin: 24px auto; padding: 0 16px; }

        /* Table Card */
        .card {
            border: 1px solid var(--fg); border-radius: 14px; overflow: hidden;
            box-shadow: 6px 6px 0 var(--fg); background: var(--bg);
        }
        .card-header {
            padding: 14px 16px; border-bottom: 1px solid var(--fg);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { margin: 0; font-size: 1.1rem; font-weight: 800; }

        /* Table */
        .table { width: 100%; border-collapse: collapse; }
        .table thead th {
            text-align: left; padding: 12px 14px; border-bottom: 1px solid var(--fg); font-size: .94rem;
        }
        .table tbody td { padding: 12px 14px; border-top: 1px solid var(--muted); vertical-align: middle; }
        .table tbody tr:hover { background: #00000006; }

        .empty { padding: 22px 16px; color: #333; }

        /* Responsive */
        @media (max-width: 640px) {
            .header { grid-template-columns: 1fr auto 1fr; }
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tbody tr { border-top: 1px solid var(--fg); }
            .table tbody td { border: none; display: flex; justify-content: space-between; gap: 10px; }
            .table tbody td::before { content: attr(data-label); font-weight: 700; }
        }
    </style>
</head>
<body>

<header class="header" role="banner">
    <div class="header-left brand">Quasong</div>

    <div class="header-center">

        <a href="{{ route('player') }}" class="btn-outline" style="background: green; color:white; border:green">Go to Player</a>
        <a href="{{ route('music.create') }}" class="btn-outline">+ New Music</a>
    </div>

    <form class="header-right" action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn">Logout</button>
    </form>
</header>

<main class="container" role="main" aria-label="Daftar Music">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Music</h2>
            <form class="header-center" method="GET" action="{{ route('music.index') }}" style="display:flex;gap:8px;align-items:center;">
                <input
                    type="text"
                    name="q"
                    value="{{ $searchQuery ?? '' }}"
                    placeholder="Cari judul / artist"
                    style="border:1px solid #000;border-radius:10px;padding:8px 10px;min-width:220px;"
                >
                <button class="btn-ghost" type="submit">Search</button>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="table" role="table" aria-label="Tabel Music">
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Music</th>
                        <th scope="col">Artist</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($musics as $music)
                        <tr>
                            {{-- <td data-label="Nama Music">{{ $music->name }}</td> --}}
                            <td data-label="Nama Music">{{ $music['name'] }}</td>
                            {{-- <td data-label="Nama Music">Music 1</td> --}}

                            <td>
                                @if($music->source === 'file')
                                    <audio controls src="{{ $music->music_url }}" style="width:200px;"></audio>
                                @else
                                    <a class="btn-ghost" href="{{ $music->music_url }}" target="_blank" rel="noopener">Open YouTube</a>
                                @endif
                            </td>


                            <td data-label="Artist">{{ $music->artist->name }}</td>
                            {{-- <td data-label="Artist">{{ $music->artist }}</td> --}}
                            {{-- <td data-label="Artist">Unknown</td> --}}

                            {{-- <td data-label="Tanggal">
                                {{ $music['date'] }}
                                {{-- 29/09/2005 --}}
                                {{-- {{ \Illuminate\Support\Carbon::parse($music->created_at ?? $music->date ?? now())->format('d M Y') }} --}}
                            {{-- </td> --}}
                            <td data-label="Action">
                                {{-- <a class="btn-ghost" href="{{ route('music.show', $music) }}">View</a> --}}
                                {{-- <a class="btn-ghost">View</a> --}}
                                <a class="btn-ghost" style="border: none" href="{{ route('music.show', $music) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="4">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($musics->hasPages())
            <div style="display:flex;gap:8px;justify-content:flex-end;padding:12px 16px;">
                @if ($musics->onFirstPage())
                <span class="btn-ghost" style="opacity:.4;pointer-events:none;">« Prev</span>
                @else
                <a class="btn-ghost" href="{{ $musics->previousPageUrl() }}">« Prev</a>
                @endif

                <span style="border:1px solid #000;border-radius:10px;padding:8px 12px;">
                Page {{ $musics->currentPage() }} / {{ $musics->lastPage() }}
                </span>

                @if ($musics->hasMorePages())
                <a class="btn-ghost" href="{{ $musics->nextPageUrl() }}">Next »</a>
                @else
                <span class="btn-ghost" style="opacity:.4;pointer-events:none;">Next »</span>
                @endif
            </div>
            @endif
        </div>
    </section>
</main>

</body>
</html>
