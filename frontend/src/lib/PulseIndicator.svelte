<script>
  export let score = 100;
  export let size = "md";

  $: normalizedScore = Math.max(0, Math.min(100, score));

  $: colorClass =
    normalizedScore >= 80
      ? "healthy"
      : normalizedScore >= 50
        ? "warning"
        : "critical";

  $: pulseDuration =
    normalizedScore > 0 ? (normalizedScore / 100) * 2 + 0.5 : 0.4;

  const sizes = {
    sm: 24,
    md: 48,
    lg: 64,
  };

  $: radius = sizes[size] / 2 - 4;
  $: circumference = 2 * Math.PI * radius;
  $: offset = circumference - (normalizedScore / 100) * circumference;
</script>

<div
  class="pulse-container {size} {colorClass}"
  style="--pulse-duration: {pulseDuration}s"
>
  <svg
    width={sizes[size]}
    height={sizes[size]}
    viewBox="0 0 {sizes[size]} {sizes[size]}"
  >
    <!-- Background track -->
    <circle
      class="track"
      cx={sizes[size] / 2}
      cy={sizes[size] / 2}
      r={radius}
    />

    <!-- Animated fill -->
    <circle
      class="fill"
      cx={sizes[size] / 2}
      cy={sizes[size] / 2}
      r={radius}
      stroke-dasharray={circumference}
      stroke-dashoffset={offset}
    />

    <circle
      class="core"
      cx={sizes[size] / 2}
      cy={sizes[size] / 2}
      r={radius * 0.4}
    />
  </svg>

  <div class="pulse-ring"></div>
</div>

<style>
  .pulse-container {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  svg {
    transform: rotate(-90deg);
  }

  circle {
    fill: none;
    stroke-width: 4px;
    stroke-linecap: round;
    transition: var(--transition);
  }

  .track {
    stroke: rgba(255, 255, 255, 0.05);
  }

  .fill {
    transition:
      stroke-dashoffset 1s ease-out,
      stroke 0.3s ease;
  }

  .core {
    stroke: none;
    fill: currentColor;
    opacity: 0.8;
  }

  /* Color Themes */
  .healthy {
    color: #10b981;
  }
  .warning {
    color: #f59e0b;
  }
  .critical {
    color: #ef4444;
  }

  .healthy .fill {
    stroke: #10b981;
  }
  .warning .fill {
    stroke: #f59e0b;
  }
  .critical .fill {
    stroke: #ef4444;
  }

  /* Pulse Animation */
  .pulse-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: currentColor;
    opacity: 0;
    pointer-events: none;
    animation: pulse var(--pulse-duration) infinite ease-out;
  }

  @keyframes pulse {
    0% {
      transform: translate(-50%, -50%) scale(0.6);
      opacity: 0.5;
    }
    100% {
      transform: translate(-50%, -50%) scale(1.6);
      opacity: 0;
    }
  }

  /* Sizes */
  .sm {
    width: 24px;
    height: 24px;
  }
  .md {
    width: 48px;
    height: 48px;
  }
  .lg {
    width: 64px;
    height: 64px;
  }

  .sm circle {
    stroke-width: 2px;
  }
  .lg circle {
    stroke-width: 6px;
  }
</style>
