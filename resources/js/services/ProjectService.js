export class ProjectService {
    constructor() {
        this.token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }

    getProjects() {
        return axios.get('/api/projects')
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch projects');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching projects:', error);
                throw error;
            });
    }

    getProject(id) {
        return axios.get(`/api/projects/${id}`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch project');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching project:', error);
                throw error;
            });
    }

    createProject(project) {
        return axios.post('/api/projects', project)
            .then(response => {
                if (response.status !== 201) {
                    throw new Error('Failed to create project');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error creating project:', error);
                throw error;
            });
    }

    updateProject(project) {
        return axios.put(`/api/projects/${project.id}`, project)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to update project');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error updating project:', error);
                throw error;
            });
    }

    deleteProject(id) {
        return axios.delete(`/api/projects/${id}`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to delete project');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error deleting project:', error);
                throw error;
            });
    }
}
