  <template>
  <div>
    <div
      class="modal fade"
      id="addUser"
      tabindex="-1"
      role="dialog"
      aria-labelledby="groupForm"
      aria-hidden="true"
      data-backdrop="false"
      style="background-color: rgba(0, 0, 0, 0.2)"
    >
      <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New User</h5>
          </div>
    <div class="modal-body">
      <div class="form-row">

        <div class="form-group form-float col-md-12">
          <div class="form-line">
            <label class="form-label"
              >Select Role
              <span class="text-danger">*</span></label
            >
            <select class="form-control" v-model="role">
              <option selected>Select from the followings...</option>
              <option value="admin">Admin</option>
              <option value="dob">Data Entry Operator</option>
              <option value="gate incharge">Gate Incharge</option>
              <option value="order collection manager">Order Collection Manager</option>
              <option value="inventory manager">Inventory Manager</option>
              <option value="qc manager">QC Manger</option>
              <option value="packing & dispatch manager">Packing & Dispatch Manager</option>
              <option value="autidor">Auditor</option>
            </select>
          </div>
        </div>

        <div class="form-group form-float col-md-12">
          <div class="form-line">
            <label class="form-label"
              >Enter Name <span class="text-danger">*</span></label
            >
            <input type="text" class="form-control" v-model="name" required />
          </div>
        </div>

        <div class="form-group form-float col-md-12">
          <div class="form-line">
            <label class="form-label"
              >Enter Email
              <span class="text-danger">*</span></label
            >
            <div class="form-group">
              <div class="input-group mb-2">
                <input
                  type="text"
                  class="form-control text-right"
                  v-model="email"
                  placeholder="Email Address"
                  autocomplete="anything"
                />
                <div class="input-group-append">
                  <div class="input-group-text">@ecomm.com</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group form-float col-md-12">
          <div class="form-line">
            <label class="form-label"
              >Set Password <span class="text-danger">*</span></label
            >
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-lock"></i>
                </div>
              </div>
              <input
                type="password"
                v-model="password"
                class="form-control"
                id="pwstrength"
                data-indicator="pwindicator"
              />
            </div>
            <div id="pwindicator" class="pwindicator">
              <div class="bar"></div>
              <div class="label"></div>
            </div>

            <small id="passwordHelpBlock" class="form-text text-muted">
              Password must be 8-20 characters long, contain letters and
              numbers, and must not contain spaces, special characters, or
              emoji.
            </small>
          </div>
        </div>

      </div>
    </div>
    <div class="modal-footer bg-whitesmoke br">
      <button type="button" @click="add()" class="btn btn-primary" v-if="!loader">
        Add New User Account
      </button>
      <button type="button" class="btn btn-primary btn-progress disabled" v-else>
        Add New User Account
      </button>
      <button
        type="button"
        @click="close()"
        class="btn btn-secondary"
        data-dismiss="modal"
      >
        Close
      </button>
    </div>
  </div>
</div>
      </div>
    </div>
</template>

<script>
export default {
  name: "UserPopup",
  props: ["accounts", "accountChilds", "fields", "loader"],
  data() {
    return {
      api_url: window.location.origin + process.env.MIX_API_URL,
      name: "",
      email: "",
      password: "",
      role: "Select from the followings...",
    };
  },
  mounted() {
        this.$parent.$on("userSaved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
  methods: {
    add() {
      let vm = this;
      if (
        vm.name == "" ||
        vm.email == "" ||
        vm.password == "" ||
        vm.role == ""
      ) {
        return swal({
                title: "Required",
                text: "Please add all the required fields, thanks",
                icon: "error",
                timer: 3000,
        });
      }


      const data = {
        name: this.name,
        email: this.email + "@ecomm.com",
        password: this.password,
        role: this.role,
      };

      vm.$emit("add", data);

    },
    close() {
      let vm = this;
      vm.name = "";
      vm.email = "";
      vm.password = "";
      vm.role = "Select from the followings...";
    },
  },
};
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
