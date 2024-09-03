<template>

    <!-- Modal -->
    <div class="modal fade" id="addColor" tabindex="-1" role="dialog" aria-labelledby="addColor" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New Color</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <label for=""><b>Color Name <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control" v-model="name">
                    </div>

                    <div class="col-md-12 mt-3">

                        <label><b>Pick Your Color <span class="text-danger">( optional )</span></b></label>
                        <div class="input-group colorpickerinput colorpicker-element">
                            <input type="text" class="form-control" v-model="hex">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-fill-drip"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 mt-3">
                        <label for=""><b>Upload Color Image <span class="text-danger">( optional )</span></b></label>
                        <input type="file" class="form-control" @change="setLogo" accept="image/png image/jpeg">
                        <code>2MB Sized Allowed</code>
                    </div>

                    <div class="col-md-12 mt-5 text-right">
                        <button type="button" class="btn btn-primary" v-if="!loader" @click="addNewColor()">Add New
                            Color</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New
                            Color</button>
                    </div>

                    <div class="col-md-12 mt-5">
                        <label for=""><b>Added Colors</b></label>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>Name</th>
                                    <th>Hex #</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in colors" :key="item.id">
                                    <td>{{ index + 1 }}</td>

                                    <td>
                                        <span v-if="!item.editable">{{ item.name }}</span>
                                        <input class="form-control" v-else type="text" v-model="item.name" />
                                    </td>
                                    <td>
                                        <span v-if="!item.editable">{{ item.hex }}</span>
                                        <div v-else class="input-group colorpickerinput colorpicker-element">
                                            <input type="text" class="form-control" v-model="item.hex">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <i class="fas fa-fill-drip"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <img v-if="!item.editable" :src="getImageUrl(item.image)"
                                            class="mr-3 rounded-circle" width="30" alt="">
                                        <input class="form-control" v-else type="file" @change="setLogo($event)" />
                                    </td>
                                    <td>
                                        <button :class="!item.editable ? 'btn btn-primary' : 'btn btn-success'"
                                            :title="!item.editable ? 'edit details' : 'update details'"
                                            @click="toggleEdit(item)">
                                            <i v-if="!item.editable" class="fa fa-edit"></i>
                                            <i v-else class="fa fa-save"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddColor',
    props: ['loader', 'colors'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            name: '',
            hex: '',
            image: ''
        }
    },
    mounted() {
        this.$parent.$on("colorSaved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    methods: {
        getImageUrl(image) {
            // Check if the image is null
            if (!image) {
                return this.public_url + 'assets/img/blank_image.jpg';
            }
            return this.public_url + 'storage/uploads/inventory/colors/' + image;
        },
        toggleEdit(item) {
            let vm = this;
            item.editable = !item.editable;
            if (!item.editable) {
                // Check if any changes were made
                if (
                    item.name !== item.originalData.name ||
                    item.hex !== item.originalData.hex ||
                    item.image !== item.originalData.image
                ) {
                    const fd = new FormData();
                    fd.append('id', item.id);
                    fd.append('name', item.name);
                    fd.append('hex', item.hex);
                    fd.append('image', vm.image);

                    vm.$emit('editColor', fd);
                }
            }
        },
        setLogo(event) {
            this.image = event.target.files[0];
        },
        addNewColor() {
            let vm = this;
            if (vm.name == '') {
                return swal({
                    title: "Required",
                    text: "Please add Color Name first, thanks",
                    icon: "error",
                    timer: 3000,
                });
            }

            const fd = new FormData();
            fd.append('name', vm.name);
            fd.append('hex', vm.hex);
            fd.append('image', vm.image);

            vm.$emit('addNewColor', fd);
        },
        close() {
            this.name = '';
            this.hex = '';
            this.image = '';
            $("input[type=file]").val('');
        }
    }
}
</script>
