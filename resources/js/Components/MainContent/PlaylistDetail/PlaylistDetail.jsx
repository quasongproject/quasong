import React from 'react';
import { Search, PlayCircle, PlusCircle, Trash2, ChevronLeft } from 'lucide-react';
import './PlaylistDetail.css';

const PlaylistDetail = ({ playlist, onBack }) => {
    // Mencegah error jika data playlist belum ada
    if (!playlist) {
        return null;
    }

    const { name, creator, coverImage, songs } = playlist;

    return (
        <section className="playlist-detail-page">
            {/* Tombol Kembali */}
            <button onClick={onBack} className="back-button">
                <ChevronLeft size={24} /> 
            </button>

            {/* Header */}
            <header className="playlist-detail-header">
                <div className="playlist-cover-art">
                    <img src={coverImage} alt={name} className="playlist-cover-image" />
                </div>
                <div className="playlist-details">
                    <span className="playlist-type">Playlist</span>
                    <h1 className="playlist-title">{name}</h1>
                    <p className="playlist-creator">Dibuat oleh: {creator}</p>
                    <span className="playlist-song-count">
                        {songs.length} lagu
                    </span>
                </div>
            </header>

            {/* Kontrol (Search, Play, dll) */}
            <div className="playlist-controls">
                <div className="search-box">
                    <Search className="search-icon" size={18} />
                    <input type="text" className="search-input" placeholder="Cari di dalam playlist" />
                </div>
                <div className="action-buttons">
                    <button className="play-button" aria-label="Play">
                        <PlayCircle size={48} strokeWidth={1.5} />
                    </button>
                    <button className="action-icon" aria-label="Add song">
                        <PlusCircle size={24} />
                    </button>
                    <button className="action-icon" aria-label="Delete song">
                        <Trash2 size={24} />
                    </button>
                </div>
            </div>

            {/* Daftar Lagu */}
            <div className="song-list-container">
                <div className="song-list-header">
                    <div className="song-number">#</div>
                    <div>Judul</div>
                    <div>Album</div>
                    <div>Durasi</div>
                </div>
                {songs.map((song, index) => (
                    <div key={song.id} className="song-row">
                        <div className="song-number">{index + 1}</div>
                        <div className="song-title-cell">
                            <img src={song.image} alt={song.title} />
                            <div className="song-info">
                                <div className="title">{song.title}</div>
                                <div className="artist">{song.artist}</div>
                            </div>
                        </div>
                        <div className="song-album">{song.album}</div>
                        <div className="song-duration">{song.duration}</div>
                    </div>
                ))}
            </div>
        </section>
    );
};

export default PlaylistDetail;