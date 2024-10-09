<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="mx-3">
                    <div class="card-header">
                        <h4>Update Profile</h4>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Full Name -->
                            <div class="form-group mb-3">
                                <label for="fullName">Name</label>
                                <input type="text" id="fullName" v-model="profileData.name" class="form-control" placeholder="Enter your full name" required />
                            </div>

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" id="email" v-model="profileData.email" class="form-control" placeholder="Enter your email" required />
                            </div>
                            
                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label for="new_password">Password</label>
                                <input type="password" name="fakePassword" style="display:none;" autocomplete="new-password"/> <!-- this is for auto complete no functionality on it-->
                                <input type="password" autocomplete="off" id="new_password" v-model="profileData.new_password" class="form-control" placeholder="Enter your password" />
                            </div>

                            <!-- Submit Button -->
                            <button type="button" @click="updateProfile()" class="btn btn-primary" :class="btnLoading ? 'btn-progress' : ''" :disabled="btnLoading">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            profileData: {},
            btnLoading: false,
        };
    },
    methods: {
        async fetchProfileData() {
            try {
                const response = await axios.get(this.api_url + 'get-profile');
                this.profileData = response.data.user;
            } catch (error) {
                console.error(error);
            }
        },
        // Update profile method
        async updateProfile() {
            
            this.btnLoading = true;
            const formData = new FormData();
            formData.append('id', this.profileData.id);
            formData.append('name', this.profileData.name);
            formData.append('email', this.profileData.email);
            formData.append('new_password', this.profileData.new_password);

            if (this.profileData.profileImage) {
                formData.append('profileImage', this.profileData.profileImage);
            }

            try {
                const response = await axios.post(this.api_url + 'update-profile', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data', // Assuming token is stored in Vuex
                    }
                });
                this.fetchProfileData();
                return swal({
                    title: "Success",
                    text: "Updated successfully",
                    icon: "success",
                    timer: 3000,
                });
            } catch (error) {
                return swal({
                    title: "Error",
                    text: error.response.data.response[0],
                    icon: "error",
                    timer: 3000,
                });
            } finally {
                this.btnLoading = false;
            }
        }
    },
    mounted() {
        // Optionally, fetch current user profile to pre-fill the form fields
        this.fetchProfileData();
    },
};
</script>
