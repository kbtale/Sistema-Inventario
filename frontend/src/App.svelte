<script>
  import { onMount } from 'svelte';
  import { fade } from 'svelte/transition';
  import Layout from './lib/Layout.svelte';
  import Card from './lib/Card.svelte';
  import Sidebar from './lib/Sidebar.svelte';
  import InventoryTable from './lib/InventoryTable.svelte';
  import MobileInventoryTable from './lib/MobileInventoryTable.svelte';
  import EntryForm from './lib/EntryForm.svelte';
  import MobileEntryForm from './lib/MobileEntryForm.svelte';
  import SupportBoard from './lib/SupportBoard.svelte';
  import SupportEntryForm from './lib/SupportEntryForm.svelte';
  import SupportExitForm from './lib/SupportExitForm.svelte';
  import AssignmentForm from './lib/AssignmentForm.svelte';
  import { api } from './lib/api';

  let view = 'dashboard';
  let metrics = { total_hardware: 0, total_telefonos: 0, in_support: 0, available_mobile: 0 };
  let loading = true;

  onMount(refreshData);

  async function refreshData() {
    loading = true;
    try {
      metrics = await api.getDashboard();
    } catch (e) {
      console.error("Health check failed", e);
    } finally {
      loading = false;
    }
  }

  const handleNavigate = (e) => {
    view = e.detail;
    if (['dashboard', 'mobile', 'support_board'].includes(view)) {
      refreshData();
    }
  };
</script>

<Layout>
  <div slot="sidebar">
    <Sidebar {view} on:navigate={handleNavigate} />
  </div>

  <div slot="content">
    <div class="view-transition-container" in:fade={{ duration: 200 }}>
      {#if view === 'dashboard'}
        <header class="page-header">
          <h1 class="main-title">SIOTIC Dashboard</h1>
          <p>Real-time infrastructure overview and asset health.</p>
        </header>

        <div class="metrics-grid">
          <Card>
            <div class="metric-info">
              <span class="metric-label">Total Assets</span>
              <div class="metric-value">{metrics.total_hardware + metrics.total_telefonos}</div>
            </div>
          </Card>
          <Card>
            <div class="metric-info">
              <span class="metric-label">In Maintenance</span>
              <div class="metric-value highlight">{metrics.in_support}</div>
            </div>
          </Card>
          <Card>
            <div class="metric-info">
              <span class="metric-label">Available Mobile</span>
              <div class="metric-value">{metrics.available_mobile}</div>
            </div>
          </Card>
        </div>

        <Card>
          <div class="card-header">
            <h3>Recent Movements</h3>
            <button class="btn-primary" on:click={() => (view = "entry")}>
              + Register Hardware
            </button>
          </div>
          <InventoryTable />
        </Card>
      {:else if view === "mobile"}
        <header class="page-header">
          <h1 class="main-title">Mobile Device Inventory</h1>
          <div class="header-actions">
            <button class="btn-primary" on:click={() => (view = "entry_mobile")}>
              + Register Mobile
            </button>
          </div>
        </header>
        <MobileInventoryTable />
      {:else if view === "entry"}
        <header class="page-header">
          <h1 class="main-title">Register Hardware</h1>
          <p>Add a new device or workstation to the central registry.</p>
        </header>

        <Card>
          <EntryForm on:success={refreshData} />
        </Card>

        <div class="footer-actions">
          <button class="btn-ghost" on:click={() => (view = "dashboard")}>
            Back to Dashboard
          </button>
        </div>
      {:else if view === "entry_mobile"}
        <header class="page-header">
          <h1 class="main-title">Mobile Registration</h1>
          <p>Register a new Smartphone, Tablet, or SIM card.</p>
        </header>

        <Card>
          <MobileEntryForm on:success={refreshData} />
        </Card>

        <div class="footer-actions">
          <button class="btn-ghost" on:click={() => (view = "dashboard")}>
            Back to Dashboard
          </button>
        </div>
      {:else if view === "support_board"}
        <header class="page-header">
          <h1 class="main-title">Support Management</h1>
          <p>Monitor and progress assets through the maintenance lifecycle.</p>
        </header>
        <SupportBoard />
      {:else if view === "support_entry"}
        <header class="page-header">
          <h1 class="main-title">Maintenance Check-in</h1>
          <p>Officially enter an asset into the maintenance cycle with a Work Order.</p>
        </header>
        <Card>
          <SupportEntryForm on:success={refreshData} />
        </Card>
        <div class="footer-actions">
          <button class="btn-ghost" on:click={() => (view = "dashboard")}>
            Back to Dashboard
          </button>
        </div>
      {:else if view === "support_exit"}
        <header class="page-header">
          <h1 class="main-title">Maintenance Release</h1>
          <p>Finalize technical service and return the asset to the available pool.</p>
        </header>
        <Card>
          <SupportExitForm on:success={refreshData} />
        </Card>
        <div class="footer-actions">
          <button class="btn-ghost" on:click={() => (view = "dashboard")}>
            Back to Dashboard
          </button>
        </div>
      {:else if view === "assignments"}
        <header class="page-header">
          <h1 class="main-title">Asset Assignment</h1>
          <p>Link an available unit in stock to a specific user or department.</p>
        </header>
        <Card>
          <AssignmentForm on:success={refreshData} />
        </Card>
        <div class="footer-actions">
          <button class="btn-ghost" on:click={() => (view = "dashboard")}>
            Back to Dashboard
          </button>
        </div>
      {/if}
    </div>
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

  .metric-info {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
  }

  .metric-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--color-primary);
    opacity: 0.8;
  }

  .metric-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--color-dark);
    line-height: 1;
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

  .card-header h3 {
    font-size: 18px;
    font-weight: 600;
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
