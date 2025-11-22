// src/components/Home.jsx
import React, { useState } from 'react';
import './Home.css';

const Home = ({ songs, onPickSong, searchQuery }) => {
    const isSearching = searchQuery && searchQuery.trim().length > 0;

    const carouselItems = [
        { src: "/images/tb.jpg", alt: "quasong" }
    ];

    const [activeIndex, setActiveIndex] = useState(0);
    const handlePrev = () => setActiveIndex(prev => prev === 0 ? carouselItems.length - 1 : prev - 1);
    const handleNext = () => setActiveIndex(prev => (prev + 1) % carouselItems.length);
    const handleIndicatorClick = (index) => setActiveIndex(index);

    return (
        <>
            {/* Carousel */}
            {!isSearching && (
                <div id="carouselExampleIndicators" className="carousel slide">
                    <div className="carousel-indicators">
                        {carouselItems.map((_, index) => (
                            <button
                                key={index}
                                type="button"
                                onClick={() => handleIndicatorClick(index)}
                                className={activeIndex === index ? "active" : ""}
                                aria-current={activeIndex === index ? "true" : "false"}
                                aria-label={`Slide ${index + 1}`}
                            />
                        ))}
                    </div>

                    <div className="carousel-inner">
                        {carouselItems.map((item, index) => (
                            <div key={index} className={`carousel-item ${activeIndex === index ? "active" : ""}`}>
                                <img src={item.src} className="d-block w-100" alt={item.alt} />
                            </div>
                        ))}
                    </div>

                    <button className="carousel-control-prev" type="button" onClick={handlePrev}>
                        <span className="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span className="visually-hidden">Previous</span>
                    </button>

                    <button className="carousel-control-next" type="button" onClick={handleNext}>
                        <span className="carousel-control-next-icon" aria-hidden="true"></span>
                        <span className="visually-hidden">Next</span>
                    </button>
                </div>
            )}

            <section>
                <h2 className="recommendations-title">Rekomendasi</h2>
                <div className="recommendations-grid">
                    {songs.map((song, idx) => (
                        <div
                            key={song.id || idx}
                            className="recommendation-item"
                            onClick={() => onPickSong(idx, songs)}
                        >
                            <img src={song.image} alt={song.title} className="recommendation-image" />
                            <div className="recommendation-info">
                                <h3 className="recommendation-title">{song.title}</h3>
                                <p className="recommendation-artist">{song.artist}</p>
                            </div>
                        </div>
                    ))}
                    {songs.length === 0 && isSearching && (
                        <p style={{ color: '#aaa', marginTop: '10px' }}>
                          Tidak ditemukan hasil untuk "{searchQuery}"
                        </p>
                    )}
                </div>
            </section>
        </>
    );
};

export default Home;
