const BASE_URL = 'http://localhost:8080/api';

/**
 * Common fetch wrapper for SIOTIC API
 * @param {string} endpoint - The API path (e.g. '/dashboard')
 * @param {object} options - Fetch options (method, body, headers)
 */
async function apiFetch(endpoint, options = {}) {
  const url = `${BASE_URL}${endpoint}`;
  
  const defaultHeaders = {
    'Content-Type': 'application/json',
  };

  try {
    const response = await fetch(url, {
      ...options,
      headers: { ...defaultHeaders, ...options.headers }
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.details || error.message || 'API Request failed');
    }

    return await response.json();
  } catch (error) {
    console.error(`SIOTIC API Error [${endpoint}]:`, error.message);
    throw error;
  }
}

export const api = {
  // GET methods
  getDashboard: () => apiFetch('/dashboard'),
  getHardware: () => apiFetch('/hardware'),
  getTelefonos: () => apiFetch('/telefonos'),
  getEntradas: () => apiFetch('/entradas'),
  getSalidas: () => apiFetch('/salidas'),
  getActiveSupport: () => apiFetch('/support/active'),

  // POST/PUT methods
  registerHardware: (data) => apiFetch('/hardware', {
    method: 'POST',
    body: JSON.stringify(data)
  }),
  registerMobile: (data) => apiFetch('/telefonos', {
    method: 'POST',
    body: JSON.stringify(data)
  }),
  supportEntry: (data) => apiFetch('/entradas', {
    method: 'POST',
    body: JSON.stringify(data)
  }),
  supportExit: (data) => apiFetch('/salidas', {
    method: 'POST',
    body: JSON.stringify(data)
  }),
  updateStatus: (id, data) => apiFetch(`/estatus/${id}`, {
    method: 'PUT',
    body: JSON.stringify(data)
  }),
  assignUser: (data) => apiFetch('/usuarios', {
    method: 'POST',
    body: JSON.stringify(data)
  })
};
