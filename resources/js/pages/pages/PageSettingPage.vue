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
                                           <tr>
                                                <td>1</td>
                                                <td>Home Page</td>
                                                <td>
                                                    <button class="btn btn-info" data-toggle="modal" data-target="#homePageSetting"><i class="fa fa-eye"></i></button>
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

          <HomePageSetting
            :tags="tags"
            :loader="homePageLoader"
            :settings="homePagesSettings"
            @updateHomePage="updateHomePage( $event )"
          />


    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../components/table/TableHeaderComponent.vue";
import HomePageSetting from "../../components/pages/HomePageSetting.vue";


    export  default {
        name : 'PageSettingPage',
        components : {
            TableHeader,
            BulletListLoader,
            HomePageSetting
        },
        data() {
            return {
                api_url : window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Page Setting's"
                },
                th: ["Sr #","Page Name", "Action"],
                table_id: "moq_table",
                tags : [],
                homePageLoader : false,
                loader : false,
                homePagesSettings : []
            };
        },
        created() {
            this.fetchTags();
            setTimeout(()=>{
                this.fetchHomePageSetting();
            },300)
        },
        methods : {
            onlyNumber($event) {
                let keyCode = $event.keyCode ? $event.keyCode : $event.which;
                if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                    // 46 is dot
                    $event.preventDefault();
                }
            },
            fetchTags() {
                let vm = this;
                axios
                    .get(this.api_url + "inventory/products/tags")
                    .then((response) => {
                        vm.tags = response.data.response.dropdown;
                    }).catch((err) => this.fetchTags());
            },
            fetchHomePageSetting(){
                let vm = this;
                axios
                    .get(this.api_url + "pages/settings/home-page")
                    .then((response) => {
                        vm.homePagesSettings = response.data.response;

                    }).catch((err) => {
                        vm.fetchHomePageSetting();
                    });
            },
            updateHomePage( data ){
                let vm = this;
                vm.homePageLoader = true;
                axios
                    .post(this.api_url + "pages/settings/home-page", data)
                    .then((response) => {
                        vm.homePageLoader = false;
                        return swal({
                            title: "Success",
                            text: 'Setting Updated',
                            icon: "success",
                            timer: 3000,
                        });
                    }).catch((err) => {
                        vm.homePageLoader = false;
                    });
            }
        }
    }
</script>
