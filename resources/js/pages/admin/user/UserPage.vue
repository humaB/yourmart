<template>
    <div>
        <UserPopup :loader="btnLoader" @add="add($event)" />

        <UserEditPopup :loader="btnLoader" :details="editDetails" @update="update($event)" />
        <div class="row">
            <div class="col-md-12 card">
                <div class="card-body px-2">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Total Users</th>
                                <th>Admin's</th>
                                <th>Collection</th>
                                <th>Inventory</th>
                                <th>QC Manager</th>
                                <th>Packing & Dispatching </th>
                                <th>Audit Manager</th>
                                <th>Data Entry</th>
                                <th>Gate Incharge</th>
                                <th>Supervisor</th>

                            </tr>
                            <tr>
                                <td>{{ totalUsers.totalUsers }}</td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-success"
                                            :style="{ width: getPercentage(totalUsers.admin) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.admin }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-warning"
                                            :style="{ width: getPercentage(totalUsers.orderCollection) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.orderCollection }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-primary"
                                            :style="{ width: getPercentage(totalUsers.inventoryManager) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.inventoryManager }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-info"
                                            :style="{ width: getPercentage(totalUsers.qcManager) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.qcManager }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-dark"
                                            :style="{ width: getPercentage(totalUsers.packing) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.packing }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-light"
                                            :style="{ width: getPercentage(totalUsers.audit) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.audit }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-success"
                                            :style="{ width: getPercentage(totalUsers.dataEntry) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.dataEntry }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-warning"
                                            :style="{ width: getPercentage(totalUsers.gateIncharge) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.gateIncharge }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-danger"
                                            :style="{ width: getPercentage(totalUsers.supervisor) + '%' }"></div>
                                    </div>
                                    {{ totalUsers.supervisor }}
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="card-body">
                        <!-- Table -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <UserTable :id="table_id" :th="th" :tbody="users" @edit="edit($event)"
                                            @deleteFunc="deleteFunc($event)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END TABLE -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteConfirmation" tabindex="-1" role="dialog"
            aria-labelledby="deleteConfirmationTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Confirmaion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <h5>Are you sure you want to delete this user ?</h5>
                        <small>This will deactivate user account not delete in actual</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" @click="deleteConfirmation()"
                            v-if="!btnLoader">Yes, Delete</button>
                        <button type="button" class="btn btn-danger btn-progress disabled" v-else>Yes, Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import UserTable from "../../../components/admin/user/UserTable.vue";
import UserPopup from "../../../components/admin/user/UserPopup.vue";
import UserEditPopup from "../../../components/admin/user/UserEditPopup.vue";

export default {
    name: "UserPage",
    components: {
        TableHeader,
        UserTable,
        UserPopup,
        UserEditPopup
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Users",
                link: "#",
                target: "#addUser",
            },
            th: ["Sr #", "Name", "Email", "Role", "Allowed IP", "Action"],
            table_id: "user_list_table",
            users: [],
            editDetails: {},
            btnLoader: false,
            user: '',
            totalUsers: {
                totalUsers: 0,
                admin: 0,
                orderCollection: 0,
                inventoryManager: 0,
                qcManager: 0,
                packing: 0,
                audit: 0,
                dataEntry: 0,
                gateIncharge: 0,
                supervisor: 0,
            },
        };
    },
    created() {
        this.fetchUsers();
        setTimeout(() => {
            $("#pwstrength").pwstrength();
            $("#pwstrength2").pwstrength();
        }, 2000)
    },
    methods: {
        fetchUsers() {
            let vm = this;
            axios
                .get(this.api_url + "users")
                .then((response) => {
                    const results = response.data.response;

                    vm.users = results;

                    // Loop through the users and count each role
                    vm.users.forEach(user => {
                        // Increment the total user count
                        vm.totalUsers.totalUsers++;

                        // Check the user's role and increment the corresponding role count
                        switch (user.role) {
                            case 'admin':
                                vm.totalUsers.admin++;
                                break;
                            case 'order collection manager':
                                vm.totalUsers.orderCollection++;
                                break;
                            case 'inventory manager':
                                vm.totalUsers.inventoryManager++;
                                break;
                            case 'qc manager':
                                vm.totalUsers.qcManager++;
                                break;
                            case 'packing & dispatch manager':
                                vm.totalUsers.packing++;
                                break;
                            case 'auditor':
                                vm.totalUsers.audit++;
                                break;
                            case 'dob':
                                vm.totalUsers.dataEntry++;
                                break;
                            case 'gate incharge':
                                vm.totalUsers.gateIncharge++;
                                break;
                            case 'supervisor':
                                vm.totalUsers.supervisor++;
                                break;
                            default:
                                // Handle unknown roles if needed
                                console.warn('Unknown role:', user.role);
                                break;
                        }
                    });

                    setTimeout(() => {
                        vm.dataTable();
                    }, 300);
                })
                .catch((err) => console.log(err));
        },
        deleteFunc(data) {
            this.user = data.id;
        },
        deleteConfirmation() {
            let vm = this;
            vm.btnLoader = true;
            const data = {
                id: this.user
            }
            axios
                .post(this.api_url + "users/delete", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.fetchUsers();
                    $("#deleteConfirmation").modal('hide')
                    return swal({
                        title: "Success",
                        text: 'User Deleted Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        getPercentage(statusCount) {
            if (this.totalUsers.totalUsers === 0) return 0;
            return Math.round((statusCount / this.totalUsers.totalUsers) * 100);
        },
        dataTable() {
            $('#user_list_table').DataTable();
        },
        add(data) {
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "users", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.fetchUsers();
                    vm.$emit('userSaved', true);
                    return swal({
                        title: "Success",
                        text: 'New User Created Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        edit(data) {
            let vm = this;
            vm.editDetails = data
        },
        update(data) {
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "users/update", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.fetchUsers();
                    return swal({
                        title: "Success",
                        text: 'User Updated Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        }
    }
};
</script>
