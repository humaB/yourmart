<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Public Notifications</h4>
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
                                <th scope="col">Color</th>
                                <th scope="col">Title</th>
                                <th scope="col">Message</th>
                                <th scope="col">Image</th>
                                <th scope="col" width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in allData" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{ data.color }}</td>
                                <td>{{ data.title }}</td>
                                <td>{{ data.message }}</td>

                                <td>
                                    <ul class="list-unstyled order-list m-b-0 m-b-0">
                                        <li class="team-member team-member-sm">
                                            <a :href="getImageUrl(data.image)"
                                                target="_blank">
                                                <img class="rounded-circle"
                                                    :src="getImageUrl(data.image)">
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="">
                                    <a href="#" class="btn  btn-primary" @click="editPageData(data)"><i
                                            class="far fa-edit"></i></a>
                                    <button class="btn btn-danger" @click="deleteNotification(data)"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- add modal -->
        <NewData :btnLoading="btnLoading" @add="add($event)" />
        <!-- update modal -->
        <EditData :btnLoading="btnLoading" :editData="editData" @update="update($event)" />

        <DeletePublicNotification :btnLoading="btnLoading" @deleteConfirmation="deleteConfirmation($event)"/>
    </section>
</template>
<script>
import { BulletListLoader } from "vue-content-loader";
import NewData from '../../components/pages/notification/AddNewPublicNotification.vue';
import EditData from '../../components/pages/notification/EditPublicNotification.vue';
import axios from 'axios';
import DeletePublicNotification from "../../components/pages/notification/DeletePublicNotification.vue";
export default {
    name: "NotificationPage",
    components: {
        NewData,
        EditData,
        DeletePublicNotification
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            btnLoading: false,
            tableLoading: false,
            allData: [],
            editData: { name: '', description: '', type: "0" },
        };
    },
    created() {
        this.fetchNotifications();
    },
    methods: {
        deleteConfirmation(){
            this.btnLoading = true;

            axios.post(this.api_url + "pages/settings/notifications/delete", { id : this.editData.id })
                .then((response) => {

                    this.fetchNotifications();
                    this.btnLoading = false;
                    $("#deleteNotification").modal('hide');
                    return swal({
                        icon: 'success',
                        title: 'Success',
                        text: 'Successfully Deleted',
                    });
                }).catch((err) => {
                    this.btnLoading = false;
                });
        },
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + '/assets/img/blank_image.jpg';
            }
            return this.public_url + '/storage/uploads/inventory/products/media/' + imageId;
        },
        async fetchNotifications() {
            this.tableLoading = true;
            axios.get(this.api_url + "pages/settings/notifications")
                .then((response) => {
                    this.allData = response.data.response;
                }).catch((err) => this.fetchNotifications());
            this.tableLoading = false;
        },
        async add(data) {

            this.btnLoading = true;

            axios.post(this.api_url + "pages/settings/notifications", data)
                .then((response) => {

                    this.$emit('saved', true);
                    this.fetchNotifications();
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
        editPageData(data) {
            this.editData = data;
            $("#editData").modal('show');
        },
        deleteNotification(data) {
            this.editData = data;
            $("#deleteNotification").modal('show');
        },
        async update(data) {

            this.btnLoading = true;

            try {
                await axios.post(this.api_url + "pages/settings/notifications/update", data);

                this.fetchNotifications(); // Refresh the course list
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
                    text: 'Failed to update the notification',
                });
            }
        }

    }
};
</script>
