<script>
  export let assets = [
    { id: 'ORD-001', type: 'Workstation', brand: 'Dell', model: 'Precision 3660', tag: 'BIT-00124', user: 'Admin OTIC', date: '2024-03-12' },
    { id: 'ORD-002', type: 'Laptop', brand: 'Lenovo', model: 'ThinkPad T14', tag: 'BIT-00125', user: 'Soporte Técnico', date: '2024-03-14' },
    { id: 'ORD-005', type: 'Mobile', brand: 'Samsung', model: 'Galaxy A54', tag: 'CEL-00042', user: 'Logística', date: '2024-03-15' }
  ];
</script>

<div class="table-container">
  <table class="inventory-table">
    <thead>
      <tr>
        <th>ID / Order</th>
        <th>Type</th>
        <th>Brand & Model</th>
        <th>Asset Tag</th>
        <th>User</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      {#each assets as asset}
        <tr>
          <td class="id-col">{asset.id}</td>
          <td>{asset.type}</td>
          <td>{asset.brand} <span class="model-text">{asset.model}</span></td>
          <td><span class="tag-badge">{asset.tag}</span></td>
          <td>{asset.user}</td>
          <td>{asset.date}</td>
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
