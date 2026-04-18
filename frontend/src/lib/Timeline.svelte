<script>
  import { onMount, createEventDispatcher } from "svelte";
  import { api } from "./api";
  import { fade, slide, fly } from "svelte/transition";

  export let assetId = null;
  export let assetType = "hardware"; // 'hardware' or 'mobile'
  export let assetName = "Asset";
  export let isOpen = false;

  const dispatch = createEventDispatcher();
  let events = [];
  let loading = true;

  $: if (isOpen && assetId) {
    loadTimeline();
  }

  async function loadTimeline() {
    loading = true;
    try {
      events = await api.getTimeline(assetType, assetId);
    } catch (e) {
      console.error("Failed to load timeline", e);
    } finally {
      loading = false;
    }
  }

  function close() {
    dispatch("close");
  }

  function getIcon(type) {
    switch (type) {
      case "Birth":
        return "👶";
      case "Support Entry":
        return "🛠️";
      case "Support Exit":
        return "✅";
      default:
        return "📍";
    }
  }

  function formatDate(dateStr) {
    if (!dateStr) return "Unknown Date";
    return new Date(dateStr).toLocaleDateString("es-ES", {
      day: "numeric",
      month: "long",
      year: "numeric",
    });
  }

  function handleKeydown(e) {
    if (e.key === "Escape") close();
  }
</script>

{#if isOpen}
  <!-- Backdrop -->
  <div
    class="backdrop"
    transition:fade
    on:click={close}
    on:keydown={handleKeydown}
    role="button"
    tabindex="-1"
    aria-label="Close timeline"
  ></div>

  <!-- Panel -->
  <div class="timeline-panel" transition:fly={{ x: 400, duration: 400 }}>
    <div class="panel-header">
      <div class="header-info">
        <h3>Asset History</h3>
        <p>{assetName} • ID: {assetId}</p>
      </div>
      <button class="btn-close" on:click={close} aria-label="Close panel"
        >&times;</button
      >
    </div>

    <div class="panel-content">
      {#if loading}
        <div class="loading-state">
          <div class="spinner"></div>
          <p>Reconstructing history...</p>
        </div>
      {:else if events.length === 0}
        <div class="empty-state">
          <p>No historical records found for this asset.</p>
        </div>
      {:else}
        <div class="timeline-container">
          <div class="timeline-line"></div>

          {#each events as event, i}
            <div class="timeline-item" in:fly={{ y: 20, delay: i * 50 }}>
              <div class="timeline-node {event.color || 'bg-gray-500'}">
                {#if event.type === "Birth"}
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    ><path d="m12 14 4-4" /><path
                      d="M3.34 19a10 10 0 1 1 17.32 0"
                    /><path d="m9.05 14 6-6" /></svg
                  >
                {:else if event.type === "Support Entry"}
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    ><path
                      d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a2 2 0 0 1-2.79-2.79L14.7 6.3Z"
                    /><path d="m20 10 2 2 2-2" /><path d="m10 22 9-9" /><path
                      d="m15 18 4 4"
                    /><path
                      d="M3.5 15.5c.3-.3.8-.5 1.3-.5s1 .2 1.3.5l4 4c.3.3.5.8.5 1.3s-.2 1-.5 1.3L7 22l-5-5 1.5-1.5Z"
                    /></svg
                  >
                {:else}
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg
                  >
                {/if}
              </div>

              <div class="timeline-card">
                <div class="card-header">
                  <span class="event-type">{event.type}</span>
                  <span class="event-date">{formatDate(event.date)}</span>
                </div>
                <p class="event-details">{event.details}</p>
                {#if event.actor}
                  <div class="event-actor">
                    <span>Responsable:</span>
                    {event.actor}
                  </div>
                {/if}
              </div>
            </div>
          {/each}
        </div>
      {/if}
    </div>

    <div class="panel-footer">
      <p>Digital Birth Certificate • SIOTIC Evolved</p>
    </div>
  </div>
{/if}

<style>
  .backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(17, 25, 40, 0.4);
    backdrop-filter: blur(4px);
    z-index: 100;
  }

  .timeline-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 450px;
    height: 100vh;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
    z-index: 101;
    display: flex;
    flex-direction: column;
    border-left: 1px solid rgba(255, 255, 255, 0.3);
  }

  .panel-header {
    padding: var(--space-xl) var(--space-lg);
    background: linear-gradient(135deg, var(--color-dark) 0%, #1a1a2e 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .header-info h3 {
    margin-bottom: 4px;
    font-size: 20px;
    letter-spacing: -0.01em;
  }

  .header-info p {
    color: rgba(255, 255, 255, 0.6);
    font-size: 13px;
  }

  .btn-close {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-close:hover {
    background: var(--color-error);
    transform: rotate(90deg);
  }

  .panel-content {
    flex-grow: 1;
    overflow-y: auto;
    padding: var(--space-xl) var(--space-lg);
  }

  .timeline-container {
    position: relative;
    padding-left: var(--space-xl);
  }

  .timeline-line {
    position: absolute;
    top: 0;
    left: 7px;
    width: 2px;
    height: 100%;
    background: rgba(0, 0, 0, 0.05);
  }

  .timeline-item {
    position: relative;
    margin-bottom: var(--space-xl);
  }

  .timeline-node {
    position: absolute;
    left: -32px;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    z-index: 1;
    background: white;
    border: 2px solid #f3f4f6;
  }

  .bg-emerald-500 {
    border-color: #10b981;
  }
  .bg-amber-500 {
    border-color: #f59e0b;
  }
  .bg-blue-500 {
    border-color: #3b82f6;
  }

  .timeline-card {
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: var(--radius-lg);
    padding: var(--space-md);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
    transition: var(--transition);
  }

  .timeline-card:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-xs);
  }

  .event-type {
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--color-dark);
  }

  .event-date {
    font-size: 11px;
    font-weight: 600;
    color: rgba(0, 0, 0, 0.4);
  }

  .event-details {
    font-size: 14px;
    color: rgba(0, 0, 0, 0.7);
    line-height: 1.4;
    margin-bottom: var(--space-sm);
  }

  .event-actor {
    font-size: 12px;
    color: rgba(0, 0, 0, 0.5);
    border-top: 1px solid rgba(0, 0, 0, 0.03);
    padding-top: var(--space-xs);
  }

  .event-actor span {
    font-weight: 600;
  }

  .loading-state,
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    opacity: 0.6;
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-top: 4px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: var(--space-md);
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  .panel-footer {
    padding: var(--space-md);
    text-align: center;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    background: #f9fafb;
    font-size: 11px;
    font-weight: 600;
    color: rgba(0, 0, 0, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.1em;
  }
</style>
