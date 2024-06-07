<script>

import {ChannelService} from "../../../services/ChannelService.js";
import {AIBotService} from "../../../services/AIBotService.js";

export default {
  data() {
    return {
      project: null,
      channel: {
        ai_bot_id: null,
        id: '',
        name: '',
        description: '',
        telegram_chat_id: '',
        members_count: 0,
        is_bot_active: false,
        is_automessage_active: false,
      },
      showCreateBotModal: false,
      showEditBotModal: false,
      username: '',

      aibots: [],
      selected_bot: null
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }

    this.getChannel(this.$route.params.id);
    this.getBots();
  },
  methods: {
    getChannel(id) {
      const channelService = new ChannelService();
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      channelService.getChannel(this.project.id, id)
          .then(response => {
            this.channel = response;
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            loader.hide();
          });
    },
    deleteChannel() {
      // add confirmation dialog
      this.$confirm.require({
        message: 'Ви впевнені, що хочете видалити канал?',
        header: 'Підтвердження',
        icon: 'pi pi-exclamation-triangle',
        accept: this.deleteChannelConfirm,
      });
    },
    deleteChannelConfirm(){
      const channelService = new ChannelService();
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      channelService.deleteChannel(this.project.id, this.channel.id)
          .then(response => {
            this.$toast.add({severity: 'success', summary: 'Успіх', detail: 'Канал успішно видалено', life: 3000});
            this.$router.push({name: 'channels'});
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Наразі неможливо видалити канал, спробуйте пізніше',
              life: 3000
            });
            loader.hide();
          });
    },
    getBots() {
      const botService = new AIBotService();

      botService.getBots(this.project.id)
          .then(response => {
            this.aibots = [
              {name: 'Не використовувати', value: null}, // Deselect option
              ...response
            ];
          })
          .catch(error => {
            console.log(error);
          });
    },
    connectBot() {
      const channelService = new ChannelService();
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      channelService.setBotToChannel(this.project.id, this.channel.id, this.selected_bot.id)
          .then(response => {
            this.$toast.add({severity: 'success', summary: 'Успіх', detail: 'АІ бота успішно підключено', life: 3000});
            this.showCreateBotModal = false;
            this.showEditBotModal = false;
            this.channel.ai_bot_id = this.selected_bot.id;
            if (this.selected_bot.id === undefined) {
              this.channel.ai_bot_id = null;
            }
            loader.hide();
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
    },
    addBot() {
      if (this.aibots.length > 0) {
        this.showCreateBotModal = true;
      } else {
        this.$router.push({'name': 'ai-bots-create'});
      }
    },
    editBot() {
      this.showEditBotModal = true;
      // select channel bot in dropdown
      this.selected_bot = this.aibots.find(bot => bot.id === this.channel.ai_bot_id);
    },
    createBot() {
      this.$router.push({'name': 'ai-bots-create'});
    },
    activateBot() {
      const channelService = new ChannelService();
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      if (this.channel.is_bot_active) {
        channelService.activateBot(this.project.id, this.channel.id)
            .then(response => {
              this.$toast.add({
                severity: 'success',
                summary: 'Успіх',
                detail: 'АІ бота успішно активовано',
                life: 3000
              });
              loader.hide();
            })
            .catch(error => {
              console.log(error);
              this.channel.is_bot_active = false;
              this.$toast.add({
                severity: 'error',
                summary: 'Помилка',
                detail: 'Наразі неможливо активувати бота, спробуйте пізніше',
                life: 3000
              });
              loader.hide();
            });
      } else {
        channelService.deactivateBot(this.project.id, this.channel.id)
            .then(response => {
              this.$toast.add({
                severity: 'success',
                summary: 'Успіх',
                detail: 'АІ бота успішно деактивовано',
                life: 3000
              });
              loader.hide();
            })
            .catch(error => {
              console.log(error);
              this.channel.is_bot_active = true;
              this.$toast.add({
                severity: 'error',
                summary: 'Помилка',
                detail: 'Наразі неможливо деактивувати бота, спробуйте пізніше',
                life: 3000
              });
              loader.hide();
            });
      }
    },
    connect() {
      this.showModal = true;
    },
    showNewPost() {
      this.$router.push({'name': 'create-post', params: {channel_id: this.channel.id}});
    },
    showPosts() {
      this.$router.push({'name': 'posts', params: {channel_id: this.channel.id}});
    }
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Налаштування каналу</h5>
    <div class="col-12">
      <div class="card">
        <div class="flex p-5">
          <div class="col-3 xl:col-2">
            <img src="" width="150px" height="150px">
          </div>
          <div class="col-9">
            <h2>{{ channel.name }}</h2>
            <p>{{ channel.description }}</p>
            <p>ID: {{ channel.telegram_chat_id }}</p>
          </div>
          <div class="col-2">
            <span class="pr-3"><i class="pi pi-users"></i> {{ channel.members_count }}</span>
            <Tag :value="channel.type" :severity="'success'" class="mt-2"></Tag>
          </div>
        </div>
        <div class="p flex justify-content-between">
          <div class="col-6 md:col-4">
            <ConfirmDialog></ConfirmDialog>
            <Button label="Видалити" @click="deleteChannel" type="button" class="py-3 px-5" severity="danger"></Button>
          </div>
          <div class="col-6 md:col-6 flex">
            <Button label="Новий пост" @click="showNewPost" type="button" class="py-3 px-5 mx-2"></Button>
            <Button label="Публікації" @click="showPosts" type="button" class="py-3 px-5"></Button>
          </div>
        </div>
      </div>

      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <div class="my-2 flex align-items-center">
            <span class="pr-4">
              <i class="pi pi-desktop" style="font-size: 2.5rem"></i>
            </span>
            <p>Підключіть АІ бот, який буде вести канал за вас безкоштовно!</p>
          </div>
        </template>

        <template v-slot:end>
          <Button v-if="channel.ai_bot_id === null" label="Підключити" @click="addBot" type="button"
                  class="py-3 px-5"></Button>
          <Button v-else label="Налаштувати" @click="editBot" type="button" class="py-3 px-5"></Button>
        </template>
      </Toolbar>

      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <div class="my-2 flex align-items-center">
            <span class="pr-4">
              <i class="pi pi-comment" style="font-size: 2.5rem"></i>
            </span>
            <p>Налаштуйте автоматичне надсилання повідомлення під кожним постом</p>
          </div>
        </template>
        <template v-slot:end>
          <Button label="Налаштувати" @click="connect" type="button" class="py-3 px-5"></Button>
        </template>
      </Toolbar>
    </div>
  </div>

  <Dialog header="Підключення АІ Бота" v-model:visible="showCreateBotModal" :breakpoints="{ '960px': '75vw' }"
          :style="{ width: '30vw' }"
          :modal="true">
    <div class="p-fluid">
      <div class="field col-12">
        <label for="bot-dropdown">Виберіть АІ Бота</label>
        <Dropdown id="bot-dropdown" v-model="selected_bot" :options="aibots" optionLabel="name" placeholder="Select"/>
      </div>
      <p class="text-center">АБО</p>
    </div>
    <div class="field flex justify-content-center mt-3">
      <Button label="Створити" @click="createBot" type="button" class="py-3 px-5"></Button>
    </div>
    <template #footer>
      <Button label="Зберегти" @click="connectBot" icon="pi pi-check" class="p-button-outlined"/>
    </template>
  </Dialog>

  <Dialog header="Налаштування АІ Бота" v-model:visible="showEditBotModal" :breakpoints="{ '960px': '75vw' }"
          :style="{ width: '30vw' }"
          :modal="true">
    <div class="p-fluid">
      <div class="flex align-items-center col-12">
        <InputSwitch id="is_bot_active" v-model="channel.is_bot_active" @update:model-value="activateBot"/>
        <label for="is_bot_active" class="ml-2">Активувати</label>
      </div>
      <div class="field col-12">
        <label for="bot-dropdown">Виберіть АІ Бота</label>
        <Dropdown id="bot-dropdown" v-model="selected_bot" :options="aibots" @update:model-value="connectBot"
                  optionLabel="name" placeholder="Select"/>
      </div>
      <p class="text-center">АБО</p>
    </div>
    <div class="field flex justify-content-center mt-3">
      <Button label="Створити" @click="createBot" type="button" class="py-3 px-5"></Button>
    </div>
    <template #footer>
    </template>
  </Dialog>
</template>