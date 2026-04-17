<script>
  import { createEventDispatcher } from 'svelte';
  import { api } from './api';
  import { fade } from 'svelte/transition';

  const dispatch = createEventDispatcher();
  let username = '';
  let password = '';
  let error = '';
  let loading = false;

  async function handleLogin() {
    if (!username || !password) {
      error = 'Please enter both username and password';
      return;
    }

    loading = true;
    error = '';

    try {
      await api.login(username, password);
      dispatch('login');
    } catch (e) {
      error = e.message || 'Authentication failed. Please try again.';
    } finally {
      loading = false;
    }
  }
</script>

<div class="login-wrapper" in:fade>
  <div class="login-container">
    <div class="glass-card">
      <div class="brand-section">
        <div class="logo-icon">S</div>
        <h1>SIOTIC</h1>
        <p class="subtitle">Secure Asset Inventory Management</p>
      </div>

      <form on:submit|preventDefault={handleLogin} class="login-form">
        {#if error}
          <div class="error-badge" transition:fade>
            {error}
          </div>
        {/if}

        <div class="form-group">
          <label for="username">Username</label>
          <input 
            type="text" 
            id="username" 
            bind:value={username} 
            placeholder="admin"
            disabled={loading}
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            bind:value={password} 
            placeholder="••••••••"
            disabled={loading}
          />
        </div>

        <button type="submit" class="btn-login" disabled={loading}>
          {loading ? 'Authenticating...' : 'Sign In'}
        </button>

        <div class="login-help">
          <p>Login with your OTIC staff credentials.</p>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .login-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top left, #f5671a 0%, #1a1a2e 100%);
    overflow: hidden;
  }

  .login-container {
    width: 100%;
    max-width: 420px;
    padding: var(--space-lg);
    z-index: 10;
  }

  .glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  }

  .brand-section {
    text-align: center;
    margin-bottom: var(--space-xl);
  }

  .logo-icon {
    width: 50px;
    height: 50px;
    background: var(--color-primary);
    color: white;
    font-size: 28px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    margin: 0 auto var(--space-md);
    box-shadow: 0 10px 15px -3px rgba(245, 103, 26, 0.3);
  }

  h1 {
    color: white;
    font-size: 32px;
    letter-spacing: -0.02em;
    margin-bottom: var(--space-xs);
  }

  .subtitle {
    color: rgba(255, 255, 255, 0.6);
    font-size: 14px;
  }

  .login-form {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg);
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
  }

  label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 13px;
    font-weight: 500;
  }

  input {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    color: white;
    font-size: 15px;
    transition: var(--transition);
  }

  input:focus {
    outline: none;
    border-color: var(--color-primary);
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 0 4px rgba(245, 103, 26, 0.2);
  }

  input::placeholder {
    color: rgba(255, 255, 255, 0.3);
  }

  .btn-login {
    background: var(--color-primary);
    color: white;
    border: none;
    padding: var(--space-md);
    border-radius: var(--radius-md);
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    margin-top: var(--space-md);
  }

  .btn-login:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(245, 103, 26, 0.4);
    filter: brightness(1.1);
  }

  .btn-login:active:not(:disabled) {
    transform: translateY(0);
  }

  .btn-login:disabled {
    opacity: 0.6;
    cursor: wait;
  }

  .error-badge {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    font-size: 13px;
    text-align: center;
  }

  .login-help {
    text-align: center;
    margin-top: var(--space-md);
  }

  .login-help p {
    color: rgba(255, 255, 255, 0.4);
    font-size: 12px;
  }
</style>
