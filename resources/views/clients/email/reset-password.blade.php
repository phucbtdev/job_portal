<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Reset password</h1>
    <a href="{{ route('account.resetPassword',$dataMail['token'] ) }}">Click here!</a>
    <p>Thanks</p>
</body>
</html>
