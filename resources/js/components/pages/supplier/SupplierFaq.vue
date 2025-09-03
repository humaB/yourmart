<template>
    <!-- Modal -->
    <div class="modal fade" id="supplierFaq" tabindex="-1" role="dialog"
        aria-labelledby="supplierFaqTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierFaqTitle">Supplier Faq's</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     <div class="row">
                        <div class="form-group col-md-12">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="helper.title"/>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="summernote"></textarea>
                        </div>

                        <div class="col-md-12 text-right">
                             <button type="button" class="btn btn-primary" v-if="!loader" @click="addFaq()">Add New Faq</button>
                             <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New Faq</button>
                        </div>

                        <div class="col-md-12">
                            <label for="">Added Faq's</label>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(faq, index) in supplierFaqs" :key="'faq-' + faq.id">
                                    <td>{{ index + 1 }}</td>
                                    <td v-if="!faq.editing">{{ faq.attachment }}</td>
                                    <td v-else>
                                        <input type="text" class="form-control" v-model="faq.attachment"/>
                                    </td>
                                    <td v-if="!faq.editing" v-html="faq.description"></td>
                                    <td v-else>
                                        <textarea class="summernote descriptionEdit"></textarea>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button v-if="!faq.editing" @click="editFaq(faq)" class="btn btn-primary">Edit</button>
                                            <button v-else @click="saveFaq(faq)" class="btn btn-success">Save</button>
                                            <button v-if="faq.editing" @click="cancelEdit(faq)" class="btn btn-secondary">Cancel</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
    name: "SupplierFaq",
    props : ['helper', 'loader', 'supplierFaqs'],
    methods : {
        addFaq(){
            const description = $('.summernote').summernote('code');
            if (!this.helper.title || !description) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Title, Description field are required',
                });
            }
            this.helper.description = description;
            this.$emit('addFaq' , this.helper);

             $('.summernote').summernote('code', '');
        },
        editFaq(faq) {
            faq.editing = true;
            faq.originalDescription = faq.description;
            this.$nextTick(() => {
                    $(".descriptionEdit").summernote({
                        dialogsInBody: true,
                        minHeight: 200,
                        toolbar: [
                            ["style", ["bold"]],
                            ["para", ["ul", "ol", "paragraph"]]
                        ]
                    });

                    // Initialize Summernote on the correct class
                    $('.descriptionEdit').summernote('code', faq.description);
            });
        },
        saveFaq(faq) {
            faq.editing = false;
            faq.description = $('.descriptionEdit').summernote('code');
            this.$emit('updateSupplierFaq', faq)
        },
        cancelEdit(faq) {
            faq.editing = false;
            faq.description = faq.originalDescription;
        }
    }
}
</script>
