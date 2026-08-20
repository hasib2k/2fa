/**
 * crypto.js — PIN-based encryption for locally stored secret keys.
 *
 * A user PIN derives an AES-256-GCM key via PBKDF2 (WebCrypto). The PIN
 * itself is never stored anywhere — only a random salt and a small
 * "verifier" ciphertext (so a wrong PIN can be detected without having to
 * decrypt real account data). Everything happens in the browser.
 */
const PinCrypto = (() => {
  const ITERATIONS = 150000;
  const SALT_BYTES = 16;
  const IV_BYTES = 12;
  const VERIFIER_PLAINTEXT = '2fa-pin-check';

  function randomBytes(len) {
    return crypto.getRandomValues(new Uint8Array(len));
  }

  function toB64(bytes) {
    let binary = '';
    for (const b of bytes) binary += String.fromCharCode(b);
    return btoa(binary);
  }

  function fromB64(str) {
    return Uint8Array.from(atob(str), (c) => c.charCodeAt(0));
  }

  async function deriveKey(pin, saltBytes, iterations) {
    const baseKey = await crypto.subtle.importKey(
      'raw',
      new TextEncoder().encode(pin),
      'PBKDF2',
      false,
      ['deriveKey']
    );
    return crypto.subtle.deriveKey(
      { name: 'PBKDF2', salt: saltBytes, iterations, hash: 'SHA-256' },
      baseKey,
      { name: 'AES-GCM', length: 256 },
      false,
      ['encrypt', 'decrypt']
    );
  }

  async function encrypt(key, plaintext) {
    const iv = randomBytes(IV_BYTES);
    const cipherBuf = await crypto.subtle.encrypt(
      { name: 'AES-GCM', iv },
      key,
      new TextEncoder().encode(plaintext)
    );
    return { iv: toB64(iv), data: toB64(new Uint8Array(cipherBuf)) };
  }

  async function decrypt(key, payload) {
    const iv = fromB64(payload.iv);
    const data = fromB64(payload.data);
    // Throws (DOMException) if the key/PIN is wrong — GCM's auth tag check.
    const plainBuf = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, key, data);
    return new TextDecoder().decode(plainBuf);
  }

  /** Set up a brand-new PIN: returns the storable config plus the derived key. */
  async function createConfig(pin) {
    const salt = randomBytes(SALT_BYTES);
    const key = await deriveKey(pin, salt, ITERATIONS);
    const verifier = await encrypt(key, VERIFIER_PLAINTEXT);
    return { config: { salt: toB64(salt), iterations: ITERATIONS, verifier }, key };
  }

  /** Verify a PIN against a stored config; returns the derived key or throws. */
  async function unlock(pin, config) {
    const salt = fromB64(config.salt);
    const iterations = config.iterations || ITERATIONS;
    const key = await deriveKey(pin, salt, iterations);
    const check = await decrypt(key, config.verifier);
    if (check !== VERIFIER_PLAINTEXT) throw new Error('Incorrect PIN');
    return key;
  }

  return { createConfig, unlock, encrypt, decrypt };
})();
