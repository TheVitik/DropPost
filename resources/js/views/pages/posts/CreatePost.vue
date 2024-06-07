<script>

import {ChannelService} from "../../../services/ChannelService.js";
import CKEditor from '@ckeditor/ckeditor5-vue';
import Editor from "../../../editor/ckeditor.ts";
import {PostService} from "../../../services/PostService.js";
import TemplateService from "../../../services/TemplateService.js";

export default {
  components: {
    ckeditor: CKEditor.component
  },
  data() {
    return {
      channelService: new ChannelService(),
      postService: new PostService(),
      project: null,
      templates: [],
      template: null,
      showPreviewModal: false,
      username: '',
      channels: [],
      selectedChannels: [],
      editor: Editor,
      editorData: '',

      selectedFiles: [],

      is_draft: false,
      is_template: false,
      is_advertisement: false,

      showPlanModal: false,
      is_plan_publish: false,
      plan_publish_date: new Date(),
      is_plan_delete: false,
      plan_delete_date: new Date(),

      datetime_now: new Date(),
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }

    this.getTemplates();
    this.getChannelsList();
  },
  methods: {
    getTemplates() {
      let loader = this.$loading.show({
        container: this.$refs.channelsContainer,
      });

      const templateService = new TemplateService();
      templateService.getTemplates(this.project.id)
          .then(templates => {
            this.templates = templates;
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            loader.hide();
          });
    },
    getChannelsList() {
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      this.channelService.getChannelsList(this.project.id)
          .then(channels => {
            this.channels = channels;
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            loader.hide();
          });
    },
    publish() {
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      // upload with files

      this.postService.createPost({
        content_html: this.editorData,
        is_draft: this.is_draft,
        is_template: this.is_template,
        is_advertisement: this.is_advertisement,
        channels: this.selectedChannels.map(channel => channel.id),
        files: this.selectedFiles
      })
          .then(response => {
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Публікація успішно опублікована',
              life: 3000
            });
            this.$router.push({'name': 'posts', params: {id: this.selectedChannels[0].id}});
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Наразі неможливо опублікувати публікацію, спробуйте пізніше',
              life: 3000
            });
            loader.hide();
          });
    },
    planModalShow() {
      this.showPlanModal = true;
    },
    plan() {
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      this.postService.createPost({
        content_html: this.editorData,
        is_draft: this.is_draft,
        is_template: this.is_template,
        is_advertisement: this.is_advertisement,
        plan_publish_date: this.plan_publish_date,
        plan_delete_date: this.plan_delete_date,
        channels: this.selectedChannels.map(channel => channel.id),
        files: this.selectedFiles
      })
          .then(response => {
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Публікація успішно запланована',
              life: 3000
            });
            this.$router.push({name: 'posts', params: {id: this.selectedChannels[0].id}});
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Наразі неможливо запланувати публікацію, спробуйте пізніше',
              life: 3000
            });
            loader.hide();
          });
    },
    uploader(event) {
      this.selectedFiles = [...this.selectedFiles, ...event.files];
      console.log(this.selectedFiles)
    },
    removeFile(event) {
      this.selectedFiles = this.selectedFiles.filter(file => file !== event.file);
      console.log(this.selectedFiles)
    },
    chooseTemplate() {
      this.editorData = this.template.content_html;
    },
    preview() {
      this.showPreviewModal = true;
    }
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Створення публікації</h5>
    <div class="col-12">
      <div class="card mb-4 p-5">
        <p>Публікацію буде надіслано до вибраних каналів</p>
        <SelectButton v-model="selectedChannels" :options="channels" optionLabel="name" :multiple="true"/>
      </div>
      <Dropdown v-model="template" :options="templates" optionLabel="name" @update:model-value="chooseTemplate"
                placeholder="Виберіть шаблон"/>
      <ckeditor :editor="editor" v-model="editorData"></ckeditor>
      <FileUpload mode="advanced" name="demo[]" v-model="selectedFiles" :multiple="true" :showUploadButton="false"
                  :showCancelButton="false" @error="uploader" @remove-uploaded-file="removeFile" auto
                  :maxFileSize="100000000"/>
      <div class="py-4">
        <div class="flex align-items-center">
          <InputSwitch id="is_draft" v-model="is_draft"/>
          <label for="is_draft" class="ml-2">Зберегти як чернетку</label>
        </div>
        <div class="flex align-items-center mt-2">
          <InputSwitch id="is_draft" v-model="is_template"/>
          <label for="is_draft" class="ml-2">Зберегти як шаблон</label>
        </div>
        <div class="flex align-items-center mt-2">
          <InputSwitch id="is_advertisement" v-model="is_advertisement"/>
          <label for="is_advertisement" class="ml-2">Рекламна публікація</label>
        </div>
      </div>
      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <Button label="Попередній перегляд" severity="contrast" @click="preview" type="button"
                  class="bg-danger py-3 px-5"></Button>
        </template>

        <template v-slot:end>
          <Button label="Запланувати" @click="planModalShow" type="button" class="py-3 px-5 mr-2"></Button>
          <Button label="Опублікувати" severity="success" @click="publish" type="button" class="py-3 px-5"></Button>
        </template>
      </Toolbar>
    </div>
  </div>

  <!-- Plan Modal -->
  <Dialog header="Планування публікації" v-model:visible="showPlanModal" :breakpoints="{ '960px': '75vw' }"
          :style="{ width: '30vw' }"
          :modal="true">
    <div class="p-fluid">
      <div class="field col-12">
        <div class="flex align-items-center">
          <InputSwitch id="is_draft" v-model="is_plan_publish"/>
          <label for="is_draft" class="ml-2">Публікація за графіком</label>
        </div>
        <Calendar v-if="is_plan_publish" show-icon icon-display="input" show-time show-button-bar
                  :min-date="datetime_now" v-model="plan_publish_date"
                  class="mt-3"></Calendar>
      </div>
      <div class="field col-12">
        <div class="flex align-items-center">
          <InputSwitch id="is_draft" v-model="is_plan_delete"/>
          <label for="is_draft" class="ml-2">Видалення за графіком</label>
        </div>
        <Calendar v-if="is_plan_delete" show-icon icon-display="input" show-time show-button-bar
                  :min-date="datetime_now" v-model="plan_delete_date"
                  class="mt-3"></Calendar>
      </div>
    </div>
    <template #footer>
      <Button label="Запланувати" @click="plan" icon="pi pi-check" class="p-button-outlined"/>
    </template>
  </Dialog>

  <!-- Preview Modal -->
  <Dialog header=" " v-model:visible="showPreviewModal" :breakpoints="{ '960px': '75vw' }"
          :style="{ width: '60vw' }"
          :modal="true">
    <div class="p-fluid tgme_background">
      <div class="tgme_background_pattern"></div>
      <div v-html="editorData" class="tgme_widget_message_bubble"></div>
    </div>
  </Dialog>
</template>

<style>
.ck-content {
  height: 40vh; /* Set height to approximately 10 lines */
}

.tgme_background {
  height: 70vh;
  background: linear-gradient(45deg, #dbddbb 25%, #dbddbb 75%, #dbddbb), linear-gradient(45deg, #6ba587 25%, transparent 25%, transparent 75%, #6ba587 75%, #6ba587), linear-gradient(45deg, #d5d88d 25%, transparent 25%, transparent 75%, #d5d88d 75%, #d5d88d), linear-gradient(45deg, #88b884 25%, transparent 25%, transparent 75%, #88b884 75%, #88b884);
}
.tgme_widget_message_bubble {
  border: none;
  filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.1));
}

.tgme_widget_message_bubble {
  position: absolute;
  background-color: #fff;
  border: none;
  filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.1));
  padding: 4px 9px;
  top:100px;
  left: 100px;
  max-width: 430px;
}

.tgme_background_pattern {
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  mix-blend-mode: overlay;
  opacity: 0.3;
  background: url('https://telegram.org/img/tgme/pattern.svg?1') center repeat;
  background-size: 420px auto;
}
</style>