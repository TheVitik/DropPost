<script>
import {TimezoneService} from "../../../services/TimezoneService.js";
import {ProjectService} from "../../../services/ProjectService.js";
import {eventBus} from "../../../utils/eventBus.js";

export default {
  data() {
    return {
      name: '',
      timezone: null,
      timezones: []
    }
  },
  mounted() {
    this.getTimezones();
  },
  methods: {
    createProject() {
      const projectService = new ProjectService();

      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      projectService.createProject({
        name: this.name,
        timezone: this.timezone.name
      })
          .then(response => {
            localStorage.setItem('current_project', JSON.stringify(response));
            this.$toast.add({severity: 'success', summary: 'Успіх', detail: 'Проект успішно створено', life: 3000});
            loader.hide();
            eventBus.emit('project-added', {project: response});
            this.$router.push('/channels');
          })
          .catch(error => {
            console.log(error);
            loader.hide();
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Наразі неможливо створити проект, спробуйте пізніше',
              life: 3000
            });
          });
    },
    getTimezones() {
      const timezoneService = new TimezoneService();
      timezoneService.getTimezones()
          .then(timezones => {
            this.timezones = timezones;
          })
          .catch(error => {
            console.log(error);
          });
    }
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Створення проекту</h5>
    <div class="col-12 xl:col-5">
      <div class="field">
        <label for="name">Назва</label>
        <InputText id="name" type="text" v-model="name" placeholder="Проект"/>
      </div>
      <div class="field">
        <label for="timezone">Часовий пояс</label>
        <Dropdown id="timezone" filter v-model="timezone" :options="timezones" optionLabel="name"
                  placeholder="Виберіть часовий пояс"></Dropdown>
      </div>

      <Button label="Створити" @click="createProject" type="button" class="py-3 px-5"></Button>
    </div>
  </div>
</template>