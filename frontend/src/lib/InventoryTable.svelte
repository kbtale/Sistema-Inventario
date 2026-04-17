<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import PulseIndicator from "./PulseIndicator.svelte";
  import Timeline from "./Timeline.svelte";

  let assets = [];
  let loading = true;
  let searchQuery = "";
  let selectedType = "";

  let selectedAsset = null;
  let isTimelineOpen = false;

  function openTimeline(asset) {
    selectedAsset = asset;
    isTimelineOpen = true;
  }

  $: filteredAssets = assets.filter((asset) => {
    const searchString =
      `${asset.tipo_hardware} ${asset.marca_hardware} ${asset.modelo_hardware} ${asset.bienes_hardware} ${asset.usuario_hardware}`.toLowerCase();
    const matchesSearch = searchString.includes(searchQuery.toLowerCase());
    const matchesType = !selectedType || asset.tipo_hardware === selectedType;
    return matchesSearch && matchesType;
  });

  $: types = [...new Set(assets.map((a) => a.tipo_hardware))].filter(Boolean);

  onMount(async () => {
    try {
      assets = await api.getHardware();
    } finally {
      loading = false;
    }
  });

  function downloadCSV() {
    if (filteredAssets.length === 0) return;

    const headers = ["Order", "Type", "Brand", "Model", "Tag", "User", "Date"];
    const rows = filteredAssets.map((asset) => [
      asset.numero_orden || "",
      `"${asset.tipo_hardware || ""}"`,
      `"${asset.marca_hardware || ""}"`,
      `"${asset.modelo_hardware || ""}"`,
      `"${asset.bienes_hardware || ""}"`,
      `"${asset.usuario_hardware || ""}"`,
      asset.fecha_entrada || "",
    ]);

    const csvContent = [headers, ...rows].map((e) => e.join(",")).join("\n");
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute(
      "download",
      `siotic_inventory_${new Date().toISOString().split("T")[0]}.csv`,
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
        placeholder="Search by tag, user, or model..."
        bind:value={searchQuery}
      />
    </div>
    <div class="filter-box">
      <select bind:value={selectedType}>
        <option value="">All Categories</option>
        {#each types as type}
          <option value={type}>{type}</option>
        {/each}
      </select>
    </div>
    <div class="export-box">
      <button
        class="btn-export"
        on:click={downloadCSV}
        disabled={filteredAssets.length === 0}
      >
        Export CSV
      </button>
      <button
        class="btn-export btn-pdf"
        on:click={() => window.print()}
        disabled={filteredAssets.length === 0}
      >
        Export PDF
      </button>
    </div>
  </div>

  <table class="inventory-table">
    <thead>
      <tr>
        <th>Health</th>
        <th>ID / Order</th>
        <th>Type</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Asset Tag</th>
        <th>User</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      {#each filteredAssets as asset}
        <tr>
          <td><PulseIndicator score={asset.pulse_score} size="sm" /></td>
          <td class="bold">{asset.numero_orden || "-"}</td>
          <td>{asset.tipo_hardware || "-"}</td>
          <td>{asset.marca_hardware || "-"}</td>
          <td>{asset.modelo_hardware || "-"}</td>
          <td
            ><span class="badge-secondary">{asset.bienes_hardware || "-"}</span
            ></td
          >
          <td>{asset.usuario_hardware || "-"}</td>
          <td>{asset.fecha_entrada || "-"}</td>
          <td>
            <button class="btn-action" on:click={() => openTimeline(asset)}>
              History
            </button>
          </td>
        </tr>
      {:else}
        <tr>
          <td colspan="9" class="empty-state">
            {#if loading}
              Loading assets...
            {:else if searchQuery || selectedType}
              No matching assets found for your search.
            {:else}
              No movements recorded yet.
            {/if}
          </td>
        </tr>
      {/each}
    </tbody>
  </table>

  <Timeline 
    isOpen={isTimelineOpen} 
    assetId={selectedAsset?.id_hardware} 
    assetType="hardware"
    assetName={selectedAsset?.tipo_hardware}
    on:close={() => (isTimelineOpen = false)}
  />
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

  .bold {
    font-weight: 600;
    color: var(--color-dark);
  }

  .badge-secondary {
    background: rgba(0, 0, 0, 0.05);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    font-family: ui-monospace, monospace;
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
