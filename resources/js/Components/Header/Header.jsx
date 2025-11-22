// resources/js/Components/Header/Header.jsx
import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { Search } from 'lucide-react';
import './Header.css';

const Header = ({ onSearch, searchQuery }) => {
  const { auth } = usePage().props;
  const isLoggedIn = !!auth?.user;

  // State lokal untuk mengontrol input teks
  const [inputQuery, setInputQuery] = useState(searchQuery);

  // Sinkronkan inputQuery dengan searchQuery dari state global saat berubah
  useEffect(() => {
    setInputQuery(searchQuery);
  }, [searchQuery]);

  const handleChange = (event) => {
    setInputQuery(event.target.value);
  };

  const handleKeyDown = (event) => {
    // Cek jika tombol yang ditekan adalah Enter
    if (event.key === 'Enter') {
      // Panggil fungsi pencarian yang diteruskan dari MainApp
      if (onSearch) {
        onSearch(inputQuery);
      }
      // Hentikan perilaku default form submission
      event.preventDefault();
    }
  };

  // Fungsi untuk memulai pencarian (jika tidak pakai enter)
  const handleSearchClick = () => {
    if (onSearch) {
      onSearch(inputQuery);
    }
  };

  return (
    <header className="header">

      <Link className="logo">

      </Link>

      <div className="search-container">
        <div className="search-input-wrapper">
          <Search className="search-icon" onClick={handleSearchClick}/>
          <input
            type="text"
            placeholder="What do you want to play?"
            className="search-input"
            value={inputQuery} // Gunakan state lokal
            onChange={handleChange} // Update state lokal saat input berubah
            onKeyDown={handleKeyDown} // Handle Enter
          />
        </div>
      </div>

      <div className="auth-buttons">
        {isLoggedIn ? (
          <>
            <span className="user-name">
              Hi, {auth.user.name}
            </span>

            {/* Logout pakai POST /logout (bawaan Breeze/Fortify) */}
            <Link
              href="/logout"
              method="post"
              as="button"
              className="logout-btn"
            >
              Logout
            </Link>
          </>
        ) : (
          <>
            <Link href="/login" className="login-btn">
              Sign In
            </Link>
            <Link href="/register" className="signup-btn">
              Sign Up
            </Link>
          </>
        )}
      </div>
    </header>
  );
};

export default Header;
