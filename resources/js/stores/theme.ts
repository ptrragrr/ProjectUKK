import { ref } from "vue";
import { defineStore } from "pinia";
import { ThemeModeComponent } from "@/assets/ts/layout";

export const THEME_MODE_LS_KEY = "kt_theme_mode_value";
export const THEME_MENU_MODE_LS_KEY = "kt_theme_mode_menu";

export const useThemeStore = defineStore("theme", () => {
  // Force light mode - ignore localStorage
  const mode = ref<"light" | "dark" | "system">("light");

  function setThemeMode(payload: "light" | "dark" | "system") {
    // Force light mode regardless of payload
    const currentMode = "light";
    
    localStorage.setItem(THEME_MODE_LS_KEY, currentMode);
    localStorage.setItem(THEME_MENU_MODE_LS_KEY, currentMode);
    mode.value = currentMode;

    // Always set to light
    document.documentElement.setAttribute("data-bs-theme", "light");
    ThemeModeComponent.init();
  }

  // Initialize with light mode on store creation
  const initLightMode = () => {
    localStorage.setItem(THEME_MODE_LS_KEY, "light");
    localStorage.setItem(THEME_MENU_MODE_LS_KEY, "light");
    document.documentElement.setAttribute("data-bs-theme", "light");
  };

  // Run initialization
  initLightMode();

  return {
    mode,
    setThemeMode,
  };
});