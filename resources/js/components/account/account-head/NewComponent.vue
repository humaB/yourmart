<template>
    <div
        class="modal fade"
        id="newHead"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        New Ledger
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
                        <div class="form-group col-md-3">
                            <label>Tier 1 <span class="text-danger">*</span></label>
                            <v-select :options="firstLevel" v-model="addData.first_level" :reduce="option => option.code" @input="getSecondLevel(addData.first_level)">
                            </v-select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tier 2 <span class="text-danger">*</span></label>
                            <v-select :options="secondLevel" v-model="addData.second_level" :reduce="option => option.code" @input="getThirdLevel(addData.second_level)">
                            </v-select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tier 3 <span class="text-danger">*</span></label>
                            <v-select :options="thirdLevel" v-model="addData.third_level" :reduce="option => option.code" @input="getFourthLevel(addData.third_level)">
                            </v-select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tier 4 <span class="text-danger">*</span></label>
                            <v-select :options="fourthLevel" v-model="addData.fourth_level" :reduce="option => option.code">
                            </v-select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Ledger Name <span class="text-danger">*</span></label>
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
  props: ['btnLoading', 'addData', 'firstLevel'],
  data() {
        return {
            secondLevel: [],
            thirdLevel: [],
            fourthLevel: [],
        };
    },
  methods: {
    add() {
      this.$emit('add');
    },
    async getSecondLevel(id) {
        const res = await this.callApi("get", 'accounts/'+id+'/second');
        if (res.status == 200) {
            this.secondLevel = res.data.secondLevel
        }
    },
    async getThirdLevel(id) {
        const res = await this.callApi("get", 'accounts/groups/'+id+'/third');
        if (res.status == 200) {
            this.thirdLevel = res.data.thirdLevel
        }
    },
    async getFourthLevel(id) {
        const res = await this.callApi("get", 'accounts/groups/'+id+'/fourth');
        if (res.status == 200) {
            this.fourthLevel = res.data.fourthLevel
        }
    },
  }
}
</script>
