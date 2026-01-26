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
<body class="bg-neutral-950 text-neutral-100 min-h-screen">
    <div class="max-w-5xl mx-auto px-6 py-12">
        <header class="mb-16">
            <div class="flex items-baseline gap-3 mb-2">
                <h1 class="text-4xl font-bold tracking-tight">MTEX</h1>
                <span class="text-neutral-500 text-sm font-medium">Service Status</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-neutral-500">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span>Live · Updated <?php echo date('H:i:s'); ?></span>
            </div>
        </header>

        <div class="space-y-3">
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
                        ? 'bg-amber-500'
                        : ($isOnline
                            ? 'bg-emerald-500'
                            : 'bg-red-500');
                $borderColor =
                    $isOnline === null
                        ? 'border-amber-500/20'
                        : ($isOnline
                            ? 'border-emerald-500/20'
                            : 'border-red-500/20');
                ?>
                <div class="bg-neutral-900 border <?php echo $borderColor; ?> rounded-xl p-6 hover:bg-neutral-900/80 transition-colors">
                    <div class="flex items-start justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-lg font-semibold"><?php echo $service['name']; ?></h2>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full <?php echo $statusColor; ?>"></div>
                                    <span class="text-xs font-medium text-neutral-400"><?php echo $statusText; ?></span>
                                </div>
                            </div>
                            <p class="text-sm text-neutral-400 mb-3"><?php echo $service['description']; ?></p>
                            <div class="flex items-center gap-4 text-xs">
                                <a href="<?php echo $service['github']; ?>" 
                                   class="text-neutral-500 hover:text-neutral-300 transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                    Repository
                                </a>
                                <span class="text-neutral-700">·</span>
                                <span class="text-neutral-600 font-mono"><?php echo $service['address']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer class="mt-16 pt-8 border-t border-neutral-900 text-center text-neutral-600 text-xs">
            <div class="flex items-center justify-center gap-2">
                <span>&copy; <?php echo date('Y'); ?> MTEX.dev</span>
                <span>·</span> Service Status <span>·</span>
                <span><?php echo $config['version']; ?></span>
            </div>
        </footer>
    </div>
</body>
</html>