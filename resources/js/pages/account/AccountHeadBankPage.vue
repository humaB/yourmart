<template>
    <section>
        <div class="card">
            <div class="card-header justify-content-between">
                <h4>Account Head For Banks</h4>
                <a href="#" class="mr-1 btn btn-primary" data-toggle="modal" data-target="#newHeadBank">New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive" v-if="tableLoading">
                    <BulletListLoader></BulletListLoader>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm" id="head_bank_table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Account Number</th>
                                <th scope="col">IBAN</th>
                                <th scope="col">Address</th>
                                <th scope="col"><div class="w-50 m-auto">Parent Account</div></th>
                                <th scope="col">Code</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(head, index) in accountHeadBanks" :key="index">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{head.name}}</td>
                                
                                <td>{{head.head_bank.account_number}}</td>
                                <td>{{head.head_bank.iban}}</td>
                                <td>{{head.head_bank.address}}</td>
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
                                        @click="editHeadBank(head)"
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
        <NewHeadBank
            :btnLoading="btnLoading"
            :addData="addData"
            @add="addHeadBank"
        />
        <!-- update modal -->
        <EditHeadBank
            :btnLoading="btnLoading"
            :editData="editData"
            @update="updateHeadBank"
        />
    </section>
</template>
<script>
import NewHeadBank from '../../components/account/account-head-bank/NewComponent.vue';
import EditHeadBank from '../../components/account/account-head-bank/EditComponent.vue';
export default {
    name: "AccountHeadBankPage",
    components: {
        NewHeadBank,
        EditHeadBank,
    },
    data() {
        return {
            btnLoading: false,
            tableLoading: false,
            accountHeadBanks: [],
            editData: {
                head_bank: {},
            },
            addDataReset: {},
            addData: {
                name: "",
                address: "",
                iban: "",
                account_number: "",
            },
        };
    },
    created() {
        this.headBanks();
        this.addDataReset = { ...this.addData};
    },
    mounted() {
    },
    methods: {
        async headBanks() {
            if ($.fn.DataTable.isDataTable("#head_bank_table")) {
                $('#head_bank_table').DataTable().destroy();
            }
            this.tableLoading = true;
            const res = await this.callApi("get", "accounts/heads/banks");
            if(res.status == 200)
            {
                this.accountHeadBanks = res.data.accountHeadBanks;
                
                setTimeout(function () {
                    $("#head_bank_table").DataTable();
                }, 300);
            }
            this.tableLoading = false;
        },
        async addHeadBank() {
            if (!this.addData.name)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Name is required",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.addData.address)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Address is required",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.addData.iban)
            {
                return this.$swal({
                    title: "Required!",
                    text: "IBAN is required",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.addData.account_number)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Account number is required",
                    icon: "error",
                    timer: 2000
                });
            }
            
            this.btnLoading = true;
            const res = await this.callApi("post", "accounts/heads/banks/add", this.addData);
            if (res.status === 201) {
                this.headBanks();
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
        async editHeadBank(head) {
            this.editData = head;
            $("#editHeadBank").modal('show');
        },
        async updateHeadBank() {
            if (!this.editData.name)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Name is required",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.editData.head_bank.address)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Address is required",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.editData.head_bank.iban)
            {
                return this.$swal({
                    title: "Required!",
                    text: "IBAN is required",
                    icon: "error",
                    timer: 2000
                });
            }
            if (!this.editData.head_bank.account_number)
            {
                return this.$swal({
                    title: "Required!",
                    text: "Account number is required",
                    icon: "error",
                    timer: 2000
                });
            }
        
            this.btnLoading = true;
            const res = await this.callApi("post", "accounts/heads/banks/update", this.editData);
            if (res.status === 200) {
                this.headBanks();
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
