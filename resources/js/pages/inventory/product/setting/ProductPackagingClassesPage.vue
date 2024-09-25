<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Packaging Class</h4>
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
                                <th scope="col">Price</th>
                                <th scope="col">Description</th>
                                <th scope="col">Added By</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in allData" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{data.name}}</td>
                                <td>{{data.price}}</td>
                                <td>{{data.description}}</td>
                                <td>{{data.added_name.name}}</td>
                                <td>
                                    <a
                                        href="#"
                                        class="btn  btn-primary"
                                        @click="editPageData(data)"
                                        ><i class="far fa-edit"></i
                                    ></a>
                                    <!-- <a
                                        href="#"
                                        class="mr-1 btn-sm btn btn-icon btn-danger"
                                        ><i class="fas fa-trash"></i
                                    ></a> -->
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
            :addData="addData"
            @add="addPageData"
        />
        <!-- update modal -->
        <EditData
            :btnLoading="btnLoading"
            :editData="editData"
            @update="updatePageData"
        />
    </section>
</template>
<script>
import { BulletListLoader } from "vue-content-loader";
import NewData from '../../../../components/inventory/product/packaging_class/AddNewClass.vue';
import EditData from '../../../../components/inventory/product/packaging_class/EditClass.vue';
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
            editData: { name: '', description: '', price: "" },
            editDataReset: { name: '', description: '', price: "" },
            addDataReset: {},
            addData: {
                name: "",
                price: "",
                description: "",
            },
        };
    },
    created() {
        this.packagingClasses();
        this.addDataReset = JSON.parse(JSON.stringify(this.addData));
    },
    mounted() {
    },
    methods: {
        async packagingClasses() {
            this.tableLoading = true;
            axios.get(this.api_url + "inventory/products/settings/packaging-classes")
            .then((response) => {
                this.allData = response.data.response;
            }).catch((err) => this.fetchTags());
            this.tableLoading = false;
        },
        async addPageData() {
            if (!this.addData.name || !this.addData.price) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Class Name and Price fields are required',
                });
            }

            this.btnLoading = true;

            axios.post(this.api_url + "inventory/products/settings/packaging-classes/add", this.addData)
                .then((response) => {
                    this.addData = JSON.parse(JSON.stringify(this.addDataReset)); // Reset form data
                    this.packagingClasses(); // Method to refresh or reload product list
                    return swal({
                        icon: 'success',
                        title: 'Success',
                        text: 'Successfully Added',
                    });
                })
                .catch((err) => {
                    console.error(err); // Handle errors
                });

            this.btnLoading = false;
        },
        async editPageData(data) {
            this.editData = data;
            $("#editData").modal('show');
        },
        async updatePageData() {
            if (!this.editData.name || !this.editData.price) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Class Name and Price fields are required',
                });
            }
            this.btnLoading = true;

            try {
                await axios.post(this.api_url + "inventory/products/settings/packaging-classes/update", this.editData);

                this.editData = JSON.parse(JSON.stringify(this.editDataReset));
                this.packagingClasses(); // Refresh the course list

                return swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Successfully Updated',
                });
            } catch (error) {
                console.error(error);
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update the data',
                });
            } finally {
                this.btnLoading = false;
            }
        }

    }
};
</script>
