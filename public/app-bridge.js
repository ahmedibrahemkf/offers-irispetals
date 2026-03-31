/* Iris Petals - Laravel bridge
   This file keeps the same IPDB interface used by index.html and admin.html.
   Data source: Laravel API + MySQL (no Supabase).
*/
(function () {
  const CONFIG = window.SERVER_CONFIG || {
    apiBase: "/api"
  };

  function endpoint(path) {
    const base = String(CONFIG.apiBase || "/api").replace(/\/+$/, "");
    const sub = String(path || "").replace(/^\/+/, "");
    return base + "/" + sub;
  }

  async function requestJSON(path, opts) {
    try {
      const res = await fetch(endpoint(path), opts || {});
      const data = await res.json().catch(() => ({}));
      return { ok: res.ok, data: data };
    } catch (_) {
      return { ok: false, data: {} };
    }
  }

  function normalizeRows(rows) {
    if (!Array.isArray(rows)) return [];
    return rows.map((r) => {
      const payload = r && typeof r.payload === "object" ? r.payload : {};
      return {
        ...payload,
        id: payload.id || r.id,
        createdAt: payload.createdAt || r.created_at || new Date().toISOString()
      };
    });
  }

  async function loadSettings(fallback) {
    const { ok, data } = await requestJSON("settings");
    if (!ok || !data || typeof data.payload !== "object") return fallback;
    return data.payload || fallback;
  }

  async function saveSettings(payload) {
    const { ok } = await requestJSON("settings", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ payload: payload || {} })
    });
    return ok;
  }

  async function listOrders() {
    const { ok, data } = await requestJSON("orders");
    if (!ok) return [];
    return normalizeRows(data.data);
  }

  async function upsertOrder(order) {
    if (!order || !order.id) return false;
    const { ok } = await requestJSON("orders", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: order.id,
        created_at: order.createdAt || new Date().toISOString(),
        payload: order
      })
    });
    return ok;
  }

  async function deleteOrder(id) {
    if (!id) return false;
    const { ok } = await requestJSON("orders/" + encodeURIComponent(id), {
      method: "DELETE"
    });
    return ok;
  }

  async function listExpenses() {
    const { ok, data } = await requestJSON("expenses");
    if (!ok) return [];
    return normalizeRows(data.data);
  }

  async function upsertExpense(expense) {
    if (!expense || !expense.id) return false;
    const { ok } = await requestJSON("expenses", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: expense.id,
        created_at: expense.createdAt || new Date().toISOString(),
        payload: expense
      })
    });
    return ok;
  }

  async function deleteExpense(id) {
    if (!id) return false;
    const { ok } = await requestJSON("expenses/" + encodeURIComponent(id), {
      method: "DELETE"
    });
    return ok;
  }

  async function uploadImage(file, folder) {
    if (!file) return null;
    const form = new FormData();
    form.append("file", file);
    form.append("folder", String(folder || "uploads"));

    try {
      const res = await fetch(endpoint("upload"), {
        method: "POST",
        body: form
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) return null;
      return data.publicUrl || null;
    } catch (_) {
      return null;
    }
  }

  window.IPDB = {
    isReady: function () { return true; },
    config: CONFIG,
    loadSettings: loadSettings,
    saveSettings: saveSettings,
    listOrders: listOrders,
    upsertOrder: upsertOrder,
    deleteOrder: deleteOrder,
    listExpenses: listExpenses,
    upsertExpense: upsertExpense,
    deleteExpense: deleteExpense,
    uploadImage: uploadImage
  };
})();
