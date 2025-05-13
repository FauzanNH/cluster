<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <div>
        <form action="{{ route('login.attempt') }}" method="post">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </div>
    @if ($errors->any())
        <script>
            alert("{{ $errors->first() }}");
        </script>
    @endif
</body>
</html>