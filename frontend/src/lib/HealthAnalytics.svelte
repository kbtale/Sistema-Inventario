<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import { Doughnut, Line } from "svelte-chartjs";
  import BudgetForecast from "./BudgetForecast.svelte";
  import StockForecast from "./StockForecast.svelte";
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
  } from "chart.js";

  ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
  );

  let analyticsData = null;
  let loading = true;

  onMount(async () => {
    try {
      analyticsData = await api.getHealthAnalytics();
    } catch (e) {
      console.error("Failed to fetch analytics", e);
    } finally {
      loading = false;
    }
  });

  $: doughnutData = analyticsData
    ? {
        labels: ["Healthy", "At Risk", "Critical"],
        datasets: [
          {
            data: [
              analyticsData.distribution.healthy,
              analyticsData.distribution.at_risk,
              analyticsData.distribution.critical,
            ],
            backgroundColor: ["#10b981", "#f59e0b", "#ef4444"],
            hoverOffset: 4,
            borderWidth: 0,
          },
        ],
      }
    : null;

  $: lineData = analyticsData
    ? {
        labels: ["Month 0", "Month 3", "Month 6", "Month 9", "Month 12"],
        datasets: [
          {
            label: "Average Infrastructure Health (%)",
            data: analyticsData.projections,
            borderColor: "#f5671a",
            backgroundColor: "rgba(245, 103, 26, 0.1)",
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: "#f5671a",
          },
        ],
      }
    : null;
</script>

<div class="analytics-view">
  {#if loading}
    <div class="loading-state">
      <div class="spinner"></div>
      <p>Analyzing infrastructure health decay...</p>
    </div>
  {:else if analyticsData}
    <div class="analytics-grid">
      <div class="chart-card card">
        <div class="card-header-simple">
          <h3>Current Risk Distribution</h3>
          <span class="badge-total"
            >{analyticsData.total_analyzed} Assets Analyzed</span
          >
        </div>
        <div class="chart-container">
          <Doughnut
            data={doughnutData}
            options={{
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { position: "bottom" } },
            }}
          />
        </div>
      </div>

      <div class="chart-card card">
        <div class="card-header-simple">
          <h3>12-Month Health Projection</h3>
          <span class="badge-info">Linear Decay Model</span>
        </div>
        <div class="chart-container">
          <Line
            data={lineData}
            options={{
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: { beginAtZero: false, max: 100, min: 0 },
              },
            }}
          />
        </div>
      </div>
    </div>

    <div class="critical-assets card">
      <div class="card-header-simple">
        <h3>Top Critical Assets (Immediate Risk)</h3>
        <p class="subtitle">
          Assets projected to cross fail-threshold within 60 days
        </p>
      </div>
      <table class="critical-table">
        <thead>
          <tr>
            <th>Asset Type</th>
            <th>Asset ID</th>
            <th>Calculated Health</th>
            <th>Projected Lifespan</th>
            <th>Recommendation</th>
          </tr>
        </thead>
        <tbody>
          {#each analyticsData.critical_assets as asset}
            <tr>
              <td><span class="type-tag">{asset.type}</span></td>
              <td class="bold">#{asset.id}</td>
              <td>
                <div class="health-meta">
                  <span class="pulse-value">{asset.pulse_score}%</span>
                  <div class="health-bar-bg">
                    <div
                      class="health-bar-fill"
                      style="width: {asset.pulse_score}%"
                    ></div>
                  </div>
                </div>
              </td>
              <td><span class="warning-text">Critical EOL</span></td>
              <td>
                <button class="btn-action-small">Schedule Replacement</button>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>

    <BudgetForecast />
    <StockForecast />
  {/if}
</div>

<style>
  .analytics-view {
    animation: fadeIn 0.4s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .analytics-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: var(--space-xl);
    margin-bottom: var(--space-xl);
  }

  .chart-card {
    padding: var(--space-xl);
    display: flex;
    flex-direction: column;
  }

  .card-header-simple {
    margin-bottom: var(--space-lg);
  }

  .card-header-simple h3 {
    font-size: 18px;
    margin-bottom: 4px;
  }

  .subtitle {
    font-size: 13px;
    color: rgba(0, 0, 0, 0.5);
  }

  .badge-total,
  .badge-info {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .chart-container {
    height: 300px;
    flex-grow: 1;
    position: relative;
  }

  .critical-assets {
    padding: var(--space-xl);
  }

  .critical-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: var(--space-lg);
  }

  .critical-table th {
    text-align: left;
    padding: var(--space-md);
    font-size: 11px;
    text-transform: uppercase;
    color: rgba(0, 0, 0, 0.4);
    border-bottom: 2px solid #f3f4f6;
  }

  .critical-table td {
    padding: var(--space-lg) var(--space-md);
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    vertical-align: middle;
  }

  .type-tag {
    text-transform: capitalize;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }

  .health-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    width: 150px;
  }

  .pulse-value {
    font-size: 12px;
    font-weight: 700;
    color: #ef4444;
  }

  .health-bar-bg {
    width: 100%;
    height: 6px;
    background: #f3f4f6;
    border-radius: 3px;
    overflow: hidden;
  }

  .health-bar-fill {
    height: 100%;
    background: #ef4444;
  }

  .warning-text {
    color: #ef4444;
    font-weight: 700;
    font-size: 12px;
  }

  .btn-action-small {
    background: var(--color-primary);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
  }

  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 60vh;
    gap: 20px;
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-top: 4px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
</style>
