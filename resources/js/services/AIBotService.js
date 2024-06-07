export class AIBotService {
    constructor() {
        this.token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }

    async getBots(projectId) {
        return axios.get(`/api/projects/${projectId}/ai-bots`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch AI bots');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching AI bots:', error);
                throw error;
            });
    }

    async getBot(projectId, botId) {
        return axios.get(`/api/projects/${projectId}/ai-bots/${botId}`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch AI bot');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching AI bot:', error);
                throw error;
            });
    }

    async createBot(projectId, bot) {
        return axios.post(`/api/projects/${projectId}/ai-bots`, bot)
            .then(response => {
                if (response.status !== 201) {
                    throw new Error('Failed to create AI bot');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error creating AI bot:', error);
                throw error;
            });
    }

    async updateBot(projectId, bot) {
        return axios.put(`/api/projects/${projectId}/ai-bots/${bot.id}`, bot)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to update AI bot');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error updating AI bot:', error);
                throw error;
            });
    }

    async deleteBot(projectId, botId) {
        return axios.delete(`/api/projects/${projectId}/ai-bots/${botId}`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to delete AI bot');
                }
            })
            .catch(error => {
                console.error('Error deleting AI bot:', error);
                throw error;
            });
    }
}