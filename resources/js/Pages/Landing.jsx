import { Head, Link } from '@inertiajs/react';
import '../../css/landing.css';

export default function Landing({ auth }) {
    return (
        <>
            <Head title="Quasong - Music Player" />

            <div className="qs-landing-root">
                <header className="qs-header">
                    <div className="qs-brand">Quasong</div>

                    <nav className="qs-nav">
                        {auth?.user ? (
                            <Link href={route('player')} className="qs-btn qs-btn-ghost">
                                Open Player
                            </Link>
                        ) : (
                            <>
                                <Link href={route('login')} className="qs-btn qs-btn-outline">
                                    Log in
                                </Link>
                                <Link href={route('register')} className="qs-btn qs-btn-primary">
                                    Register
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                <main className="qs-hero">
                    <div className="qs-hero-content">
                        <h1 className="qs-title">Listen. Feel. Move.</h1>
                        <p className="qs-sub">A modern music player for discovering tracks, building favourite, and sharing vibes.</p>

                        <div className="qs-hero-ctas">
                            <Link href={auth?.user ? route('player') : route('register')} className="qs-cta qs-cta-primary">
                                Get Started
                            </Link>
                        </div>
                    </div>

                    <div className="qs-hero-art" aria-hidden>
                        <div className="qs-disc">
                            <div className="qs-center" />
                        </div>
                    </div>
                </main>

                <footer className="qs-footer">140810240029 - 140810240073 - 140810240075</footer>
            </div>
        </>
    );
}
