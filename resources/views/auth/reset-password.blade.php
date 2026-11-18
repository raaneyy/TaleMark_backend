<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Kata Sandi — TaleMark</title>
    <style>
        body { font-family: Inter, system-ui, sans-serif; background:#f8fafc; display:flex; min-height:100vh; align-items:center; justify-content:center; margin:0; }
        .card { background:#fff; width:100%; max-width:420px; padding:40px; border-radius:24px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        h1 { font-family: Georgia, serif; font-weight:400; text-align:center; margin:0 0 28px; color:#111827; }
        label { display:block; font-size:12px; font-weight:600; letter-spacing:.08em; color:#4b5563; margin:20px 0 8px; }
        input { width:100%; box-sizing:border-box; padding:16px; border:1px solid #e5e7eb; border-radius:12px; background:#f3f4f6; font-size:16px; }
        button { width:100%; margin-top:28px; padding:16px; border:0; border-radius:12px; background:#111827; color:#fff; font-weight:600; font-size:16px; cursor:pointer; }
        .alert { padding:12px 16px; border-radius:12px; font-size:14px; margin-bottom:12px; background:#fef2f2; color:#991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Buat Kata Sandi Baru</h1>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert">{{ $error }}</div>
            @endforeach
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">EMAIL</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required readonly>

            <label for="password">KATA SANDI BARU</label>
            <input id="password" type="password" name="password" required minlength="8">

            <label for="password_confirmation">ULANGI KATA SANDI</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8">

            <button type="submit">Simpan Kata Sandi</button>
        </form>
    </div>
</body>
</html>