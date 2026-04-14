<script>
  import { createEventDispatcher } from "svelte";
  const dispatch = createEventDispatcher();

  export let view = "dashboard";

  const navigate = (v) => dispatch("navigate", v);
</script>

<nav class="sidebar-nav">
  <div class="nav-group">
    <button
      type="button"
      class="nav-item"
      class:active={view === "dashboard"}
      on:click={() => navigate("dashboard")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><rect x="3" y="3" width="18" height="18" rx="2" ry="2" /><line
          x1="3"
          y1="9"
          x2="21"
          y2="9"
        /><line x1="9" y1="21" x2="9" y2="9" /></svg
      >
      <span class="nav-label">Dashboard</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "inventory"}
      on:click={() => navigate("inventory")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><rect x="2" y="3" width="20" height="14" rx="2" ry="2" /><line
          x1="8"
          y1="21"
          x2="16"
          y2="21"
        /><line x1="12" y1="17" x2="12" y2="21" /></svg
      >
      <span class="nav-label">Hardware</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "mobile"}
      on:click={() => navigate("mobile")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><rect x="5" y="2" width="14" height="20" rx="2" ry="2" /><line
          x1="12"
          y1="18"
          x2="12.01"
          y2="18"
        /></svg
      >
      <span class="nav-label">Mobile Devices</span>
    </button>

    <div class="nav-group-title">Operations</div>

    <button
      type="button"
      class="nav-item"
      class:active={view === "support_board"}
      on:click={() => navigate("support_board")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><path
          d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"
        /><rect x="8" y="2" width="8" height="4" rx="1" ry="1" /></svg
      >
      <span class="nav-label">Support Board</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "support_entry"}
      on:click={() => navigate("support_entry")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" /><polyline
          points="10 17 15 12 10 7"
        /><line x1="15" y1="12" x2="3" y2="12" /></svg
      >
      <span class="nav-label">Support Entry</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "support_exit"}
      on:click={() => navigate("support_exit")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline
          points="16 17 21 12 16 7"
        /><line x1="21" y1="12" x2="9" y2="12" /></svg
      >
      <span class="nav-label">Support Exit</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "entry_hardware"}
      on:click={() => navigate("entry_hardware")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg
      >
      <span class="nav-label">New Hardware</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "entry_mobile"}
      on:click={() => navigate("entry_mobile")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg
      >
      <span class="nav-label">New Mobile</span>
    </button>
    <button
      type="button"
      class="nav-item"
      class:active={view === "assignments"}
      on:click={() => navigate("assignments")}
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        ><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle
          cx="8.5"
          cy="7"
          r="4"
        /><line x1="20" y1="8" x2="20" y2="14" /><line
          x1="23"
          y1="11"
          x2="17"
          y2="11"
        /></svg
      >
      <span class="nav-label">Assignments</span>
    </button>
  </div>
</nav>

<style>
  .sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
  }

  .nav-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
  }

  .nav-group-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 600;
    color: var(--color-primary);
    opacity: 0.4;
    margin: var(--space-md) 0 var(--space-xs) var(--space-sm);
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    border: 1.5px solid transparent;
    background: transparent;
    color: var(--color-dark);
    text-align: left;
    width: 100%;
  }

  .nav-item svg {
    flex-shrink: 0;
    opacity: 0.6;
    transition: var(--transition);
  }

  .nav-item:hover {
    background: rgba(245, 103, 26, 0.05);
    color: var(--color-primary);
    border-color: rgba(245, 103, 26, 0.1);
  }

  .nav-item:hover svg {
    opacity: 1;
    transform: scale(1.1);
  }

  .nav-item.active {
    background: rgba(245, 103, 26, 0.1);
    color: var(--color-primary);
    border-color: rgba(245, 103, 26, 0.2);
    font-weight: 600;
  }

  .nav-item.active svg {
    opacity: 1;
  }

  @media (max-width: 1024px) {
    .sidebar-nav {
      gap: var(--space-md);
      align-items: center;
    }

    .nav-label,
    .nav-group-title {
      display: none;
    }

    .nav-item {
      justify-content: center;
      padding: var(--space-md);
      width: 54px;
      height: 54px;
    }
  }
</style>
