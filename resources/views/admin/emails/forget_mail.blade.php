<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body>
    <p>Hello {{ $userDetails['name'] }},</p>
    <p>Your OTP code is: {{ $userDetails['otp'] }}</p>
    <p>Thank you.</p>
</body>
</html>
