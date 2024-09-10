<template>
  <div>
    <UserPopup
        :loader="btnLoader"
        @add="add( $event )"
    />

    <UserEditPopup
        :loader="btnLoader"
        :details="editDetails"
        @update="update( $event )"
    />
    <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
        <div class="card card-primary">
          <TableHeader :tableHeader="tableHeader" />

          <div class="card-body">
            <!-- Table -->
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <UserTable
                      :id="table_id"
                      :th="th"
                      :tbody="users"
                      @edit="edit( $event )"
                    />
                  </div>
                </div>
              </div>
            </div>
            <!-- END TABLE -->
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
      api_url : window.location.origin + process.env.MIX_API_URL,
      tableHeader: {
        heading: "Users",
        link: "#",
        target: "#addUser",
      },
      th: ["Sr #","Name", "Email","Role","Allowed IP", "Action"],
      table_id: "user_list_table",
      users : [],
      editDetails : {},
      btnLoader : false
    };
  },
  created() {
    this.fetchUsers();
    setTimeout(()=>{
      $("#pwstrength").pwstrength();
      $("#pwstrength2").pwstrength();
    },2000)
  },
  methods : {
    fetchUsers(){
     let vm = this;
      axios
        .get( this.api_url + "users")
        .then((response) => {
          const results = response.data.response;

          vm.users = results;

           setTimeout(()=>{
            vm.dataTable();
          },300);
        })
        .catch((err) => console.log(err));
    },
    dataTable(){
       $('#user_list_table').DataTable();
    },
    add( data ){
        let vm = this;
        vm.btnLoader = true;
        axios
            .post( this.api_url + "users", data)
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
    edit( data ){
        let vm = this;
        vm.editDetails = data
    },
    update( data ){
        let vm = this;
        vm.btnLoader = true;
        axios
            .post( this.api_url + "users/update", data)
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
