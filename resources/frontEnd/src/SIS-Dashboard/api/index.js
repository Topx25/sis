import { mockApi } from "./mockApi.js";
import { adminApi } from "./adminApi.js";

// Single switch: mock (dummy data, no network) ↔ real backend.
export const USE_MOCK = false;

export const isMock = USE_MOCK;

const source = USE_MOCK ? mockApi : adminApi;

export const adminAPI = source;

export default adminAPI;