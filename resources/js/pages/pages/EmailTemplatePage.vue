<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Email Templates</h4>
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
                                <th scope="col">Type</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Body</th>
                                <th scope="col">Added By</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in allData" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{ data.type }}</td>
                                <td>{{ data.subject }}</td>
                                <td>
                                    <div v-html="data.body"></div>
                                </td>
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
            @sendTestMail="sendTestMail($event)"
        />
    </section>
</template>
<script>
import { BulletListLoader } from "vue-content-loader";
import NewData from '../../components/pages/email_template/AddNewEmailTemplate.vue';
import EditData from '../../components/pages/email_template/EditEmailTemplate.vue';
import axios from 'axios';
export default {
    name: "EmailTemplatePage",
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
        this.fetchTemplates();
    },
    mounted() {
    },
    methods: {
        sendTestMail( data ){
            this.btnLoading = true;
            axios.post(this.api_url + "pages/settings/email-templates/send-test-mail", data)
            .then((response) => {
                this.btnLoading = false;
                return swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Mail Sent Successfully',
                });
            }).catch((err) => {
                this.btnLoading = false;
            });
        },
        async fetchTemplates() {
            this.tableLoading = true;
            axios.get(this.api_url + "pages/settings/email-templates")
            .then((response) => {
                this.allData = response.data.response;
            }).catch((err) => this.fetchTemplates());
            this.tableLoading = false;
        },
        async add( data ) {

            this.btnLoading = true;

            axios.post(this.api_url + "pages/settings/email-templates", data)
            .then((response) => {

                this.$emit('saved', true);
                this.fetchTemplates();
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
                await axios.post(this.api_url + "pages/settings/email-templates/update", data);

                this.fetchTemplates(); // Refresh the course list
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
