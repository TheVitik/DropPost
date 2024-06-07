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
      telegram_message_id: null,
      showPreviewModal: false,
      username: '',
      channels: [],
      selectedChannels: [],
      editor: Editor,
      editorData: '',

      selectedFiles: [],

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
    this.getPost();
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
    getPost() {
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      this.postService.getPost(this.$route.params.id)
          .then(post => {
            console.log(post)
            this.editorData = post.content_html;
            this.selectedChannels = [post.channel];
            this.telegram_message_id = post.telegram_message_id;
            if (this.telegram_message_id) {
              this.channels = [post.channel];
            }
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            this.$router.push({'name': 'channels', params: {id: this.project.id}});
            loader.hide();
          });
    },
    savePost() {
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      this.postService.updatePost({
        id: this.$route.params.id,
        content_html: this.editorData,
      })
          .then(response => {
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Публікація успішно збережена',
              life: 3000
            });
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Наразі неможливо зберегти публікацію, спробуйте пізніше',
              life: 3000
            });
            loader.hide();
          });
    },
    deletePost() {
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      this.postService.deletePost(this.$route.params.id)
          .then(response => {
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Публікація успішно видалена',
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
              detail: 'Наразі неможливо видалити публікацію, спробуйте пізніше',
              life: 3000
            });
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
        channels: this.selectedChannels.map(channel => channel.id)
      })
          .then(response => {
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Публікація успішно запланована',
              life: 3000
            });
            this.$router.push({'name': 'posts'});
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: error.response.data.message ?? 'Наразі неможливо запланувати публікацію, спробуйте пізніше',
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
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Редагування публікації</h5>
    <div class="col-12">
      <div class="card mb-4 p-5">
        <p>Публікація знаходиться у наступному каналі</p>
        <SelectButton v-model="selectedChannels" :options="channels" optionLabel="name" :disabled="telegram_message_id"
                      :multiple="true"/>
      </div>
      <Dropdown v-model="template" :options="templates" optionLabel="name" @update:model-value="chooseTemplate" placeholder="Виберіть шаблон"/>
      <ckeditor :editor="editor" v-model="editorData"></ckeditor>
      <FileUpload v-if="!telegram_message_id" mode="advanced" name="demo[]" v-model="selectedFiles" :multiple="true"
                  :showUploadButton="false" :showCancelButton="false" @error="uploader"
                  @remove-uploaded-file="removeFile" auto :maxFileSize="100000000"/>
      <div class="py-4">

      </div>
      <Toolbar class="mb-4 p-5">
        <template v-slot:start>
          <Button v-if="!telegram_message_id" label="Попередній перегляд" severity="contrast" @click="preview"
                  type="button"
                  class="bg-danger py-3 px-5"></Button>
        </template>

        <template v-slot:end>
          <Button label="Видалити" severity="danger" @click="deletePost" type="button"
                  class="py-3 px-5"></Button>
          <Button v-if="!telegram_message_id" label="Запланувати" @click="planModalShow" type="button"
                  class="py-3 px-5 ml-2"></Button>
          <Button v-if="!telegram_message_id" label="Опублікувати" severity="success" @click="publish" type="button"
                  class="py-3 px-5 ml-2"></Button>
          <Button v-if="telegram_message_id" label="Зберегти" @click="savePost" type="button"
                  class="py-3 px-5 ml-2"></Button>
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
</template>

<style>
.ck-content {
  height: 40vh; /* Set height to approximately 10 lines */
}
</style>