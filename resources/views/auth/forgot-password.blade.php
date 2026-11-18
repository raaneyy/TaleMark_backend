<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi — TaleMark</title>
    <style>
        body { font-family: Inter, system-ui, sans-serif; background:#f8fafc; display:flex; min-height:100vh; align-items:center; justify-content:center; margin:0; }
        .card { background:#fff; width:100%; max-width:420px; padding:40px; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        h1 { font-family: Georgia, serif; font-weight:400; text-align:center; margin:0 0 8px; color:#111827; }
        p.sub { text-align:center; color:#6b7280; font-size:14px; margin:0 0 28px; }
        label { display:block; font-size:12px; font-weight:600; letter-spacing:.08em; color:#4b5563; margin-bottom:8px; }
        input { width:100%; box-sizing:border-box; padding:16px; border:1px solid #e5e7eb; border-radius:12px; background:#f3f4f6; font-size:16px; }
        button { width:100%; margin-top:24px; padding:16px; border:0; border-radius:12px; background:#111827; color:#fff; font-weight:600; font-size:16px; cursor:pointer; }
        .alert { padding:12px 16px; border-radius:12px; font-size:14px; margin-bottom:20px; }
        .ok { background:#ecfdf5; color:#065f46; }
        .err { background:#fef2f2; color:#991b1b; }
        a { display:block; text-align:center; margin-top:20px; color:#4b5563; font-size:14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Lupa Kata Sandi</h1>
        <p class="sub">Masukkan email admin untuk menerima tautan reset.</p>

        @if (session('status'))
            <div class="alert ok">{{ session('status') }}</div>
        @endif
        @error('email')
            <div class="alert err">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email">EMAIL</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <button type="submit">Kirim Tautan Reset</button>
        </form>

        <a href="{{ route('admin.login') }}">Kembali ke halaman masuk</a>
    </div>
</body>
</html>