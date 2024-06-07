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

    this.getTemplate();
  },
  methods: {
    getTemplate() {
      const templateService = new TemplateService();
      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      templateService.getTemplate(this.project.id, this.$route.params.id)
          .then(template => {
            this.name = template.name;
            this.editorData = template.content_html;
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            loader.hide();
          });
    },
    updateTemplate() {
      const templateService = new TemplateService();

      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      templateService.updateTemplate(this.project.id, {
        id: this.$route.params.id,
        name: this.name,
        content_html: this.editorData
      })
          .then(response => {
            loader.hide();
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Шаблон успішно оновлено',
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
              detail: 'Помилка при оновленні шаблону',
              life: 3000
            });
          });
    },
    deleteTemplate() {
      const templateService = new TemplateService();

      let loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      templateService.deleteTemplate(this.project.id, this.$route.params.id)
          .then(response => {
            loader.hide();
            this.$toast.add({
              severity: 'success',
              summary: 'Успіх',
              detail: 'Шаблон успішно видалено',
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
              detail: 'Помилка при видаленні шаблону',
              life: 3000
            });
          });
    },
  }
}
</script>
<template>
  <div class="card p-fluid">
    <h5>Редагування шаблону</h5>
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

      <div class="flex">
        <Button label="Зберегти" @click="updateTemplate" type="button" class="py-3 px-5 mr-1"></Button>
        <Button label="Видалити" @click="deleteTemplate" severity="danger" type="button" class="py-3 px-5 ml-1"></Button>
      </div>
    </div>
  </div>
</template>

<style>
.ck-content {
  height: 30vh; /* Set height to approximately 10 lines */
}
</style>