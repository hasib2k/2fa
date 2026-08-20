<?php
/**
 * Site-wide configuration and shared data.
 * Edit here to change branding, navigation, or the sponsor slot content.
 */

$site_name = '2FA Generator';

$nav_links = [
    ['label' => 'Generator',          'url' => 'index.php'],
    ['label' => 'About',              'url' => 'about.php'],
    ['label' => 'Sponsor',            'url' => 'index.php#sponsor'],
    ['label' => 'Contact on Telegram','url' => 'https://t.me/', 'external' => true],
];

$current_year = date('Y');

/**
 * Placeholder sponsor slot. Replace with your own sponsor's details.
 * This is intentionally generic — swap the copy below for a real partner.
 */
$sponsor = [
    'eyebrow'   => 'Grow With Confidence',
    'eyebrow_icon' => 'rocket',
    'badge'     => 'Sponsored',
    'partner'   => 'Your Sponsor Name',
    'tag'       => 'Featured Partner',
    'headline'  => 'Reach Thousands of Security-Conscious Users',
    'body'      => 'This space is reserved for a sponsor. Advertise your product or service to visitors who care about privacy and account security.',
    'features'  => [
        ['icon' => 'target',       'title' => 'Targeted Audience', 'desc' => 'Reach users who actively care about digital security.'],
        ['icon' => 'trending-up',  'title' => 'Simple Placement',  'desc' => 'One clean, responsive banner slot — no clutter.'],
        ['icon' => 'users',        'title' => 'Fair Partnership',  'desc' => 'Transparent, editable sponsorship placement.'],
    ],
    'cta_label' => 'Become a Sponsor →',
    'cta_url'   => 'about.php#contact',
    'trust'     => [],
];
