<script>
import AppMenuItem from './AppMenuItem.vue';

export default {
  components: {
    AppMenuItem,
  },
  data() {
    return {
      model: [
        {
          items: [
            {label: 'Головна', icon: 'pi pi-fw pi-home', to: '/dashboard', isProject: false},
            {label: 'Канали', icon: 'pi pi-fw pi-megaphone', to: '/channels', isProject: true},
            {label: 'AI Боти', icon: 'pi pi-fw pi-discord', to: '/ai-bots', isProject: true},
            {label: 'Шаблони', icon: 'pi pi-fw pi-print', to: '/templates', isProject: true},
            {label: 'Налаштування', icon: 'pi pi-fw pi-cog', to: '/settings', isProject: true},
          ],
        },
      ],
    };
  },
  methods: {
    shouldDisplayItem(item) {
      console.log(item);
      if (item.separator) {
        return false;
      }

      if (item.isProject) {
        return localStorage.getItem('current_project') !== undefined;
      }
      console.log("final");
      return true;
    },
  },
};
</script>

<template>
  <ul class="layout-menu">
    <template v-for="(item, i) in model" :key="item">
      <app-menu-item v-if="shouldDisplayItem(item)" :item="item" :index="i"></app-menu-item>
      <li v-if="item.separator" class="menu-separator"></li>
    </template>
  </ul>
</template>

<style lang="scss" scoped></style>
