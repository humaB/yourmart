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
                                <div class="col-md-4    ">
                                    <h5>For Guest User's</h5>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" @keypress="onlyNumber" v-model="guestQuantity" placeholder="Enter quantity">
                                    <code class="form-text">
                                        <b>Usage:</b> Enter a numeric value here to ensure that guest users cannot place an order for fewer items than the specified quantity. This helps in managing order volumes and maintaining order efficiency.
                                    </code>
                                </div>

                                <div class="col-md-4 mt-5">
                                    <h5>For Registered User's</h5>
                                </div>
                                <div class="col-md-8 mt-5">
                                    <input type="text" class="form-control" @keypress="onlyNumber" v-model="registeredQuantity" placeholder="Enter quantity">
                                    <code class="form-text">
                                       <b>Purpose:</b> Specifies the minimum quantity of items that a registered user must order.
                                    </code>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <button class="btn btn-primary w-100" @click="updateQuantity()" v-if="!btnLoader">Update Quantities</button>
                                    <button class="btn btn-primary disabled btn-progress w-100" v-else>Update Quantities</button>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="card-body table-responsive" v-if="loader">
                                    <bullet-list-loader :width="250"> </bullet-list-loader>
                                  </div>
                                <div class="col-md-12" v-else>
                                    <h5>History Log</h5>
                                    <table class="table table-bordered" :id="table_id">
                                        <thead>
                                            <tr>
                                                <th v-for="(item, index) in th" :key="item">{{ item }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in records" :key="item.id">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.type == 0 ? 'For Guest Users' : 'For Registered User'}}</td>
                                                <td>{{ item.quantity }}</td>
                                                <td>
                                                    <span v-if="item.status == 0" class="badge badge-success">Active</span>
                                                    <span v-if="item.status == 1" class="badge badge-danger">In Active</span>
                                                </td>
                                                <td>{{ item.user ? item.user.name : '' }}</td>
                                                <td>{{ formatDate(item.created_at) }}</td>
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
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
    export  default {
        name : 'MinimumOrderQuantityPage',
        components : {
            TableHeader,
            BulletListLoader
        },
        data() {
            return {
                api_url : window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Minimum Order Quantity",
                },
                th: ["Sr #","Label", "Quantity","Status", "Added by", "Added Date"],
                table_id: "moq_table",
                guestQuantity : 0,
                registeredQuantity : 0,
                btnLoader : false,
                records : [],
                loader : true
            };
        },
        created(){
            this.fetchHistory();
        },
        methods : {
            formatDate(date) {
                 return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            fetchHistory(){
                let vm = this;

                vm.loader = false;
                axios
                .get(this.api_url + "inventory/products/settings/minimum-order-quantities")
                .then((response) => {
                    vm.records = response.data.response

                    setTimeout(() => {
                        vm.dataTable();
                    }, 300);
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
            updateQuantity(){
                let vm = this;
                const data = {
                    guest : vm.guestQuantity,
                    registered : vm.registeredQuantity
                }

                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/settings/minimum-order-quantities", data)
                .then((response) => {

                    vm.clearDataTable()
                    vm.btnLoader = false;
                    vm.registeredQuantity = 0;
                    vm.guestQuantity = 0;

                    vm.fetchHistory();
                    return swal({
                        title: "Success",
                        text:  'Setting Updated Successfully',
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
