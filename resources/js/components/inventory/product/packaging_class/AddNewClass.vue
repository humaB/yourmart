<template>
    <div
        class="modal fade"
        id="newData"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        Add New Packaging Class
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
                            <label>Class Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="addData.name"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="addData.price"/>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Description</label>
                            <textarea class="form-control" v-model="addData.description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" v-if="!btnLoading" @click="add()">Add New Class</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New Class</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: ['btnLoading', 'addData'],
  methods: {
    add() {
      this.$emit('add');
    },
  },
}
</script>
