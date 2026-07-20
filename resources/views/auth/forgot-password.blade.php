<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Inviniux</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-slate-900 rounded-3xl p-8 border border-slate-800 shadow-2xl">
        <div class="mb-6 text-center">
            <h3 class="text-2xl font-bold text-white mb-2">Lupa Password</h3>
            <p class="text-slate-400 text-sm">
                {{ session('otp_sent') ? 'Masukkan kode OTP yang dikirim ke email Anda.' : 'Masukkan email Gmail terdaftar untuk menerima kode OTP.' }}
            </p>
        </div>

        @if(session('status'))
            <div class="bg-blue-500/10 border border-blue-500/50 text-blue-400 px-4 py-3 rounded-xl text-xs mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form action="{{ session('otp_sent') ? route('password.verify') : route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Alamat Email</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', session('otp_email')) }}" placeholder="contoh@gmail.com" 
                        {{ session('otp_sent') ? 'readonly' : '' }}
                        class="w-full placeholder:text-slate-500 bg-slate-800/40 border @error('email') border-red-500 @else border-slate-700 @enderror text-white text-sm rounded-xl px-11 py-4 outline-none transition-all focus:ring-2 focus:ring-blue-500/50 {{ session('otp_sent') ? 'opacity-60 cursor-not-allowed' : '' }}" required>
                </div>
                @error('email')
                    <p class="text-red-400 text-xs mt-2 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                @enderror
            </div>

            @if(session('otp_sent'))
                <div>
                    <label class="block text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Kode OTP (6 Digit)</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 group-focus-within:text-amber-500 transition-colors">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <input type="text" name="otp" value="{{ old('otp') }}" placeholder="Masukkan OTP" autocomplete="off" maxlength="6"
                            class="w-full placeholder:text-slate-500 bg-slate-800/40 border @error('otp') border-red-500 @else border-slate-700 @enderror text-white text-sm rounded-xl px-11 py-4 outline-none focus:ring-2 focus:ring-amber-500/50" required>
                    </div>
                    @error('otp')
                        <p class="text-red-400 text-xs mt-2 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                @if(session('otp_sent'))
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                        <span>Verifikasi OTP</span>
                        <i class="fas fa-check-circle"></i>
                    </button>
                @else
                    <button type="submit" id="btnOtp" onclick="this.disabled=true;this.classList.add('bg-slate-700','cursor-not-allowed');this.classList.remove('bg-blue-600','hover:bg-blue-500');this.querySelector('span').textContent='Mengirim...';this.querySelector('i').className='fas fa-spinner fa-spin';this.form.submit();" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                        <span>Kirim Kode OTP</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                @endif
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>

</body>
</html>