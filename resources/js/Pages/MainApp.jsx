// resources/js/Pages/MainApp.jsx
import React, { useState, useEffect, useMemo } from 'react';
import { router } from '@inertiajs/react';

import Header from '../Components/Header/Header';
import MainContent from '../Components/MainContent/MainContent';
import MusicPlayer from '../Components/MusicPlayer/MusicPlayer';
import './MainApp.css';

export default function MainApp({ auth, songs: serverSongs = [] }) {
  // Sumber data utama semua lagu (tanpa filter)
  const [songs, setSongs] = useState(
    Array.isArray(serverSongs) ? serverSongs : []
  );

  // QUEUE: daftar ID lagu yang jadi urutan pemutaran
  const [playQueue, setPlayQueue] = useState([]); // [songId, songId, ...]

  // Query pencarian
  const [searchQuery, setSearchQuery] = useState('');

  // Simpan ID lagu yang sedang diputar
  const [currentSongId, setCurrentSongId] = useState(null);

  const [isPlaying, setIsPlaying] = useState(false);
  const [activeMenu, setActiveMenu] = useState('home');
  const [currentTime, setCurrentTime] = useState(0);
  const [isShuffle, setIsShuffle] = useState(false);
  const [repeatMode, setRepeatMode] = useState('none'); // 'none' | 'all' | 'one'

  // Daftar lagu yang sudah difilter (hanya untuk tampilan & pemilihan)
  const filteredSongs = useMemo(() => {
    const allSongs = Array.isArray(songs) ? songs : [];
    const trimmedQuery = searchQuery.trim().toLowerCase();

    if (trimmedQuery === '') {
      return allSongs;
    }

    return allSongs.filter((song) => {
      const title = (song.title || '').toLowerCase();
      const artist = (song.artist || '').toLowerCase();
      return (
        title.includes(trimmedQuery) ||
        artist.includes(trimmedQuery)
      );
    });
  }, [songs, searchQuery]);

  // Lagu yang sedang diputar diambil dari daftar penuh `songs` berdasarkan ID
  const currentSong = useMemo(
    () => songs.find((song) => song.id === currentSongId) || null,
    [songs, currentSongId]
  );

  // Inisialisasi currentSongId & playQueue ketika lagu dari server pertama kali masuk
  useEffect(() => {
    if (songs.length > 0 && playQueue.length === 0) {
      const ids = songs.map((s) => s.id);
      setPlayQueue(ids);
      if (!currentSongId) {
        setCurrentSongId(ids[0]); // hanya set sekali di awal
      }
    }
  }, [songs, playQueue.length, currentSongId]);

  // Kalau semua lagu hilang, reset player
  useEffect(() => {
    if (songs.length === 0) {
      setIsPlaying(false);
      setCurrentTime(0);
      setCurrentSongId(null);
      setPlayQueue([]);
    }
  }, [songs.length]);

  // Fungsi untuk melakukan pencarian
  const handleSearch = (query) => {
    setSearchQuery(query); // trimming & lowerCase di-handle di useMemo
    setActiveMenu('home'); // Pastikan tampilan kembali ke Home
  };

  // Fungsi untuk mereset pencarian (dipanggil saat klik Home)
  const handleResetSearch = () => {
    setSearchQuery('');
    // currentSongId & isPlaying tetap -> lagu tetap lanjut
  };

  // Dipanggil saat user klik lagu di list (Home / Favorite / dll)
  // indexInQueue = index lagu dalam queueSongs
  // queueSongs = array lagu yang mau dijadikan scope antrian (misal: favoriteSongs saja)
  const handlePickSong = (indexInQueue, queueSongs) => {
    if (!queueSongs || !queueSongs.length) return;

    const safeIndex =
      indexInQueue < 0 || indexInQueue >= queueSongs.length
        ? 0
        : indexInQueue;

    const queueIds = queueSongs.map((s) => s.id);
    const pickedSong = queueSongs[safeIndex];

    // Set queue hanya ke lagu-lagu di view tersebut (misal Favorite)
    setPlayQueue(queueIds);
    setCurrentSongId(pickedSong.id);
    setIsPlaying(true);
    setCurrentTime(0);
  };

  const hasQueue = playQueue.length > 0;

  const nextSong = () => {
    if (!hasQueue || !currentSongId) return;

    // Jika shuffle aktif, pilih lagu random berbeda
    if (isShuffle && playQueue.length > 1) {
      let attempts = 0;
      let randId = currentSongId;
      while (randId === currentSongId && attempts < 10) {
        const r = Math.floor(Math.random() * playQueue.length);
        randId = playQueue[r];
        attempts += 1;
      }
      if (randId && randId !== currentSongId) {
        setCurrentSongId(randId);
        setIsPlaying(true);
        setCurrentTime(0);
        return;
      }
    }

    const idx = playQueue.indexOf(currentSongId);
    if (idx === -1) return;

    const nextId = playQueue[(idx + 1) % playQueue.length];
    setCurrentSongId(nextId);
    setIsPlaying(true);
    setCurrentTime(0);
  };

  const prevSong = () => {
    if (!hasQueue || !currentSongId) return;

    const idx = playQueue.indexOf(currentSongId);
    if (idx === -1) return;

    const prevIndex = (idx - 1 + playQueue.length) % playQueue.length;
    const prevId = playQueue[prevIndex];

    setCurrentSongId(prevId);
    setIsPlaying(true);
    setCurrentTime(0);
  };

  const handleShuffleNext = () => {
    // Called when player requests a shuffle-next explicitly (from MusicPlayer ended handler)
    if (!hasQueue || !currentSongId) return;
    if (playQueue.length <= 1) {
      // nothing to change, just restart current
      setIsPlaying(true);
      setCurrentTime(0);
      return;
    }

    let randId = currentSongId;
    let attempts = 0;
    while (randId === currentSongId && attempts < 10) {
      const r = Math.floor(Math.random() * playQueue.length);
      randId = playQueue[r];
      attempts += 1;
    }
    if (randId && randId !== currentSongId) {
      setCurrentSongId(randId);
      setIsPlaying(true);
      setCurrentTime(0);
    }
  };

  const toggleShuffle = () => setIsShuffle((s) => !s);

  const toggleRepeat = () => {
    setRepeatMode((m) => {
      if (m === 'none') return 'all';
      if (m === 'all') return 'one';
      return 'none';
    });
  };

  // Handle like toggle
  const handleLikeToggle = async (songId) => {
    try {
      const response = await fetch(`/music/${songId}/toggle-like`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN':
            document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
      });

      const data = await response.json();

      if (data.success) {
        // Update state utama `songs`, termasuk likedAt kalau backend kirim
        setSongs((prevSongs) =>
          prevSongs.map((song) =>
            song.id === songId
              ? {
                  ...song,
                  isLiked: data.isLiked,
                  likesCount: data.likesCount,
                  likedAt: data.likedAt ?? song.likedAt,
                }
              : song
          )
        );
      } else {
        console.error('Failed to toggle like:', data.message);
      }
    } catch (error) {
      console.error('Error toggling like:', error);
    }
  };

  return (
    <div className="App">
      <Header
        onSearch={handleSearch}
        searchQuery={searchQuery}
      />

      <MainContent
        currentSong={currentSong}
        setActiveMenu={setActiveMenu}
        activeMenu={activeMenu}
        currentTime={currentTime}
        songs={filteredSongs}        // untuk tampilan (Home/Favorite)
        onPickSong={handlePickSong}  // <<— penting: kirim handler queue
        onResetSearch={handleResetSearch}
        searchQuery={searchQuery}
      />

      <MusicPlayer
        currentSong={currentSong}
        isPlaying={isPlaying}
        setIsPlaying={setIsPlaying}
        onTimeUpdate={setCurrentTime}
        onEnded={nextSong}
        nextSong={nextSong}
        prevSong={prevSong}
        onLikeToggle={handleLikeToggle}
        isShuffle={isShuffle}
        repeatMode={repeatMode}
        toggleShuffle={toggleShuffle}
        toggleRepeat={toggleRepeat}
        onShuffleNext={handleShuffleNext}
      />
    </div>
  );
}
