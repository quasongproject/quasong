{{-- resources/views/music/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Music</title>
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
      display: grid; grid-template-columns: 1fr auto 1fr;
      align-items: center; gap: 12px; padding: 14px 18px;
      border-bottom: 1px solid var(--fg); background: var(--bg);
      position: sticky; top: 0;
    }
    .brand { font-weight: 800; }
    .header-left { justify-self: start; }
    .header-center { justify-self: center; }
    .header-right { justify-self: end; }

    .btn, .btn-ghost {
      border: 1px solid var(--fg); padding: 10px 14px;
      border-radius: 10px; font-weight: 700; cursor: pointer;
      transition: transform .05s ease, background .1s ease;
      text-decoration: none; display: inline-block;
    }
    .btn { background: var(--fg); color: var(--bg); }
    .btn-ghost { background: transparent; color: var(--fg); }
    .btn:active, .btn-ghost:active { transform: translateY(1px); }
    .btn-ghost:hover { background: #00000008; }

    /* Container */
    .container {
      max-width: 820px; /* batas lebar konten */
      margin: 24px auto;
      padding: 0 16px;
      overflow-wrap: anywhere; /* cegah overflow konten panjang (URL/lirik) */
    }

    .card {
      border: 1px solid var(--fg); border-radius: 14px; background: var(--bg);
      box-shadow: 6px 6px 0 var(--fg); overflow: hidden;
    }
    .card-header { padding: 14px 16px; border-bottom: 1px solid var(--fg); }
    .title { margin: 0; font-size: 1.2rem; font-weight: 800; }

    /* Layout konten: gambar di atas (center), detail di bawah */
    .content {
      display: block;
      padding: 16px;
    }
    .media {
      display: grid;
      place-items: center;      /* center horizontal & vertikal area */
      margin-bottom: 16px;
    }
    .thumb {
      width: 100%;
      max-width: 200px;         /* batas ukuran gambar */
      height: auto;
      aspect-ratio: 1 / 1;      /* bentuk persegi rapi; bisa dihapus jika tak perlu */
      object-fit: cover;
      border: 1px solid var(--fg);
      border-radius: 10px;
      background: #f8f8f8;
    }

    .meta {
      display: grid;
      gap: 10px;
    }
    .row {
      display: grid;
      grid-template-columns: 140px 1fr;
      gap: 10px;
    }
    .label { font-weight: 700; }

    /* Elemen blok bawah */
    .block {
      padding: 16px;
      border-top: 1px solid var(--muted);
    }
    .lyrics {
      white-space: pre-wrap;
      border: 1px solid var(--fg);
      border-radius: 10px;
      padding: 12px;
      min-height: 80px;
      width: 40%;
      text-align: left;
    }

    /* Media player & embed responsif */
    .audio { width: 60%; }
    .embed-wrap {
      position: relative; width: 100%;
      max-width: 820px; /* ikut container */
      padding-top: 56.25%; /* 16:9 */
      border: 1px solid var(--fg); border-radius: 10px; overflow: hidden;
    }
    .embed-wrap iframe {
      position: absolute; inset: 0; width: 100%; height: 100%; border: 0;
    }

    .url {
      display: inline-block;
      border: 1px solid var(--fg);
      border-radius: 10px;
      padding: 6px 10px;
      text-decoration: none;
      color: var(--fg);
      background: transparent;
    }
    .url:hover { background: #00000008; }

    .actions {
      display: flex; gap: 10px; padding: 16px; border-top: 1px solid var(--fg);
      justify-content: flex-end; flex-wrap: wrap;
    }

    @media (max-width: 560px) {
      .row { grid-template-columns: 1fr; } /* label di atas nilai */
      .thumb { max-width: 100%; aspect-ratio: auto; } /* biarkan gambar mengikuti proporsinya */
    }
  </style>
</head>
<body>

<header class="header">
  <div class="header-left brand"><a href="{{ route('music.index') }}" class="btn-ghost">← Back</a></div>
  <div class="header-center brand">Music Detail</div>
  <div class="header-right">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn">Logout</button>
    </form>
  </div>
</header>

<main class="container">
  <section class="card">
    <div class="card-header">
      <h1 class="title">{{ $music->name }}</h1>
    </div>

    <div class="content">
      {{-- Gambar di atas & center --}}
      <div class="media">
        @if($music->thumbnail_url)
          <img class="thumb" src="{{ $music->thumbnail_url }}" alt="Thumbnail {{ $music->name }}">
        @else
          <div class="thumb" style="display:grid;place-items:center;">No Image</div>
        @endif
      </div>

      {{-- Detail di bawah gambar --}}
      <div class="meta">
        <div class="row">
          <div class="label">Nama Music</div>
          <div>{{ $music->name }}</div>
        </div>
        <div class="row">
          <div class="label">Artist</div>
          <div>{{ $music->artist->name }}</div>
        </div>
        <div class="row">
          <div class="label">Music</div>
          <div>
            @if($music->source === 'file')
              <audio class="audio" controls src="{{ $music->music_url }}"></audio>
            @else
              @php
                $url = $music->music_url;
                $vid = null;
                if (preg_match('~(?:v=|be/)([A-Za-z0-9_-]{11})~', $url, $m)) { $vid = $m[1]; }
              @endphp
              @if($vid)
                <div class="embed-wrap">
                  <iframe
                    src="https://www.youtube.com/embed/{{ $vid }}"
                    title="YouTube player"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                  </iframe>
                </div>
              @else
                <a class="url" href="{{ $music->music_url }}" target="_blank" rel="noopener">Open YouTube</a>
              @endif
            @endif
          </div>
        </div>
        <div class="row">
          <div class="label">Music URL</div>
          <div><a class="url" href="{{ $music->music_url }}" target="_blank" rel="noopener">{{ $music->music_url }}</a></div>
        </div>
        <div class="row">
          <div class="label">Thumbnail URL</div>
          <div>
            @if($music->thumbnail_url)
              <a class="url" href="{{ $music->thumbnail_url }}" target="_blank" rel="noopener">{{ $music->thumbnail_url }}</a>
            @else
              -
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="block">
        <center>

            <div class="label" style="margin-bottom:6px;">Lirik</div>
            <div class="lyrics">{{ $music->lyrics ?? '—' }}</div>
        </center>
    </div>

    <div class="actions">
      <a class="btn-ghost" href="{{ route('music.edit', $music) }}">Edit</a>

      <form action="{{ route('music.destroy', $music) }}" method="POST" onsubmit="return confirm('Hapus music ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn">Hapus</button>
      </form>
    </div>
  </section>
</main>

</body>
</html>
