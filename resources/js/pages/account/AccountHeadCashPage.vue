<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Account Head For Cash</h4>
                <a href="#" class="mr-1 btn btn-primary" data-toggle="modal" data-target="#newHeadCash">New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="tableLoading">
                    <BulletListLoader></BulletListLoader>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm" id="head_cash_table">
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
                            <tr v-for="(head, index) in accountHeadCash" :key="index">
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
                                        @click="editHeadCash(head)"
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
        <NewHeadCash
            :btnLoading="btnLoading"
            :addData="addData"
            @add="addHeadCash"
        />
        <!-- update modal -->
        <EditHeadCash
            :btnLoading="btnLoading"
            :editData="editData"
            @update="updateHeadCash"
        />
    </section>
</template>
<script>
import NewHeadCash from '../../components/account/account-head-cash/NewComponent.vue';
import EditHeadCash from '../../components/account/account-head-cash/EditComponent.vue';
export default {
    name: "AccountHeadCashPage",
    components: {
        NewHeadCash,
        EditHeadCash,
    },
    data() {
        return {
            btnLoading: false,
            tableLoading: false,
            accountHeadCash: [],
            editData: {
                head_cash: {},
            },
            addDataReset: {},
            addData: {
                name: "",
            },
        };
    },
    created() {
        this.headCash();
        this.addDataReset = { ...this.addData};
    },
    mounted() {
    },
    methods: {
        async headCash() {
            if ($.fn.DataTable.isDataTable("#head_cash_table")) {
                $('#head_cash_table').DataTable().destroy();
            }
            this.tableLoading = true;
            const res = await this.callApi("get", "accounts/heads/cash");
            if(res.status == 200)
            {
                this.accountHeadCash = res.data.accountHeadCash;
                
                setTimeout(function () {
                    $("#head_cash_table").DataTable();
                }, 300);
            }
            this.tableLoading = false;
        },
        async addHeadCash() {
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
            const res = await this.callApi("post", "accounts/heads/cash/add", this.addData);
            if (res.status === 201) {
                this.headCash();
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
        async editHeadCash(head) {
            this.editData = head;
            $("#editHeadCash").modal('show');
        },
        async updateHeadCash() {
            if (!this.editData.name)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Name is required",
                    icon: "error",
                    timer: 2000
                });
            }

            this.btnLoading = true;
            const res = await this.callApi("post", "accounts/heads/cash/update", this.editData);
            if (res.status === 200) {
                this.headCash();
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
