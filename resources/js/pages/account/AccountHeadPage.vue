<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Account Heads</h4>
                <a href="#" class="mr-1 btn btn-primary" data-toggle="modal" data-target="#newHead">New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="tableLoading">
                    <BulletListLoader></BulletListLoader>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm" id="head_table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col"><div class="w-50 m-auto">Parent Account</div></th>
                                <th scope="col">Code</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(head, index) in accountHeads" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{head.name}}</td>
                                <td>
                                    <div class="w-50 m-auto">
                                        <b>Level 1: </b>{{head.level_one.name}} <br>
                                        <b>Level 2: </b>{{head.level_two.name}} <br>
                                        <b>Level 3: </b>{{head.level_three.name}} <br>
                                        <b>Level 4: </b>{{head.level_four.name}}
                                    </div>
                                </td>
                                <td>
                                    {{head.level_one.code}}-
                                    {{head.level_two.code}}-
                                    {{head.level_three.code}}-
                                    {{head.level_four.code}}-
                                    {{head.code}}
                                </td>
                                <td>
                                    <a
                                        href="#"
                                        class="mr-1 btn-sm btn btn-icon btn-primary"
                                        @click="editHead(head)"
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
        <NewHead
            :btnLoading="btnLoading"
            :addData="addData"
            :firstLevel="firstLevel"
            @add="addHead"
        />
        <!-- update modal -->
        <EditHead
            :btnLoading="btnLoading"
            :editData="editData"
            @update="updateHead"
        />
    </section>
</template>
<script>
import NewHead from '../../components/account/account-head/NewComponent.vue';
import EditHead from '../../components/account/account-head/EditComponent.vue';
export default {
    name: "AccountHeadPage",
    components: {
        NewHead,
        EditHead,
    },
    data() {
        return {
            btnLoading: false,
            tableLoading: false,
            accountHeads: [],
            firstLevel: [],
            thirdLevel: [],
            editData: {},
            addDataReset: {},
            addData: {
                name: "",
                first_level: { code: 0, label: "Select from the following" },
                second_level: { code: 0, label: "Select from the following" },
                third_level: { code: 0, label: "Select from the following" },
                fourth_level: { code: 0, label: "Select from the following" },
            },
        };
    },
    created() {
        this.heads();
        this.addDataReset = { ...this.addData};
    },
    mounted() {
    },
    methods: {
        async heads() {
            if ($.fn.DataTable.isDataTable("#head_table")) {
                $('#head_table').DataTable().destroy();
            }
            this.tableLoading = true;
            const res = await this.callApi("get", "accounts/heads");
            if(res.status == 200)
            {
                this.accountHeads = res.data.accountHeads;
                this.firstLevel = res.data.firstLevel;
                
                setTimeout(function () {
                    $("#head_table").DataTable();
                }, 300);
            }
            this.tableLoading = false;
        },
        async addHead() {
            if (this.addData.first_level.code == 0)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Please select tier 1",
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
            if (this.addData.third_level.code == 0)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Please select tier 3",
                    icon: "error",
                    timer: 2000
                });
            }
            if (this.addData.fourth_level.code == 0)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Please select tier 4",
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
            const res = await this.callApi("post", "accounts/heads/add", this.addData);
            if (res.status === 201) {
                this.heads();
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
        async editHead(head) {
            this.editData = head;
            $("#editHead").modal('show');
        },
        async updateHead() {
            if(!this.editData.name)
            {
                return this.$swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Name is required',
                });
            }
        
            this.btnLoading = true;
            const res = await this.callApi("post", "accounts/heads/update", this.editData);
            if (res.status === 200) {
                this.heads();
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
