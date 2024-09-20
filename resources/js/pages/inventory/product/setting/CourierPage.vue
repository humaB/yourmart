<template>
    <div>
      <!-- Add Courier Popup -->
      <CourierAddPopup
        :loader="btnLoader"
        @addNewCourier="add($event)"
        @fetchRange="fetchRange( $event )"
      />

      <!-- Edit Courier Popup -->
      <CourierEditPopup
        :loader="btnLoader"
        :details="editDetails"
        @update="update($event)"
      />


      <CourierDetailPopup
        :details="courierDetails"
        :loader="btnLoader"
        :ranges="ranges"
        @addNewCourier="add($event)"
        @fetchRange="fetchRange( $event )"
      />

      <AddCourierCategory
        :loader="btnLoader"
        :details="courierDetails"
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
                        @fetchDetails="fetchDetails($event)"
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
  import CourierDetailPopup from "../../../../components/inventory/product/courier/CourierDetailPopup.vue";

  export default {
    name: "CourierPage",
    components: {
      CourierTable,
      TableHeader,
      CourierAddPopup,
      CourierEditPopup,
      AddCourierCategory,
      CourierDetailPopup
    },
    data() {
      return {
        api_url: window.location.origin + process.env.MIX_API_URL,
        tableHeader: {
          heading: "Couriers",
          link: "#",
          target: "#addCourier"
        },
        th: ["Sr #", "Courier Name", "Contact Person", "Contact", "Action"],
        table_id: "courier_list_table",
        couriers: [],
        editDetails: {},
        btnLoader: false,
        courierDetails : {},
        ranges : []
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

      fetchDetails( data ){
        let vm = this;
        axios
          .post(this.api_url + "couriers/details", data)
          .then((response) => {
            const results = response.data.response[0];
            vm.courierDetails = results;
            // Extract categories and store in data
            results.categories.forEach(category => {
                this.ranges[category.id] = category.ranges;
            });
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
            vm.ranges = [];
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

                    vm.fetchDetails(data);

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
    }
  };
  </script>
