import { seedData } from "./seed.js";

const clone = (v) => (typeof structuredClone === "function" ? structuredClone(v) : JSON.parse(JSON.stringify(v)));

export const db = clone(seedData);

export const nextId = (rows) => rows.reduce((max, r) => Math.max(max, Number(r.id) || 0), 0) + 1;

export const nowIso = () => new Date().toISOString();

export const findRow = (rows, id) => rows.find((r) => Number(r.id) === Number(id));

export const insertRow = (rows, row) => {
  rows.push(row);
  return row;
};

export const updateRow = (rows, id, patch) => {
  const row = findRow(rows, id);
  if (!row) return null;
  Object.assign(row, patch, { id: row.id });
  return row;
};

export const removeRow = (rows, id) => {
  const idx = rows.findIndex((r) => Number(r.id) === Number(id));
  if (idx === -1) return false;
  rows.splice(idx, 1);
  return true;
};