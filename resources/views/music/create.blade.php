<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <title>New Music</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    :root { --bg:#fff; --fg:#000; --muted:#00000020; }
    html, body { background: var(--bg); color: var(--fg); margin: 0; font-family: system-ui, sans-serif; line-height: 1.5; }
    .page { max-width: 680px; margin: 36px auto; padding: 0 16px; }
    .title { margin-bottom: 20px; font-weight: 800; font-size: 1.5rem; }

    .card { border: 1px solid var(--fg); border-radius: 14px; background: var(--bg); box-shadow: 6px 6px 0 var(--fg); overflow: hidden; }
    .card-header { padding: 14px 16px; border-bottom: 1px solid var(--fg); font-weight: 800; }
    .card-body { padding: 20px 16px; }

    .field { margin-bottom: 16px; }
    .label { display:block; font-size:.9rem; margin-bottom:6px; font-weight:700; }
    .input, .file, .select, .textarea { width: 100%; padding: 10px; border: 1px solid var(--fg); border-radius: 10px; background: #fff; color: #000; outline: none; box-sizing: border-box; }
    .input:focus, .select:focus, .textarea:focus { box-shadow: 0 0 0 3px rgba(0,0,0,0.1); }
    .textarea { min-height: 100px; resize: vertical; }

    .error { margin-top: 6px; font-size:.85rem; color: #d32f2f; border: 1px solid #d32f2f; border-radius: 8px; padding: 6px 10px; background: #fff5f5; }

    /* Helper untuk Radio Logic CSS */
    .sr { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }

    /* Nav Style */
    .toggle-box { border: 1px solid var(--fg); border-radius: 10px; overflow: hidden; margin-top: 5px; }
    .toggle-nav { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--fg); }
    .toggle-nav label { padding: 10px; text-align: center; font-weight: 700; cursor: pointer; background: #f0f0f0; transition: background 0.2s; }
    .toggle-nav label:hover { background: #e0e0e0; }
    .toggle-nav label:first-child { border-right: 1px solid var(--fg); }

    .toggle-content { padding: 14px; background: #fff; }
    .section-panel { display: none; }

    /* Logic CSS yang BENAR untuk Create */
    /* Kita gunakan selector saudara (sibling) dari input radio ke container wrapper */

    #artist_existing:checked ~ .artist-wrapper .toggle-nav label[for="artist_existing"],
    #artist_new:checked ~ .artist-wrapper .toggle-nav label[for="artist_new"],
    #src_file:checked ~ .source-wrapper .toggle-nav label[for="src_file"],
    #src_yt:checked ~ .source-wrapper .toggle-nav label[for="src_yt"] {
        background: var(--fg); color: var(--bg);
    }

    #artist_existing:checked ~ .artist-wrapper .panel-existing { display: block; }
    #artist_new:checked ~ .artist-wrapper .panel-new { display: block; }

    #src_file:checked ~ .source-wrapper .panel-file { display: block; }
    #src_yt:checked ~ .source-wrapper .panel-yt { display: block; }

    .actions { margin-top: 24px; display:flex; gap:12px; }
    .btn { background: var(--fg); color: var(--bg); padding: 12px 20px; border-radius: 8px; font-weight: 800; border: none; cursor: pointer; }
    .btn-ghost { padding: 12px 20px; text-decoration: none; color: var(--fg); font-weight: 700; }
  </style>
</head>
<body>

<div class="page">
  <h1 class="title">Yeayy, New Music (^_^)</h1>

  {{-- Global Error Check --}}
  @if ($errors->any())
    <div style="margin-bottom: 16px; padding: 12px; border: 1px solid red; border-radius: 8px; background: #fff0f0; color: red;">
        <strong>Oops!</strong> Ada kesalahan input. Silakan periksa kembali form di bawah.
    </div>
  @endif

  <div class="card">
    <div class="card-header">Tambah Lagu Baru</div>
    <div class="card-body">
      <form action="{{ route('music.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nama Music --}}
        <div class="field">
          <label class="label" for="name">Nama Music</label>
          <input id="name" name="name" type="text" class="input" value="{{ old('name') }}" required>
          @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Artist Logic --}}
        <div class="field">
           <label class="label">Artist</label>

           <input class="sr" type="radio" name="artist_type" id="artist_existing" value="existing" {{ old('artist_type', 'existing') == 'existing' ? 'checked' : '' }}>
           <input class="sr" type="radio" name="artist_type" id="artist_new" value="new" {{ old('artist_type') == 'new' ? 'checked' : '' }}>

           <div class="artist-wrapper toggle-box">
               <div class="toggle-nav">
                   <label for="artist_existing">Pilih Artist</label>
                   <label for="artist_new">Artist Baru</label>
               </div>
               <div class="toggle-content">
                   <div class="section-panel panel-existing">
                       <select name="artist_id" class="select">
                           <option value="">-- Pilih Artist --</option>
                           @foreach($artists as $artist)
                               <option value="{{ $artist->id }}" {{ old('artist_id') == $artist->id ? 'selected' : '' }}>{{ $artist->name }}</option>
                           @endforeach
                       </select>
                       @error('artist_id') <div class="error">{{ $message }}</div> @enderror
                   </div>
                   <div class="section-panel panel-new">
                       <input name="new_artist_name" type="text" class="input" placeholder="Nama Artist Baru" value="{{ old('new_artist_name') }}">
                       @error('new_artist_name') <div class="error">{{ $message }}</div> @enderror
                   </div>
               </div>
           </div>
           @error('artist_type') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Thumbnail --}}
        <div class="field">
          <label class="label" for="thumbnail">Thumbnail</label>
          <input id="thumbnail" name="thumbnail" type="file" class="file" accept="image/*">
          @error('thumbnail') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Source Logic --}}
        <div class="field">
           <label class="label">Sumber Audio</label>

           <input class="sr" type="radio" name="source" id="src_file" value="file" {{ old('source', 'file') == 'file' ? 'checked' : '' }}>
           <input class="sr" type="radio" name="source" id="src_yt" value="youtube" {{ old('source') == 'youtube' ? 'checked' : '' }}>

           <div class="source-wrapper toggle-box">
               <div class="toggle-nav">
                   <label for="src_file">Upload File</label>
                   <label for="src_yt">Link YouTube</label>
               </div>
               <div class="toggle-content">
                   <div class="section-panel panel-file">
                       <label class="label">File Audio (Max 20MB)</label>
                       <input name="file" type="file" class="file" accept=".mp3,.wav,audio/*">
                       @error('file') <div class="error">{{ $message }}</div> @enderror
                   </div>
                   <div class="section-panel panel-yt">
                       <label class="label">URL YouTube</label>
                       <input name="youtube_url" type="url" class="input" placeholder="https://youtube.com/..." value="{{ old('youtube_url') }}">
                       @error('youtube_url') <div class="error">{{ $message }}</div> @enderror
                   </div>
               </div>
           </div>
           @error('source') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Lyrics --}}
        <div class="field">
          <label class="label" for="lyrics">Lirik</label>
          <textarea id="lyrics" name="lyrics" class="textarea">{{ old('lyrics') }}</textarea>
        </div>

        <div class="actions">
          <button type="submit" class="btn">Simpan Lagu</button>
          <a href="{{ route('music.index') }}" class="btn-ghost">Batal</a>
        </div>

      </form>
    </div>
  </div>
</div>

</body>
</html>
