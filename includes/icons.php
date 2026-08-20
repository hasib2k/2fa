<?php
/**
 * Small inline SVG icon helper.
 * Usage: icon('shield', 'shield-icon');
 */
function icon(string $name, string $class = ''): string
{
    $paths = [
        'shield' => '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3Z"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'moon' => '<path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5Z"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'arrow-left' => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'trash' => '<path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m2 0-1 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 6h12Z"/>',
        'eye' => '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off' => '<path d="M3 3l18 18M10.6 10.6a3 3 0 0 0 4.24 4.24M9.9 5.1A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a13.2 13.2 0 0 1-3.4 4.1M6.2 6.2C3.5 7.9 1 12 1 12s4 7 11 7c1.3 0 2.5-.2 3.6-.6"/>',
        'copy' => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'stopwatch' => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2M9 2h6M12 2v2"/>',
        'lock' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a13 13 0 0 1 0 18M12 3a13 13 0 0 0 0 18"/>',
        'phone' => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/>',
        'shield-outline' => '<path d="M12 3 5 6v6c0 4.5 3 8.5 7 9.9 4-1.4 7-5.4 7-9.9V6l-7-3Z"/>',
        'rocket' => '<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09Z"/><path d="M12 15c3-1 7-6 7-11-5 0-10 4-11 7l4 4Z"/><path d="M9 12 4 10c.28-1.15 1-3 3-4l4 1M12 15l2 5c1.15-.28 3-1 4-3l-1-4"/>',
        'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'trending-up' => '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 6h6v6"/>',
        'users' => '<path d="M17 20v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1"/><circle cx="9" cy="7" r="3.5"/><path d="M20 20v-1a3.5 3.5 0 0 0-2.5-3.36M15 3.63a3.5 3.5 0 0 1 0 6.74"/>',
        'download' => '<path d="M12 3v12m0 0-4-4m4 4 4-4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>',
        'upload' => '<path d="M12 21V9m0 0-4 4m4-4 4 4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>',
        'refresh' => '<path d="M21 2v6h-6"/><path d="M21 13a9 9 0 1 1-3-7.7L21 8"/>',
    ];

    $body = $paths[$name] ?? '';
    $classAttr = $class !== '' ? ' class="' . htmlspecialchars($class, ENT_QUOTES) . '"' : '';

    return '<svg' . $classAttr . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
}
