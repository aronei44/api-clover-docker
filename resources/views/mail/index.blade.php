<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <h1>SELAMAT DATANG {{$details['username']}} di {{env('APP_NAME')}}</h1>
    <br>
    <br>
    <a href="{{$details['link']}}">Verify Email</a>
    <br>
    <br>
    <p>Best Regards,</p>
    <p>{{env('APP_URL')}}</p>
    <p>Jika tidak bisa klik link di atas. silakan copy :</p>
    <p>{{$details['link']}}</p>
</body>
</html>