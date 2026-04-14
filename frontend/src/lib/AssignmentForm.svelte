<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import Card from "./Card.svelte";

  let availableAssets = [];
  let loading = false;
  let searching = true;
  let message = "";
  let error = "";

  let formData = {
    asset_id: "",
    asset_type: "hardware",
    nombre: "",
    apellido: "",
    ci: "",
  };

  onMount(loadAvailable);

  async function loadAvailable() {
    searching = true;
    try {
      const [hardware, telefonos] = await Promise.all([
        api.getHardware(),
        api.getTelefonos(),
      ]);

      const hAvailable = hardware
        .filter((item) => item.estado_actual_unidad == 1)
        .map((item) => ({
          id: item.id_hardware,
          type: "hardware",
          label: `[HW] ${item.tipo_hardware} - ${item.marca_hardware} (${item.bienes_hardware || "No Tag"})`,
        }));

      const tAvailable = telefonos
        .filter((item) => item.estado_actual_unidad == 1)
        .map((item) => ({
          id: item.id_telefono,
          type: "telefonos",
          label: `[MOB] ${item.marca_telefono} ${item.modelo_telefono} - ${item.nro_telefono || "No Number"}`,
        }));

      availableAssets = [...hAvailable, ...tAvailable];
    } finally {
      searching = false;
    }
  }

  async function handleAssign() {
    if (!formData.asset_id || !formData.nombre || !formData.ci) {
      error = "Please fill in all required fields.";
      return;
    }

    loading = true;
    message = "";
    error = "";

    try {
      const selected = availableAssets.find((a) => a.id == formData.asset_id);
      if (selected) formData.asset_type = selected.type;

      const response = await api.assignUser(formData);
      if (response.success) {
        message = "Asset assigned successfully!";
        formData = {
          asset_id: "",
          asset_type: "hardware",
          nombre: "",
          apellido: "",
          ci: "",
        };
        await loadAvailable();
      }
    } catch (e) {
      error = e.message || "Assignment failed.";
    } finally {
      loading = false;
    }
  }
</script>

<div class="assignment-container">
  {#if message}
    <div class="alert alert-success">{message}</div>
  {:else if error}
    <div class="alert alert-error">{error}</div>
  {/if}

  <div class="form-grid">
    <div class="field-group full-width">
      <label for="asset-select">Select Asset (In Stock)</label>
      <select
        id="asset-select"
        class="custom-select"
        bind:value={formData.asset_id}
        disabled={searching}
      >
        <option value="">-- Choose an asset --</option>
        {#each availableAssets as asset}
          <option value={asset.id}>{asset.label}</option>
        {/each}
      </select>
      {#if searching}
        <span class="helper-text">Fetching available units...</span>
      {/if}
    </div>

    <div class="field-group">
      <label for="u-nombre">Receptor First Name</label>
      <input
        id="u-nombre"
        type="text"
        placeholder="e.g. Maria"
        bind:value={formData.nombre}
      />
    </div>

    <div class="field-group">
      <label for="u-apellido">Receptor Last Name</label>
      <input
        id="u-apellido"
        type="text"
        placeholder="e.g. Rojas"
        bind:value={formData.apellido}
      />
    </div>

    <div class="field-group">
      <label for="u-ci">ID Document (CI)</label>
      <input
        id="u-ci"
        type="text"
        placeholder="V-XXXXXXXX"
        bind:value={formData.ci}
      />
    </div>

    <div class="form-actions full-width">
      <button class="btn-cancel" on:click={() => (message = error = "")}
        >Clear</button
      >
      <button
        class="btn-primary"
        on:click={handleAssign}
        disabled={loading || searching}
      >
        {loading ? "Assigning..." : "Process Assignment"}
      </button>
    </div>
  </div>
</div>

<style>
  .assignment-container {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
  }

  .alert {
    padding: var(--space-md);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
  }

  .alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
  }

  .alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-lg);
  }

  .field-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
  }

  .field-group.full-width {
    grid-column: span 2;
  }

  label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(17, 25, 40, 0.5);
  }

  input,
  .custom-select {
    padding: var(--space-md);
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    font-size: 14px;
    font-family: inherit;
    transition: var(--transition);
    background: white;
  }

  input:focus,
  .custom-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(245, 103, 26, 0.1);
  }

  .helper-text {
    font-size: 11px;
    color: var(--color-primary);
    font-style: italic;
    margin-top: 4px;
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--space-md);
    margin-top: var(--space-md);
    grid-column: span 2;
  }

  button {
    padding: var(--space-sm) var(--space-xl);
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
  }

  button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .btn-primary {
    background: var(--color-primary);
    color: white;
    border: none;
  }

  .btn-primary:hover:not(:disabled) {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }

  .btn-cancel {
    background: transparent;
    color: var(--color-dark);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
  }

  .btn-cancel:hover:not(:disabled) {
    background: rgba(0, 0, 0, 0.03);
  }

  @media (max-width: 640px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
    .form-actions {
      grid-column: span 1;
    }
  }
</style>
