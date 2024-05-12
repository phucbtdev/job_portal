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
    <h1>{{ $mailData['employer']->name }}</h1>
    <p>Job title:{{ $mailData['job']->title }}</p>

    <p>Employee Details:</p>

    <p>Employee Name: {{$mailData['user']->name}}</p>
    <p>Employee Email: {{$mailData['user']->email}}</p>
    <p>Employee Mobile: {{$mailData['user']->mobile}}</p>
</body>
</html>
