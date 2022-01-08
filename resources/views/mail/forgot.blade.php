<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Reset Password</title>
</head>
<body>
    <h1>Permintaan Reset Password untuk email : {{$details['email']}}</h1>
    <br>
    <br>
    <p>Jika Bukan Anda Yang Meminta Perubahan Password Mohon Abaikan dan Tidak Menyebarkan OTP</p>
    <br>
    <br>
    <h2>{{$details['otp']}}</h2>
    <p>Best Regards,</p>
    <p>{{env('APP_URL')}}</p>
</body>
</html>