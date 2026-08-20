<?php
/**
 * Site-wide configuration and shared data.
 * Edit here to change branding, navigation, or the sponsor slot content.
 */

$site_name = '2FA Generator';

$nav_links = [
    ['label' => 'Generator',          'url' => '/'],
    ['label' => 'About',              'url' => '/about'],
    ['label' => 'Sponsor',            'url' => '/#sponsor'],
    ['label' => 'Contact on Telegram','url' => 'https://t.me/mrmarketer247', 'external' => true],
];

$current_year = date('Y');

/**
 * Placeholder sponsor slot. Replace with your own sponsor's details.
 * This is intentionally generic — swap the copy below for a real partner.
 */
$sponsor = [
    'eyebrow'      => 'Trusted Facebook & Meta Ad Accounts',
    'eyebrow_icon' => 'trending-up',
    'badge'        => 'Sponsored',
    'partner'      => 'FBM24',
    'tag'          => 'Featured Partner',
    'headline'     => 'Verified Facebook Business Manager & Ad Accounts',
    'body'         => 'The trusted marketplace for verified Facebook Business Manager accounts, Ad Accounts, WhatsApp Business API, and TikTok Agency Ad Accounts. 12,400+ verified customers across 40+ countries.',
    'features'  => [
        ['icon' => 'zap',     'title' => 'Fast Delivery',       'desc' => 'Average delivery under 90 minutes.'],
        ['icon' => 'shield',  'title' => '7-Day Guarantee',     'desc' => 'Replacement guarantee on all accounts.'],
        ['icon' => 'globe',   'title' => '40+ Countries',       'desc' => '12,400+ verified customers worldwide.'],
    ],
    'cta_label' => 'Visit FBM24.com →',
    'cta_url'   => 'https://www.fbm24.com/',
    'trust'     => [],
];
