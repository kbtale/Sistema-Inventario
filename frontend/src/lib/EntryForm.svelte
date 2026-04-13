<div class="form-grid">
  <div class="field-group">
    <label for="asset-type">Asset Type</label>
    <select id="asset-type" class="custom-select">
      <option value="PC">Desktop PC</option>
      <option value="Laptop">Laptop</option>
      <option value="Monitor">Monitor</option>
      <option value="Printer">Printer</option>
      <option value="Other">Other Hardware</option>
    </select>
  </div>

  <div class="field-group">
    <label for="brand">Manufacturer / Brand</label>
    <input id="brand" type="text" placeholder="e.g. Dell" />
  </div>

  <div class="field-group">
    <label for="model">Model Name</label>
    <input id="model" type="text" placeholder="e.g. Precision 3660" />
  </div>

  <div class="field-group">
    <label for="tag">Asset Tag (Bienes)</label>
    <input id="tag" type="text" placeholder="BIT-XXXXX" />
  </div>

  <div class="field-group full-width">
    <label for="assigned">Assigned User or Department</label>
    <input id="assigned" type="text" placeholder="e.g. Finance Department" />
  </div>

  <div class="form-actions full-width">
    <button class="btn-cancel">Discard</button>
    <button class="btn-primary">Register Asset</button>
  </div>
</div>

<style>
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-lg);
  }

  .field-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
  }

  .field-group.full-width {
    grid-column: span 2;
  }

  label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(17, 25, 40, 0.5);
  }

  input, .custom-select {
    padding: var(--space-md);
    border-radius: var(--radius-md);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    font-size: 14px;
    font-family: inherit;
    transition: var(--transition);
    background: white;
  }

  input:focus, .custom-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(245, 103, 26, 0.1);
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--space-md);
    margin-top: var(--space-md);
  }

  button {
    padding: var(--space-sm) var(--space-xl);
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
  }

  .btn-primary {
    background: var(--color-primary);
    color: white;
    border: none;
  }

  .btn-primary:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }

  .btn-cancel {
    background: transparent;
    color: var(--color-dark);
    border: 1.5px solid rgba(0, 0, 0, 0.1);
  }

  .btn-cancel:hover {
    background: rgba(0,0,0,0.03);
  }

  @media (max-width: 640px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
    .field-group.full-width {
      grid-column: span 1;
    }
  }
</style>
