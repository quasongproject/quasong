import React from 'react';
import { Home, Music, Star } from 'lucide-react';
import './Sidebar.css';

const Sidebar = ({ activeMenu, setActiveMenu, onResetSearch }) => {
    const handleHomeClick = () => {
        // Reset daftar lagu ke semua lagu (jika ada pencarian)
        if (onResetSearch) {
            onResetSearch();
        }
        // Set menu aktif ke home
        setActiveMenu('home');
    };

    return (
        <nav className="nav">
            <button
                // onClick={() => setActiveMenu('home')}
                onClick={handleHomeClick}
                className={`nav-button ${activeMenu === 'home' ? 'nav-button-active' : ''}`}
            >
                <Home className="nav-icon" />
                Home
            </button>



            <button
                onClick={() => setActiveMenu('favorite')}
                className={`nav-button ${activeMenu === 'favorite' ? 'nav-button-active' : ''}`}
            >
                <Star className="nav-icon" />
                Favorite
            </button>
        </nav>
    );
};

export default Sidebar;
