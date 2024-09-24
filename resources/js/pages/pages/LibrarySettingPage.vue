<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Library Page</h4>
                <a href="#" class="mr-1 btn btn-primary" data-toggle="modal" data-target="#newCourse">Add New</a>
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
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Added By</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(course, index) in allCourses" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>
                                    <a :href="public_url+'storage/uploads/pages/library/courses/'+course.attachment" target="_blank">
                                        <img width="80" :src="public_url+'storage/uploads/pages/library/courses/'+course.attachment">
                                    </a>
                                </td>
                                <td>{{course.name}}</td>
                                <td>{{course.description}}</td>
                                <td>{{course.added_name.name}}</td>
                                <td>
                                    <a
                                        href="#"
                                        class="btn btn-primary"
                                        @click="editCourse(course)"
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
        <NewLibraryCourse
            :btnLoading="btnLoading"
            :addData="addData"
            @add="addPartner"
        />
        <!-- update modal -->
        <EditLibraryCourse
            :btnLoading="btnLoading"
            :editData="editData"
            @update="updateCourse"
        />
    </section>
</template>
<script>
import { BulletListLoader } from "vue-content-loader";
import NewLibraryCourse from '../../components/pages/library/NewLibraryCourse.vue';
import EditLibraryCourse from '../../components/pages/library/EditLibraryCourse.vue';
import axios from 'axios';
export default {
    name: "PartnerPage",
    components: {
        NewLibraryCourse,
        EditLibraryCourse,
    },
    data() {
        return {
            api_url : window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            btnLoading: false,
            tableLoading: false,
            allCourses: [],
            editData: { name: '', description: '', video_links: [] },
            editDataReset: { name: '', description: '', video_links: [] },
            addDataReset: {},
            addData: {
                name: "",
                description: "",
                image: null, // keep track of the image file
                video_links: [], // keep track of the image file
            },
        };
    },
    created() {
        this.courses();
        this.addDataReset = JSON.parse(JSON.stringify(this.addData));
    },
    mounted() {
    },
    methods: {
        async courses() {
            this.tableLoading = true;
            axios.get(this.api_url + "pages/settings/library-page")
            .then((response) => {
                this.allCourses = response.data.response;
            }).catch((err) => this.fetchTags());

            // if ($.fn.DataTable.isDataTable("#course_table")) {
            //     $('#course_table').DataTable().destroy();
            // }
            // setTimeout(function () {
            //     $('#course_table').DataTable();
            // }, 300);
            this.tableLoading = false;
        },
        async addPartner(selectedImage) {
            if (!this.addData.name || !this.addData.description || !selectedImage) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Title, Image, Description field are required',
                });
            }

            const formData = new FormData();
            formData.append('data', JSON.stringify(this.addData));
            formData.append('image', selectedImage); // append the image

            this.btnLoading = true;

            axios.post(this.api_url + "pages/settings/library-page/add", formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                this.addData = JSON.parse(JSON.stringify(this.addDataReset));
                this.courses();
                return swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Successfully Added',
                });
            }).catch((err) => {
            });
            this.btnLoading = false;
        },
        async editCourse(course) {
            this.editData = course;
            $("#editCourse").modal('show');
        },
        async updateCourse(selectedImage) {
            if (!this.editData.name || !this.editData.description) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Title and Description are required',
                });
            }

            const formData = new FormData();
            formData.append('data', JSON.stringify(this.editData));

            // Append the image if it's selected
            if (selectedImage) {
                formData.append('image', selectedImage);
            }

            this.btnLoading = true;

            try {
                await axios.post(this.api_url + "pages/settings/library-page/update", formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.editData = JSON.parse(JSON.stringify(this.editDataReset));
                this.courses(); // Refresh the course list

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
                    text: 'Failed to update the course',
                });
            } finally {
                this.btnLoading = false;
            }
        }

    }
};
</script>
