const BASE_URL = 'http://localhost:8080/api';

/**
 * Common fetch wrapper for SIOTIC API
 * @param {string} endpoint - The API path (e.g. '/dashboard')
 * @param {object} options - Fetch options (method, body, headers)
 */
async function apiFetch(endpoint, options = {}) {
  const url = `${BASE_URL}${endpoint}`;
  const token = localStorage.getItem('siotic_token');
  
  const defaultHeaders = {};
  
  if (!(options.body instanceof FormData)) {
    defaultHeaders['Content-Type'] = 'application/json';
  }

  if (token) {
    defaultHeaders['Authorization'] = `Bearer ${token}`;
  }

  try {
    const response = await fetch(url, {
      ...options,
      headers: { ...defaultHeaders, ...options.headers }
    });

    if (response.status === 401 && endpoint !== '/auth/login') {
      api.logout();
      window.location.reload();
      throw new Error('Session expired');
    }

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
  // Auth
  login: async (username, password) => {
    const res = await apiFetch('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ username, password })
    });
    if (res.token) {
      localStorage.setItem('siotic_token', res.token);
      localStorage.setItem('siotic_user', JSON.stringify(res.user));
    }
    return res;
  },

  logout: () => {
    localStorage.removeItem('siotic_token');
    localStorage.removeItem('siotic_user');
  },

  isAuthenticated: () => !!localStorage.getItem('siotic_token'),
  getUser: () => JSON.parse(localStorage.getItem('siotic_user') || 'null'),

  // GET methods
  getDashboard: () => apiFetch('/dashboard'),
  getHardware: () => apiFetch('/hardware'),
  getTelefonos: () => apiFetch('/telefonos'),
  getEntradas: () => apiFetch('/entradas'),
  getSalidas: () => apiFetch('/salidas'),
  getActiveSupport: () => apiFetch('/support/active'),
  getTimeline: (type, id) => apiFetch(`/timeline?type=${type}&id=${id}`),
  getSedeDistribution: () => apiFetch('/sedes/distribution'),
  getHealthAnalytics: () => apiFetch('/analytics/health'),
  getBudgetForecast: () => apiFetch('/analytics/budget'),
  getAlerts: () => apiFetch('/alerts'),
  getUsuarios: () => apiFetch('/usuarios'),

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
  }),

  uploadPhoto: async (file) => {
    const formData = new FormData();
    formData.append('file', file);
    return apiFetch('/upload', {
      method: 'POST',
      body: formData
    });
  }
};
