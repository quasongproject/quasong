import React, { useState } from 'react';
import { PlusCircle, Trash2 } from 'lucide-react';
import './Playlist.css';
import PlaylistDetail from '../PlaylistDetail/PlaylistDetail';

// Data dummy untuk playlist
const dummyPlaylists = [
    {
        id: 1,
        name: 'Playlist 1',
        creator: 'Rizky Khatami',
        coverImage: 'https://assets.himatif.org/images/anggota/2024/140810240073.jpg',
        songs: [
            { id: 101, title: 'Lagu Santai', artist: 'Artis A', album: 'Album X', duration: '3:45', image: 'https://via.placeholder.com/150/111/fff?text=A' },
            { id: 102, title: 'Nada Senja', artist: 'Artis B', album: 'Album X', duration: '4:15', image: 'https://via.placeholder.com/150/222/fff?text=B' },
            { id: 103, title: 'Melodi Pagi', artist: 'Artis C', album: 'Album Z', duration: '2:50', image: 'https://via.placeholder.com/150/333/fff?text=C' },
        ]
    },
    {
        id: 2,
        name: 'Playlist 2',
        creator: 'Hamzah Gabriel',
        coverImage: 'https://assets.himatif.org/images/anggota/2024/140810240029.jpg',
        songs: [
            { id: 201, title: 'Rhythm Malam', artist: 'Artis D', album: 'Album Y', duration: '2:55', image: 'https://via.placeholder.com/150/444/fff?text=D' },
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 3,
        name: 'Playlist 3',
        creator: 'Rehan Aziz',
        coverImage: 'https://assets.himatif.org/images/anggota/2024/140810240075.jpg',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 4,
        name: 'Playlist 4',
        creator: 'Creator 4',
        coverImage: 'https://i.scdn.co/image/ab67706f00000002aa9393e18a0f117c0a370b3c',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 5,
        name: 'Playlist 5',
        creator: 'Creator 5',
        coverImage: 'https://i.scdn.co/image/ab67706f00000002a83335f67c293671f65c1a40',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 6,
        name: 'Playlist 6',
        creator: 'Creator 6',
        coverImage: 'https://i.scdn.co/image/ab67706f00000002f1d7a8d5f3a0c5c0a2a1bd79',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 7,
        name: 'Playlist 7',
        creator: 'Creator 7',
        coverImage: 'https://i.scdn.co/image/ab67706f00000002b545f2a1380965e135e69317',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 8,
        name: 'Playlist 8',
        creator: 'Creator 8',
        coverImage: 'https://i.scdn.co/image/ab67706f000000021e0a89a0155b44484b1d50c9',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
    {
        id: 9,
        name: 'Playlist 9',
        creator: 'Creator 9',
        coverImage: 'https://i.scdn.co/image/ab67706f000000021e0a89a0155b44484b1d50c9',
        songs: [
            { id: 202, title: 'Indie Pop', artist: 'Artis E', album: 'Album Y', duration: '3:10', image: 'https://via.placeholder.com/150/555/fff?text=E' },
        ]
    },
];

const Playlist = () => {
    // State untuk melacak playlist yang sedang dilihat, null berarti melihat grid
    const [selectedPlaylist, setSelectedPlaylist] = useState(null);

    // Jika ada playlist yang dipilih, tampilkan halaman detailnya
    if (selectedPlaylist) {
        return (
            <PlaylistDetail
                playlist={selectedPlaylist}
                onBack={() => setSelectedPlaylist(null)} // Fungsi untuk kembali
            />
        );
    }

    // Jika tidak ada playlist yang dipilih, tampilkan grid seperti biasa
    return (
        <section className="playlist-page">
            <div className="playlist-page-header">
                <h2 className="playlist-title">Playlist Anda</h2>
                <div className="action-buttons">
                    <button className="action-icon" aria-label="Add new playlist">
                        <PlusCircle size={24} />
                    </button>
                    <button className="action-icon" aria-label="Delete selected playlists">
                        <Trash2 size={24} />
                    </button>
                </div>
            </div>

            <div className="playlist-grid">
                {dummyPlaylists.map(playlist => (
                    // Saat card diklik, ubah state menjadi playlist yang dipilih
                    <div
                        key={playlist.id}
                        className="playlist-card"
                        onClick={() => setSelectedPlaylist(playlist)}
                    >
                        <div className="playlist-card-image-container">
                            <img
                                src={playlist.coverImage}
                                alt={`Cover for ${playlist.name}`}
                                className="playlist-card-image"
                            />
                        </div>
                        <div className="playlist-card-info">
                            <h3 className="playlist-card-title">{playlist.name}</h3>
                            <p className="playlist-card-creator">{playlist.creator}</p>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    );
};

export default Playlist;