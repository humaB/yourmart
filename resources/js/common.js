import axios from "axios";
export default {
    data() {
        return {
        }
    },
    methods: {

        async callApi(method, url, data) {
            try {
                return await axios({
                    method: method,
                    url: window.location.origin + process.env.MIX_API_URL + url,
                    data: data,
                });
            } catch (error) {
                if (error.response.status === 422) {
                    let errorMessage = '<ul>';
                    Object.keys(error.response.data.errors).forEach(key => {
                        Object.keys(error.response.data.errors[key]).forEach(inner => {
                            errorMessage += `<li>${error.response.data.errors[key][inner]}</li>`; 
                        });
                    });
                    errorMessage += '</ul>';

                    swal({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                    });
                }
                if (error.response.status === 409) {
                    swal({
                        icon: 'error',
                        title: 'Error',
                        html: error.response.data.message,
                    });
                }
                if(error.response.status == 401)
                {
                    store.dispatch('clearUserData');
                }
                return error.response
            }
        }
    }
}
