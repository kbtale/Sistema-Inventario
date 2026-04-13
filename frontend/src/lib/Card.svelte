<div class="card">
  <slot />
</div>

<style>
  .card {
    background: var(--color-bg-card);
    padding: var(--space-xl);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-premium);
    border: 1px solid rgba(0, 0, 0, 0.05);
  }
</style>
