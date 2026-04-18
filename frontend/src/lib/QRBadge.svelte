<script>
  import { onMount } from "svelte";
  import QRCode from "qrcode";

  export let type = "hardware"; // hardware or mobile
  export let id = "";
  export let label = "Asset";
  export let size = 150;

  let qrDataUrl = "";
  $: qrValue = `siotic://${type}/${id}`;

  onMount(async () => {
    try {
      qrDataUrl = await QRCode.toDataURL(qrValue, {
        width: size,
        margin: 2,
        color: {
          dark: "#111928",
          light: "#ffffff"
        }
      });
    } catch (err) {
      console.error(err);
    }
  });

  function downloadLabel() {
    const link = document.createElement("a");
    link.download = `siotic_${type}_${id}.png`;
    link.href = qrDataUrl;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>

<div class="qr-badge-container">
  <div class="qr-frame">
    {#if qrDataUrl}
      <img src={qrDataUrl} alt="Asset QR Code" />
    {:else}
      <div class="qr-placeholder" style="width: {size}px; height: {size}px;">
        Generating...
      </div>
    {/if}
  </div>
  
  <div class="qr-info">
    <span class="qr-label">{label}</span>
    <span class="qr-id">ID: {id}</span>
  </div>

  <button class="btn-download" on:click={downloadLabel} disabled={!qrDataUrl}>
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
    Download Label
  </button>
</div>

<style>
  .qr-badge-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md);
    background: #f9fafb;
    border-radius: var(--radius-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
  }

  .qr-frame {
    background: white;
    padding: var(--space-sm);
    border-radius: var(--radius-md);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }

  .qr-frame img {
    display: block;
    max-width: 100%;
  }

  .qr-info {
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .qr-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-dark);
  }

  .qr-id {
    font-size: 11px;
    font-weight: 600;
    color: rgba(17, 25, 40, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .btn-download {
    margin-top: var(--space-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-xs);
    background: var(--color-dark);
    color: white;
    border: none;
    padding: var(--space-sm) var(--space-lg);
    border-radius: var(--radius-md);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
  }

  .btn-download:hover:not(:disabled) {
    background: #000;
    transform: translateY(-1px);
  }

  .btn-download:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .qr-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #9ca3af;
  }
</style>
