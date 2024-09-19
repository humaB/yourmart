<template>
    <div>
      <!-- Add Courier Popup -->
      <CourierAddPopup
        :loader="btnLoader"
        @add="add($event)"
      />

      <!-- Edit Courier Popup -->
      <CourierEditPopup
        :loader="btnLoader"
        :details="editDetails"
        @update="update($event)"
      />

      <AddCourierCategory
        :categories="categories"
        :loader="btnLoader"
        @addNewCategory="addNewCategory( $event )"
        @editCategory="editCategory($event)"
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
                      <CourierTable
                        :id="table_id"
                        :th="th"
                        :tbody="couriers"
                        @edit="edit($event)"
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
  import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
  import CourierTable from "../../../../components/inventory/product/courier/CourierTable.vue";
  import CourierAddPopup from "../../../../components/inventory/product/courier/CourierAddPopup.vue";
  import CourierEditPopup from "../../../../components/inventory/product/courier/CourierEditPopup.vue";
  import AddCourierCategory from "../../../../components/inventory/product/courier/AddCourierCategory.vue";

  export default {
    name: "CourierPage",
    components: {
      CourierTable,
      TableHeader,
      CourierAddPopup,
      CourierEditPopup,
      AddCourierCategory
    },
    data() {
      return {
        api_url: window.location.origin + process.env.MIX_API_URL,
        tableHeader: {
          heading: "Couriers",
          link: "#",
          target: "#addCourier"
        },
        th: ["Sr #", "Name", "Contact", "Address", "Action"],
        table_id: "courier_list_table",
        couriers: [],
        editDetails: {},
        btnLoader: false,
        categories : []
      };
    },
    created() {
      this.fetchCouriers();
    },
    methods: {
      fetchCouriers() {
        let vm = this;
        axios
          .get(this.api_url + "couriers")
          .then((response) => {
            const results = response.data.response;

            vm.couriers = results;
            vm.dataTable();
          })
          .catch((err) => console.log(err));
      },
      dataTable() {
        if ($.fn.DataTable.isDataTable("#courier_list_table")) {
            $('#courier_list_table').DataTable().destroy();
        }
        setTimeout(function () {
            $("#courier_list_table").DataTable();
        }, 300);
      },
      add(data) {
        let vm = this;
        vm.btnLoader = true;
        axios
          .post(this.api_url + "couriers/add", data)
          .then((response) => {
            vm.btnLoader = false;
            vm.fetchCouriers();
            vm.$emit('courierSaved', true);
            return swal({
              title: "Success",
              text: "New Courier Created Successfully",
              icon: "success",
              timer: 3000
            });
          })
          .catch((err) => {
            vm.btnLoader = false;
            return swal({
              title: "Error",
              text: err.response.data.response[0],
              icon: "error",
              timer: 3000
            });
          });
      },
      edit(data) {
        let vm = this;
        vm.editDetails = data;
      },
      update(data) {
        let vm = this;
        vm.btnLoader = true;
        axios
          .post(this.api_url + "couriers/update", data)
          .then((response) => {
            vm.btnLoader = false;
            vm.fetchCouriers();
            return swal({
              title: "Success",
              text: "Courier Updated Successfully",
              icon: "success",
              timer: 3000
            });
          })
          .catch((err) => {
            vm.btnLoader = false;
            return swal({
              title: "Error",
              text: err.response.data.response[0],
              icon: "error",
              timer: 3000
            });
          });
      },
      addNewCategory(data) {
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "couriers/categories", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchCategories();
                    vm.$emit('categorySaved', true);
                    return swal({
                        title: "Success",
                        text: 'New Category Added Successfully',
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
        editCategory(data) {
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "couriers/categories/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchCategories();
                    vm.$emit('categorySaved', true);
                    return swal({
                        title: "Success",
                        text: 'Category Updated Successfully',
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
    }
  };
  </script>
