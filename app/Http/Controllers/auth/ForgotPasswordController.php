<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\SendOtpMail;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        // Cek apakah ada email di session yang masih memiliki OTP aktif di cache
        $email = session('otp_email');
        if ($email && Cache::has('otp_' . $email)) {
            // Jika masih ada dan aktif, paksa status 'otp_sent' tetap true saat refresh
            session()->flash('otp_sent', true);
        } else {
            // Jika sudah hangus atau belum minta, bersihkan session email otp-nya
            session()->forget('otp_email');
        }

        return view('auth.forgot-password');
    }

    // 1. Kirim OTP ke Email
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:karyawan,email',
        ], [
            'email.exists' => 'Alamat email tidak ditemukan di dalam sistem kami.',
        ]);

        $email = $request->email;
        $otp = rand(100000, 999999);

        // Simpan OTP di cache selama 5 menit
        Cache::put('otp_' . $email, $otp, now()->addMinutes(5));

        Mail::to($email)->send(new SendOtpMail($otp));

        // Simpan email ke session persistent agar data tidak hilang saat refresh
        session(['otp_email' => $email]);

        return back()->withInput()->with([
            'otp_sent' => true,
            'status' => 'Kode OTP berhasil dikirimkan ke email Anda.'
        ]);
    }

    // 2. Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:karyawan,email',
            'otp' => 'required|numeric',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withInput()->with('otp_sent', true)->withErrors(['otp' => 'Kode OTP salah atau telah kedaluwarsa.']);
        }

        // Hapus OTP & session penanda kirim jika berhasil
        Cache::forget('otp_' . $request->email);
        session()->forget('otp_email');

        // Pindahkan email ke session khusus reset password
        session(['reset_email' => $request->email]);

        return redirect()->route('password.reset');
    }

    // 3. Tampilkan Form Reset (Hanya Password)
    public function showResetForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }

    // 4. Eksekusi Update Password
    public function reset(Request $request)
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password minimal diisi sebanyak 8 karakter.',
        ]);

        $user = User::where('email', session('reset_email'))->first();
        
        if ($user) {
            $user->update([
                'password' => $request->password
            ]);

            session()->forget('reset_email');

            return redirect()->route('login')->with('toast_success', 'Password berhasil diperbarui! Silakan login.');
        }

        return redirect()->route('password.request')->withErrors(['email' => 'Terjadi kesalahan, silakan coba lagi.']);
    }
}