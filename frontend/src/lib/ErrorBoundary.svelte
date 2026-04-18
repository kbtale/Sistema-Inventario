<script>
  let { children, fallback = null } = $props();
  let hasError = $state(false);
  let errorMessage = $state("");

  // Global error listener for this boundary context
  function handleError(event) {
    console.error("ErrorBoundary caught an error:", event.error || event.message);
    hasError = true;
    errorMessage = event.error?.message || event.message || "An unexpected error occurred in this view.";
    event.preventDefault();
  }
</script>

<svelte:window on:error={handleError} on:unhandledrejection={handleError} />

{#if hasError}
  {#if fallback}
    {@render fallback({ message: errorMessage, reset: () => (hasError = false) })}
  {:else}
    <div class="error-boundary-fallback card">
      <div class="error-icon">⚠️</div>
      <h3>Component Error</h3>
      <p>{errorMessage}</p>
      <button class="btn-reset" on:click={() => (hasError = false)}>
        Try again
      </button>
    </div>
  {/if}
{:else}
  {@render children()}
{/if}

<style>
  .error-boundary-fallback {
    padding: var(--space-xl);
    text-align: center;
    background: rgba(239, 68, 68, 0.05);
    border: 1px solid rgba(239, 68, 68, 0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-md);
    margin: var(--space-xl) 0;
  }

  .error-icon {
    font-size: 32px;
  }

  .error-boundary-fallback h3 {
    margin: 0;
    color: var(--color-error);
    font-size: 18px;
  }

  .error-boundary-fallback p {
    font-size: 13px;
    max-width: 400px;
    margin: 0;
  }

  .btn-reset {
    background: var(--color-error);
    color: white;
    border: none;
    padding: var(--space-sm) var(--space-xl);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-reset:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }
</style>
