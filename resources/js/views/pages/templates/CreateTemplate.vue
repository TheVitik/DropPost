<script>
import CKEditor from '@ckeditor/ckeditor5-vue';
import Editor from "../../../editor/ckeditor.ts";
import TemplateService from "../../../services/TemplateService.js";

export default {
  components: {
    ckeditor: CKEditor.component
  },
  data() {
    return {
      project: null,
      name: '',
      editor: Editor,
      editorData: '',
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }
  },
  methods: {
    createTemplate() {
      const templateService = new TemplateService();

      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      templateService.createTemplate(this.project.id, {
        name: this.name,
        content_html: this.editorData
      })
          .then(response => {
            loader.hide();
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Шаблон успішно створено',
              life: 3000
            });
            this.$router.push({name: 'templates'});
          })
          .catch(error => {
            console.log(error);
            loader.hide();
            this.$toast.add({
              severity: 'error',
              summary: 'Помилка',
              detail: 'Помилка при створенні шаблону',
              life: 3000
            });
          });
    },
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Створення шаблону</h5>
    <div class="col-12 xl:col-5">
      <div class="field">
        <label for="name">Назва</label>
        <InputText id="name" type="text" v-model="name" placeholder="Шаблон з посиланням"/>
      </div>
      <div class="field">
        <label for="timezone">Шаблон</label>
        <span class="font-light"> - використовуй %content% для заповнення контентом</span>
        <ckeditor :editor="editor" v-model="editorData"></ckeditor>
      </div>

      <Button label="Створити" @click="createTemplate" type="button" class="py-3 px-5"></Button>
    </div>
  </div>
</template>

<style>
.ck-content {
  height: 30vh; /* Set height to approximately 10 lines */
}
</style>