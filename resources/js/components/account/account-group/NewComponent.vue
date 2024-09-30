<template>
    <div
        class="modal fade"
        id="newGroup"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        New Group - Tier 3/4
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
                        <div class="form-group col-md-12">
                            <label class="d-block">Select Group Type <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="thirdLevel" value="tier 3" v-model="addData.group_type">
                            <label class="form-check-label" for="thirdLevel">Tier 3</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="fourthLevel" value="tier 4" v-model="addData.group_type">
                            <label class="form-check-label" for="fourthLevel">Tier 4</label>
                            </div>
                        </div>
                        <div class="form-group col-md-4" v-if="addData.group_type == 'tier 3' || addData.group_type == 'tier 4'">
                            <label>Tier 2 <span class="text-danger">*</span></label>
                            <v-select :options="secondLevel" v-model="addData.second_level" :reduce="option => option.code" @input="getThirdLevel(addData.second_level)">
                            </v-select>
                        </div>
                        <div class="form-group col-md-4" v-if="addData.group_type == 'tier 4'">
                            <label>Tier 3 <span class="text-danger">*</span></label>
                            <v-select :options="thirdLevel" v-model="addData.third_level" :reduce="option => option.code">
                            </v-select>
                        </div>
                        <div class="form-group col-md-4" v-if="addData.group_type == 'tier 3' || addData.group_type == 'tier 4'">
                            <label>{{ addData.group_type == 'tier 3' ? 'Tier 3' : 'Tier 4' }} <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" v-model="addData.name">
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
  props: ['btnLoading', 'addData', 'secondLevel'],
  data() {
        return {
            thirdLevel: []
        };
    },
  methods: {
    add() {
      this.$emit('add');
    },
    async getThirdLevel(id) {
        const res = await this.callApi("get", 'accounts/groups/'+id+'/third');
        if (res.status == 200) {
            this.thirdLevel = res.data.thirdLevel
        }
    },
  }
}
</script>