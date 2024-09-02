<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
              <div class="card card-primary">
                <TableHeader :tableHeader="tableHeader" />

                <div class="card-body row">
                  <!-- Table -->
                    <div class="col-md-12 mt-3">
                      <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-body table-responsive" v-if="loader">
                                    <bullet-list-loader :width="250"> </bullet-list-loader>
                                  </div>
                                <div class="col-md-12" v-else>
                                    <table class="table table-bordered" :id="table_id">
                                        <thead>
                                            <tr>
                                                <th v-for="(item, index) in th" :key="item">{{ item }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in records" :key="item.id">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.name }}</td>
                                                <td>{{ item.description }}</td>
                                                <td>
                                                    <span v-if="item.is_active == 0" class="badge badge-success">Active</span>
                                                    <span v-if="item.is_active == 1" class="badge badge-danger">In Active</span>
                                                </td>
                                                <td>{{ item.user ? item.user.name : '' }}</td>
                                                <td>{{ formatDate(item.created_at) }}</td>
                                                <td>
                                                    <button class="btn btn-info" @click="fetchDetail( item.id, item.is_active )" data-toggle="modal" data-target="#shippingClassDetails" title="View Details"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-primary" @click="fetchForEditDetail( item.id )" title="Edit Details" data-toggle="modal" data-target="#editShippingClassDetails"><i class="fa fa-edit"></i></button>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                      </div>

                  </div>
                  <!-- END TABLE -->
                </div>
              </div>
            </div>
          </div>

          <AddShippingClass
            :loader="btnLoader"
            @addNewClass="addNewClass( $event )"
          />

          <EditShippingClass
            :loader="btnLoader"
            :details="editDetails"
            @editClass="editClass( $event )"
          />

          <ShippingClassDetail
            :details="details"
            :status="activeStatus"
            @changeStatus="changeStatus( $event )"
          />
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import AddShippingClass from "../../../../components/inventory/product/setting/AddShippingClass.vue";
import ShippingClassDetail from "../../../../components/inventory/product/setting/ShippingClassDetail.vue";
import EditShippingClass from "../../../../components/inventory/product/setting/EditShippingClass.vue";

    export  default {
        name : 'ProductShippingClassesPage',
        components : {
            TableHeader,
            BulletListLoader,
            AddShippingClass,
            ShippingClassDetail,
            EditShippingClass
        },
        data() {
            return {
                api_url : window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Shipping Classes",
                    link: "#",
                    target: "#addShippingClass",
                },
                th: ["Sr #","Name", "Description", "Status", "Added by", "Added Date", "Action"],
                table_id: "moq_table",
                guestQuantity : 0,
                registeredQuantity : 0,
                btnLoader : false,
                records : [],
                loader : true,
                details : {},
                activeStatus : '',
                editDetails : {}
            };
        },
        created(){
            this.fetchRecord();
        },
        methods : {
            formatDate(date) {
                 return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            fetchRecord(){
                let vm = this;

                vm.loader = false;
                axios
                .get(this.api_url + "inventory/products/settings/shipping-classes")
                .then((response) => {
                    vm.records = response.data.response

                    setTimeout(() => {
                        vm.dataTable();
                    }, 300);
                });
            },
            fetchDetail( id, status ){
                let vm = this;
                vm.activeStatus = status;

                axios
                .post(this.api_url + "inventory/products/settings/shipping-classes/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
            },
            fetchForEditDetail( id ){
                let vm = this;

                axios
                .post(this.api_url + "inventory/products/settings/shipping-classes/edit-details", { id })
                .then((response) => {
                    vm.editDetails = response.data.response[0]
                });
            },
            changeStatus( data ){
                let vm = this;
                axios
                .post(this.api_url + "inventory/products/settings/shipping-classes/change-status", data )
                .then((response) => {
                   vm.fetchRecord();
                   vm.activeStatus = !vm.activeStatus;
                });
            },
            onlyNumber($event) {
                let keyCode = $event.keyCode ? $event.keyCode : $event.which;
                if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                    // 46 is dot
                    $event.preventDefault();
                }
            },
            dataTable(){
                $("#moq_table").DataTable();
            },
            clearDataTable() {
                const table = $("#moq_table").DataTable();
                table.destroy();
            },
            addNewClass( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/settings/shipping-classes", data)
                .then((response) => {

                    vm.clearDataTable()
                    vm.btnLoader = false;

                    vm.fetchRecord();
                    vm.$emit('saved', true);
                    return swal({
                        title: "Success",
                        text:  'Shipping Classes Added Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text:  err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
            },
            editClass( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/settings/shipping-classes/edit", data)
                .then((response) => {

                    vm.clearDataTable()
                    vm.btnLoader = false;

                    vm.fetchRecord();
               
                    return swal({
                        title: "Success",
                        text:  'Shipping Classes Updated Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text:  err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
            }
        }
    }
</script>
