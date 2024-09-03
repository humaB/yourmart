<template>

    <!-- Modal -->
    <div class="modal fade" id="addTag" tabindex="-1" role="dialog" aria-labelledby="addTag"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New Tag</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <label for=""><b>Tag Name <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control" v-model="name">
                    </div>

                    <div class="col-md-12 mt-5 text-right">
                        <button type="button" class="btn btn-primary" v-if="!loader" @click="addNewTag()">Add New Tag</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New Tag</button>
                    </div>

                    <div class="col-md-12 mt-5">
                        <label for=""><b>Added Tags</b></label>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in tags" :key="item.id">
                                    <td>{{ index + 1 }}</td>

                                    <td>
                                        <span v-if="!item.editable">{{ item.name }}</span>
                                        <input class="form-control" v-else type="text" v-model="item.name" />
                                    </td>
                                    <td>
                                        <button :title="!item.editable ? 'edit details' : 'update details'" :class="!item.editable ? 'btn btn-primary' : 'btn btn-success'" @click="toggleEdit(item)">
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
    name: 'AddTag',
    props : ['loader', 'tags'],
    data(){
        return {
            name : '',
        }
    },
    mounted() {
        this.$parent.$on("tagSaved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    methods : {
        toggleEdit(item) {
            let vm = this;
            item.editable = !item.editable;
            if (!item.editable) {
                // Check if any changes were made
                if (
                    item.name !== item.originalData.name
                ) {
                    const fd = new FormData();
                    fd.append('id', item.id);
                    fd.append('name', item.name);

                    vm.$emit('editTag', fd);
                }
            }
        },
        setLogo( event ){
            this.image = event.target.files[0];
        },
        addNewTag(){
            let vm = this;
            if( vm.name == '' ){
                return swal({
                    title: "Required",
                    text: "Please add Tag Name first, thanks",
                    icon: "error",
                    timer: 3000,
                });
            }

           const fd = new FormData();
           fd.append('name', vm.name);

           vm.$emit('addNewTag', fd);
        },
        close(){
            this.name = '';
        }
    }
}
</script>
