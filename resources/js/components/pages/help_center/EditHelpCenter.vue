<template>
    <div
        class="modal fade"
        id="editData"
        tabindex="-1"
        role="dialog"
        aria-labelledby="editCourseLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseLabel">
                        Edit Course
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="name"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Type <span class="text-danger">*</span></label>
                            <select class="form-control" v-model="type">
                                <option selected value="0">Select From Following</option>
                                <option selected value="user guidline">User Guidelines</option>
                                <option selected value="faq">Faq</option>
                                <option selected value="policy">Policy</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="summernote descriptionEdit"></textarea>
                            <!-- <textarea class="form-control" v-model="editData.description"></textarea> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" v-if="!btnLoading" @click="update()">Edit Information {{ (type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New {{ (type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: ['btnLoading', 'editData'],
  data() {
    return {
        name : '',
        type : '0',
        description : ''
    };
  },
  methods: {
    update() {
        const description = $('.descriptionEdit').summernote('code');
            if (!this.name || this.type==0 || !description) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Title, type, Description field are required',
                });
            }

            const data = {
                id : this.editData.id,
                name : this.name,
                type : this.type,
                description : description
            }

      this.$emit('update', data);
    },
  },
  watch : {
    editData(data){
        this.name = this.editData.name;
        this.type = this.editData.type;
        $('.descriptionEdit').summernote('code', this.editData.description);
    }
  }
};
</script>
