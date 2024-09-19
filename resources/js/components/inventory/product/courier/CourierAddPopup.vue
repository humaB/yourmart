<template>
  <div>
    <div
      class="modal fade"
      id="addCourier"
      tabindex="-1"
      role="dialog"
      aria-labelledby="courierForm"
      aria-hidden="true"
      data-backdrop="false"
      style="background-color: rgba(0, 0, 0, 0.2)"
    >
      <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Courier</h5>
          </div>
          <div class="modal-body">
            <div class="form-row">

              <div class="form-group form-float col-md-12">
                <div class="form-line">
                  <label class="form-label">Enter Courier Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="name" required />
                </div>
              </div>

              <div class="form-group form-float col-md-12">
                <div class="form-line">
                  <label class="form-label">Enter Contact</label>
                  <input type="text" class="form-control" v-model="contact" />
                </div>
              </div>

              <div class="form-group form-float col-md-12">
                <div class="form-line">
                  <label class="form-label">Enter Address</label>
                  <input type="text" class="form-control" v-model="address" />
                </div>
              </div>

            </div>
          </div>
          <div class="modal-footer bg-whitesmoke br">
            <button type="button" @click="add()" class="btn btn-primary" v-if="!loader">
              Add New Courier
            </button>
            <button type="button" class="btn btn-primary btn-progress disabled" v-else>
              Adding...
            </button>
            <button type="button" @click="close()" class="btn btn-secondary" data-dismiss="modal">
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
  name: "CourierPopup",
  props: ["loader"],
  data() {
    return {
      name: "",
      contact: "",
      address: "",
    };
  },
  mounted() {
    this.$parent.$on("courierSaved", (value) => {
      if (value) {
        this.close();
      }
    });
  },
  methods: {
    add() {
      let vm = this;
      if (vm.name === "") {
        return swal({
          title: "Required",
          text: "Please enter the courier name, thanks",
          icon: "error",
          timer: 3000,
        });
      }
      if (this.contact.length > 0) {
        // Check if the contact number is non-numeric or not 11 digits
        if (this.contact.length !== 11 || isNaN(this.contact)) {
          return swal({
            title: "Required",
            text: "Please enter a valid 11-digit contact number.",
            icon: "error",
            timer: 3000,
          });
        }
      }

      const data = {
        name: this.name,
        contact: this.contact,
        address: this.address,
      };

      vm.$emit("add", data);
    },
    close() {
      this.name = "";
      this.contact = "";
      this.address = "";
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
