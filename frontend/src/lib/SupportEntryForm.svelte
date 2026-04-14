<script>
  import { onMount } from "svelte";
  import { api } from "./api";

  let availableAssets = [];
  let technicians = [];
  let loading = false;
  let searching = true;
  let message = "";
  let error = "";

  let formData = {
    asset_id: "",
    asset_type: "hardware",
    numero_orden: "",
    nom_responsable: "OTIC Support",
    id_encargado: "",
  };

  onMount(async () => {
    searching = true;
    try {
      const [hardware, telefonos, entradas] = await Promise.all([
        api.getHardware(),
        api.getTelefonos(),
        api.getEntradas(),
      ]);

      const hAvailable = hardware
        .filter((item) => item.estado_actual_unidad == 1)
        .map((item) => ({
          id: item.id_hardware,
          type: "hardware",
          label: `[HW] ${item.marca_hardware} ${item.modelo_hardware} (${item.bienes_hardware || "No Tag"})`,
        }));

      const tAvailable = telefonos
        .filter((item) => item.estado_actual_unidad == 1)
        .map((item) => ({
          id: item.id_telefono,
          type: "telefonos",
          label: `[MOB] ${item.marca_telefono} ${item.modelo_telefono} - ${item.nro_telefono || "No Number"}`,
        }));

      availableAssets = [...hAvailable, ...tAvailable];

      // Use the technicians from the entradas history as a quick list or fetch all Usuarios
      // For now, let's assume all users in the Usuarios table can be technicians
      // In a real app we'd have a specific technicains list
      technicians = [
        { id: 1, name: "Admin OTIC" },
        { id: 2, name: "Soporte Técnico 1" },
        { id: 3, name: "Coordinador PCP" },
      ];
    } finally {
      searching = false;
    }
  });

  async function handleEntry() {
    if (
      !formData.asset_id ||
      !formData.numero_orden ||
      !formData.id_encargado
    ) {
      error = "Please fill in all required fields.";
      return;
    }

    loading = true;
    message = "";
    error = "";

    try {
      const selected = availableAssets.find((a) => a.id == formData.asset_id);
      if (selected) formData.asset_type = selected.type;

      const response = await api.supportEntry(formData);
      if (response.success) {
        message = "Asset checked into support successfully!";
        formData = {
          asset_id: "",
          asset_type: "hardware",
          numero_orden: "",
          nom_responsable: "OTIC Support",
          id_encargado: "",
        };
      }
    } catch (e) {
      error = e.message || "Failed to initiate support entry.";
    } finally {
      loading = false;
    }
  }
</script>

<div class="entry-container">
  {#if message}
    <div class="alert alert-success">{message}</div>
  {:else if error}
    <div class="alert alert-error">{error}</div>
  {/if}

  <div class="form-grid">
    <div class="field-group full-width">
      <label for="e-asset">Select Asset to Support</label>
      <select
        id="e-asset"
        class="custom-select"
        bind:value={formData.asset_id}
        disabled={searching}
      >
        <option value="">-- Select Available Unit --</option>
        {#each availableAssets as asset}
          <option value={asset.id}>{asset.label}</option>
        {/each}
      </select>
    </div>

    <div class="field-group">
      <label for="e-orden">Work Order Number</label>
      <input
        id="e-orden"
        type="text"
        placeholder="e.g. ORD-2024-001"
        bind:value={formData.numero_orden}
      />
    </div>

    <div class="field-group">
      <label for="e-tech">Assigned Technician</label>
      <select
        id="e-tech"
        class="custom-select"
        bind:value={formData.id_encargado}
      >
        <option value="">-- Choose Staff --</option>
        {#each technicians as tech}
          <option value={tech.id}>{tech.name}</option>
        {/each}
      </select>
    </div>

    <div class="field-group full-width">
      <label for="e-resp">Responsibility Bearer</label>
      <input
        id="e-resp"
        type="text"
        placeholder="Department or Person in charge"
        bind:value={formData.nom_responsable}
      />
    </div>

    <div class="form-actions full-width">
      <button class="btn-cancel" on:click={() => (message = error = "")}
        >Clear</button
      >
      <button
        class="btn-primary"
        on:click={handleEntry}
        disabled={loading || searching}
      >
        {loading ? "Registering..." : "Start Maintenance"}
      </button>
    </div>
  </div>
</div>

<style>
  .entry-container {
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
