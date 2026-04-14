<div class="layout-wrapper">
  <aside class="sidebar">
    <slot name="sidebar" />
  </aside>

  <main class="main-content">
    <slot name="content" />
  </main>
</div>

<style>
  .layout-wrapper {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: var(--space-lg);
    max-width: 1320px;
    margin: var(--space-xxl) auto;
    padding: 0 var(--space-xl);
    min-height: 100vh;
  }

  /* Desktop Layout */
  .sidebar {
    grid-column: span 4;
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
  }

  .main-content {
    grid-column: span 8;
  }

  /* Responsive Breakpoints */
  @media (max-width: 1024px) {
    .layout-wrapper {
      grid-template-columns: 80px 1fr;
      margin: var(--space-md) auto;
      padding: 0 var(--space-sm);
      gap: var(--space-sm);
    }

    .sidebar {
      grid-column: span 1;
    }

    .main-content {
      grid-column: span 1;
    }
  }
</style>
