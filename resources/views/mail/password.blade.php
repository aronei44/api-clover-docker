<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Berubah</title>
</head>
<body>
    <h1>Password untuk username : {{$details['username']}} di {{env('APP_NAME')}} Telah berubah</h1>
    <br>
    <br>
    <p>Jangan bagikan password baru anda dengan orang lain</p>
    <br>
    <br>
    <p>Best Regards,</p>
    <p>{{env('APP_URL')}}</p>
</body>
</html>