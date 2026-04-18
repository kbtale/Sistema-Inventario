<script>
  import { onMount, createEventDispatcher } from "svelte";
  import { api } from "./api";
  import { slide, fade } from "svelte/transition";

  const dispatch = createEventDispatcher();
  let alerts = [];
  let isOpen = false;
  let loading = true;

  export async function refresh() {
    try {
      alerts = await api.getAlerts();
    } catch (e) {
      console.error("Alert fetch failed", e);
    } finally {
      loading = false;
    }
  }

  onMount(refresh);

  function handleAlertClick(alert) {
    isOpen = false;
    dispatch("focusAsset", { id: alert.asset_id, type: alert.type });
  }
</script>

<div class="alert-center-wrapper">
  <button
    class="bell-trigger"
    class:has-alerts={alerts.length > 0}
    on:click={() => (isOpen = !isOpen)}
    aria-label="Toggle notifications"
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="20"
      height="20"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" /><path
        d="M10.3 21a1.94 1.94 0 0 0 3.4 0"
      />
    </svg>
    {#if alerts.length > 0}
      <span class="badge" in:fade>{alerts.length}</span>
    {/if}
  </button>

  {#if isOpen}
    <div
      class="alerts-dropdown card"
      in:slide={{ duration: 200 }}
      out:fade={{ duration: 150 }}
    >
      <div class="dropdown-header">
        <h4>Operational Alerts</h4>
        <button class="btn-refresh" on:click={refresh} disabled={loading}>
          {loading ? "..." : "Refresh"}
        </button>
      </div>

      <div class="alerts-list">
        {#each alerts as alert}
          <button
            type="button"
            class="alert-item"
            class:critical={alert.severity === "critical"}
            on:click={() => handleAlertClick(alert)}
          >
            <div class="alert-icon">
              {#if alert.severity === "critical"}
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  ><circle cx="12" cy="12" r="10" /><line
                    x1="12"
                    y1="8"
                    x2="12"
                    y2="12"
                  /><line x1="12" y1="16" x2="12.01" y2="16" /></svg
                >
              {:else}
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  ><path
                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                  /><line x1="12" y1="9" x2="12" y2="13" /><line
                    x1="12"
                    y1="17"
                    x2="12.01"
                    y2="17"
                  /></svg
                >
              {/if}
            </div>
            <div class="alert-content">
              <span class="alert-title">{alert.title}</span>
              <p class="alert-desc">{alert.description}</p>
            </div>
          </button>
        {:else}
          <div class="empty-alerts">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              ><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline
                points="22 4 12 14.01 9 11.01"
              /></svg
            >
            <p>System clean. No active risks.</p>
          </div>
        {/each}
      </div>
    </div>
  {/if}
</div>

<style>
  .alert-center-wrapper {
    position: relative;
    z-index: 1000;
  }

  .bell-trigger {
    background: transparent;
    border: none;
    color: var(--color-dark);
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .bell-trigger:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--color-primary);
  }

  .badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 800;
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid white;
  }

  .alerts-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 320px;
    max-height: 480px;
    overflow-y: auto;
    background: white;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.05);
    padding: 0;
    z-index: 1001;
    border-radius: 16px;
  }

  .dropdown-header {
    padding: var(--space-md) var(--space-lg);
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: white;
    z-index: 2;
  }

  .dropdown-header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: -0.02em;
  }

  .btn-refresh {
    background: transparent;
    border: none;
    font-size: 11px;
    font-weight: 700;
    color: var(--color-primary);
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .alerts-list {
    display: flex;
    flex-direction: column;
  }

  .alert-item {
    padding: var(--space-md) var(--space-lg);
    display: flex;
    gap: var(--space-md);
    cursor: pointer;
    transition: background 0.2s ease;
    border: none;
    border-bottom: 1px solid #f3f4f6;
    background: transparent;
    text-align: left;
    width: 100%;
  }

  .alert-item:last-child {
    border-bottom: none;
  }

  .alert-item:hover {
    background: #f9fafb;
  }

  .alert-icon {
    flex-shrink: 0;
    margin-top: 2px;
  }

  .alert-item.critical .alert-icon {
    color: #ef4444;
  }
  .alert-item:not(.critical) .alert-icon {
    color: #f59e0b;
  }

  .alert-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .alert-title {
    font-size: 12px;
    font-weight: 800;
    color: var(--color-dark);
  }

  .alert-desc {
    font-size: 11px;
    color: #6b7280;
    margin: 0;
    line-height: 1.4;
    font-weight: 500;
  }

  .empty-alerts {
    padding: var(--space-xl);
    text-align: center;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-md);
  }

  .empty-alerts p {
    font-size: 12px;
    margin: 0;
    font-weight: 700;
  }
</style>
