import React from 'react';
import './RightSidebar.css';

const RightSidebar = ({ currentSong }) => {
    return (
        <div className="song-details">
            <div className="song-info-center">
                <h3 className="current-song-title">{currentSong?.title || 'Judul Lagu'}</h3>
                <p className="current-song-artist">{currentSong?.artist || 'Penyanyi'}</p>

                <div className="current-song-image-container">
                    <img
                        src={currentSong?.image}
                        alt={currentSong?.title || 'Current Song'}
                        className="current-song-image"
                    />
                </div>

                <div className="lyrics-container">
                    <h4 className="lyrics-title">Lyrics</h4>
                    <div className="lyrics-content">
                        {currentSong?.lyrics}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default RightSidebar;
