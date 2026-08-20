<?php
// Generates the OG image (1200×630) as PNG using GD.
// og:image meta tag points here: /assets/img/og-image.php

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

$w = 1200;
$h = 630;

$img = imagecreatetruecolor($w, $h);
imagesavealpha($img, true);

// ── Colors ──────────────────────────────────────────────────
$bg          = imagecolorallocate($img, 243, 245, 249);
$white       = imagecolorallocate($img, 255, 255, 255);
$blue        = imagecolorallocate($img,  37,  99, 235);
$dark        = imagecolorallocate($img,  15,  23,  42);
$muted       = imagecolorallocate($img, 100, 116, 139);
$blue_tint   = imagecolorallocate($img, 239, 246, 255);
$green_tint  = imagecolorallocate($img, 236, 253, 245);
$green       = imagecolorallocate($img,   5, 150, 105);
$purple_tint = imagecolorallocate($img, 245, 243, 255);
$purple      = imagecolorallocate($img, 124,  58, 237);

// ── Background & card ────────────────────────────────────────
imagefilledrectangle($img, 0, 0, $w, $h, $bg);
imagefilledrectangle($img, 60, 60, $w - 60, $h - 60, $white);

// ── Helper: draw text scaled up (GD built-in fonts are tiny) ─
function drawBigText($img, $text, $y, $color, $scale) {
    $font = 5;
    $fw   = imagefontwidth($font);
    $fh   = imagefontheight($font);
    $tw   = strlen($text) * $fw;

    $tmp = imagecreatetruecolor($tw, $fh);
    $bg  = imagecolorallocate($tmp, 255, 255, 255);
    $fg  = imagecolorallocate($tmp, ...array_values(imagecolorsforindex($img, $color)));
    imagefilledrectangle($tmp, 0, 0, $tw, $fh, $bg);
    imagestring($tmp, $font, 0, 0, $text, $fg);

    $sw = $tw * $scale;
    $sh = $fh * $scale;
    $scaled = imagescale($tmp, $sw, $sh, IMG_NEAREST_NEIGHBOUR);

    $imgW = imagesx($img);
    $x = (int)(($imgW - $sw) / 2);
    imagecopy($img, $scaled, $x, $y, 0, 0, $sw, $sh);
    imagedestroy($tmp);
    imagedestroy($scaled);
}

// ── Helper: filled rounded rect ──────────────────────────────
function filledRoundedRect($img, $x1, $y1, $x2, $y2, $r, $color) {
    imagefilledrectangle($img, $x1 + $r, $y1,      $x2 - $r, $y2,      $color);
    imagefilledrectangle($img, $x1,      $y1 + $r, $x2,      $y2 - $r, $color);
    imagefilledellipse($img, $x1 + $r, $y1 + $r, $r * 2, $r * 2, $color);
    imagefilledellipse($img, $x2 - $r, $y1 + $r, $r * 2, $r * 2, $color);
    imagefilledellipse($img, $x1 + $r, $y2 - $r, $r * 2, $r * 2, $color);
    imagefilledellipse($img, $x2 - $r, $y2 - $r, $r * 2, $r * 2, $color);
}

// ── Helper: centered pill with label ────────────────────────
function drawPill($img, $cx, $y, $label, $bg, $fg) {
    $font = 5;
    $fw   = imagefontwidth($font);
    $fh   = imagefontheight($font);
    $tw   = strlen($label) * $fw * 2;
    $ph   = $fh * 2 + 16;
    $pw   = $tw + 40;
    $x1   = $cx - (int)($pw / 2);
    $x2   = $cx + (int)($pw / 2);
    filledRoundedRect($img, $x1, $y, $x2, $y + $ph, (int)($ph / 2), $bg);

    // Draw label scaled x2
    $tmp  = imagecreatetruecolor(strlen($label) * $fw, $fh);
    $tbg  = imagecolorallocate($tmp, ...array_values(imagecolorsforindex($img, $bg)));
    $tfg  = imagecolorallocate($tmp, ...array_values(imagecolorsforindex($img, $fg)));
    imagefilledrectangle($tmp, 0, 0, strlen($label) * $fw, $fh, $tbg);
    imagestring($tmp, $font, 0, 0, $label, $tfg);
    $sc   = imagescale($tmp, strlen($label) * $fw * 2, $fh * 2, IMG_NEAREST_NEIGHBOUR);
    $tx   = $cx - (int)(strlen($label) * $fw);
    $ty   = $y + (int)(($ph - $fh * 2) / 2);
    imagecopy($img, $sc, $tx, $ty, 0, 0, strlen($label) * $fw * 2, $fh * 2);
    imagedestroy($tmp);
    imagedestroy($sc);
}

// ── Shield icon (simple polygon) ────────────────────────────
imagesetthickness($img, 5);
$pts = [600,130, 548,152, 548,196, 600,222, 652,196, 652,152];
imagepolygon($img, $pts, 6, $blue);
imagesetthickness($img, 1);

// ── Title ────────────────────────────────────────────────────
drawBigText($img, '2FACode.co', 245, $dark, 8);

// ── Subtitle ─────────────────────────────────────────────────
drawBigText($img, 'Free, Secure & Private 2FA Code Generator', 375, $muted, 3);

// ── Pills ────────────────────────────────────────────────────
drawPill($img, 330,  470, '100% Private', $blue_tint,   $blue);
drawPill($img, 600,  470, 'Works Offline', $green_tint,  $green);
drawPill($img, 870,  470, 'Free Forever', $purple_tint, $purple);

imagepng($img);
imagedestroy($img);
