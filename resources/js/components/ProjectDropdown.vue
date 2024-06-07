<script>
import {eventBus} from "../utils/eventBus.js";
import {ProjectService} from "../services/ProjectService.js";

export default {
  data() {
    return {
      currentProject: null,
      projects: [],
      isOpen: false,
      selectedOption: null,
      defaultTitle: "Вибрати проект",
      defaultDescription: "",
    };
  },
  mounted() {
    const currentProject = localStorage.getItem('current_project');
    if (currentProject) {
      this.currentProject = JSON.parse(currentProject);
    }
    this.updateProjects();

    eventBus.on('project-added', e => {
      this.updateProjectsNew(e.project)
    });
  },
  methods: {
    updateProjects() {
      const projectService = new ProjectService();
      projectService.getProjects()
          .then(projects => {
            this.projects = projects;
            if (projects.length > 0) {
              // if current project is set and exists in list
              if (this.currentProject) {

                const projectExists = projects.find(p => p.id === this.currentProject.id);
                if (!projectExists) {
                  localStorage.removeItem('current_project');
                  this.selectedOption = this.currentProject = projects[0];
                  localStorage.setItem('current_project', JSON.stringify(projects[0]));
                } else {
                  this.selectedOption = this.currentProject;
                }
              } else {
                this.selectedOption = this.currentProject = projects[0];
                localStorage.setItem('current_project', JSON.stringify(projects[0]));
              }
            } else {
              this.selectedOption = this.currentProject = null;
              localStorage.removeItem('current_project');
            }
          })
          .catch(error => {

          });
    },
    updateProjectsNew(newAccount) {
      const projectService = new ProjectService();
      projectService.getProjects()
          .then(projects => {
            this.projects = projects;
            projects.forEach(project => {
              console.log(project);
              if (project.number === newAccount.number) {
                this.selectedOption = this.currentProject = project;
                return;
              }
            });
          })
          .catch(error => {

          });
    },

    toggleDropdown() {
      this.isOpen = !this.isOpen;
    },
    selectOption(project) {
      if (this.selectedOption === project) {
        return;
      }
      this.selectedOption = project;
      localStorage.setItem('current_project', JSON.stringify(project));
      this.currentProject = project;
      this.isOpen = false;

      eventBus.emit('switch-project', {project: project})
    },
    addProject() {
      this.$router.push('/projects/create');
    }
  },
};
</script>

<template>
  <div class="project-dropdown" @click="toggleDropdown">
    <div class="text-blue-700 text-center">{{ selectedOption ? selectedOption.name : defaultTitle }}</div>
    <div class="dropdown-description text-center">
      {{ selectedOption ? selectedOption.role : defaultDescription }}
    </div>
    <ul v-if="isOpen" class="dropdown-menu text-center">
      <li @click="addProject">
        <span class="text-blue-700 text-center add-project flex align-items-center justify-content-center">
          <i class="pi pi-plus-circle mx-1" style="font-size: 1.5rem"></i>
          Створити проект
        </span>
      </li>
      <li v-for="project in projects" @click="selectOption(project)"
          :class="project.id === currentProject.id ? 'bg-gray-100' : ''">
        <div class="text-blue-700 text-center">{{ project.name }}</div>
        <div class="dropdown-description text-center">{{ project.role }}</div>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.project-dropdown {
  position: relative;
  cursor: pointer;
  width: 100%;
}

.dropdown-header {
  color: var(--blue-700)
}

.dropdown-description {
  font-size: 12px;
  color: #888;
  padding: 5px 10px;
  white-space: nowrap;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background-color: #fff;
  border: 1px solid #ccc;
  list-style: none;
  padding: 0;
  margin: 0;
  z-index: 1111;
}

.dropdown-menu li {
  padding: 10px;
  cursor: pointer;
}

.dropdown-menu li:hover {
  background-color: #f0f0f0;
}

.add-project {
  font-size: 12pt;
}
</style>
