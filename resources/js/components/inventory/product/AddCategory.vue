<template>

    <!-- Modal -->
    <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="addCategory"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <label for=""><b>Select Parent Category</b> </label>
                        <v-select :options="parentCategories" v-model="category">
                        </v-select>
                        <small>Please don't select if you want to add Parent Category</small>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for=""><b>Category Name <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control" v-model="name">
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for=""><b>Description <span class="text-danger">( optional )</span></b></label>
                        <input type="text" class="form-control" v-model="description">
                    </div>

                    <div class="col-md-12 mt-5 text-right">
                        <button type="button" class="btn btn-primary" v-if="!loader" @click="addNewCategory()">Add New Category</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New Category</button>
                    </div>

                    <div class="col-md-12 mt-5">
                        <label for=""><b>Added Categories</b></label>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>Belong to</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in categories" :key="item.id">
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                        <span v-if="!item.editable">{{ item.parent ? item.parent.name : '-' }}</span>
                                        <v-select :options="parentCategories" v-model="category" v-else>
                                        </v-select>
                                        <small v-else>Please don't select if you want to add Parent Category</small>
                                    </td>
                                    <td>
                                        <span v-if="!item.editable">{{ item.name }}</span>
                                        <input class="form-control" v-else type="text" v-model="item.name" />
                                    </td>
                                    <td>
                                        <span v-if="!item.editable">{{ item.description }}</span>
                                        <input class="form-control" v-else v-model="item.description" />
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
    name: 'AddCategory',
    props : ['loader', 'categories', 'parentCategories'],
    data(){
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            category : { code : 0 , label : 'Select from the following'},
            description : '',
            name : ''
        }
    },
    mounted() {
        this.$parent.$on("categorySaved", (value) => {
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
                    item.name !== item.originalData.name ||
                    item.description !== item.originalData.description ||
                    (vm.category.code !== 0 && vm.category.code !== item.originalData.parent_id)
                ) {
                    const fd = new FormData();
                    fd.append('id', item.id);
                    fd.append('name', item.name);
                    fd.append('description',item.description);
                    fd.append('parent', vm.category.code);

                    vm.$emit('editCategory', fd);
                }
            }
        },
        addNewCategory(){
            let vm = this;
            if( vm.name == '' ){
                return swal({
                    title: "Required",
                    text: "Please add Category Name first, thanks",
                    icon: "error",
                    timer: 3000,
                });
            }

           const fd = new FormData();
           fd.append('name', vm.name);
           fd.append('description', vm.description);
           fd.append('parent', vm.category.code);

           vm.$emit('addNewCategory', fd);
        },
        close(){
            this.name = '';
            this.description = '';
            this.category = { code : 0 , label : 'Select form the following'};
        }
    }
}
</script>
