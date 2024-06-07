export class InvitationService {
    constructor() {
        this.token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }

    getInvitation(hash) {
        return axios.get(`/api/invitations/${hash}`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch invitation');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching invitation:', error);
                throw error;
            });
    }

    acceptInvitation(hash) {
        return axios.post(`/api/invitations/${hash}/accept`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to accept invitation');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error accepting invitation:', error);
                throw error;
            });
    }

    declineInvitation(hash) {
        return axios.post(`/api/invitations/${hash}/decline`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to decline invitation');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error declining invitation:', error);
                throw error;
            });
    }
}