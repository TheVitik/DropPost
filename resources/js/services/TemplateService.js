export default class TemplateService {
    constructor() {
        this.token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }

    async getTemplates(projectId) {
        return axios.get(`/api/projects/${projectId}/post-templates`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch templates');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching templates:', error);
                throw error;
            });
    }

    async getTemplate(projectId, templateId) {
        return axios.get(`/api/projects/${projectId}/post-templates/${templateId}`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch template');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching template:', error);
                throw error;
            });
    }

    async createTemplate(projectId, template) {
        return axios.post(`/api/projects/${projectId}/post-templates`, template)
            .then(response => {
                if (response.status !== 201) {
                    throw new Error('Failed to create template');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error creating template:', error);
                throw error;
            });
    }

    async updateTemplate(projectId, template) {
        return axios.put(`/api/projects/${projectId}/post-templates/${template.id}`, template)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to update template');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error updating template:', error);
                throw error;
            });
    }

    async deleteTemplate(projectId, templateId) {
        return axios.delete(`/api/projects/${projectId}/post-templates/${templateId}`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to delete template');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error deleting template:', error);
                throw error;
            });
    }
}