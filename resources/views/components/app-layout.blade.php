<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pluralize/8.0.0/pluralize.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="max-w-4xl mx-auto my-12 px-6">
        {{ $slot }}
    </div>
</body>
</html>
