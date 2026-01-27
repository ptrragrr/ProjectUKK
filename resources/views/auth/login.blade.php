<form method="POST" action="/auth/login">
    @csrf

    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')
            <small style="color:red">{{ $message }}</small>
        @enderror
    </div>

    <div>
        <label>Password</label>
        <input type="password" name="password">
        @error('password')
            <small style="color:red">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit">Login</button>
</form>
