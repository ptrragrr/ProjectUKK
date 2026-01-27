<script setup lang="ts">
import { computed, nextTick, onBeforeMount, onMounted, watch } from "vue";
import { useRoute, RouterView } from "vue-router";

import KTHeader from "@/layouts/default-layout/components/header/Header.vue";
import KTSidebar from "@/layouts/default-layout/components/sidebar/Sidebar.vue";
import KTContent from "@/layouts/default-layout/components/content/Content.vue";
import KTToolbar from "@/layouts/default-layout/components/toolbar/Toolbar.vue";
import KTScrollTop from "@/layouts/default-layout/components/extras/ScrollTop.vue";

import { reinitializeComponents } from "@/core/plugins/keenthemes";
import LayoutService from "@/core/services/LayoutService";

const route = useRoute();

/**
 * 🔥 INI PENENTUNYA
 * kalau login / register → JANGAN PAKAI DEFAULT LAYOUT
 */
const isAuthPage = computed(() =>
  ["/login", "/register"].includes(route.path)
);

onBeforeMount(() => {
  if (!isAuthPage.value) {
    LayoutService.init();
  }
});

onMounted(() => {
  if (!isAuthPage.value) {
    nextTick(() => {
      reinitializeComponents();
    });
  }
});

watch(
  () => route.path,
  () => {
    if (!isAuthPage.value) {
      nextTick(() => {
        reinitializeComponents();
      });
    }
  }
);
</script>

<template>
  <!-- 🔥 AUTH PAGE → TANPA LAYOUT -->
  <RouterView v-if="isAuthPage" />

  <!-- 🔥 DEFAULT LAYOUT -->
  <div
    v-else
    class="d-flex flex-column flex-root app-root"
    id="kt_app_root"
  >
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
      <KTHeader />

      <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
        <KTSidebar />

        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
          <div class="d-flex flex-column flex-column-fluid">
            <KTToolbar />

            <div id="kt_app_content" class="app-content flex-column-fluid">
              <KTContent />
            </div>
          </div>
        </div>
      </div>
    </div>

    <KTScrollTop />
  </div>
</template>
