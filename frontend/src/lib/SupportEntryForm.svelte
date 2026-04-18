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

  let selectedFile = null;
  let photoPreview = null;

  function handleFileChange(e) {
    const file = e.target.files[0];
    if (file) {
      selectedFile = file;
      const reader = new FileReader();
      reader.onload = (e) => (photoPreview = e.target.result);
      reader.readAsDataURL(file);
    }
  }

  onMount(async () => {
    searching = true;
    try {
      const [hardware, telefonos, entradas, staff] = await Promise.all([
        api.getHardware(),
        api.getTelefonos(),
        api.getEntradas(),
        api.getUsuarios(),
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
      technicians = staff;
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

      // 1. Upload photo if present
      let foto_url = null;
      if (selectedFile) {
        const uploadRes = await api.uploadPhoto(selectedFile);
        if (uploadRes.success) {
          foto_url = uploadRes.url;
        }
      }

      // 2. Submit entry with foto_url
      const response = await api.supportEntry({ ...formData, foto_url });
      if (response.success) {
        message = "Asset checked into support successfully!";
        formData = {
          asset_id: "",
          asset_type: "hardware",
          numero_orden: "",
          nom_responsable: "OTIC Support",
          id_encargado: "",
        };
        selectedFile = null;
        photoPreview = null;
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

    <div class="field-group">
      <label for="e-resp">Responsible Party</label>
      <input
        id="e-resp"
        type="text"
        placeholder="Department or Person in charge"
        bind:value={formData.nom_responsable}
      />
    </div>

    <div class="field-group full-width">
      <label>Visual Auditing Proof</label>
      <div class="photo-capture-box">
        {#if photoPreview}
          <div class="preview-container">
            <img src={photoPreview} alt="Audit preview" />
            <button class="btn-remove" on:click={() => { selectedFile = null; photoPreview = null; }}>&times;</button>
          </div>
        {:else}
          <label class="camera-trigger">
            <input type="file" accept="image/*" capture="environment" on:change={handleFileChange} />
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <span>Capture Condition Photo</span>
          </label>
        {/if}
      </div>
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

  .photo-capture-box {
    border: 2px dashed rgba(0, 0, 0, 0.1);
    border-radius: var(--radius-lg);
    padding: var(--space-md);
    background: #f9fafb;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
  }

  .photo-capture-box:hover {
    border-color: var(--color-primary);
    background: rgba(245, 103, 26, 0.02);
  }

  .camera-trigger {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-xs);
    color: rgba(0, 0, 0, 0.4);
    font-weight: 700;
    text-transform: none;
    letter-spacing: normal;
  }

  .camera-trigger input {
    display: none;
  }

  .preview-container {
    position: relative;
    width: 100%;
    max-width: 300px;
  }

  .preview-container img {
    width: 100%;
    border-radius: var(--radius-md);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    display: block;
  }

  .btn-remove {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 24px;
    height: 24px;
    padding: 0;
    border-radius: 50%;
    background: var(--color-error);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
