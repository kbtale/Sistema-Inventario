<script>
  import { onMount } from "svelte";
  import { api } from "./api";

  let supportAssets = [];
  let loading = false;
  let searching = true;
  let message = "";
  let error = "";

  let formData = {
    id_entrada: "",
    reporte: "",
    receptor: "",
    nom_responsable: "OTIC Technical Staff",
  };

  onMount(async () => {
    searching = true;
    try {
      const active = await api.getActiveSupport();
      supportAssets = active.map(item => ({
        id_entrada: item.id_entrada,
        label: `[${item.type.toUpperCase()}] ${item.marca} ${item.modelo} (${item.tag || 'No Tag'})`
      }));
    } finally {
      searching = false;
    }
  });

  async function handleExit() {
    if (!formData.id_entrada || !formData.reporte || !formData.receptor) {
      error = "Please fill in all required fields.";
      return;
    }

    loading = true;
    message = "";
    error = "";

    try {
      const response = await api.supportExit(formData);
      if (response.success) {
        message = "Asset released and marked as Available.";
        formData = { id_entrada: "", reporte: "", receptor: "", nom_responsable: "OTIC Technical Staff" };
        // Refresh the list
        supportAssets = supportAssets.filter(a => a.id_entrada != formData.id_entrada);
      }
    } catch (e) {
      error = e.message || "Failed to process support exit.";
    } finally {
      loading = false;
    }
  }
</script>

<div class="exit-container">
  {#if message}
    <div class="alert alert-success">{message}</div>
  {:else if error}
    <div class="alert alert-error">{error}</div>
  {/if}

  <div class="form-grid">
    <div class="field-group full-width">
      <label for="x-asset">Select Asset in Maintenance</label>
      <select id="x-asset" class="custom-select" bind:value={formData.id_entrada} disabled={searching}>
        <option value="">-- Assets awaiting release --</option>
        {#each supportAssets as asset}
          <option value={asset.id_entrada}>{asset.label} (Entry #{asset.id_entrada})</option>
        {/each}
      </select>
    </div>

    <div class="field-group full-width">
      <label for="x-report">Technical Report / Resolution</label>
      <textarea id="x-report" rows="4" placeholder="Describe the repairs performed..." bind:value={formData.reporte}></textarea>
    </div>

    <div class="field-group">
      <label for="x-receptor">Unit Receptor (User)</label>
      <input id="x-receptor" type="text" placeholder="Who is taking the unit?" bind:value={formData.receptor} />
    </div>

    <div class="field-group">
      <label for="x-resp">Responsible Technician</label>
      <input id="x-resp" type="text" placeholder="Authorized Staff" bind:value={formData.nom_responsable} />
    </div>

    <div class="form-actions full-width">
      <button class="btn-cancel" on:click={() => (message = error = "")}>Clear</button>
      <button class="btn-primary" on:click={handleExit} disabled={loading || searching}>
        {loading ? "Releasing..." : "Finalize & Release"}
      </button>
    </div>
  </div>
</div>

<style>
  .exit-container {
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

  input, .custom-select, textarea {
    padding: var(--space-md);
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    font-size: 14px;
    font-family: inherit;
    transition: var(--transition);
    background: white;
  }

  input:focus, .custom-select:focus, textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(245, 103, 26, 0.1);
  }

  textarea {
    resize: vertical;
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
  }
</style>
