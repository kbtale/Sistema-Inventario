<script>
  import { createEventDispatcher } from 'svelte';
  const dispatch = createEventDispatcher();
  
  export let view = 'dashboard';

  const navigate = (v) => dispatch('navigate', v);
</script>

<nav class="sidebar-nav">
  <div class="nav-group">
    <button type="button" class="nav-item" class:active={view === 'dashboard'} on:click={() => navigate('dashboard')}>Dashboard</button>
    <button type="button" class="nav-item" class:active={view === 'inventory'} on:click={() => navigate('inventory')}>Hardware</button>
    <button type="button" class="nav-item" class:active={view === 'mobile'} on:click={() => navigate('mobile')}>Mobile Devices</button>
    <div class="nav-group-title">Operations</div>
    <button type="button" class="nav-item" class:active={view === 'entry_hardware'} on:click={() => navigate('entry_hardware')}>New Hardware</button>
    <button type="button" class="nav-item" class:active={view === 'entry_mobile'} on:click={() => navigate('entry_mobile')}>New Mobile</button>
    <button type="button" class="nav-item">Support Entry</button>
    <button type="button" class="nav-item">Assignments</button>
  </div>
</nav>

<style>
  .sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
  }

  .nav-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
  }

  .nav-group-title {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(17, 25, 40, 0.4);
    margin: var(--space-md) 0 var(--space-xs) var(--space-sm);
  }

  .nav-item {
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    border: 1px solid transparent;
  }

  .nav-item:hover {
    background: var(--color-bg-accent);
    color: var(--color-primary);
  }

  .nav-item.active {
    background: var(--color-bg-accent);
    color: var(--color-primary);
    border-color: rgba(245, 103, 26, 0.1);
  }
</style>
