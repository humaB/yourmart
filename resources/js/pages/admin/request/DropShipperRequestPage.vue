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
                                                <td>{{ item.full_name }}</td>
                                                <td>{{ item.email }}</td>
                                                <td>
                                                    <span v-if="item.status == 0" class="badge badge-warning">Pending</span>
                                                    <span v-if="item.status == 1" class="badge badge-success">Approved</span>
                                                    <span v-if="item.status == 2" class="badge badge-danger">Rejected</span>
                                                </td>
                                                <td>{{ formatDate(item.created_at) }}</td>
                                                <td>
                                                    <button class="btn btn-info" @click="fetchDetail( item.id )" data-toggle="modal" data-target="#dropShipperDetail" title="View Details"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-dark" @click="printRequest( item.id )" title="Print"><i class="fa fa-print"></i></button>

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

          <DropshipperDetails
            :details="details"
            :loader="btnLoader"
            @decision="decision($event)"
          />

                <!-- Summary PRINT -->
        <form method="POST" :action="public_url+'/requests/dropshippers/pdf'" target="_blank" ref="requestForm">
            <input type="hidden" name="_token" :value="csrf" >
            <input type="hidden" name="id" :value="id" >
        </form>
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import DropshipperDetails from "../../../components/admin/request/DropshipperDetails.vue";

    export  default {
        name : 'DropShipperRequestPage',
        components : {
            TableHeader,
            BulletListLoader,
            DropshipperDetails
        },
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
                api_url : window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Dropshipper Request's",
                },
                th: ["Sr #","Name", "email", "Status", "Added Date", "Action"],
                table_id: "moq_table",
                guestQuantity : 0,
                registeredQuantity : 0,
                btnLoader : false,
                records : [],
                loader : true,
                details : {},
                activeStatus : '',
                editDetails : {},
                id : ''
            };
        },
        created(){
            this.csrf = $('meta[name=csrf-token]').attr('content');
            this.fetchRecord();
        },
        methods : {
            printRequest( id ){
                this.id = id;
                const form = this.$refs.requestForm;
                setTimeout(()=>{
                    form.submit();
                },500)
            },
            formatDate(date) {
                 return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            decision( data ){
                let vm = this;

                vm.btnLoader = true;
                axios
                .post(this.api_url + "dropshippers/decisions",data)
                .then((response) => {
                    vm.fetchRecord();
                    $(".modal").click();
                    this.btnLoader = false;
                    return swal({
                        title: "Success",
                        text:  'Decision Made Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                }).catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
            },
            fetchRecord(){
                let vm = this;

                vm.loader = false;
                axios
                .get(this.api_url + "dropshippers")
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
                .post(this.api_url + "dropshippers/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
            },
            dataTable(){
                $("#moq_table").DataTable();
            },
            clearDataTable() {
                const table = $("#moq_table").DataTable();
                table.destroy();
            },
        }
    }
</script>
