<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
<p>Hello {{ $name }},</p>

<p>You are receiving this email because we received a password reset request for your account.</p>

<p>Click the following link to reset your password:</p>

<a href="{{ $resetLink }}">{{ $resetLink }}</a>

<p>This password reset link will expire in 2 hours.</p>

<p>If you did not request a password reset, no further action is required.</p>

<p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
