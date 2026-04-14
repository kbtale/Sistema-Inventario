<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import Card from "./Card.svelte";

  let inSupport = [];
  let loading = true;

  onMount(loadData);

  async function loadData() {
    loading = true;
    try {
      const [hardware, telefonos] = await Promise.all([
        api.getHardware(),
        api.getTelefonos(),
      ]);

      const hSupport = hardware
        .filter((item) => item.estado_actual_unidad == 2)
        .map((item) => ({ ...item, type: "Hardware" }));
      const tSupport = telefonos
        .filter((item) => item.estado_actual_unidad == 2)
        .map((item) => ({ ...item, type: "Mobile" }));

      inSupport = [...hSupport, ...tSupport];
    } finally {
      loading = false;
    }
  }

  function getDaysInSupport(date) {
    if (!date) return 0;
    const diff = Date.now() - new Date(date).getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24));
  }
</script>

<div class="board-container">
  <div class="board-header">
    <div class="header-info">
      <h2>Active Support Cases</h2>
      <p>{inSupport.length} assets currently in technical service</p>
    </div>
    <button class="btn-refresh" on:click={loadData} disabled={loading}>
      {loading ? "Syncing..." : "Refresh Board"}
    </button>
  </div>

  <div class="kanban-grid">
    <div class="kanban-column">
      <div class="column-header">
        <span class="status-dot warning"></span>
        <h3>In Maintenance</h3>
        <span class="count-badge">{inSupport.length}</span>
      </div>

      <div class="cards-list">
        {#each inSupport as item}
          <div class="support-card">
            <Card>
              <div class="card-top">
                <span class="asset-tag"
                  >{item.bienes_hardware ||
                    item.imei_telefono ||
                    "No Tag"}</span
                >
                <span
                  class="time-badge"
                  class:critical={getDaysInSupport(item.fecha_entrada) > 3}
                >
                  {getDaysInSupport(item.fecha_entrada)}d
                </span>
              </div>

              <div class="card-body">
                <h4 class="asset-title">
                  {item.marca_hardware || item.marca_telefono}
                  {item.modelo_hardware || item.modelo_telefono}
                </h4>
                <p class="asset-desc">
                  {item.tipo_hardware || item.tipo_telefono}
                </p>

                {#if item.fallas}
                  <div class="fault-box">
                    <strong>Reported Fault:</strong>
                    <p>{item.fallas}</p>
                  </div>
                {/if}
              </div>

              <div class="card-footer">
                <div class="user-info">
                  <span class="icon">👤</span>
                  {item.usuario_hardware ||
                    item.usuario_asignado ||
                    "Unassigned"}
                </div>
                <button class="btn-action">View Details</button>
              </div>
            </Card>
          </div>
        {:else}
          <div class="empty-column">
            {#if loading}
              <p>Loading active cases...</p>
            {:else}
              <p>All clear! No assets in maintenance.</p>
            {/if}
          </div>
        {/each}
      </div>
    </div>

    <div class="kanban-column empty">
      <div class="column-header">
        <span class="status-dot success"></span>
        <h3>Ready for Release</h3>
        <span class="count-badge">0</span>
      </div>
      <div class="drop-zone">
        <p>Complete repair to move items here</p>
      </div>
    </div>
  </div>
</div>

<style>
  .board-container {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
  }

  .board-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
  }

  .header-info h2 {
    font-size: 24px;
    margin-bottom: 4px;
  }

  .header-info p {
    color: rgba(17, 25, 40, 0.5);
    font-size: 14px;
  }

  .kanban-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--space-xl);
    align-items: start;
  }

  .column-header {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-lg);
  }

  .column-header h3 {
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    flex-grow: 1;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
  }

  .status-dot.warning {
    background: #f59e0b;
    box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
  }
  .status-dot.success {
    background: #10b981;
  }

  .count-badge {
    background: rgba(0, 0, 0, 0.05);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .cards-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
  }

  .support-card {
    transition: var(--transition);
  }

  .support-card:hover {
    transform: translateY(-2px);
  }

  .card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-md);
  }

  .asset-tag {
    font-family: ui-monospace, monospace;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-primary);
    background: rgba(245, 103, 26, 0.05);
    padding: 2px 8px;
    border-radius: 4px;
  }

  .time-badge {
    font-size: 11px;
    font-weight: 700;
    background: #edf2f7;
    padding: 2px 6px;
    border-radius: 4px;
    color: #4a5568;
  }

  .time-badge.critical {
    background: #fed7d7;
    color: #c53030;
  }

  .asset-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 2px;
  }

  .asset-desc {
    font-size: 13px;
    opacity: 0.6;
    margin-bottom: var(--space-md);
  }

  .fault-box {
    background: #f8fafc;
    border-left: 3px solid #cbd5e1;
    padding: var(--space-sm) var(--space-md);
    margin: var(--space-md) 0;
    font-size: 13px;
  }

  .fault-box strong {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
  }

  .card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: var(--space-lg);
    padding-top: var(--space-md);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
  }

  .user-info {
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .btn-action {
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.1);
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-action:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
  }

  .drop-zone {
    border: 2px dashed rgba(0, 0, 0, 0.1);
    border-radius: var(--radius-lg);
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(17, 25, 40, 0.3);
    font-size: 14px;
  }

  .btn-refresh {
    background: white;
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    padding: var(--space-sm) var(--space-lg);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-refresh:hover:not(:disabled) {
    background: #f8fafc;
    border-color: var(--color-primary);
    color: var(--color-primary);
  }

  .empty-column {
    padding: var(--space-xl);
    text-align: center;
    color: rgba(0, 0, 0, 0.4);
    font-style: italic;
    font-size: 14px;
  }

  @media (max-width: 640px) {
    .kanban-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
