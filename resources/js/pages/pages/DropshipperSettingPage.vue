<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Dropshipper/Supplier Page settings</h4>

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
                                <th scope="col">Attachment</th>
                                <th scope="col">Video Link</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="dropshipperData">
                                <th scope="row">1</th>
                                <td>{{ dropshipperData.name  }}</td>
                                <td v-if="!dropshipperData.isEditing">
                                    <a :href="public_url + 'storage/uploads/pages/library/courses/' + dropshipperData.attachment"
                                        target="_blank">
                                        <img width="80"
                                            :src="public_url + 'storage/uploads/pages/library/courses/' + dropshipperData.attachment">
                                    </a>
                                </td>
                                <td v-else>
                                    <input type="file" @change="setDropshipperAttachment($event)" class="form-control">
                                </td>
                                <td v-if="!dropshipperData.isEditing">{{ dropshipperData.video_links }}</td>
                                <td v-else>
                                    <input type="text" v-model="dropshipper.video" class="form-control">
                                </td>
                                <td>
                                    <button class="btn btn-primary" @click="editItem(dropshipperData)" v-if="!dropshipperData.isEditing">Edit</button>
                                    <button class="btn btn-success" @click="saveItem('dropshipper-page')" v-else>Save</button>
                                    <button class="btn btn-danger" @click="cancelEdit(dropshipperData)" v-if="dropshipperData.isEditing">Cancel</button>
                                </td>
                            </tr>
                            <!-- Add default rows if data is empty -->
                            <tr v-if="!dropshipperData">
                                <th scope="row">1</th>
                                <td>Dropshipper</td>
                                <td>
                                    <input type="file" class="form-control" @change="setDropshipperAttachment($event)">
                                </td>
                                <td>
                                    <input type="text" class="form-control" placeholder="Enter Video Link" v-model="dropshipper.video">
                                </td>
                                <td>
                                    <button class="btn btn-success" @click="saveItem('dropshipper-page')">Update</button>
                                </td>
                            </tr>
                            <tr v-if="supplierData">
                                <th scope="row">2</th>
                                <td>{{ supplierData.name }}</td>
                                <td v-if="!supplierData.isEditing">
                                    <a :href="public_url + 'storage/uploads/pages/library/courses/' + supplierData.attachment"
                                        target="_blank">
                                        <img width="80"
                                            :src="public_url + 'storage/uploads/pages/library/courses/' + supplierData.attachment">
                                    </a>
                                </td>
                                <td v-else>
                                    <input type="file" @change="setSupplierAttachment($event)" class="form-control">
                                </td>
                                <td v-if="!supplierData.isEditing">{{ supplierData.video_links }}</td>
                                <td v-else>
                                    <input type="text" v-model="supplier.video" class="form-control">
                                </td>
                                <td>
                                    <button class="btn btn-primary" @click="editItem(supplierData)" v-if="!supplierData.isEditing">Edit</button>
                                    <button class="btn btn-success" @click="saveItem('supplier-page')" v-else>Save</button>
                                    <button class="btn btn-danger" @click="cancelEdit(supplierData)" v-if="supplierData.isEditing">Cancel</button>
                                </td>
                            </tr>
                            <tr v-if="!supplierData">
                                <th scope="row">2</th>
                                <td>Supplier</td>
                                <td>
                                    <input type="file" class="form-control" @change="setSupplierAttachment($event)">
                                </td>
                                <td>
                                    <input type="text" class="form-control" placeholder="Enter Video Link"  v-model="supplier.video">
                                </td>
                                <td>
                                    <button class="btn btn-success" @click="saveItem('supplier-page')">Update</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</template>
<script>
import { BulletListLoader } from "vue-content-loader";
import NewLibraryCourse from '../../components/pages/library/NewLibraryCourse.vue';
import EditLibraryCourse from '../../components/pages/library/EditLibraryCourse.vue';
import axios from 'axios';
export default {
    name: "DropshipperSettingPage",
    components: {
        NewLibraryCourse,
        EditLibraryCourse,
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            btnLoading: false,
            tableLoading: false,
            dropshipperData: {
                isEditing: false,
            },
            supplierData: {
                isEditing: false,
            },
            dropshipper : {
                video : "",
                attachment : ""
            },
            supplier : {
                video : "",
                attachment : ""
            },
        };
    },
    created() {
        this.settings();
    },
    methods: {
        setDropshipperAttachment( event ){
            this.dropshipper.attachment = event.target.files[0];
        },
        setSupplierAttachment( event ){
            this.supplier.attachment = event.target.files[0];
        },
        editItem(item) {
            if( item.name == 'dropshipper-page'){
                Vue.set(this.dropshipperData, 'isEditing', true);
            }
            if( item.name == 'supplier-page'){
                Vue.set(this.supplierData, 'isEditing', true);
            }
        },
        saveItem(item) {
            const fd = new FormData();
            if( item == 'dropshipper-page'){
                if( this.dropshipper.attachment == '' && !this.dropshipperData){
                    return swal({
                        icon: 'error',
                        title: 'Required',
                        text: 'Please upload attachment first',
                    });
                }
                if( this.dropshipper.video == ''){
                    return swal({
                        icon: 'error',
                        title: 'Required',
                        text: 'Please add video link first',
                    });
                }

                this.dropshipper.type = 'dropshipper';

                fd.append('type', 'dropshipper');
                fd.append('attachment', this.dropshipper.attachment);
                fd.append('video', this.dropshipper.video);
            }

            if( item == 'supplier-page'){
                if( this.supplier.attachment == '' && !this.supplierData ){
                    return swal({
                        icon: 'error',
                        title: 'Required',
                        text: 'Please upload attachment first',
                    });
                }
                if( this.supplier.video == ''){
                    return swal({
                        icon: 'error',
                        title: 'Required',
                        text: 'Please add video link first',
                    });
                }

                fd.append('type', 'supplier');
                fd.append('attachment', this.supplier.attachment);
                fd.append('video', this.supplier.video);
            }


            axios.post(this.api_url + "pages/settings/dropshipper-page", fd)
            .then((response) => {
                this.dropshipper = {
                    attachment : "",
                    video : ""
                }

                this.supplier = {
                    attachment : "",
                    video : ""
                }

                this.settings();
                return swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Data Successfully added',
                });
            })
        },
        cancelEdit(item) {
            if( item.name == 'dropshipper-page'){
                this.dropshipperData.isEditing = false;
            }
            if( item.name == 'supplier-page'){
                this.supplierData.isEditing = false;
            }
        },
        handleFileChange(event, type) {
            // Handle the file change event
            if( type == 'dropshipper-page'){
                this.setDropshipperAttachment( event );
            }else{
                this.setSupplierAttachment( event );
            }
        },
        settings() {
            this.tableLoading = true;
            axios.get(this.api_url + "pages/settings/dropshipper-page")
                .then((response) => {
                    this.supplierData = response.data.response.supplier;
                    this.dropshipperData = response.data.response.dropshipper;
                    if( this.dropshipperData ){
                        this.dropshipper.video = this.dropshipperData.video_links;
                    }
                    if( this.supplierData ){
                        this.supplier.video = this.supplierData.video_links;
                    }
                })
        },
    }
};
</script>
