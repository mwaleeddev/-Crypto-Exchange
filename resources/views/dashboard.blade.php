<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crypto Exchange - Trading Platform</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAACvSURBVHgB7ZKxDYAwDESdEAZiA1ZgAsZiKvaAAaAAK7AIZRzJIiuXGJxg1S+9lNjpXaE2DEg+WzvgB6c1Xg+2XlC31qN8YBbOQoz+4jjB5e2ShG1CjHhG1S31SLUZq/2U9VOTY3cN5EVoDuyBHXqg0cBMGpglEkiDuMA2KkJ0GEjflSfEMXAhL8f4D8wWcCDv5M0QJ8J33qEAr7X+h2p/Acm4DkPDVYfZAAAAAElFTkSuQmCC">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900">
    <div id="app"></div>
    
    <script>
        // Pass user data to Vue if needed
        window.user = @json(auth()->user());
    </script>
</body>
</html>