/**
 * Youth on the Rock (YTR) - Frontend API Client
 * Centralized API handler communicating with the Python Flask backend.
 */

const API_BASE_URL = window.YTR_API_URL || 'http://127.0.0.1:5000/api';

const YTR_API = {
  baseUrl: API_BASE_URL,

  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const defaultHeaders = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };

    const config = {
      ...options,
      headers: {
        ...defaultHeaders,
        ...options.headers
      },
      credentials: options.credentials || 'include'
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();
      return { ok: response.ok, status: response.status, ...data };
    } catch (err) {
      console.error(`API Request to ${url} failed:`, err);
      return {
        ok: false,
        status: 0,
        statusText: 'Network Error',
        message: 'Could not connect to the Python backend API (http://127.0.0.1:5000). Please ensure it is running.'
      };
    }
  },

  // Auth Endpoints
  auth: {
    login(email, password) {
      return YTR_API.request('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password })
      });
    },
    register(payload) {
      return YTR_API.request('/auth/register', {
        method: 'POST',
        body: JSON.stringify(payload)
      });
    },
    logout() {
      return YTR_API.request('/auth/logout', { method: 'POST' });
    },
    me() {
      return YTR_API.request('/auth/me');
    }
  },

  // Chapters Endpoints
  chapters: {
    getAll() {
      return YTR_API.request('/chapters');
    },
    getById(id) {
      return YTR_API.request(`/chapters/${id}`);
    },
    getWorkspace(id) {
      return YTR_API.request(`/chapters/${id}/workspace`);
    },
    getUnreportedEvents(id) {
      return YTR_API.request(`/chapters/${id}/unreported-events`);
    }
  },

  // Manage Accounts Endpoints
  accounts: {
    getMetrics() {
      return YTR_API.request('/accounts/metrics');
    },
    list(filter = 'all', search = '') {
      const params = new URLSearchParams({ filter, search });
      return YTR_API.request(`/accounts?${params.toString()}`);
    },
    getById(id) {
      return YTR_API.request(`/accounts/${id}`);
    },
    approve(id, role, chapter_id) {
      return YTR_API.request(`/accounts/${id}/approve`, {
        method: 'POST',
        body: JSON.stringify({ role, chapter_id })
      });
    },
    decline(id, review_note) {
      return YTR_API.request(`/accounts/${id}/decline`, {
        method: 'POST',
        body: JSON.stringify({ review_note })
      });
    }
  },

  // Proposals Endpoints
  proposals: {
    getMetrics() {
      return YTR_API.request('/proposals/metrics');
    },
    list(status = 'all', chapter_id = 'all', search = '') {
      const params = new URLSearchParams({ status, chapter_id, search });
      return YTR_API.request(`/proposals?${params.toString()}`);
    },
    getById(id) {
      return YTR_API.request(`/proposals/${id}`);
    },
    create(data) {
      return YTR_API.request('/proposals', {
        method: 'POST',
        body: JSON.stringify(data)
      });
    },
    review(id, status, review_note) {
      return YTR_API.request(`/proposals/${id}/review`, {
        method: 'POST',
        body: JSON.stringify({ status, review_note })
      });
    }
  },

  // Reports Endpoints
  reports: {
    getMetrics() {
      return YTR_API.request('/reports/metrics');
    },
    list(filter = 'all', search = '') {
      const params = new URLSearchParams({ filter, search });
      return YTR_API.request(`/reports?${params.toString()}`);
    },
    getById(id) {
      return YTR_API.request(`/reports/${id}`);
    },
    submit(payload) {
      return YTR_API.request('/reports', {
        method: 'POST',
        body: JSON.stringify(payload)
      });
    }
  },

  // Poster Requests Endpoints
  posterRequests: {
    getMetrics() {
      return YTR_API.request('/poster-requests/metrics');
    },
    list(tab = 'accepted') {
      return YTR_API.request(`/poster-requests?tab=${encodeURIComponent(tab)}`);
    },
    approve(id, review_note = '') {
      return YTR_API.request(`/poster-requests/${id}/approve`, {
        method: 'POST',
        body: JSON.stringify({ review_note })
      });
    },
    reject(id, review_note = '') {
      return YTR_API.request(`/poster-requests/${id}/reject`, {
        method: 'POST',
        body: JSON.stringify({ review_note })
      });
    },
    create(data) {
      return YTR_API.request('/poster-requests', {
        method: 'POST',
        body: JSON.stringify(data)
      });
    }
  },

  // Dashboard Overview Endpoints
  dashboard: {
    getOverview() {
      return YTR_API.request('/dashboard/overview');
    }
  },

  // Notifications & Due Date Alerts
  notifications: {
    getSummary() {
      return YTR_API.request('/notifications/summary');
    }
  }
};

window.YTR_API = YTR_API;
