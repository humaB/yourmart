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

        <UserAdminForm />

      </div>
    </div>
    <div class="modal-footer bg-whitesmoke br">
      <button type="button" @click="add()" class="btn btn-primary">
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
import UserAdminForm from './UserAdminForm.vue';
export default {
  name: "UserPopup",
  props: ["accounts", "accountChilds", "fields"],
  components : [
    UserAdminForm
  ],
  data() {
    return {
      api_url: window.location.origin + process.env.MIX_API_URL,
      name: "",
      email: "",
      CNIC: "",
      cnicMax: 13,
      password: "",
      employee: [],
      role: "Select from the followings...",
      error: false,
      errorText: "",
      noRecord: false,
      showCNICinput: false,
      showLevelForRecovery: false,
      level: 0,
      success: false,
    };
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


        if ( typeof vm.employee.id == 'undefined') {
          return swal({
                title: "Required",
                text: "Please enter CNIC number and press enter",
                icon: "error",
                timer: 3000,
        });
        }

      if (vm.role == "recovery agent" || vm.role == "Recovery Head") {
        if (typeof vm.employee.id == 'undefined' || vm.level == "") {
          return swal({
                title: "Required",
                text: "Please Select Level for recovery user",
                icon: "error",
                timer: 3000,
        });
        }
      }

      vm.error = false;

      const data = {
        name: this.name,
        email: this.email + "@gch.com",
        password: this.password,
        role: this.role,
        employeeID: this.employee.id ? this.employee.id : 0,
        level: this.level ? this.level : 0,
      };

      vm.$emit("add", data);

      setTimeout(() => {
        vm.success = false;
        vm.error = false;
        vm.errorText = "";
        vm.name = "";
        vm.email = "";
        vm.password = "";
        vm.noRecord = false;
        vm.showCNICinput = false;
        vm.employee = [];
        vm.employeeID = "";
        vm.CNIC = "";
        vm.level = 0;
        vm.showLevelForRecovery = false;
        vm.role = "Select from the followings...";

        if (vm.fields.catchError === true) {
          return swal({
                title: "Error",
                text: vm.fields.catchErrorText,
                icon: "error",
                timer: 3000,
        });
        }
        return swal({
                title: "Success",
                text: "New User Successfully Created",
                icon: "success",
                timer: 3000,
        });
      }, 500);
    },
    showCNIC(event) {
      this.showCNICinput = true;
    },
    onlyNumber($event) {
      let keyCode = $event.keyCode ? $event.keyCode : $event.which;
      if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
        // 46 is dot
        $event.preventDefault();
      }
    },
    onEnter() {
      if (this.CNIC.length == 13) {
        let vm = this;
        axios
          .get(this.api_url + "users/employees/cnic/" + this.CNIC)
          .then((response) => {
            const results = response.data.response;
            if (results == "[]") {
              this.noRecord = false;
              return;
            }
            this.noRecord = true;
            vm.employee = results[0];
            vm.name = results[0].name;
          })
          .catch((err) => console.log(err));
      } else {
        return;
      }
    },
    close() {
      let vm = this;
      vm.success = false;
      vm.error = false;
      vm.errorText = "";
      vm.name = "";
      vm.email = "";
      vm.password = "";
      vm.noRecord = false;
      vm.showCNICinput = false;
      vm.employee = [];
      vm.employeeID = "";
      vm.CNIC = "";
      vm.level = 0;
      vm.showLevelForRecovery = false;
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
