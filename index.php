<?php

date_default_timezone_set('Europe/Berlin');

$config = require 'config.php';

function checkService($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode >= 200 && $httpCode < 400;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTEX Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <header class="mb-12">
            <h1 class="text-3xl font-bold">MTEX Service Status</h1>
            <p class="text-gray-400">Last updated: <?php echo date('H:i:s'); ?></p>
        </header>

        <div class="grid gap-6">
            <?php foreach ($config['services'] as $service): ?>
                <?php
                $isOnline = $service['is_deployed']
                    ? checkService($service['address'])
                    : null;
                $statusText =
                    $isOnline === null
                        ? $config['states']['maintenance']
                        : ($isOnline
                            ? $config['states']['online']
                            : $config['states']['offline']);
                $statusColor =
                    $isOnline === null
                        ? 'text-yellow-500'
                        : ($isOnline
                            ? 'text-green-500'
                            : 'text-red-500');
                ?>
                <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold"><?php echo $service[
                            'name'
                        ]; ?></h2>
                        <p class="text-gray-400 text-sm"><?php echo $service[
                            'description'
                        ]; ?></p>
                        <a href="<?php echo $service[
                            'github'
                        ]; ?>" class="text-blue-400 text-xs hover:underline mt-2 inline-block">GitHub Repository</a>
                    </div>
                    <div class="text-right">
                        <span class="font-mono font-bold <?php echo $statusColor; ?>">
                            <?php echo $statusText; ?>
                        </span>
                        <div class="text-xs text-gray-500 mt-1"><?php echo $service[
                            'address'
                        ]; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer class="mt-12 pt-8 border-t border-zinc-900 text-center text-zinc-600 text-xs">
            &copy; <?php echo date('Y'); ?> MTEX.dev &bull; System Status &bull; <?php echo $config['version']; ?>
        </footer>
    </div>
</body>
</html>