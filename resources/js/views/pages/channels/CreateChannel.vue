<script>

import {ChannelService} from "../../../services/ChannelService.js";

export default {
  data() {
    return {
      project: null,
      showModal: false,
      username: '',
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }
  },
  methods: {
    createChannel() {
      const channelService = new ChannelService();
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      channelService.createChannel(this.project.id, {
        username: this.username,
      })
          .then(response => {
            this.$toast.add({severity: 'success', summary: 'Успіх', detail: 'Канал успішно підключено', life: 3000});
            loader.hide();
            this.$router.push({'name': 'channels'});
          })
          .catch(error => {
            console.log(error);
            // if error 404 show toast
            if (error.response.status === 404) {
              this.$toast.add({
                severity: 'error',
                summary: 'Помилка',
                detail: 'Канал не знайдено або бот не є адміністратором каналу',
                life: 3000
              });
            } else if (error.response.status === 409) {
              this.$toast.add({
                severity: 'error',
                summary: 'Помилка',
                detail: 'Канал вже підключено до цього проекту',
                life: 3000
              });
            } else {
              this.$toast.add({
                severity: 'error',
                summary: 'Помилка',
                detail: 'Наразі неможливо підключити канал, спробуйте пізніше',
                life: 3000
              });
            }
            loader.hide();
          });
    }
    ,
    addBot() {
      window.open('https://t.me/droppost_xbot', '_blank');
    }
    ,
    getId() {
      window.open('https://t.me/droppost_xbot', '_blank');
    }
    ,
    connect() {
      this.showModal = true;
    }
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Підключення каналу</h5>
    <div class="col-12">
      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <div class="my-2 flex align-items-center">
            <span class="pr-4">
              <svg width="50" height="50">
                <circle cx="25" cy="25" r="20" stroke="black" stroke-width="2" fill="white"/>
                <text x="25" y="33" text-anchor="middle" font-size="25" fill="black">1</text>
              </svg>
            </span>
            <p>Додайте нашого бота @droppost_xbot до учасників вашого каналу та надайте права адміністратора</p>
          </div>
        </template>

        <template v-slot:end>
          <Button label="Додати" @click="addBot" type="button" class="py-3 px-5"></Button>
        </template>
      </Toolbar>

      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <div class="my-2 flex align-items-center">
            <span class="pr-4">
              <svg width="50" height="50">
                <circle cx="25" cy="25" r="20" stroke="black" stroke-width="2" fill="white"/>
                <text x="25" y="33" text-anchor="middle" font-size="25" fill="black">2</text>
              </svg>
            </span>
            <p>Перешліть боту @droppost_xbot повідомлення з вашого каналу, щоб дізнатись його ID. Якщо канал публічний,
              знайдіть його @username</p>
          </div>
        </template>

        <template v-slot:end>
          <Button label="Отримати ID" @click="getId" type="button" class="py-3 px-5"></Button>
        </template>
      </Toolbar>

      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <div class="my-2 flex align-items-center">
            <span class="pr-4">
              <svg width="50" height="50">
                <circle cx="25" cy="25" r="20" stroke="black" stroke-width="2" fill="white"/>
                <text x="25" y="33" text-anchor="middle" font-size="25" fill="black">3</text>
              </svg>
            </span>
            <p>Натисніть кнопку "Підключити", вкажіть отриманий ID або @username, та перевірте успішність підключення
              каналу</p>
          </div>
        </template>

        <template v-slot:end>
          <Button label="Підключити" @click="connect" type="button" class="py-3 px-5"></Button>
        </template>
      </Toolbar>


    </div>
  </div>
  <Dialog header="Підключення каналу" v-model:visible="showModal" :breakpoints="{ '960px': '75vw' }"
          :style="{ width: '30vw' }"
          :modal="true">
    <div class="p-fluid">
      <div class="field col-12">
        <label for="id-username">ID або @username каналу</label>
        <InputText id="id-username" v-model="username" type="text"/>
      </div>
    </div>
    <template #footer>
      <Button label="Зберегти" @click="createChannel" icon="pi pi-check" class="p-button-outlined"/>
    </template>
  </Dialog>
</template>