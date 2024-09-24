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
                        Add New FAQ's , User Guidelines and Policies
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
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="addData.name"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Type <span class="text-danger">*</span></label>
                            <select class="form-control" v-model="addData.type">
                                <option selected value="0">Select From Following</option>
                                <option selected value="user guidline">User Guidelines</option>
                                <option selected value="faq">Faq</option>
                                <option selected value="policy">Policy</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="addData.description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" v-if="!btnLoading" @click=add()>Add New {{ (addData.type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New {{ (addData.type).toUpperCase() }}</button>
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
    };
  },
  methods: {
        add() {
            this.$emit('add');
        },
    },
}
</script>


