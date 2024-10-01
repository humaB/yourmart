<template>
    <div
        class="modal fade"
        id="editGroup"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        Update Group
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
                        <div class="form-group col-md-4">
                            <label>Tier 2 <span class="text-danger">*</span></label>
                            <v-select disabled :options="secondLevel" v-model="editData.account_id" :reduce="option => option.code">
                            </v-select>
                        </div>
                        <div class="form-group col-md-4" >
                            <label>Tier 3 <span class="text-danger">*</span></label>
                            <v-select disabled :options="thirdLevel" v-model="editData.parent_id" :reduce="option => option.code">
                            </v-select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>{{ editData.group_type == 'tier 3' ? 'Tier 3' : 'Tier 4' }} <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" v-model="editData.name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" :class="{ 'disabled btn-progress': btnLoading }" @click=update()>Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: ['btnLoading', 'editData', 'thirdLevel', 'secondLevel'],
  methods: {
    update() {
      this.$emit('update');
    },
  }
}
</script>