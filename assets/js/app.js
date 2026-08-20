/**
 * app.js — UI wiring for the 2FA code generator.
 * Handles: PIN-encrypted account storage (localStorage, 7-day expiry),
 * rendering, live TOTP codes, the shared countdown/progress bar,
 * theme + menu toggles, and clipboard copy.
 */
(() => {
  const STORAGE_KEY = 'tfa_accounts';
  const CRYPTO_KEY_STORAGE = 'tfa_crypto';
  const THEME_KEY = 'tfa_theme';
  const MAX_AGE_MS = 7 * 24 * 60 * 60 * 1000; // 7 days

  const accountsList = document.getElementById('accountsList');
  const addAccountBtn = document.getElementById('addAccountBtn');
  const clearHistoryBtn = document.getElementById('clearHistoryBtn');
  const exportBackupBtn = document.getElementById('exportBackupBtn');
  const importBackupBtn = document.getElementById('importBackupBtn');
  const importFileInput = document.getElementById('importFileInput');
  const timerText = document.getElementById('timerText');
  const progressBar = document.getElementById('progressBar');
  const themeToggle = document.getElementById('themeToggle');
  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  const lockScreen = document.getElementById('lockScreen');
  const lockPinInput = document.getElementById('lockPinInput');
  const lockError = document.getElementById('lockError');
  const lockUnlockBtn = document.getElementById('lockUnlockBtn');
  const lockResetBtn = document.getElementById('lockResetBtn');

  const pinSetupModal = document.getElementById('pinSetupModal');
  const setupPinInput = document.getElementById('setupPinInput');
  const setupPinConfirm = document.getElementById('setupPinConfirm');
  const setupError = document.getElementById('setupError');
  const setupConfirmBtn = document.getElementById('setupConfirmBtn');
  const setupCancelBtn = document.getElementById('setupCancelBtn');

  let accounts = [];
  /** @type {CryptoKey|null} Derived once per page load — never persisted. */
  let cryptoKey = null;

  // ---------------- Crypto config storage ----------------

  function getCryptoConfig() {
    try {
      return JSON.parse(localStorage.getItem(CRYPTO_KEY_STORAGE) || 'null');
    } catch (e) {
      return null;
    }
  }
  function saveCryptoConfig(config) {
    localStorage.setItem(CRYPTO_KEY_STORAGE, JSON.stringify(config));
  }
  function hasCryptoConfig() {
    return getCryptoConfig() !== null;
  }

  // ---------------- Account storage ----------------

  function loadAccounts() {
    let stored = [];
    try {
      const parsed = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
      stored = Array.isArray(parsed) ? parsed.filter((a) => a && typeof a === 'object') : [];
    } catch (e) {
      stored = [];
    }
    const now = Date.now();
    const fresh = stored.filter((a) => now - (a.createdAt || 0) < MAX_AGE_MS);
    if (fresh.length !== stored.length) {
      persistAccounts(fresh);
    }
    return fresh;
  }

  function persistAccounts(list) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
  }

  /** Serializes only encrypted secrets to storage — plaintext never persists. */
  async function saveAccounts() {
    const storable = [];
    for (const a of accounts) {
      let encSecret = a.encSecret || null;
      if (cryptoKey && a.secret) {
        encSecret = await PinCrypto.encrypt(cryptoKey, a.secret);
      }
      storable.push({ id: a.id, name: a.name, encSecret, createdAt: a.createdAt });
    }
    persistAccounts(storable);
  }

  function newId() {
    return 'acc_' + Math.random().toString(36).slice(2, 10) + Date.now().toString(36);
  }

  // ---------------- Rendering ----------------

  function render() {
    accountsList.innerHTML = '';
    accounts.forEach((account) => {
      accountsList.appendChild(renderAccountCard(account));
    });
    accounts.forEach((account) => refreshCode(account.id));
  }

  function renderAccountCard(account) {
    const wrap = document.createElement('div');
    wrap.className = 'account-card';
    wrap.dataset.id = account.id;

    wrap.innerHTML = `
      <div class="account-card-head">
        <button type="button" class="remove-account-btn" aria-label="Remove account" data-action="remove">
          ${iconSvg('trash')}
        </button>
      </div>
      <div class="field">
        <label>Account Name</label>
        <input type="text" class="account-name-input" placeholder="e.g. Google, Facebook, GitHub" value="${escapeAttr(account.name || '')}">
      </div>
      <div class="field">
        <label>Secret Key</label>
        <div class="secret-input-wrap">
          <input type="password" class="account-secret-input" placeholder="Paste your secret key here..." value="${escapeAttr(account.secret || '')}">
          <button type="button" class="toggle-visibility" data-action="toggle-visibility" aria-label="Show secret key">
            ${iconSvg('eye')}
          </button>
        </div>
      </div>
      <div class="code-box" data-role="code-box">
        <p class="code-error" hidden>Enter a valid Base32 secret key to generate a code.</p>
        <p class="code-value" data-action="copy" title="Click to copy" hidden>------</p>
        <div class="code-meta" hidden>
          <span class="badge-active">Active</span>
          <button type="button" class="copy-btn" data-action="copy" aria-label="Copy code">
            ${iconSvg('copy')}
          </button>
        </div>
      </div>
    `;

    const nameInput = wrap.querySelector('.account-name-input');
    const secretInput = wrap.querySelector('.account-secret-input');
    const toggleBtn = wrap.querySelector('[data-action="toggle-visibility"]');
    const removeBtn = wrap.querySelector('[data-action="remove"]');
    const copyTargets = wrap.querySelectorAll('[data-action="copy"]');

    nameInput.addEventListener('input', () => {
      account.name = nameInput.value;
      saveAccounts();
    });

    secretInput.addEventListener('input', () => {
      account.secret = secretInput.value;
      refreshCode(account.id); // live preview works even before a PIN exists

      if (account.secret && !cryptoKey) {
        // First real secret ever typed on this device: gate saving behind
        // setting a PIN, so nothing unencrypted ever reaches localStorage.
        requirePinSetup(
          () => saveAccounts(),
          () => {
            account.secret = '';
            secretInput.value = '';
            refreshCode(account.id);
          }
        );
        return;
      }
      saveAccounts();
    });

    toggleBtn.addEventListener('click', () => {
      const showing = secretInput.type === 'text';
      secretInput.type = showing ? 'password' : 'text';
      toggleBtn.innerHTML = iconSvg(showing ? 'eye' : 'eye-off');
      toggleBtn.setAttribute('aria-label', showing ? 'Show secret key' : 'Hide secret key');
    });

    removeBtn.addEventListener('click', () => {
      accounts = accounts.filter((a) => a.id !== account.id);
      saveAccounts();
      if (accounts.length === 0) addBlankAccount(false);
      render();
    });

    copyTargets.forEach((el) => {
      el.addEventListener('click', () => copyCode(account.id));
    });

    return wrap;
  }

  async function refreshCode(accountId) {
    const account = accounts.find((a) => a.id === accountId);
    const card = accountsList.querySelector(`.account-card[data-id="${accountId}"]`);
    if (!account || !card) return;

    const errorEl = card.querySelector('.code-error');
    const valueEl = card.querySelector('.code-value');
    const metaEl = card.querySelector('.code-meta');

    if (!account.secret || !TOTP.isValidSecret(account.secret)) {
      // Only show the "invalid secret" message once the user has typed
      // something — an empty field isn't an error yet.
      errorEl.hidden = !account.secret;
      valueEl.hidden = true;
      metaEl.hidden = true;
      return;
    }

    try {
      const code = await TOTP.generate(account.secret);
      account.currentCode = code;
      valueEl.textContent = code;
      errorEl.hidden = true;
      valueEl.hidden = false;
      metaEl.hidden = false;
    } catch (e) {
      errorEl.hidden = false;
      valueEl.hidden = true;
      metaEl.hidden = true;
    }
  }

  async function refreshAllCodes() {
    await Promise.all(accounts.map((account) => refreshCode(account.id)));
  }

  function copyCode(accountId) {
    const account = accounts.find((a) => a.id === accountId);
    if (!account || !account.currentCode) return;
    navigator.clipboard.writeText(account.currentCode).then(() => {
      showToast('Code copied to clipboard');
      const card = accountsList.querySelector(`.account-card[data-id="${accountId}"]`);
      const copyBtn = card && card.querySelector('.copy-btn');
      if (copyBtn) {
        copyBtn.classList.add('copied');
        copyBtn.innerHTML = iconSvg('check');
        setTimeout(() => {
          copyBtn.classList.remove('copied');
          copyBtn.innerHTML = iconSvg('copy');
        }, 1500);
      }
    }).catch(() => showToast('Could not copy — please copy manually'));
  }

  function addBlankAccount(persist = true) {
    const account = { id: newId(), name: '', secret: '', createdAt: Date.now() };
    accounts.push(account);
    if (persist) saveAccounts();
    return account;
  }

  // ---------------- Timer ----------------

  function tickTimer() {
    if (!timerText || !progressBar) return;
    const remaining = TOTP.secondsRemaining();
    timerText.textContent = `${remaining}s until next refresh`;
    progressBar.style.width = `${(remaining / TOTP.STEP) * 100}%`;

    if (remaining === TOTP.STEP) {
      refreshAllCodes();
    }
  }

  // ---------------- Toast ----------------

  let toastEl = null;
  function showToast(message) {
    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.className = 'toast';
      document.body.appendChild(toastEl);
    }
    toastEl.textContent = message;
    toastEl.classList.add('show');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => toastEl.classList.remove('show'), 1800);
  }

  // ---------------- Theme ----------------

  function initTheme() {
    const saved = localStorage.getItem(THEME_KEY);
    const theme = saved || 'light';
    document.documentElement.setAttribute('data-theme', theme);
  }

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem(THEME_KEY, next);
  }

  // ---------------- Mobile menu ----------------

  function toggleMenu() {
    if (!mobileMenu) return;
    const isHidden = mobileMenu.hasAttribute('hidden');
    if (isHidden) {
      mobileMenu.removeAttribute('hidden');
      menuToggle.setAttribute('aria-expanded', 'true');
      menuToggle.classList.add('menu-open');
    } else {
      mobileMenu.setAttribute('hidden', '');
      menuToggle.setAttribute('aria-expanded', 'false');
      menuToggle.classList.remove('menu-open');
    }
  }

  // ---------------- PIN lock screen (shown when a PIN was set previously) ----------------

  function showLockScreen() {
    if (!lockScreen) return finishInit(); // page has no lock UI (e.g. About page)
    lockScreen.hidden = false;
    lockPinInput.value = '';
    lockError.hidden = true;
    lockPinInput.focus();
  }

  function hideLockScreen() {
    if (lockScreen) lockScreen.hidden = true;
  }

  async function attemptUnlock() {
    const pin = lockPinInput.value.trim();
    const config = getCryptoConfig();
    if (!pin || !config) return;
    try {
      const key = await PinCrypto.unlock(pin, config);
      cryptoKey = key;
      for (const account of accounts) {
        if (account.encSecret) {
          account.secret = await PinCrypto.decrypt(key, account.encSecret);
        }
      }
      hideLockScreen();
      finishInit();
    } catch (e) {
      lockError.hidden = false;
      lockPinInput.value = '';
      lockPinInput.focus();
    }
  }

  function resetAllData() {
    if (!confirm('This erases every saved account and your PIN from this browser — there is no way to undo this. Continue?')) return;
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(CRYPTO_KEY_STORAGE);
    location.reload();
  }

  // ---------------- Backup export / import ----------------
  // The exported file carries the same PIN-encrypted blobs already in
  // localStorage — it's still useless without the PIN that produced them.

  function exportBackup() {
    const config = getCryptoConfig();
    const rawAccounts = localStorage.getItem(STORAGE_KEY);
    if (!config || !rawAccounts) {
      showToast('Nothing to export yet — save a key first');
      return;
    }

    const payload = {
      app: '2fa-online-clone-backup',
      version: 1,
      crypto: config,
      accounts: JSON.parse(rawAccounts),
    };

    const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `2fa-backup-${Date.now()}.json`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
    showToast('Backup downloaded — keep the file and your PIN safe');
  }

  async function handleImportFile(file) {
    let payload;
    try {
      payload = JSON.parse(await file.text());
    } catch (e) {
      showToast('That file is not a valid backup');
      return;
    }

    const validShape =
      payload && payload.crypto && payload.crypto.salt && payload.crypto.verifier && Array.isArray(payload.accounts);
    if (!validShape) {
      showToast('That file is not a valid backup');
      return;
    }

    if (!confirm('This replaces every account and the PIN currently on this device with the backup file. Continue?')) {
      return;
    }

    saveCryptoConfig(payload.crypto);
    persistAccounts(payload.accounts);
    // Full reload so the normal lock-screen flow picks up the restored
    // data fresh — the original PIN is still required to see it.
    location.reload();
  }

  // ---------------- PIN setup modal (shown the first time a secret is saved) ----------------

  let pendingSetupResolve = null;

  function requirePinSetup(onSuccess, onCancel) {
    if (!pinSetupModal) { onSuccess(); return; } // no modal on this page — just proceed
    pendingSetupResolve = { onSuccess, onCancel };
    pinSetupModal.hidden = false;
    setupPinInput.value = '';
    setupPinConfirm.value = '';
    setupError.hidden = true;
    setupPinInput.focus();
  }

  function closePinSetupModal() {
    if (pinSetupModal) pinSetupModal.hidden = true;
    pendingSetupResolve = null;
  }

  async function confirmPinSetup() {
    const pin = setupPinInput.value.trim();
    const confirmPin = setupPinConfirm.value.trim();

    if (!/^\d{4,6}$/.test(pin)) {
      setupError.textContent = 'PIN must be 4–6 digits.';
      setupError.hidden = false;
      return;
    }
    if (pin !== confirmPin) {
      setupError.textContent = "PINs don't match.";
      setupError.hidden = false;
      return;
    }

    const { config, key } = await PinCrypto.createConfig(pin);
    saveCryptoConfig(config);
    cryptoKey = key;

    const resolve = pendingSetupResolve;
    closePinSetupModal();
    if (resolve && resolve.onSuccess) await resolve.onSuccess();
  }

  function cancelPinSetup() {
    const resolve = pendingSetupResolve;
    closePinSetupModal();
    if (resolve && resolve.onCancel) resolve.onCancel();
  }

  // ---------------- Icons ----------------

  const ICONS = {
    trash: '<path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m2 0-1 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 6h12Z"/>',
    eye: '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/>',
    'eye-off': '<path d="M3 3l18 18M10.6 10.6a3 3 0 0 0 4.24 4.24M9.9 5.1A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a13.2 13.2 0 0 1-3.4 4.1M6.2 6.2C3.5 7.9 1 12 1 12s4 7 11 7c1.3 0 2.5-.2 3.6-.6"/>',
    copy: '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
    check: '<path d="M20 6 9 17l-5-5"/>',
  };

  function iconSvg(name) {
    return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONS[name] || ''}</svg>`;
  }

  function escapeAttr(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  // ---------------- Init ----------------

  initTheme();

  if (themeToggle) themeToggle.addEventListener('click', toggleTheme);
  if (menuToggle) menuToggle.addEventListener('click', toggleMenu);

  if (lockUnlockBtn) lockUnlockBtn.addEventListener('click', attemptUnlock);
  if (lockPinInput) lockPinInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') attemptUnlock(); });
  if (lockResetBtn) lockResetBtn.addEventListener('click', resetAllData);

  if (setupConfirmBtn) setupConfirmBtn.addEventListener('click', confirmPinSetup);
  if (setupCancelBtn) setupCancelBtn.addEventListener('click', cancelPinSetup);
  if (setupPinConfirm) setupPinConfirm.addEventListener('keydown', (e) => { if (e.key === 'Enter') confirmPinSetup(); });

  if (exportBackupBtn) exportBackupBtn.addEventListener('click', exportBackup);
  if (importBackupBtn) importBackupBtn.addEventListener('click', () => importFileInput.click());
  if (importFileInput) {
    importFileInput.addEventListener('change', () => {
      const file = importFileInput.files[0];
      importFileInput.value = ''; // allow re-selecting the same file later
      if (file) handleImportFile(file);
    });
  }

  function finishInit() {
    if (accounts.length === 0) addBlankAccount(false);
    render();

    if (addAccountBtn) {
      addAccountBtn.addEventListener('click', () => {
        addBlankAccount();
        render();
      });
    }

    if (clearHistoryBtn) {
      clearHistoryBtn.addEventListener('click', () => {
        if (!confirm('Clear all saved accounts, secret keys, and your PIN from this browser?')) return;
        accounts = [];
        cryptoKey = null;
        localStorage.removeItem(CRYPTO_KEY_STORAGE);
        saveAccounts();
        addBlankAccount(false);
        render();
        showToast('History cleared');
      });
    }

    setInterval(tickTimer, 1000);
    tickTimer();
  }

  if (accountsList) {
    accounts = loadAccounts();
    if (hasCryptoConfig()) {
      showLockScreen();
    } else if (accounts.some((a) => a.secret && !a.encSecret)) {
      // Accounts saved before PIN encryption existed: still plaintext in
      // storage. Require a PIN now so saveAccounts() can encrypt them:
      // declining leaves them viewable this session but unmigrated.
      requirePinSetup(
        () => { saveAccounts(); finishInit(); },
        () => finishInit()
      );
    } else {
      finishInit();
    }
  }
})();
