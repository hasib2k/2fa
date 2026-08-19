/**
 * TOTP.js — RFC 6238 (TOTP) / RFC 4226 (HOTP) implementation using WebCrypto.
 * Runs entirely client-side — no secret ever leaves the browser.
 */
const TOTP = (() => {
  const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
  const STEP = 30;   // seconds per code window
  const DIGITS = 6;

  /** Normalize + validate a user-supplied Base32 secret. */
  function cleanSecret(secret) {
    return (secret || '')
      .toUpperCase()
      .replace(/\s+/g, '')
      .replace(/=+$/, '');
  }

  function isValidSecret(secret) {
    const clean = cleanSecret(secret);
    return clean.length > 0 && /^[A-Z2-7]+$/.test(clean);
  }

  /** Decode a Base32 string into a Uint8Array. */
  function base32Decode(input) {
    const clean = cleanSecret(input);
    let bits = '';
    for (const char of clean) {
      const val = BASE32_ALPHABET.indexOf(char);
      if (val === -1) continue;
      bits += val.toString(2).padStart(5, '0');
    }
    const bytes = [];
    for (let i = 0; i + 8 <= bits.length; i += 8) {
      bytes.push(parseInt(bits.substring(i, i + 8), 2));
    }
    return new Uint8Array(bytes);
  }

  /** Encode an 8-byte big-endian buffer for the given counter value. */
  function counterToBytes(counter) {
    const buf = new ArrayBuffer(8);
    const view = new DataView(buf);
    // Counter fits comfortably in the low 32 bits for many centuries.
    view.setUint32(0, Math.floor(counter / 0x100000000), false);
    view.setUint32(4, counter >>> 0, false);
    return new Uint8Array(buf);
  }

  /** RFC 4226 HOTP value for a given key + counter. */
  async function hotp(keyBytes, counter) {
    const cryptoKey = await crypto.subtle.importKey(
      'raw',
      keyBytes,
      { name: 'HMAC', hash: 'SHA-1' },
      false,
      ['sign']
    );
    const signature = await crypto.subtle.sign('HMAC', cryptoKey, counterToBytes(counter));
    const digest = new Uint8Array(signature);

    const offset = digest[digest.length - 1] & 0x0f;
    const binCode =
      ((digest[offset] & 0x7f) << 24) |
      ((digest[offset + 1] & 0xff) << 16) |
      ((digest[offset + 2] & 0xff) << 8) |
      (digest[offset + 3] & 0xff);

    const code = (binCode % 10 ** DIGITS).toString().padStart(DIGITS, '0');
    return code;
  }

  /** RFC 6238 TOTP value for a Base32 secret at the given timestamp (ms). */
  async function generate(secret, atMs = Date.now()) {
    if (!isValidSecret(secret)) throw new Error('Invalid secret key');
    const keyBytes = base32Decode(secret);
    const counter = Math.floor(atMs / 1000 / STEP);
    return hotp(keyBytes, counter);
  }

  /** Seconds remaining until the current TOTP window refreshes. */
  function secondsRemaining(atMs = Date.now()) {
    const seconds = Math.floor(atMs / 1000);
    return STEP - (seconds % STEP);
  }

  return { generate, isValidSecret, secondsRemaining, STEP, DIGITS };
})();
