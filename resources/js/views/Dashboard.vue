<script>
import {eventBus} from "../utils/eventBus.js";

export default {
  data() {
    return {
      project: null
    }
  },
  methods: {
    addAccount() {
      this.$router.push({ 'name': 'projects-create' });
    },
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }
  }
}
</script>

<template>
  <div v-if="!project" class="grid">
    <div class="col-12">
      <div class="card text-center">
        <h4>У вас немає проектів</h4>
        <Button label="Створити проект" @click="addAccount" type="button" class="py-3 px-5"></Button>
      </div>
    </div>
  </div>
</template>
