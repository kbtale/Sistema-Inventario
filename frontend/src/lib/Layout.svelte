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
      grid-template-columns: 1fr;
      margin: var(--space-lg) auto;
      padding: 0 var(--space-md);
    }

    .sidebar {
      grid-column: span 1;
      order: 2;
    }

    .main-content {
      grid-column: span 1;
      order: 1;
    }
  }
</style>
