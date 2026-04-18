<script>
  let {
    width = "100%",
    height = "1rem",
    borderRadius = "var(--radius-sm)",
    margin = "0",
  } = $props();
</script>

<div
  class="skeleton-loading"
  style:width
  style:height
  style:border-radius={borderRadius}
  style:margin
></div>

<style>
  .skeleton-loading {
    background: linear-gradient(
      90deg,
      rgba(0, 0, 0, 0.05) 25%,
      rgba(0, 0, 0, 0.08) 50%,
      rgba(0, 0, 0, 0.05) 75%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite linear;
    position: relative;
    overflow: hidden;
  }

  @keyframes shimmer {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }

  .skeleton-loading::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(4px);
  }
</style>
