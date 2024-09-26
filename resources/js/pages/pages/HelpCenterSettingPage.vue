<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Help Center Page</h4>
                <a href="#" class="mr-1 btn btn-primary" data-toggle="modal" data-target="#newData">Add New</a>
            </div>
            <div class="card-body">
                <!-- <div class="table-responsive" v-if="tableLoading">
                    <bullet-list-loader :width="250"> </bullet-list-loader>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="course_table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Type</th>
                                <th scope="col">Added By</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in allData" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{ data.name }}</td>
                                <td>
                                    <div v-html="data.description"></div>
                                </td>
                                <td>{{ data.type }}</td>
                                <td>
                                    {{ data.added_name.name }}
                                </td>
                                <td>
                                    <a
                                        href="#"
                                        class="btn  btn-primary"
                                        @click="editPageData(data)"
                                        ><i class="far fa-edit"></i
                                    ></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- add modal -->
        <NewData
            :btnLoading="btnLoading"
            @add="add( $event )"
        />
        <!-- update modal -->
        <EditData
            :btnLoading="btnLoading"
            :editData="editData"
            @update="update($event)"
        />
    </section>
</template>
<script>
import { BulletListLoader } from "vue-content-loader";
import NewData from '../../components/pages/help_center/AddNewHelpCenter.vue';
import EditData from '../../components/pages/help_center/EditHelpCenter.vue';
import axios from 'axios';
export default {
    name: "HelpCenterSettingPage",
    components: {
        NewData,
        EditData,
    },
    data() {
        return {
            api_url : window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            btnLoading: false,
            tableLoading: false,
            allData: [],
            editData: { name: '', description: '', type: "0" },
        };
    },
    created() {
        this.helpCenter();
    },
    mounted() {
    },
    methods: {
        async helpCenter() {
            this.tableLoading = true;
            axios.get(this.api_url + "pages/settings/help-center-page")
            .then((response) => {
                this.allData = response.data.response;
            }).catch((err) => this.fetchTags());
            this.tableLoading = false;
        },
        async add( data ) {

            this.btnLoading = true;

            axios.post(this.api_url + "pages/settings/help-center-page/add", data)
            .then((response) => {

                this.$emit('saved', true);
                this.helpCenter();
                this.btnLoading = false;
                return swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Successfully Added',
                });
            }).catch((err) => {
                this.btnLoading = false;
            });

        },
        async editPageData(course) {
            this.editData = course;
            $("#editData").modal('show');
        },
        async update( data ) {

            this.btnLoading = true;

            try {
                await axios.post(this.api_url + "pages/settings/help-center-page/update", data);

                this.helpCenter(); // Refresh the course list
                this.btnLoading = false;
                return swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Successfully Updated',
                });
            } catch (error) {
                this.btnLoading = false;
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update the course',
                });
            }
        }

    }
};
</script>
