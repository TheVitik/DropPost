<script>
import VueMeetingSelector from 'vue-meeting-selector';
import 'vue-meeting-selector/dist/style.css';
import slotsGenerator from "vue-meeting-selector/src/helpers/slotsGenerator.js";
import {AIBotService} from "../../../services/AIBotService.js";

export default {
  components: {
    VueMeetingSelector,
  },
  data() {
    return {
      project: null,

      bot: {
        topic: '',
        keywords: [],
        prompt: '',
        is_generated_photos: false,
        is_real_photos: true,
        post_planning: []
      },

      with_photos: false,

      post_template: null,
      post_templates: [],

      // Calendar
      showCalendarModal: false,
      date: new Date(2099, 1, 2),
      planning: [],
      planningDays: [],
      loading: false,
      options: {
        daysLabel: [
          'Неділя',
          'Понеділок',
          'Вівторок',
          'Середа',
          'Четвер',
          "П'ятниця",
          'Субота'
        ],
        limit: 12,
        spacing: 12
      }
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }

    this.initPlanningCalendar();
  },
  methods: {
    createBot() {
      const botService = new AIBotService();

      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      botService.createBot(this.project.id, this.bot)
          .then(response => {
            this.$toast.add({severity: 'success', summary: 'Успіх', detail: 'AI бот успішно створено', life: 3000});
            loader.hide();
            this.$router.push({ name: 'ai-bots' });
          })
          .catch(error => {
            console.log(error);
            loader.hide();
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Наразі неможливо створити АІ бота, спробуйте пізніше',
              life: 3000
            });
          });
    },
    async initPlanningCalendar() {
      const start = {
        hours: 0,
        minutes: 0,
      };
      const end = {
        hours: 23,
        minutes: 45,
      };

      this.planningDays = slotsGenerator(this.date, 7, start, end, 15);
    },
    openCalendar() {
      this.showCalendarModal = true;
    },
    savePlanning() {
      this.showCalendarModal = false;
      const groupedDates = {};

      this.planning.forEach(date => {
        date = date.date;
        const dayOfWeek = date.getDay();

        if (!groupedDates[dayOfWeek]) {
          groupedDates[dayOfWeek] = [];
        }

        groupedDates[dayOfWeek].push(date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}));
      });

      this.bot.post_planning = Object.entries(groupedDates).map(([day, time]) => ({
        day: parseInt(day),
        time
      }));
    },
    changePhotos() {
      if (!this.with_photos) {
        this.bot.is_real_photos = false;
        this.bot.is_generated_photos = false;
      } else {
        this.bot.is_real_photos = true;
        this.bot.is_generated_photos = false;
      }
    },
    changeRealPhotos() {
      this.bot.is_generated_photos = !this.bot.is_real_photos;
    },
    changeGeneratedPhotos() {
      this.bot.is_real_photos = !this.bot.is_generated_photos;
    }
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Створення AI бота</h5>
    <div class="col-12 xl:col-5">
      <div class="field">
        <label for="topic">Тематика</label>
        <InputText id="topic" type="text" v-model="bot.topic" placeholder="Тема публікацій"/>
      </div>
      <div class="field">
        <label for="keywords">Ключові слова</label>
        <Chips id="keywords" v-model="bot.keywords" placeholder="Введіть ключове слово"/>
      </div>
      <div class="field">
        <label for="topic">Настанова боту</label>
        <InputText id="topic" type="text" v-model="bot.prompt" placeholder="Інструкція зі написання публікацій боту"/>
      </div>
      <div class="field">
        <label for="timezone">Графік публікацій</label>
        <div class="mb-3">
          <p v-for="(day, i) in bot.post_planning"><strong>{{ this.options.daysLabel[day['day']] }}:</strong> {{
              day['time'].join(', ')
            }}
          </p>
        </div>
        <Button label="Змінити графік" @click="openCalendar" severity="success" type="button"
                class="py-3 px-5"></Button>
      </div>
      <div class="flex align-items-center">
        <InputSwitch id="is_draft" v-model="with_photos" @update:model-value="changePhotos"/>
        <label for="is_draft" class="ml-2">Публікації з фото</label>
      </div>
      <div v-if="with_photos" class="mt-3">
        <div class="flex align-items-center">
          <InputSwitch id="is_draft" v-model="bot.is_real_photos" @update:model-value="changeRealPhotos"/>
          <label for="is_draft" class="ml-2">Реальні фото</label>
        </div>
        <div class="flex align-items-center mt-2">
          <InputSwitch id="is_draft" v-model="bot.is_generated_photos" @update:model-value="changeGeneratedPhotos"/>
          <label for="is_draft" class="ml-2">Згенеровані фото</label>
        </div>
      </div>

      <Button label="Створити" @click="createBot" type="button" class="py-3 px-5 mt-3"></Button>
    </div>
  </div>

  <Dialog header="Графік публікацій" v-model:visible="showCalendarModal" :breakpoints="{ '960px': '75vw' }"
          :style="{ width: '800px' }"
          :modal="true">
    <vue-meeting-selector
        class="simple-multi-example__meeting-selector"
        v-model="planning"
        :date="date"
        :loading="loading"
        :meetings-days="planningDays"
        :multi="true"
        :calendarOptions="options"
    >
      <template #header="{ meetings }">
        <h5>{{ this.options.daysLabel[meetings.date.getDay()] }}</h5>
      </template>
      <template #button-previous>
        <span></span>
      </template>
      <template #button-next>
        <span></span>
      </template>
    </vue-meeting-selector>
    <template #footer>
      <Button label="Зберегти" @click="savePlanning" icon="pi pi-check" class="p-button-outlined"/>
    </template>
  </Dialog>
</template>

<style scoped lang="scss">
.simple-multi-example {
  &__meeting-selector {
    max-width: 800px;
  }
}

// since our scss is scoped we need to use ::v-deep
:deep(.loading-div) {
  top: 58px !important;
}
</style>