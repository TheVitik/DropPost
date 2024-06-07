export class TimezoneService {
    getTimezones() {
        return axios.get('/api/timezones')
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch timezones');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching timezones:', error);
                throw error;
            });
    }
}