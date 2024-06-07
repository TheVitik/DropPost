import axios from "axios";

export default async function authorize() {
    const token = localStorage.getItem('authToken');
    if (token !== null) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        return await axios.get('/api/user').then(response => {
            let result = response.data;
            if (result.id) {
                localStorage.setItem('user', JSON.stringify(result));
                return true;
            }
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            return false;
        }).catch(error => {
            console.log(error);
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            return false;
        });
    }
    console.log('false3')
    return false;
}
