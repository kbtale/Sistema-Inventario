<script>
  import { onMount } from "svelte";
  import { fade } from "svelte/transition";
  import Layout from "./lib/Layout.svelte";
  import Card from "./lib/Card.svelte";
  import Sidebar from "./lib/Sidebar.svelte";
  import InventoryTable from "./lib/InventoryTable.svelte";
  import MobileInventoryTable from "./lib/MobileInventoryTable.svelte";
  import EntryForm from "./lib/EntryForm.svelte";
  import MobileEntryForm from "./lib/MobileEntryForm.svelte";
  import SupportBoard from "./lib/SupportBoard.svelte";
  import SupportEntryForm from "./lib/SupportEntryForm.svelte";
  import SupportExitForm from "./lib/SupportExitForm.svelte";
  import AssignmentForm from "./lib/AssignmentForm.svelte";
  import SedeMapper from "./lib/SedeMapper.svelte";
  import HealthAnalytics from "./lib/HealthAnalytics.svelte";
  import AlertCenter from "./lib/AlertCenter.svelte";
  import Timeline from "./lib/Timeline.svelte";
  import QRScanner from "./lib/QRScanner.svelte";
  import { api } from "./lib/api";

  let isLoggedIn = api.isAuthenticated();
  let view = "dashboard";
  let metrics = {
    total_hardware: 0,
    total_telefonos: 0,
    in_support: 0,
    available_mobile: 0,
  };
  let loading = true;
  let selectedSede = null;
  let isScannerOpen = false;
  let scannedAsset = null;

  let alertCenter;

  onMount(() => {
    if (isLoggedIn) refreshData();

    // Global polling for operational alerts every 2 minutes
    const interval = setInterval(() => {
      if (isLoggedIn && alertCenter) alertCenter.refresh();
    }, 120000);

    return () => clearInterval(interval);
  });

  async function refreshData() {
    if (!isLoggedIn) return;
    loading = true;
    try {
      metrics = await api.getDashboard();
    } catch (e) {
      console.error("Health check failed", e);
    } finally {
      loading = false;
    }
  }

  const handleSedeFilter = (e) => {
    selectedSede = e.detail;
  };

  const handleNavigate = (e) => {
    view = e.detail;
    if (["dashboard", "mobile", "support_board", "analytics"].includes(view)) {
      refreshData();
    }
  };

  const handleLoginSuccess = () => {
    isLoggedIn = true;
    refreshData();
  };

  const handleScanSuccess = (e) => {
    isScannerOpen = false;
    scannedAsset = {
      id: e.detail.id,
      type: e.detail.type,
      name: `Scanned ${e.detail.type}`
    };
  };
</script>

{#if !isLoggedIn}
  <Login on:login={handleLoginSuccess} />
{:else}
  <Layout>
    <div slot="sidebar">
      <Sidebar 
        {view} 
        on:navigate={handleNavigate} 
        on:openScanner={() => (isScannerOpen = true)} 
      />
    </div>

    <div slot="content">
      <div class="top-bar">
        <div class="user-context">
          <div class="avatar-circle">AD</div>
          <div class="user-meta">
            <span class="user-role">Administrator</span>
            <span class="system-status">System Live</span>
          </div>
        </div>
        <AlertCenter bind:this={alertCenter} />
      </div>

      <div class="view-transition-container" in:fade={{ duration: 200 }}>
        {#if view === "dashboard"}
          <header class="page-header">
            <h1 class="main-title">SIOTIC Dashboard</h1>
            <p>Real-time infrastructure overview and asset health.</p>
          </header>

          <div class="metrics-grid">
            <Card>
              <div class="metric-info">
                <span class="metric-label">Total Assets</span>
                <div class="metric-value">
                  {metrics.total_hardware + metrics.total_telefonos}
                </div>
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

          <SedeMapper on:filter={handleSedeFilter} />

          <Card>
            <div class="card-header">
              <div class="header-titles">
                <h3>Recent Movements</h3>
                {#if selectedSede}
                  <div class="filter-badge" in:fade>
                    Showing {selectedSede.name}
                    <button
                      class="clear-filter"
                      on:click={() => (selectedSede = null)}>&times;</button
                    >
                  </div>
                {/if}
              </div>
              <button class="btn-primary" on:click={() => (view = "entry")}>
                + Register Hardware
              </button>
            </div>
            <InventoryTable filterSede={selectedSede?.id} />
          </Card>
        {:else if view === "mobile"}
          <header class="page-header">
            <h1 class="main-title">Mobile Device Inventory</h1>
            <div class="header-actions">
              <button
                class="btn-primary"
                on:click={() => (view = "entry_mobile")}
              >
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
            <p>
              Monitor and progress assets through the maintenance lifecycle.
            </p>
          </header>
          <SupportBoard />
        {:else if view === "support_entry"}
          <header class="page-header">
            <h1 class="main-title">Maintenance Check-in</h1>
            <p>
              Officially enter an asset into the maintenance cycle with a Work
              Order.
            </p>
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
            <p>
              Finalize technical service and return the asset to the available
              pool.
            </p>
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
            <p>
              Link an available unit in stock to a specific user or department.
            </p>
          </header>
          <Card>
            <AssignmentForm on:success={refreshData} />
          </Card>
          <div class="footer-actions">
            <button class="btn-ghost" on:click={() => (view = "dashboard")}>
              Back to Dashboard
            </button>
          </div>
        {:else if view === "analytics"}
          <header class="page-header">
            <h1 class="main-title">Infrastructure Health Prediction</h1>
            <p>Analyzing asset lifespan and maintenance risks.</p>
          </header>
          <HealthAnalytics />
          <div class="footer-actions">
            <button class="btn-ghost" on:click={() => (view = "dashboard")}>
              Back to Dashboard
            </button>
          </div>
        {/if}
      </div>
    </div>
  </Layout>

  {#if isScannerOpen}
    <QRScanner on:close={() => (isScannerOpen = false)} on:scan={handleScanSuccess} />
  {/if}

  {#if scannedAsset}
    <Timeline 
      isOpen={!!scannedAsset} 
      assetId={scannedAsset.id} 
      assetType={scannedAsset.type} 
      assetName={scannedAsset.name}
      on:close={() => (scannedAsset = null)}
    />
  {/if}
{/if}

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

  .header-titles {
    display: flex;
    align-items: center;
    gap: var(--space-md);
  }

  .filter-badge {
    background: rgba(245, 103, 26, 0.1);
    border: 1px solid var(--color-primary);
    color: var(--color-primary);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .clear-filter {
    background: var(--color-primary);
    color: white;
    border: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
    padding: 0;
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

  .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-md) 0;
    margin-bottom: var(--space-xl);
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
  }

  .user-context {
    display: flex;
    align-items: center;
    gap: var(--space-md);
  }

  .avatar-circle {
    width: 38px;
    height: 38px;
    background: var(--color-bg-accent);
    color: var(--color-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 13px;
    border: 2px solid rgba(245, 103, 26, 0.1);
  }

  .user-meta {
    display: flex;
    flex-direction: column;
  }

  .user-role {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-dark);
  }

  .system-status {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #10b981;
    letter-spacing: 0.05em;
  }

  @media (max-width: 1024px) {
    .metrics-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
