<script>
  import Layout from './lib/Layout.svelte';
  import Card from './lib/Card.svelte';
  import Sidebar from './lib/Sidebar.svelte';
</script>

<Layout>
  <div slot="sidebar">
    <Sidebar />
  </div>

  <div slot="content">
    <header style="margin-bottom: var(--space-xl);">
      <h1 style="color: var(--color-primary); margin-bottom: var(--space-xs);">Asset Inventory</h1>
      <p>Modern Infrastructure Management for SIOTIC</p>
    </header>

    <div class="metrics-grid">
      <Card>
        <div class="metric-label">Registered Assets</div>
        <div class="metric-value">642</div>
      </Card>
      <Card>
        <div class="metric-label">In Support</div>
        <div class="metric-value" style="color: var(--color-primary)">18</div>
      </Card>
      <Card>
        <div class="metric-label">Available Mobile</div>
        <div class="metric-value">42</div>
      </Card>
    </div>
  </div>
</Layout>

<style>
  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
  }

  .metric-label {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.6;
    margin-bottom: var(--space-xs);
  }

  .metric-value {
    font-size: 32px;
    font-weight: 600;
    font-family: var(--font-primary);
  }

  @media (max-width: 1024px) {
    .metrics-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
