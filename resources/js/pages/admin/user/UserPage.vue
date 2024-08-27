<template>
  <div>
    <UserPopup />
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

export default {
  name: "UserPage",
  components: {
    TableHeader,
    UserTable,
    UserPopup
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
      users : []
    };
  },
  created() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("_token");
      this.fetchUsers();
    setTimeout(()=>{
      $("#pwstrength").pwstrength();
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
  }
};
</script>