<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stabiz API</title>
</head>
<body>
<h1 style="text-align: center; margin: 50px auto;">
    Hello, you're on {{ app()->environment() == 'local' ? 'dev': app()->environment() }} server
</h1>
</body>
</html>
