<script>
  import { api } from "./api";

  let formData = {
    tipo: "Smartphone",
    marca: "",
    modelo: "",
    nro: "",
    imei: "",
    imeisim: "",
    puk: "",
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
      const response = await api.registerMobile(formData);
      if (response.success) {
        message = "Mobile device registered successfully!";
        formData = {
          tipo: "Smartphone",
          marca: "",
          modelo: "",
          nro: "",
          imei: "",
          imeisim: "",
          puk: "",
          usuario: "",
        };
      }
    } catch (e) {
      error = e.message || "Failed to register mobile device.";
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
      <label for="m-type">Device Category</label>
      <select id="m-type" class="custom-select" bind:value={formData.tipo}>
        <option value="Smartphone">Smartphone</option>
        <option value="Tablet">Tablet</option>
        <option value="SIM Card">SIM Card Only</option>
        <option value="Router 4G">4G/LTE Router</option>
      </select>
    </div>

    <div class="field-group">
      <label for="m-brand">Brand</label>
      <input id="m-brand" type="text" placeholder="e.g. Samsung" bind:value={formData.marca} />
    </div>

    <div class="field-group">
      <label for="m-model">Model</label>
      <input id="m-model" type="text" placeholder="e.g. Galaxy A54" bind:value={formData.modelo} />
    </div>

    <div class="field-group">
      <label for="m-nro">Phone Number (MSISDN)</label>
      <input id="m-nro" type="text" placeholder="+58 4XX..." bind:value={formData.nro} />
    </div>

    <div class="field-group">
      <label for="m-imei">IMEI / Serial</label>
      <input id="m-imei" type="text" placeholder="15-digit code" bind:value={formData.imei} />
    </div>

    <div class="field-group">
      <label for="m-imeisim">SIM Card ID (ICCID)</label>
      <input id="m-imeisim" type="text" placeholder="20-digit code" bind:value={formData.imeisim} />
    </div>

    <div class="field-group">
      <label for="m-puk">PUK Code</label>
      <input id="m-puk" type="text" placeholder="8-digit safety code" bind:value={formData.puk} />
    </div>

    <div class="field-group">
      <label for="m-assigned">Assigned User</label>
      <input id="m-assigned" type="text" placeholder="e.g. Juan Perez" bind:value={formData.usuario} />
    </div>

    <div class="form-actions full-width">
      <button class="btn-cancel" on:click={() => (message = error = "")}>Clear</button>
      <button class="btn-primary" on:click={handleRegister} disabled={loading}>
        {loading ? "Registering..." : "Register Mobile"}
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

  label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(17, 25, 40, 0.5);
  }

  input, .custom-select {
    padding: var(--space-md);
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    font-size: 14px;
    font-family: inherit;
    transition: var(--transition);
    background: white;
  }

  input:focus, .custom-select:focus {
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
