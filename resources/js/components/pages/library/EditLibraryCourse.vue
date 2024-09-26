<template>
    <div
        class="modal fade"
        id="editCourse"
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
                            <input type="text" class="form-control" v-model="editData.name"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Image</label>
                            <input type="file" class="form-control" @change="onFileChange"/>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="editData.description"></textarea>
                        </div>
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-success" @click="addRow">Add Video Link</button>
                    </div>
                    <div class="row" v-if="loop > 0">
                        <div class="form-group col-md-11">
                            <label>Video Link</label><br>
                            <b>Please add embedded Youtube Link</b>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Remove</label>
                        </div>
                    </div>
                    <div class="row" v-for="(i, index) in loop" :key="index">
                        <div class="form-group col-md-11">
                            <input class="form-control" type="text" :value="editData.video_links[index]" @change="saveRow($event, index)">
                        </div>
                        <div class="form-group col-md-1">
                            <button type="button" class="btn btn-danger" @click="removeRow($event, index)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" :class="{ 'disabled btn-progress': btnLoading }" @click="update">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: ['btnLoading', 'editData'],
  name : 'EditLibraryCourse',
  data() {
    return {
      selectedImage: null,
      loop: this.editData.video_links ? this.editData.video_links.length : 0
    };
  },
  methods: {
    update() {
      this.$emit('update', this.selectedImage);
    },
    onFileChange(event) {
      this.selectedImage = event.target.files[0];
    },
    saveRow(event, index) {
      this.editData.video_links[index] = event.target.value ? event.target.value : "";
    },
    addRow() {
      this.loop++;
      this.editData.video_links.push('');
    },
    removeRow(event, index) {
      this.editData.video_links.splice(index, 1);
      this.loop--;
    }
  },
  watch: {
    editData(newData) {
      this.loop = newData.video_links.length;
    }
  }
};
</script>
