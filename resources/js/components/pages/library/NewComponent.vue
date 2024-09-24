<template>
    <div
        class="modal fade"
        id="newCourse"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        New Course
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
                            <input type="text" class="form-control" v-model="addData.name"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" @change="onFileChange"/>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="addData.description"></textarea>
                        </div>
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-success" @click="addRow">Add Video Link</button>
                    </div>
                    <div class="row" v-if="loop > 0">
                        <div class="form-group col-md-11">
                            <label>Video Link</label>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Remove</label>
                        </div>
                    </div>
                    <div class="row"  v-for="(i, index) in loop" :key="index">
                        <div class="form-group col-md-11">
                            <input class="form-control" type="text" :value="addData.video_links[index]" @change="saveRow($event, index)">
                        </div>
                        <div class="form-group col-md-1">
                            <button type="button" class="mt-1 btn-sm btn btn-outline-danger" @click="removeRow($event, index)"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" :class="{ 'disabled btn-progress': btnLoading }" @click=add()>Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
  props: ['btnLoading', 'addData'],
  data() {
    return {
      selectedImage: null,
      loop: 0
    };
  },
  methods: {
        add() {
            this.$emit('add', this.selectedImage);
        },
        onFileChange(event) {
            this.selectedImage = event.target.files[0];
        },
        saveRow(event,index) {
            this.addData.video_links[index] = event.target.value ? event.target.value : "";
        },
        addRow() {
            this.loop++;
        },
        removeRow(event, index) {
            this.addData.video_links.splice(index, 1);
            this.loop--;
        },
    },
}
</script>


