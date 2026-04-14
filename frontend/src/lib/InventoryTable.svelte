<script>
  import { onMount } from 'svelte';
  import { api } from './api';

  let assets = [];
  let loading = true;

  onMount(async () => {
    try {
      assets = await api.getHardware();
    } finally {
      loading = false;
    }
  });
</script>

<div class="table-container">
  <table class="inventory-table">
    <thead>
      <tr>
        <th>ID / Order</th>
        <th>Type</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Asset Tag</th>
        <th>User</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      {#each assets as asset}
        <tr>
          <td class="bold">{asset.numero_orden || '-'}</td>
          <td>{asset.tipo_hardware || '-'}</td>
          <td>{asset.marca_hardware || '-'}</td>
          <td>{asset.modelo_hardware || '-'}</td>
          <td><span class="badge-secondary">{asset.bienes_hardware || '-'}</span></td>
          <td>{asset.usuario_hardware || '-'}</td>
          <td>{asset.fecha_entrada || '-'}</td>
        </tr>
      {:else}
        <tr>
          <td colspan="7" class="empty-state">
            {#if loading}
              Loading assets...
            {:else}
              No movements recorded yet.
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

  .id-col {
    font-weight: 600;
    color: var(--color-dark);
  }

  .model-text {
    opacity: 0.6;
    font-weight: 400;
  }

  .tag-badge {
    background: #E5E7EB;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: ui-monospace, monospace;
    font-size: 12px;
    font-weight: 500;
  }
</style>
