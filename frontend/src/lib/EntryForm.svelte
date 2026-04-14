<script>
  import { api } from "./api";

  let formData = {
    tipo: "PC",
    marca: "",
    modelo: "",
    bienes: "",
    usuario: "",
  };

  let loading = false;
  let message = "";
  let error = "";

  async function handleRegister() {
    loading = true;
    message = "";
    error = "";

    try {
      const response = await api.registerHardware(formData);
      if (response.success) {
        message = "Asset registered successfully!";
        formData = {
          tipo: "PC",
          marca: "",
          modelo: "",
          bienes: "",
          usuario: "",
        };
      }
    } catch (e) {
      error = e.message || "Failed to register asset.";
    } finally {
      loading = false;
    }
  }
</script>

<div class="form-container">
  {#if message}
    <div class="alert alert-success">{message}</div>
  {:else if error}
    <div class="alert alert-error">{error}</div>
  {/if}

  <div class="form-grid">
    <div class="field-group">
      <label for="asset-type">Asset Type</label>
      <select id="asset-type" class="custom-select" bind:value={formData.tipo}>
        <option value="PC">Desktop PC</option>
        <option value="Laptop">Laptop</option>
        <option value="Monitor">Monitor</option>
        <option value="Printer">Printer</option>
        <option value="Other">Other Hardware</option>
      </select>
    </div>

    <div class="field-group">
      <label for="brand">Manufacturer / Brand</label>
      <input
        id="brand"
        type="text"
        placeholder="e.g. Dell"
        bind:value={formData.marca}
      />
    </div>

    <div class="field-group">
      <label for="model">Model Name</label>
      <input
        id="model"
        type="text"
        placeholder="e.g. Precision 3660"
        bind:value={formData.modelo}
      />
    </div>

    <div class="field-group">
      <label for="tag">Asset Tag (Bienes)</label>
      <input
        id="tag"
        type="text"
        placeholder="BIT-XXXXX"
        bind:value={formData.bienes}
      />
    </div>

    <div class="field-group full-width">
      <label for="assigned">Assigned User or Department</label>
      <input
        id="assigned"
        type="text"
        placeholder="e.g. Finance Department"
        bind:value={formData.usuario}
      />
    </div>

    <div class="form-actions full-width">
      <button class="btn-cancel" on:click={() => (message = error = "")}
        >Clear</button
      >
      <button class="btn-primary" on:click={handleRegister} disabled={loading}>
        {loading ? "Registering..." : "Register Asset"}
      </button>
    </div>
  </div>
</div>

<style>
  .form-container {
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
    .field-group.full-width {
      grid-column: span 1;
    }
  }
</style>
