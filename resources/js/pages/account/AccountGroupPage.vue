<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Account Groups | Tier 3/4</h4>
                <a href="#" class="mr-1 btn btn-primary" data-toggle="modal" data-target="#newGroup">New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="tableLoading">
                    <BulletListLoader></BulletListLoader>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm" id="group_table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tier 4</th>
                                <th scope="col">Tier 3</th>
                                <th scope="col">Tier 2</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(group, index) in fourthLevel" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{group.name}}</td>
                                <td>{{group.level_three.name}}</td>
                                <td>{{group.level_two.name}}</td>
                                <td>
                                    <a
                                        href="#"
                                        class="mr-1 btn-sm btn btn-icon btn-primary"
                                        @click="editGroup(group)"
                                        ><i class="far fa-edit"></i
                                    ></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- add modal -->
        <NewGroup
            :btnLoading="btnLoading"
            :addData="addData"
            :secondLevel="secondLevel"
            @add="addGroup"
        />
        <!-- update modal -->
        <EditGroup
            :btnLoading="btnLoading"
            :editData="editData"
            :thirdLevel="thirdLevel"
            :secondLevel="secondLevel"
            @update="updateGroup"
        />
    </section>
</template>
<script>
import NewGroup from '../../components/account/account-group/NewComponent.vue';
import EditGroup from '../../components/account/account-group/EditComponent.vue';
export default {
    name: "AccountGroupPage",
    components: {
        NewGroup,
        EditGroup,
    },
    data() {
        return {
            btnLoading: false,
            tableLoading: false,
            fourthLevel: [],
            secondLevel: [],
            thirdLevel: [],
            editData: {},
            addDataReset: {},
            addData: {
                name: "",
                group_type: "",
                second_level: { code: 0, label: "Select from the following" },
                third_level: { code: 0, label: "Select from the following" },
            },
        };
    },
    created() {
        this.groups();
        this.addDataReset = { ...this.addData};
    },
    mounted() {
    },
    methods: {
        async groups() {
            if ($.fn.DataTable.isDataTable("#group_table")) {
                $('#group_table').DataTable().destroy();
            }
            this.tableLoading = true;
            const res = await this.callApi("get", "accounts/groups");
            if(res.status == 200)
            {
                this.fourthLevel = res.data.fourthLevel;
                this.secondLevel = res.data.secondLevel;
                
                setTimeout(function () {
                    $("#group_table").DataTable();
                }, 300);
            }
            this.tableLoading = false;
        },
        async addGroup() {
            if (this.addData.group_type == "")
            {
                return this.$swal({
                    title: "Required!",
                    text: "Please select type",
                    icon: "error",
                    timer: 2000
                });
            }
            if (this.addData.second_level.code == 0)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Please select tier 2",
                    icon: "error",
                    timer: 2000
                });
            }
            if (this.addData.group_type == "tier 4" && this.addData.third_level.code == 0)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Please select tier 3",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.addData.name)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Name is required",
                    icon: "error",
                    timer: 2000
                });
            }
            
            this.btnLoading = true;
            const res = await this.callApi("post", "accounts/groups/add", this.addData);
            if (res.status === 201) {
                this.groups();
                this.addData = { ...this.addDataReset};
                $(".modal").modal('hide');
                this.$swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Successfully Added',
                });
            }
            this.btnLoading = false;
        },
        async editGroup(fourthLevel) {
            this.editData = fourthLevel;
            this.editData.group_type = "tier 4";
            this.getThirdLevel(fourthLevel.account_id);
            
            $("#editGroup").modal('show');
        },
        async getThirdLevel(id) {
            const res = await this.callApi("get", 'accounts/groups/'+id+'/third');
            if (res.status == 200) {
                this.thirdLevel = res.data.thirdLevel
            }
        },
        async updateGroup() {
            if(!this.editData.name)
            {
                return this.$swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Name is required',
                });
            }
        
            this.btnLoading = true;
            const res = await this.callApi("post", "accounts/groups/update", this.editData);
            if (res.status === 200) {
                this.groups();
                $(".modal").modal('hide');
                this.$swal({
                    icon: 'success',
                    title: 'Success',
                    text: 'Successfully Updated',
                });
            }
            this.btnLoading = false;
        },
    }
};
</script>
