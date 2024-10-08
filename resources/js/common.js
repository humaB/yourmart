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
                    let errorMessage = '';
                    Object.keys(error.response.data.errors).forEach(key => {
                        Object.keys(error.response.data.errors[key]).forEach(inner => {
                            errorMessage += `${error.response.data.errors[key][inner]}`;
                        });
                    });
                    errorMessage += '';

                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                }
                if (error.response.status === 409) {
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: error.response.data.message,
                    });
                }
                if(error.response.status == 401)
                {
                    // store.dispatch('clearUserData');
                }
                return error.response
            }
        }
    }
}
