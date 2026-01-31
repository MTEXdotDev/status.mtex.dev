<?php

return [
    'version' => 'v1.2.5',
    'services' => [
        [
            'address' => 'https://mtex.dev',
            'name' => 'Landingpage',
            'description' => 'Our main landingpage',
            'github' => 'https://github.com/MTEDdotDev/static-landingpage',
            'is_deployed' => true,
        ],
        [
            'address' => 'https://legal.mtex.dev',
            'name' => 'Legal Center',
            'description' => 'Multilingual hub for imprint, privacy policies, and legal documentation.',
            'github' => 'https://github.com/MTEXdotDev/legal.mtex.dev',
            'is_deployed' => true,
        ],
        [
            'address' => 'https://tw.mtex.dev',
            'name' => 'Tailwind Components Libary',
            'description' => 'Our TailwindCSS component library for rapid UI development.',
            'github' => 'https://github.com/MTEDdotDev/tw.mtex.dev',
            'is_deployed' => true,
        ],
        [
            'address' => 'https://nx.mtex.dev',
            'name' => 'MTEX Nexus',
            'description' => 'A lightweight JSON API gateway for seamless data exchange and rapid prototyping.',
            'github' => 'https://github.com/MTEDdotDev/nx.mtex.dev',
            'is_deployed' => true,
        ],
        [
            'address' => 'https://gimy.site',
            'name' => 'GimySite',
            'description' => 'Free static website hosting for modern developers.',
            'github' => 'https://github.com/MTEDdotDev/gimy.site',
            'is_deployed' => false,
        ],
        [
            'address' => 'https://getmy.name',
            'name' => 'GetMyName',
            'description' => 'A headless API to power your personal portfolio data.',
            'github' => 'https://github.com/MTEDdotDev/getmy.name',
            'is_deployed' => true,
        ],
        [
            'address' => 'https://getmy.blog',
            'name' => 'GetMyBlog',
            'description' => 'Lightweight blogging-api for the minimalist writer.',
            'github' => 'https://github.com/MTEDdotDev/getmy.blog',
            'is_deployed' => false,
        ],
    ],
    'states' => [
        'online' => 'Operational',
        'offline' => 'Down',
        'maintenance' => 'Maintenance',
    ],
];