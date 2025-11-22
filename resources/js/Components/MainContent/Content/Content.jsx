// js/Components/MainContent/Content/Content.jsx
import React from 'react';
import './Content.css';
import Home from '../Home/Home';
import Playlist from '../Playlist/Playlist';
import Favorite from '../Favorite/Favorite';


const Content = ({ activeMenu, songs, onPickSong, searchQuery }) => {
  if (activeMenu === 'favorite') {
    return (
      <Favorite
        songs={songs}
        onPickSong={onPickSong}
      />
    );
  }
  return <Home songs={songs} onPickSong={onPickSong} searchQuery={searchQuery} />;
};

export default Content;
