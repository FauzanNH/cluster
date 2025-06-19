<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="{{ route('register.tambahakun') }}" method="POST">
        @csrf
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="no_hp" placeholder="No HP" required>
        <select name="gender" id="gender" required>
            <option value="laki-laki">Laki-laki</option>
            <option value="perempuan">Perempuan</option>
        </select>
        <input type="text" name="alamat" placeholder="Alamat" required>
        <input type="password" name="password" placeholder="Password" required>
        @error('password')
            <div style="color: red;">{{ $message }}</div>
        @enderror
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        @error('password_confirmation')
            <div style="color: red;">{{ $message }}</div>
        @enderror
        <input type="checkbox" name="agreement" id="agreement" required>
        <label for="agreement">Saya Bersedia dan bertanggung jawab atas data yang saya masukkan dan peraturan yang ditetapkan oleh Bukit Indah Cluster.</label>
        
        <!-- Tambahkan reCAPTCHA -->
        <div class="g-recaptcha" data-sitekey="{{ $recaptcha_site_key }}"></div>
        
        <button type="submit">Register</button>
    </form>
    @if ($errors->has('captcha'))
        <div style="color:red;">{{ $errors->first('captcha') }}</div>
    @endif
    <a href="{{ route('login') }}">kembali</a>
    <!-- Tambahkan script reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>