<script>
  import Layout from './lib/Layout.svelte';
  import Card from './lib/Card.svelte';
  import Sidebar from './lib/Sidebar.svelte';
  import InventoryTable from './lib/InventoryTable.svelte';
  import EntryForm from './lib/EntryForm.svelte';

  let view = 'dashboard';
</script>

<Layout>
  <div slot="sidebar">
    <Sidebar />
  </div>

  <div slot="content">
    {#if view === 'dashboard'}
      <header class="page-header">
        <h1 class="main-title">Asset Inventory</h1>
        <p>Modern Infrastructure Management for SIOTIC</p>
      </header>

      <div class="metrics-grid">
        <Card>
          <div class="metric-label">Registered Assets</div>
          <div class="metric-value">642</div>
        </Card>
        <Card>
          <div class="metric-label">In Support</div>
          <div class="metric-value highlight">18</div>
        </Card>
        <Card>
          <div class="metric-label">Available Mobile</div>
          <div class="metric-value">42</div>
        </Card>
      </div>

      <Card>
        <div class="card-header">
          <h2>Recent Movements</h2>
          <button class="btn-primary" on:click={() => view = 'entry'}>
            New Entry
          </button>
        </div>
        <InventoryTable />
      </Card>
    {:else if view === 'entry'}
      <header class="page-header">
        <h1 class="main-title">Register New Asset</h1>
        <p>Fill in the details to add hardware to the database</p>
      </header>

      <Card>
        <EntryForm />
      </Card>

      <div class="footer-actions">
        <button class="btn-ghost" on:click={() => view = 'dashboard'}>
          Back to Dashboard
        </button>
      </div>
    {/if}
  </div>
</Layout>

<style>
  .page-header {
    margin-bottom: var(--space-xl);
  }

  .main-title {
    color: var(--color-primary);
    margin-bottom: var(--space-xs);
  }

  .metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
    margin-bottom: var(--space-xl);
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

  .metric-value.highlight {
    color: var(--color-primary);
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-lg);
  }

  .card-header h2 {
    font-size: 18px;
  }

  .btn-primary {
    background: var(--color-primary);
    color: white;
    border: none;
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-primary:hover {
    filter: brightness(1.1);
  }

  .footer-actions {
    margin-top: var(--space-lg);
    text-align: center;
  }

  .btn-ghost {
    background: transparent;
    border: none;
    color: var(--color-dark);
    opacity: 0.6;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
  }

  .btn-ghost:hover {
    opacity: 1;
    color: var(--color-primary);
  }

  @media (max-width: 1024px) {
    .metrics-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
