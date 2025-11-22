import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import '../../css/auth.css';

export default function GuestLayout({ children }) {
    return (
        <div className="qs-auth-root">
            <div className="qs-auth-inner">
                <div className="qs-auth-brand">
                    <Link href="/">
                        <ApplicationLogo />
                    </Link>
                </div>

                <div className="qs-auth-card">
                    <h1 className="qs-auth-title">Quasong</h1>
                    <p className="qs-auth-desc">Sign in or create an account to enjoy music</p>
                    {children}
                </div>
            </div>
        </div>
    );
}
