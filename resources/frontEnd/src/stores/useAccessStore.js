import { defineStore } from "pinia";

const STORAGE_KEY = "sis-work-samples-unlocked";

export const useAccessStore = defineStore("access", {
  state: () => ({
    unlocked: sessionStorage.getItem(STORAGE_KEY) === "1",
  }),
  actions: {
    unlock() {
      this.unlocked = true;
      sessionStorage.setItem(STORAGE_KEY, "1");
    },
  },
});
