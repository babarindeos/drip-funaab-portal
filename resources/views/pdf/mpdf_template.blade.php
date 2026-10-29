<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
        h1 {
            color: #333;
        }
        p {
            line-height: 1.6;
            margin: 15px 0;
        }
        /* Additional CSS styles for formatting */
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Date: {{ $date }}</p>
    
    <!-- Content to demonstrate multi-page capability -->
    <p>{{ $content }}</p>
    <p>{{ $content }}</p>
    <p>{{ $content }}</p>
    <p>{{ $content }}</p>
    <p>{{ $content }}</p>
    <p>{{ $content }}</p>
</body>
</html>
