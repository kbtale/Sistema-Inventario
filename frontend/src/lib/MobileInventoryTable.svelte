<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import PulseIndicator from "./PulseIndicator.svelte";
  import Timeline from "./Timeline.svelte";
  import QRBadge from "./QRBadge.svelte";
  import Skeleton from "./Skeleton.svelte";

  let devices = [];
  let loading = true;
  let searchQuery = "";
  let selectedBrand = "";

  let selectedDevice = null;
  let isTimelineOpen = false;

  function openTimeline(dev) {
    selectedDevice = dev;
    isTimelineOpen = true;
  }

  let showQRAsset = null;

  $: filteredDevices = devices.filter((dev) => {
    const searchString =
      `${dev.marca_telefono} ${dev.modelo_telefono} ${dev.nro_telefono} ${dev.imei_telefono} ${dev.usuario_asignado}`.toLowerCase();
    const matchesSearch = searchString.includes(searchQuery.toLowerCase());
    const matchesBrand = !selectedBrand || dev.marca_telefono === selectedBrand;
    return matchesSearch && matchesBrand;
  });

  $: brands = [...new Set(devices.map((d) => d.marca_telefono))].filter(
    Boolean,
  );

  onMount(async () => {
    try {
      devices = await api.getTelefonos();
    } finally {
      loading = false;
    }
  });

  function downloadCSV() {
    if (filteredDevices.length === 0) return;

    const headers = [
      "Brand",
      "Model",
      "Number",
      "IMEI",
      "SIM/ICCID",
      "PUK",
      "User",
      "Status",
    ];
    const rows = filteredDevices.map((dev) => [
      `"${dev.marca_telefono || ""}"`,
      `"${dev.modelo_telefono || ""}"`,
      `"${dev.nro_telefono || ""}"`,
      `"${dev.imei_telefono || ""}"`,
      `"${dev.imeisim_telefono || ""}"`,
      `"${dev.puk_telefono || ""}"`,
      `"${dev.usuario_asignado || ""}"`,
      dev.estado_actual_unidad == 1 ? "Available" : "Maintenance",
    ]);

    const csvContent = [headers, ...rows].map((e) => e.join(",")).join("\n");
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute(
      "download",
      `siotic_mobile_${new Date().toISOString().split("T")[0]}.csv`,
    );
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>

<div class="table-container">
  <div class="table-filters">
    <div class="search-box">
      <input
        type="text"
        placeholder="Search by Number, IMEI, or User..."
        bind:value={searchQuery}
      />
    </div>
    <div class="filter-box">
      <select bind:value={selectedBrand}>
        <option value="">All Brands</option>
        {#each brands as brand}
          <option value={brand}>{brand}</option>
        {/each}
      </select>
    </div>
    <div class="export-box">
      <button
        class="btn-export"
        on:click={downloadCSV}
        disabled={filteredDevices.length === 0}
      >
        Export CSV
      </button>
      <button
        class="btn-export btn-pdf"
        on:click={() => window.print()}
        disabled={filteredDevices.length === 0}
      >
        Export PDF
      </button>
    </div>
  </div>

  <table class="inventory-table">
    <thead>
      <tr>
        <th>Health</th>
        <th>Brand & Model</th>
        <th>Phone Number</th>
        <th>IMEI / Serial</th>
        <th>SIM / ICCID</th>
        <th>PUK</th>
        <th>Assigned User</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      {#each filteredDevices as dev}
        <tr>
          <td>
            <PulseIndicator score={dev.pulse_score} size="sm" />
          </td>
          <td>
            <div class="model-cell">
              <span class="bold">{dev.marca_telefono}</span>
              <span class="subtext">{dev.modelo_telefono}</span>
            </div>
          </td>
          <td><span class="number-cell">{dev.nro_telefono || "-"}</span></td>
          <td><span class="mono">{dev.imei_telefono || "-"}</span></td>
          <td
            ><span class="mono subtext">{dev.imeisim_telefono || "-"}</span></td
          >
          <td><span class="puk-badge">{dev.puk_telefono || "****"}</span></td>
          <td>{dev.usuario_asignado || "Unassigned"}</td>
          <td>
            <span
              class="status-badge"
              class:available={dev.estado_actual_unidad == 1}
            >
              {dev.estado_actual_unidad == 1 ? "Available" : "Maintenance"}
            </span>
          </td>
          <td>
            <div class="actions-group">
              <button class="btn-action" on:click={() => openTimeline(dev)}>
                History
              </button>
              <button 
                class="btn-action btn-qr" 
                on:click={() => (showQRAsset = dev)}
                title="View QR Label"
              >
                QR
              </button>
            </div>
          </td>
        </tr>
      {:else}
        <tr>
          <td colspan="9" class="empty-state">
            {#if loading}
              <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                <Skeleton width="100%" height="40px" />
                <Skeleton width="100%" height="40px" />
                <Skeleton width="100%" height="40px" />
                <Skeleton width="100%" height="40px" />
                <Skeleton width="100%" height="40px" />
              </div>
            {:else if searchQuery || selectedBrand}
              No matching devices found.
            {:else}
              No mobile assets registered yet.
            {/if}
          </td>
        </tr>
      {/each}
    </tbody>
  </table>

  <Timeline
    isOpen={isTimelineOpen}
    assetId={selectedDevice?.id_telefono}
    assetType="mobile"
    assetName={selectedDevice?.marca_telefono}
    on:close={() => (isTimelineOpen = false)}
  />

  {#if showQRAsset}
    <div 
      class="modal-overlay" 
      on:click={() => (showQRAsset = null)}
      on:keydown={(e) => ["Enter", " ", "Escape"].includes(e.key) && (showQRAsset = null)}
      role="button"
      tabindex="0"
    >
      <div class="qr-modal card" on:click|stopPropagation role="presentation">
        <div class="modal-header">
          <h3>Mobile Identifier</h3>
          <button class="btn-close" on:click={() => (showQRAsset = null)}>&times;</button>
        </div>
        <QRBadge 
          type="mobile" 
          id={showQRAsset.id_telefono} 
          label={`${showQRAsset.marca_telefono} ${showQRAsset.modelo_telefono}`}
          size={200}
        />
      </div>
    </div>
  {/if}
</div>

<style>
  .table-container {
    width: 100%;
    overflow-x: auto;
  }

  .table-filters {
    display: flex;
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
  }

  .search-box {
    flex-grow: 1;
  }

  .search-box input,
  .filter-box select {
    width: 100%;
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    font-size: 13px;
    font-family: inherit;
    transition: var(--transition);
    background: white;
  }

  .search-box input:focus,
  .filter-box select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(245, 103, 26, 0.1);
  }

  .export-box {
    display: flex;
    gap: var(--space-xs);
  }

  .btn-export {
    background: white;
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    color: var(--color-dark);
    white-space: nowrap;
  }

  .btn-export:hover:not(:disabled) {
    border-color: var(--color-primary);
    color: var(--color-primary);
    background: rgba(245, 103, 26, 0.05);
  }

  .btn-export:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .btn-pdf {
    background: var(--color-primary);
    color: white;
    border: none;
  }

  .btn-pdf:hover:not(:disabled) {
    background: var(--color-primary);
    filter: brightness(1.1);
    color: white;
  }

  .inventory-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    text-align: left;
  }

  th {
    padding: var(--space-md);
    font-weight: 600;
    color: rgba(17, 25, 40, 0.5);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.05em;
  }

  td {
    padding: var(--space-md);
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    vertical-align: middle;
  }

  tr:hover td {
    background: var(--color-bg-accent);
  }

  .model-cell {
    display: flex;
    flex-direction: column;
  }

  .bold {
    font-weight: 600;
    color: var(--color-dark);
  }

  .subtext {
    font-size: 12px;
    opacity: 0.6;
  }

  .number-cell {
    font-weight: 600;
    color: var(--color-primary);
  }

  .white-space-nowrap {
    white-space: nowrap;
  }

  .mono {
    font-family: ui-monospace, monospace;
    font-size: 12px;
  }

  .puk-badge {
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #4b5563;
  }

  .status-badge {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 12px;
    background: #fee2e2;
    color: #ef4444;
  }

  .status-badge.available {
    background: #d1fae5;
    color: #10b981;
  }

  .btn-action {
    background: transparent;
    border: 1px solid var(--color-primary);
    color: var(--color-primary);
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-action:hover {
    background: var(--color-primary);
    color: white;
  }

  .actions-group {
    display: flex;
    gap: 4px;
  }

  .btn-qr {
    border-color: var(--color-dark);
    color: var(--color-dark);
    opacity: 0.6;
  }

  .btn-qr:hover {
    background: var(--color-dark);
    color: white;
    opacity: 1;
  }

  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }

  .qr-modal {
    width: 300px;
    padding: var(--space-xl);
    text-align: center;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-lg);
  }

  .modal-header h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
  }

  .btn-close {
    background: transparent;
    border: none;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    opacity: 0.5;
  }

  .empty-state {
    text-align: center;
    padding: var(--space-xl);
    opacity: 0.5;
    font-style: italic;
  }

  @media print {
    :global(body .table-container) {
      overflow: visible;
    }

    .inventory-table {
      font-size: 10px;
    }

    th,
    td {
      padding: 4px var(--space-sm);
    }
  }
</style>
