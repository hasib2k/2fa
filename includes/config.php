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
    ['label' => 'Contact on Telegram','url' => 'https://t.me/', 'external' => true],
];

$current_year = date('Y');

/**
 * Placeholder sponsor slot. Replace with your own sponsor's details.
 * This is intentionally generic — swap the copy below for a real partner.
 */
$sponsor = [
    'eyebrow'      => 'বাংলাদেশের বিশ্বস্ত অনলাইন শপ',
    'eyebrow_icon' => 'shopping-bag',
    'badge'        => 'Sponsored',
    'partner'      => 'সব বাজার',
    'tag'          => 'Featured Partner',
    'headline'     => 'শিশুদের খেলনা ও পণ্য — ঘরে বসে অর্ডার করুন',
    'body'         => 'বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। ক্যাশ অন ডেলিভারি, ঢাকায় ৪৮ ঘণ্টায় ডেলিভারি এবং সারাদেশে সাশ্রয়ী মূল্যে শিপিং।',
    'features'  => [
        ['icon' => 'truck',        'title' => 'দ্রুত ডেলিভারি',   'desc' => 'ঢাকায় ৪৮ ঘণ্টা, সারাদেশে সাশ্রয়ী শিপিং।'],
        ['icon' => 'tag',          'title' => '৩০% পর্যন্ত ছাড়',  'desc' => 'বিভিন্ন পণ্যে আকর্ষণীয় ডিসকাউন্ট।'],
        ['icon' => 'shield-check', 'title' => 'ক্যাশ অন ডেলিভারি', 'desc' => 'পণ্য হাতে পেয়ে টাকা দিন।'],
    ],
    'cta_label' => 'sobbazar.com ভিজিট করুন →',
    'cta_url'   => 'https://sobbazar.com/',
    'trust'     => [],
];
