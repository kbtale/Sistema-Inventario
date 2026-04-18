<script>
  import { onMount, onDestroy, createEventDispatcher } from "svelte";
  import { Html5Qrcode } from "html5-qrcode";

  const dispatch = createEventDispatcher();
  let scanner;
  let cameraId;
  let isScanning = false;
  let error = "";

  onMount(async () => {
    try {
      const devices = await Html5Qrcode.getCameras();
      if (devices && devices.length > 0) {
        // Prefer the environment-facing (back) camera
        const backCamera = devices.find(d => d.label.toLowerCase().includes("back"));
        cameraId = backCamera ? backCamera.id : devices[0].id;
        startScanner();
      } else {
        error = "No camera hardware detected on this device.";
      }
    } catch (err) {
      error = "Camera access was denied or is unavailable.";
      console.error(err);
    }
  });

  async function startScanner() {
    scanner = new Html5Qrcode("reader");
    isScanning = true;
    
    // Config: 10 FPS, 250px scanning box
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    
    try {
      await scanner.start(
        cameraId, 
        config,
        (decodedText) => {
          handleScan(decodedText);
        },
        () => { /* Ignore verbose decoding updates */ }
      );
    } catch (err) {
      error = "Failed to initialize camera stream.";
      isScanning = false;
    }
  }

  function handleScan(text) {
    // Protocol identifier: siotic://{type}/{id}
    if (text.startsWith("siotic://")) {
      const parts = text.replace("siotic://", "").split("/");
      if (parts.length === 2) {
        const [type, id] = parts;
        stopScanner().then(() => {
          dispatch("scan", { type, id });
        });
      }
    }
  }

  async function stopScanner() {
    if (scanner && scanner.isScanning) {
      await scanner.stop();
      isScanning = false;
    }
  }

  onDestroy(async () => {
    await stopScanner();
  });
</script>

<div class="scanner-overlay">
  <div class="scanner-container card">
    <div class="scanner-header">
      <div class="title-group">
        <h3>Asset Audit Scanner</h3>
        <p>Point at the SIOTIC QR code</p>
      </div>
      <button class="btn-close" on:click={() => dispatch("close")}>&times;</button>
    </div>

    {#if error}
      <div class="error-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p>{error}</p>
        <button class="btn-primary" on:click={() => dispatch("close")}>Back to Dashboard</button>
      </div>
    {:else}
      <div id="reader"></div>
      <div class="scanner-hint">
        Position the QR label within the scanning frame for automatic detection.
      </div>
    {/if}
  </div>
</div>

<style>
  .scanner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
  }

  .scanner-container {
    width: 400px;
    max-width: 90vw;
    background: white;
    padding: var(--space-xl);
    border-radius: var(--radius-xl);
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.4);
    animation: zoomIn 0.3s ease-out;
  }

  @keyframes zoomIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
  }

  .scanner-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--space-xl);
  }

  .title-group h3 {
    font-size: 20px;
    font-weight: 800;
    margin: 0;
    color: var(--color-dark);
  }

  .title-group p {
    font-size: 13px;
    opacity: 0.6;
    margin: 4px 0 0 0;
  }

  .btn-close {
    background: transparent;
    border: none;
    font-size: 28px;
    color: var(--color-dark);
    cursor: pointer;
    opacity: 0.4;
    transition: var(--transition);
  }

  .btn-close:hover {
    opacity: 1;
    transform: rotate(90deg);
  }

  #reader {
    width: 100%;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: #000;
  }

  .scanner-hint {
    margin-top: var(--space-xl);
    font-size: 12px;
    text-align: center;
    color: rgba(0,0,0,0.5);
    font-weight: 500;
  }

  .error-state {
    text-align: center;
    padding: var(--space-xl) 0;
  }

  .error-state svg {
    color: #ef4444;
    margin-bottom: var(--space-md);
  }

  .error-state p {
    font-size: 14px;
    font-weight: 600;
    color: #ef4444;
    margin-bottom: var(--space-xl);
  }

  .btn-primary {
    background: var(--color-primary);
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
  }
</style>
