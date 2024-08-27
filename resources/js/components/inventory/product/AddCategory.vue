<template>

    <!-- Modal -->
    <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="addCategory"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <v-select :options="categories" v-model="category">
                        </v-select>
                    </div>
                    <div class="col-md-12">
                        <label for=""><b>Category Name <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Add New</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddCategory',
    data () {
        return {
            categories : ["Electronics","Wireless","Bluetooth","Apple","Modern","Office","Unisex","Black","Plastic"],
            category : { code : 0, label : "Select from the following"},
        }
    },
}
</script>
