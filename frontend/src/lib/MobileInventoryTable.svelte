<script>
  import { onMount } from "svelte";
  import { api } from "./api";

  let devices = [];
  let loading = true;
  let searchQuery = "";
  let selectedBrand = "";

  $: filteredDevices = devices.filter((dev) => {
    const searchString = `${dev.marca_telefono} ${dev.modelo_telefono} ${dev.nro_telefono} ${dev.imei_telefono} ${dev.usuario_asignado}`.toLowerCase();
    const matchesSearch = searchString.includes(searchQuery.toLowerCase());
    const matchesBrand = !selectedBrand || dev.marca_telefono === selectedBrand;
    return matchesSearch && matchesBrand;
  });

  $: brands = [...new Set(devices.map((d) => d.marca_telefono))].filter(Boolean);

  onMount(async () => {
    try {
      devices = await api.getTelefonos();
    } finally {
      loading = false;
    }
  });

  function downloadCSV() {
    if (filteredDevices.length === 0) return;
    
    const headers = ["Brand", "Model", "Number", "IMEI", "SIM/ICCID", "PUK", "User", "Status"];
    const rows = filteredDevices.map(dev => [
      `"${dev.marca_telefono || ""}"`,
      `"${dev.modelo_telefono || ""}"`,
      `"${dev.nro_telefono || ""}"`,
      `"${dev.imei_telefono || ""}"`,
      `"${dev.imeisim_telefono || ""}"`,
      `"${dev.puk_telefono || ""}"`,
      `"${dev.usuario_asignado || ""}"`,
      dev.estado_actual_unidad == 1 ? "Available" : "Maintenance"
    ]);

    const csvContent = [headers, ...rows].map(e => e.join(",")).join("\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", `siotic_mobile_${new Date().toISOString().split('T')[0]}.csv`);
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
      <button class="btn-export" on:click={downloadCSV} disabled={filteredDevices.length === 0}>
        Export CSV
      </button>
      <button class="btn-export btn-pdf" on:click={() => window.print()} disabled={filteredDevices.length === 0}>
        Export PDF
      </button>
    </div>
  </div>

  <table class="inventory-table">
    <thead>
      <tr>
        <th>Brand & Model</th>
        <th>Phone Number</th>
        <th>IMEI / Serial</th>
        <th>SIM / ICCID</th>
        <th>PUK</th>
        <th>Assigned User</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      {#each filteredDevices as dev}
        <tr>
          <td>
            <div class="model-cell">
              <span class="bold">{dev.marca_telefono}</span>
              <span class="subtext">{dev.modelo_telefono}</span>
            </div>
          </td>
          <td><span class="number-cell">{dev.nro_telefono || "-"}</span></td>
          <td><span class="mono">{dev.imei_telefono || "-"}</span></td>
          <td><span class="mono subtext">{dev.imeisim_telefono || "-"}</span></td>
          <td><span class="puk-badge">{dev.puk_telefono || "****"}</span></td>
          <td>{dev.usuario_asignado || "Unassigned"}</td>
          <td>
            <span class="status-badge" class:available={dev.estado_actual_unidad == 1}>
              {dev.estado_actual_unidad == 1 ? "Available" : "Maintenance"}
            </span>
          </td>
        </tr>
      {:else}
        <tr>
          <td colspan="7" class="empty-state">
            {#if loading}
              Loading mobile devices...
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

  .empty-state {
    text-align: center;
    padding: var(--space-xl);
    opacity: 0.5;
    font-style: italic;
  }
</style>
