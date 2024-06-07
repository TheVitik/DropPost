export class ChannelService {
    constructor() {
        this.token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }

    async getChannels(projectId) {
        return axios.get(`/api/projects/${projectId}/channels`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch channels');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching projects:', error);
                throw error;
            });
    }

    async getChannelsList(projectId) {
        return axios.get(`/api/projects/${projectId}/channels-list`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch channels');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching projects:', error);
                throw error;
            });
    }

    async getChannel(projectId, channelId) {
        return axios.get(`/api/projects/${projectId}/channels/${channelId}`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch channel');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching channel:', error);
                throw error;
            });
    }

    async createChannel(projectId, channel) {
        return axios.post(`/api/projects/${projectId}/channels`, channel)
            .then(response => {
                if (response.status !== 201) {
                    throw new Error('Failed to create channel');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error creating channel:', error);
                throw error;
            });
    }

    async deleteChannel(projectId, channelId) {
        return axios.delete(`/api/projects/${projectId}/channels/${channelId}`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to delete channel');
                }
            })
            .catch(error => {
                console.error('Error deleting channel:', error);
                throw error;
            });
    }

    // set bot to channel
    async setBotToChannel(projectId, channelId, botId) {
        return axios.post(`/api/projects/${projectId}/channels/${channelId}/set-bot`, { ai_bot_id: botId })
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to set bot to channel');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error setting bot to channel:', error);
                throw error;
            });
    }

    // activate bot
    async activateBot(projectId, channelId) {
        return axios.post(`/api/projects/${projectId}/channels/${channelId}/activate-bot`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to activate bot');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error activating bot:', error);
                throw error;
            });
    }

    // deactivate bot
    async deactivateBot(projectId, channelId) {
        return axios.post(`/api/projects/${projectId}/channels/${channelId}/deactivate-bot`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to deactivate bot');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error deactivating bot:', error);
                throw error;
            });
    }
}