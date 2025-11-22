// src/components/MusicPlayer/MusicPlayer.jsx
import React, { useRef, useEffect, useState } from "react";
import { Play, Pause, SkipBack, SkipForward, Volume2, Shuffle, Repeat } from "lucide-react";
import { Head } from '@inertiajs/react';
import "./MusicPlayer.css";

function MusicPlayer({
  currentSong,
  isPlaying,
  setIsPlaying,
  onTimeUpdate,
  onEnded,
  nextSong,
  prevSong,
  onLikeToggle,
  // optional props passed from parent
  isShuffle = false,
  repeatMode = 'none', // 'none'|'all'|'one'
  toggleShuffle,
  toggleRepeat,
  onShuffleNext
}) {
  const audioRef = useRef(null);
  const [progress, setProgress] = useState(0);
  const [duration, setDuration] = useState(0);
  const [volume, setVolume] = useState(1);
  const [isLiking, setIsLiking] = useState(false);

  // Hanya load ulang jika SRC berubah (ganti lagu), bukan ketika data lain berubah
  useEffect(() => {
    const audio = audioRef.current;
    if (!audio) return;
    audio.load();
    audio.volume = volume;
    if (isPlaying) {
      audio.play().catch(() => setIsPlaying(false));
    }
  }, [currentSong?.src]); // Hanya depend pada src, bukan seluruh currentSong object

  // Handle play/pause terpisah
  useEffect(() => {
    const audio = audioRef.current;
    if (!audio) return;

    if (isPlaying) {
      audio.play().catch(() => setIsPlaying(false));
    } else {
      audio.pause();
    }
  }, [isPlaying]);

  useEffect(() => {
    const audio = audioRef.current;
    if (!audio) return;

    const handleTime = () => {
      setProgress(audio.currentTime);
      onTimeUpdate?.(audio.currentTime);
    };

    const handleLoaded = () => setDuration(audio.duration || 0);
    const handleEnded = () => {
      // Repeat one: restart same track
      if (repeatMode === 'one') {
        audio.currentTime = 0;
        audio.play().then(() => setIsPlaying(true)).catch(() => setIsPlaying(false));
        return;
      }

      // Shuffle: prefer parent's onShuffleNext if provided
      if (isShuffle) {
        if (typeof onShuffleNext === 'function') {
          onShuffleNext();
        } else {
          // fallback to default next handler which may also handle shuffle
          onEnded?.();
        }
        return;
      }

      // Default: call provided onEnded (usually advances to next)
      onEnded?.();
    };

    audio.addEventListener("timeupdate", handleTime);
    audio.addEventListener("loadedmetadata", handleLoaded);
    audio.addEventListener("ended", handleEnded);

    return () => {
      audio.removeEventListener("timeupdate", handleTime);
      audio.removeEventListener("loadedmetadata", handleLoaded);
      audio.removeEventListener("ended", handleEnded);
    };
  }, [onTimeUpdate, onEnded, isShuffle, repeatMode, onShuffleNext]);

  const playPause = () => {
    const audio = audioRef.current;
    if (!audio) return;
    if (isPlaying) {
      audio.pause();
      setIsPlaying(false);
    } else {
      audio.play().then(() => setIsPlaying(true)).catch(() => setIsPlaying(false));
    }
  };

  const handleSeek = (e) => {
    const t = Number(e.target.value);
    const audio = audioRef.current;
    if (!audio) return;
    audio.currentTime = t;
    setProgress(t);
    onTimeUpdate?.(t);
  };

  const handleVolume = (e) => {
    const v = Number(e.target.value);
    setVolume(v);
    if (audioRef.current) audioRef.current.volume = v;
  };

  const handleLike = async () => {
    if (!currentSong || isLiking) return;

    setIsLiking(true);
    try {
      await onLikeToggle(currentSong.id);
    } finally {
      setIsLiking(false);
    }
  };

  const formatTime = (time) => {
    if (!time || isNaN(time)) return "0:00";
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60).toString().padStart(2, "0");
    return `${minutes}:${seconds}`;
  };

  if (!currentSong) {
    return (
      <div className="player-container">
        <p>Tidak ada lagu dipilih 🎵</p>
      </div>
    );
  }

  return (
    <div className="player-container">
      <Head title="Quasong" />
      <audio ref={audioRef} src={currentSong.src} preload="metadata" />

      {/* KIRI: Info Lagu */}
      <div className="player-info">
        <img src={currentSong.image} alt={currentSong.title} />
        <div>
          <h4>{currentSong.title}</h4>
          <p>{currentSong.artist}</p>
        </div>
        {/* Like Button */}
        <button
          className={`like-button ${currentSong.isLiked ? 'liked' : ''} ${isLiking ? 'liking' : ''}`}
          onClick={handleLike}
          disabled={isLiking}
          title={currentSong.isLiked ? 'Unlike' : 'Like'}
        >
          <svg
            className="heart-icon"
            viewBox="0 0 24 24"
            width="20"
            height="20"
          >
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
          {currentSong.likesCount > 0 && (
            <span className="like-count">{currentSong.likesCount}</span>
          )}
        </button>
      </div>

      {/* TENGAH: Kontrol + Progress */}
      <div className="player-center">
        <div className="player-controls">
          <button onClick={prevSong}><SkipBack size={20} /></button>
          <button onClick={playPause}>
            {isPlaying ? <Pause size={24} /> : <Play size={24} />}
          </button>
          <button onClick={nextSong}><SkipForward size={20} /></button>
        </div>
        <div className="player-progress">
          <span>{formatTime(progress)}</span>
          <input
            type="range"
            min="0"
            max={duration || 0}
            value={progress}
            onChange={handleSeek}
          />
          <span>{formatTime(duration)}</span>
        </div>
      </div>

      {/* KANAN: Volume + Like + Opsi */}
      <div className="player-options">
        <div className="extra-controls">
          <button
            className={`btn-icon shuffle-btn ${isShuffle ? 'active' : ''}`}
            title={isShuffle ? 'Shuffle: On' : 'Shuffle: Off'}
            onClick={() => toggleShuffle?.()}
          >
            <Shuffle size={18} />
          </button>

          <button
            className={`btn-icon repeat-btn ${repeatMode !== 'none' ? 'active' : ''} ${repeatMode === 'one' ? 'repeat-one' : ''}`}
            title={repeatMode === 'one' ? 'Repeat: One' : repeatMode === 'all' ? 'Repeat: All' : 'Repeat: Off'}
            onClick={() => toggleRepeat?.()}
          >
            <Repeat size={18} />
          </button>
        </div>

        <div className="volume-control">
          <Volume2 size={18} />
          <input
            type="range"
            min="0"
            max="1"
            step="0.01"
            value={volume}
            onChange={handleVolume}
          />
        </div>
      </div>
    </div>
  );
}

export default MusicPlayer;
