# 2FA.Online (PHP Clone)

A fully functional, responsive clone of the 2FA.Online free TOTP code
generator, built with plain PHP (no framework/build step) plus a
client-side TOTP (RFC 6238) engine.

## Features

- Free 2FA (TOTP) code generator — enter an account name + Base32 secret
  key and get a live, auto-refreshing 6-digit code.
- Real RFC 6238 / RFC 4226 implementation using the browser's WebCrypto
  API (`HMAC-SHA1`) — compatible with Google Authenticator, Authy,
  Microsoft Authenticator, and any RFC 6238-compliant app.
- Works fully offline once the page is loaded; secrets are never sent to
  a server — everything runs client-side in the browser.
- Multiple accounts, each with show/hide secret and one-click copy.
- Accounts are saved in `localStorage` and auto-expire after 7 days.
- "Clear History" wipes all saved accounts instantly.
- Shared 30-second countdown timer with a progress bar.
- Light/dark theme toggle, responsive mobile menu.
- About page: mission, privacy/security details, "How It Works", and FAQ.

## Project structure

```
index.php            Home page — the code generator
about.php             About page
includes/
  config.php          Site name, nav links, sponsor slot content
  icons.php            Inline SVG icon helper
  header.php           Shared <head> + header + nav markup
  footer.php            Shared footer + script includes
assets/
  css/style.css        All styling (light + dark themes, responsive)
  js/totp.js           TOTP/HOTP engine (WebCrypto, Base32 decode)
  js/app.js             UI logic: accounts, timer, theme, clipboard
  img/favicon.svg
```

## Running locally

Requires PHP 7.4+ (no other dependencies).

```bash
php -S localhost:8000
```

Then open http://localhost:8000/ in a browser.

## Notes

- The homepage's "Sponsored" section is a placeholder slot — edit the
  `$sponsor` array in `includes/config.php` to point it at your own
  sponsor/partner content.
- TOTP generation requires the WebCrypto API (`crypto.subtle`), which is
  available in all modern browsers over `https://` or `http://localhost`.
