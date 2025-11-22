// src/components/Favorite.jsx
import React, { useMemo } from 'react';
import { Heart } from 'lucide-react';
import './Favorite.css';

const Favorite = ({ songs = [], onPickSong }) => {
  // Ambil lagu yang di-like & sort berdasarkan likedAt
  const favoriteSongs = useMemo(() => {
    return songs
      .filter((song) => song.isLiked)
      .sort((a, b) => {
        const dateA = a.likedAt ? new Date(a.likedAt).getTime() : 0;
        const dateB = b.likedAt ? new Date(b.likedAt).getTime() : 0;
        return dateB - dateA;
      });
  }, [songs]);

  // Sekarang: kirim index + queue favoriteSongs ke MainApp
  const handlePlayFromFavorite = (songIndex) => {
    if (!onPickSong) return;
    if (!favoriteSongs.length) return;
    onPickSong(songIndex, favoriteSongs);
  };

  const formatDate = (isoString) => {
    if (!isoString) return '-';
    const date = new Date(isoString);
    return date.toLocaleDateString();
  };

  return (
    <section className="favorite-page">
      <header className="favorite-header">
        <div className="playlist-cover-art">
          <Heart className="icon" strokeWidth={1} />
        </div>
        <div className="playlist-details">
          <span className="playlist-type">Playlist</span>
          <span className="playlist-title">Favorite</span>
          <span className="playlist-song-count">
            {favoriteSongs.length} songs
          </span>
        </div>
      </header>

      <div className="song-list-container">
        <div className="song-list-header">
          <div className="song-number">No</div>
          <div>Title</div>
          <div>Date added</div>
        </div>

        {favoriteSongs.length === 0 && (
          <div className="song-row">
            <div className="song-number">-</div>
            <div className="song-title-cell">
              <div className="song-info">
                <div className="title">Belum ada lagu favorit</div>
                <div className="artist">
                  Like beberapa lagu dulu di halaman Home.
                </div>
              </div>
            </div>
            <div className="song-date-added">-</div>
          </div>
        )}

        {favoriteSongs.map((song, index) => (
          <div key={song.id} className="song-row">
            <div className="song-number">{index + 1}</div>

            <div
              className="song-title-cell"
              onClick={() => handlePlayFromFavorite(index)}
              style={{ cursor: 'pointer' }}
            >
              <img src={song.image} alt={song.title} />
              <div className="song-info">
                <div className="title">{song.title}</div>
                <div className="artist">{song.artist}</div>
              </div>
            </div>

            <div className="song-date-added">
              {formatDate(song.likedAt)}
            </div>
          </div>
        ))}
      </div>
    </section>
  );
};

export default Favorite;
