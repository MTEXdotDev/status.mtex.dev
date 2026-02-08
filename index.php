<?php
date_default_timezone_set('Europe/Berlin');
$config = require 'config.php';

$gh_redirect_active = false;
$gh_service_index = $config['gh_service_index'] ?? 5;
if (isset($config['services'][$gh_service_index])) {
    $gh_service = $config['services'][$gh_service_index];
    if ($gh_service['is_deployed']) {
        $ch_gh = curl_init($gh_service['address']);
        curl_setopt($ch_gh, CURLOPT_NOBODY, true);
        curl_setopt($ch_gh, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch_gh, CURLOPT_TIMEOUT, 2); // Fast check
        curl_exec($ch_gh);
        $gh_code = curl_getinfo($ch_gh, CURLINFO_HTTP_CODE);
        curl_close($ch_gh);
        if ($gh_code >= 200 && $gh_code < 400) {
            $gh_redirect_active = true;
        }
    }
}

if (isset($_GET['type'])) {
    $type = strtolower($_GET['type']);
    if ($type === 'raw') {
        header('Content-Type: application/json');
        echo json_encode($config['services']);
        exit();
    }
    if ($type === 'check') {
        header('Content-Type: application/json');
        $all_online = true;
        foreach ($config['services'] as $service) {
            if ($service['is_deployed']) {
                $ch = curl_init($service['address']);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($code < 200 || $code >= 400) {
                    $all_online = false;
                    break;
                }
            }
        }
        echo json_encode(['operational' => $all_online]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>MTEX Status — Service Monitoring</title>
    <meta name="title" content="MTEX Status — Service Monitoring">
    <meta name="description" content="Real-time operational status and uptime monitoring for MTEX infrastructure and services.">
    <meta name="keywords" content="MTEX, Status, Uptime, Services, Monitoring, API Status">
    <meta name="author" content="MTEXdotDev">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://status.mtex.dev/">
    <meta property="og:title" content="MTEX Status">
    <meta property="og:description" content="Check the current status and performance of all MTEX services.">
    <meta property="og:image" content="https://github.com/MTEXdotDev.png">

    <meta property="twitter:card" content="summary">
    <meta property="twitter:url" content="https://status.mtex.dev/">
    <meta property="twitter:title" content="MTEX Status">
    <meta property="twitter:description" content="Check the current status and performance of all MTEX services.">
    <meta property="twitter:image" content="https://github.com/MTEXdotDev.png">

    <link rel="icon" type="image/png" href="http://github.com/MTEXdotDev.png" />
    <link rel="apple-touch-icon" href="https://github.com/MTEXdotDev.png">
    <meta name="theme-color" content="#0a0a0a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes custom-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-custom {
            animation: custom-spin 1s cubic-bezier(.17,.63,.82,.42) infinite;
        }
        .glass-header {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(12px);
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #262626; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #404040; }
    </style>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen selection:bg-emerald-500/30">
    <div class="max-w-5xl mx-auto px-4 py-8 md:px-6 md:py-12">
        <header class="sticky top-0 z-50 mb-8 md:mb-16 pt-4 pb-6 glass-header border-b border-neutral-900 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-0">
            <div>
                <div class="flex items-baseline gap-3 mb-1">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-b from-white to-neutral-400">MTEX</h1>
                    <span class="text-neutral-500 text-xs md:text-sm font-medium uppercase tracking-widest">Status</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-neutral-500">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span id="last-update" class="font-mono text-xs md:text-sm">Live · Initializing...</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="https://github.com/MTEXdotDev/status.mtex.dev" target="_blank" class="flex-1 md:flex-none flex justify-center items-center p-2.5 bg-neutral-900 border border-neutral-800 rounded-xl text-neutral-400 hover:text-white hover:border-neutral-700 transition-all shadow-sm" title="View Source">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
                <button id="refresh-btn" onclick="checkAllServices()" class="flex-1 md:flex-none flex justify-center items-center p-2.5 bg-neutral-900 border border-neutral-800 rounded-xl text-neutral-400 hover:text-white hover:border-neutral-700 transition-all shadow-sm" title="Refresh Status">
                    <svg id="refresh-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>
            </div>
        </header>

        <div class="space-y-4">
            <?php foreach ($config['services'] as $index => $service): ?>
                <?php
                    $targetGithubUrl = $service['github'];
                    $usingGhMtex = false;
                    if ($gh_redirect_active && isset($service['github_slug']) && !empty($service['github_slug'])) {
                        $targetGithubUrl = 'https://gh.mtex.dev/' . $service['github_slug'];
                        $usingGhMtex = true;
                    }
                ?>
                <div id="service-<?php echo $index; ?>" 
                     data-address="<?php echo $service['address']; ?>" 
                     data-deployed="<?php echo $service['is_deployed'] ? 'true' : 'false'; ?>"
                     class="service-card bg-neutral-900 border border-neutral-800 rounded-2xl p-5 md:p-6 hover:border-neutral-700 hover:shadow-2xl hover:shadow-black/50 transition-all duration-300">
                    <div class="flex flex-col sm:flex-row items-start justify-between gap-4 sm:gap-6">
                        <div class="w-full flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-lg font-semibold tracking-tight"><?php echo $service['name']; ?></h2>
                                <div class="flex items-center gap-2 px-2 py-0.5 rounded-full bg-neutral-950 border border-neutral-800">
                                    <div class="status-dot w-2 h-2 rounded-full bg-neutral-700"></div>
                                    <span class="status-text text-[10px] uppercase font-bold tracking-wider text-neutral-500">Checking</span>
                                </div>
                            </div>
                            <p class="text-sm text-neutral-400 mb-4 leading-relaxed max-w-2xl"><?php echo $service['description']; ?></p>
                            <div class="flex items-center flex-wrap gap-x-3 gap-y-2 text-xs">
                                <a href="<?php echo $targetGithubUrl; ?>" target="_blank"
                                   class="text-neutral-500 hover:text-white transition-colors flex items-center gap-1.5 group">
                                    <svg class="w-3.5 h-3.5 opacity-70 group-hover:opacity-100" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                    Repository <?php if ($usingGhMtex) echo '<span class="ml-1 opacity-50" title="Routed via gh.mtex.dev">↗</span>'; ?>
                                </a>
                                <span class="text-neutral-800">|</span>
                                <span class="text-neutral-600 font-mono tracking-tighter truncate max-w-[150px] sm:max-w-none"><?php echo str_replace(['https://', 'http://'], '', $service['address']); ?></span>
                                <span class="hidden sm:inline text-neutral-800">|</span>
                                <span class="last-check text-neutral-600 italic block w-full sm:w-auto mt-1 sm:mt-0">Never checked</span>
                            </div>
                        </div>
                        <a href="<?php echo $service['address']; ?>" target="_blank" class="w-full sm:w-auto flex justify-center items-center p-3 bg-neutral-950 border border-neutral-800 hover:border-neutral-600 rounded-xl text-neutral-500 hover:text-white transition-all group">
                            <span class="sm:hidden text-sm font-medium mr-2">Visit Service</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer class="mt-16 md:mt-24 pt-8 md:pt-12 border-t border-neutral-900 pb-8 md:pb-12">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 text-neutral-600 text-xs">
                <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 text-center sm:text-left">
                    <span class="font-medium text-neutral-500">&copy; <?php echo date('Y'); ?> MTEX.dev</span>
                    <span class="hidden sm:inline text-neutral-800">|</span>
                    <div class="flex items-center gap-4">
                        <a href="https://legal.mtex.dev/imprint" target="_blank" class="hover:text-neutral-300 transition-colors">Imprint</a>
                        <a href="https://legal.mtex.dev/privacy" target="_blank" class="hover:text-neutral-300 transition-colors">Privacy</a>
                    </div>
                </div>
                <div class="flex items-center gap-4 uppercase tracking-widest font-bold">
                    <span class="text-neutral-700"><?php echo $config['version']; ?></span>
                    <span class="text-neutral-800">/</span>
                    <span class="text-neutral-700">PHP <?php echo PHP_VERSION; ?></span>
                    <span class="text-neutral-800">/</span>
                    <span class="text-neutral-700"><?php echo(round(memory_get_usage() / 1024 / 1024, 2) . ' MB') ?></span>
                </div>
            </div>
        </footer>
    </div>

    <script>
        const states = <?php echo json_encode($config['states']); ?>;

        async function checkService(card) {
            const address = card.dataset.address;
            const isDeployed = card.dataset.deployed === 'true';
            const dot = card.querySelector('.status-dot');
            const label = card.querySelector('.status-text');
            const checkTime = card.querySelector('.last-check');

            if (!isDeployed) {
                updateUI(card, dot, label, 'maintenance', 'bg-amber-500', 'border-amber-500/30');
                return;
            }

            try {
                const response = await fetch(address, { mode: 'no-cors', cache: 'no-cache' });
                updateUI(card, dot, label, 'online', 'bg-emerald-500', 'border-emerald-500/30');
            } catch (error) {
                updateUI(card, dot, label, 'offline', 'bg-red-500', 'border-red-500/30');
            } finally {
                const now = new Date();
                checkTime.textContent = `Checked at ${now.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}`;
            }
        }

        function updateUI(card, dot, label, stateKey, colorClass, borderClass) {
            dot.className = `status-dot w-2 h-2 rounded-full ${colorClass} shadow-[0_0_8px_rgba(0,0,0,0.5)]`;
            label.textContent = states[stateKey];
            card.style.borderColor = ''; 
            card.className = `service-card bg-neutral-900 border ${borderClass} rounded-2xl p-5 md:p-6 hover:shadow-2xl hover:shadow-black/50 transition-all duration-300`;
        }

        async function checkAllServices() {
            const icon = document.getElementById('refresh-icon');
            icon.classList.add('animate-spin-custom');
            
            const now = new Date();
            const timeString = now.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('last-update').textContent = `Live · Updated ${timeString}`;
            
            const cards = document.querySelectorAll('.service-card');
            const promises = Array.from(cards).map(card => checkService(card));
            
            await Promise.all(promises);
            setTimeout(() => icon.classList.remove('animate-spin-custom'), 500);
        }

        window.onload = checkAllServices;
    </script>
</body>
</html>