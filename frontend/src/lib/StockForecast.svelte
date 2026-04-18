<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import { fade, slide } from "svelte/transition";

  let forecastData = [];
  let loading = true;

  onMount(async () => {
    try {
      forecastData = await api.getStockForecast();
    } catch (e) {
      console.error("Forecast fetch failed", e);
    } finally {
      loading = false;
    }
  });

  function getStatusColor(status) {
    switch (status) {
      case "critical": return "#ef4444";
      case "warning": return "#f59e0b";
      case "healthy": return "#10b981";
      default: return "#6b7280";
    }
  }

  function getStatusLabel(status) {
    switch (status) {
      case "critical": return "Immediate Restock";
      case "warning": return "Restock Required";
      case "healthy": return "Stock Healthy";
      default: return "Monitoring";
    }
  }
</script>

<div class="forecast-container card" in:fade>
  <div class="forecast-header">
    <div class="header-titles">
      <h3>Stock Exhaustion Trajectory</h3>
      <p>Predicted stockout dates based on historical attrition velocity.</p>
    </div>
    <div class="analysis-window">
      <span>180-Day Analysis Period</span>
    </div>
  </div>

  {#if loading}
    <div class="loading-state">
      <div class="spinner"></div>
      <p>Analyzing movement velocity...</p>
    </div>
  {:else if forecastData.length === 0}
    <div class="empty-state">
      <p>Not enough movement data to generate accurate projections.</p>
    </div>
  {:else}
    <div class="forecast-grid">
      {#each forecastData as item}
        <div class="forecast-card {item.status}">
          <div class="card-title">
            <span class="category-name">{item.category}</span>
            <span class="status-badge" style="background: {getStatusColor(item.status)}15; color: {getStatusColor(item.status)}">
              {getStatusLabel(item.status)}
            </span>
          </div>

          <div class="metric-row">
            <div class="metric">
              <span class="label">Available</span>
              <span class="value">{item.available} units</span>
            </div>
            <div class="metric">
              <span class="label">Monthly Velocity</span>
              <span class="value">{item.velocity} units/mo</span>
            </div>
          </div>

          <div class="exhaustion-meta">
            <div class="days-pill" style="border-left: 4px solid {getStatusColor(item.status)}">
              <span class="big-days">{item.days_left}</span>
              <span class="days-label">Days to Zero</span>
            </div>
          </div>

          <div class="progress-track">
            <div class="progress-bar" style="width: {Math.min(100, (item.days_left / 90) * 100)}%; background: {getStatusColor(item.status)}"></div>
          </div>
        </div>
      {/each}
    </div>

    <div class="procurement-draft">
      <div class="draft-header">
        <h4>Procurement Auto-Draft</h4>
        <p>Recommended restock quantities to maintain a 45-day safety buffer.</p>
      </div>
      
      <table class="draft-table">
        <thead>
          <tr>
            <th>Category</th>
            <th>Current Gap</th>
            <th>Suggested Restock</th>
            <th>Estimated Priority</th>
          </tr>
        </thead>
        <tbody>
          {#each forecastData.filter(i => i.status !== 'healthy') as item}
            <tr transition:slide>
              <td><strong>{item.category}</strong></td>
              <td>-{Math.round(item.velocity * 1.5)} units (45d)</td>
              <td><span class="quantity-badge">{Math.round(item.velocity * 2)} units</span></td>
              <td><span class="prio prio-{item.status}">{item.status.toUpperCase()}</span></td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
  {/if}
</div>

<style>
  .forecast-container {
    padding: var(--space-xl);
    margin-top: var(--space-xl);
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .forecast-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--space-xl);
  }

  .header-titles h3 {
    font-size: 20px;
    font-weight: 800;
    color: var(--color-dark);
    margin-bottom: 4px;
  }

  .header-titles p {
    font-size: 13px;
    color: rgba(0, 0, 0, 0.5);
  }

  .analysis-window {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 6px 12px;
    background: #f3f4f6;
    border-radius: 20px;
    color: rgba(0, 0, 0, 0.4);
  }

  .forecast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-lg);
    margin-bottom: var(--space-xl);
  }

  .forecast-card {
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    transition: var(--transition);
  }

  .forecast-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  }

  .card-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-lg);
  }

  .category-name {
    font-weight: 800;
    font-size: 16px;
    color: var(--color-dark);
    text-transform: capitalize;
  }

  .status-badge {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 4px;
    letter-spacing: 0.05em;
  }

  .metric-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
  }

  .metric {
    display: flex;
    flex-direction: column;
  }

  .metric .label {
    font-size: 11px;
    font-weight: 600;
    color: rgba(0, 0, 0, 0.4);
    text-transform: uppercase;
  }

  .metric .value {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-dark);
  }

  .exhaustion-meta {
    margin-bottom: var(--space-md);
  }

  .days-pill {
    background: #f9fafb;
    padding: var(--space-md);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    gap: var(--space-md);
  }

  .big-days {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    font-family: ui-monospace, monospace;
    color: var(--color-dark);
  }

  .days-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: rgba(0, 0, 0, 0.4);
    letter-spacing: 0.05em;
  }

  .progress-track {
    height: 4px;
    background: #f3f4f6;
    border-radius: 2px;
    overflow: hidden;
  }

  .progress-bar {
    height: 100%;
    transition: width 1s ease-out;
  }

  .procurement-draft {
    background: var(--color-bg-accent);
    padding: var(--space-xl);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(245, 103, 26, 0.1);
  }

  .draft-header {
    margin-bottom: var(--space-lg);
  }

  .draft-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
  }

  .draft-header p {
    font-size: 12px;
    color: rgba(0, 0, 0, 0.5);
    margin: 4px 0 0 0;
  }

  .draft-table {
    width: 100%;
    border-collapse: collapse;
  }

  .draft-table th {
    text-align: left;
    font-size: 10px;
    text-transform: uppercase;
    color: rgba(0, 0, 0, 0.4);
    padding: var(--space-sm);
    border-bottom: 2px solid rgba(0, 0, 0, 0.05);
  }

  .draft-table td {
    padding: var(--space-md) var(--space-sm);
    font-size: 13px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
  }

  .quantity-badge {
    background: var(--color-primary);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 700;
    font-size: 12px;
  }

  .prio {
    font-size: 10px;
    font-weight: 800;
  }
  .prio-critical { color: #ef4444; }
  .prio-warning { color: #f59e0b; }

  .loading-state, .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 50px;
    opacity: 0.6;
  }

  .spinner {
    width: 30px;
    height: 30px;
    border: 3px solid rgba(0,0,0,0.1);
    border-top: 3px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: var(--space-md);
  }

  @keyframes spin { to { transform: rotate(360deg); } }
</style>
