import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useEffect } from 'react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        try {
            const saved = localStorage.getItem('qs_remember_email');
            if (saved) {
                setData('email', saved);
                setData('remember', true);
            }
        } catch (e) {
            // ignore localStorage errors (e.g., disabled)
        }
    }, []);

    const submit = (e) => {
        e.preventDefault();

        post(route('login'), {
            onSuccess: () => {
                try {
                    if (data.remember) {
                        localStorage.setItem('qs_remember_email', data.email || '');
                    } else {
                        localStorage.removeItem('qs_remember_email');
                    }
                } catch (e) {
                    // ignore localStorage errors
                }
            },
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div style={{marginBottom:12,fontSize:13,color:'#7ee7a6'}}>
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        autoComplete="username"
                        isFocused={true}
                        onChange={(e) => setData('email', e.target.value)}
                    />

                    <InputError message={errors.email} />
                </div>

                <div style={{marginTop:12}}>
                    <InputLabel htmlFor="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        autoComplete="current-password"
                        onChange={(e) => setData('password', e.target.value)}
                    />

                    <InputError message={errors.password} />
                </div>

                <div className="qs-auth-row">
                    <label className="qs-auth-remember">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                        />
                        <span>Remember me</span>
                    </label>

                    <div className="qs-auth-actions">
                        <Link href={route('register')} className="qs-btn qs-btn-ghost">
                            Register
                        </Link>

                        <PrimaryButton disabled={processing}>Log in</PrimaryButton>
                    </div>
                </div>
            </form>
        </GuestLayout>
    );
}
