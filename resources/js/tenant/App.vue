<template>

  <component :is="layout">
    <router-view />
  </component>


</template>
<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute()
const layout = computed((): string => {
  const template = (route.meta.layout || 'auth') + '-layout';
  return template
})
</script>
<style>
/* Si Stitch te dio clases como 'glass-panel', asegúrate de que tengan prioridad */
.glass-panel {
  z-index: 1;
}
</style>