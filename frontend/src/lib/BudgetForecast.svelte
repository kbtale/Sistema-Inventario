<script>
  import { onMount } from "svelte";
  import { api } from "./api";
  import { Bar } from "svelte-chartjs";
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
  } from "chart.js";

  ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
  );

  let budgetData = null;
  let loading = true;

  onMount(async () => {
    try {
      budgetData = await api.getBudgetForecast();
    } catch (e) {
      console.error("Budget fetch failed", e);
    } finally {
      loading = false;
    }
  });

  $: chartData = budgetData
    ? {
        labels: Object.keys(budgetData.quarters),
        datasets: [
          {
            label: "Estimated Replacement Budget ($)",
            data: Object.values(budgetData.quarters),
            backgroundColor: [
              "#ef4444", // Q1 - Red
              "#f5671a", // Q2 - Orange (Primary)
              "#f59e0b", // Q3 - Amber
              "#10b981", // Q4 - Emerald
            ],
            borderRadius: 8,
            borderWidth: 0,
            barThickness: 60,
          },
        ],
      }
    : null;
</script>

<div class="budget-section card" class:loading>
  {#if loading}
    <div class="loading-placeholder">
      <div class="shimmer-block"></div>
    </div>
  {:else if budgetData}
    <div class="budget-header">
      <div class="header-main">
        <h3>Quarterly Replacement Budget</h3>
        <p class="subtitle">
          Estimated financial requirements for the next four fiscal quarters
        </p>
      </div>
      <div class="total-investment">
        <span class="label">Total Projected Investment</span>
        <span class="amount"
          >${budgetData.total_forecast.toLocaleString()}</span
        >
      </div>
    </div>

    <div class="chart-container">
      <Bar
        data={chartData}
        options={{
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (context) => `Budget: $${context.raw.toLocaleString()}`,
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => "$" + value.toLocaleString(),
                font: { size: 11, weight: "600" },
              },
              grid: { color: "rgba(0,0,0,0.05)" },
            },
            x: {
              grid: { display: false },
              ticks: { font: { size: 11, weight: "700" } },
            },
          },
        }}
      />
    </div>
  {/if}
</div>

<style>
  .budget-section {
    padding: var(--space-xl);
    margin-top: var(--space-xl);
    animation: slideIn 0.5s ease-out;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .budget-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--space-xl);
  }

  .header-main h3 {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
  }

  .subtitle {
    font-size: 13px;
    color: rgba(0, 0, 0, 0.5);
    margin: 4px 0 0 0;
  }

  .total-investment {
    background: var(--color-bg-accent);
    padding: var(--space-md) var(--space-lg);
    border-radius: var(--radius-lg);
    text-align: right;
    border: 1px solid rgba(245, 103, 26, 0.1);
  }

  .total-investment .label {
    display: block;
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 800;
    color: rgba(0, 0, 0, 0.4);
    letter-spacing: 0.1em;
    margin-bottom: 4px;
  }

  .total-investment .amount {
    font-size: 22px;
    font-weight: 800;
    color: var(--color-primary);
    font-family: ui-monospace, monospace;
  }

  .chart-container {
    height: 350px;
    width: 100%;
  }

  .loading-placeholder {
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .shimmer-block {
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #f0f0f0 25%, #f8f8f8 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 12px;
  }

  @keyframes shimmer {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }
</style>
