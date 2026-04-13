<nav class="sidebar-nav">
  <div class="nav-group">
    <div class="nav-item active">Hardware</div>
    <div class="nav-item">Mobile Devices</div>
    <div class="nav-group-title">Operations</div>
    <div class="nav-item">Support Entry</div>
    <div class="nav-item">Unit Salida</div>
    <div class="nav-item">Assignments</div>
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
