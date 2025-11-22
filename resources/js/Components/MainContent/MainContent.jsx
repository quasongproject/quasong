// src/components/MainContent/MainContent.jsx
import React from 'react';
import './MainContent.css';
import Sidebar from './Sidebar/Sidebar';
import Content from './Content/Content';
import RightSidebar from './RightSidebar/RightSidebar';

const MainContent = ({
    currentSong,
    activeMenu,
    setActiveMenu,
    currentTime,
    songs,
    onPickSong,
    onResetSearch, // Terima prop baru
    searchQuery
}) => {
    return (
        <div className="main-layout">
            <aside className="sidebar">
                {/* Teruskan onResetSearch ke Sidebar */}
                <Sidebar activeMenu={activeMenu} setActiveMenu={setActiveMenu} onResetSearch={onResetSearch}/>
            </aside>

            <main className="main-content">
                <Content
                    activeMenu={activeMenu}
                    songs={songs}
                    onPickSong={onPickSong}
                    searchQuery={searchQuery}
                />
            </main>

            <aside className="right-sidebar">
                <RightSidebar currentSong={currentSong} currentTime={currentTime} />
            </aside>
        </div>
    );
};

export default MainContent;
