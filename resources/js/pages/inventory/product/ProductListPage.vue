<template>
    <div>

        <div class="row">
          <div class="col-12 col-md-12 col-lg-12">
            <div class="card card-primary">
              <TableHeader :tableHeader="tableHeader" />

              <div class="card-body row">
                <!-- Table -->

                    <div class="col-md-3">
                        <label for=""><b>Filter By Category</b></label>
                        <v-select :options="categories" v-model="filter.category">

                        </v-select>
                    </div>
                    <div class="col-md-3">
                        <label for=""><b>Filter By Tag</b></label>
                        <v-select :options="tags" v-model="filter.tag">

                        </v-select>
                    </div>

                    <div class="col-md-3">
                        <label for=""><b>Search by Name</b></label>
                       <input type="text" class="form-control" v-model="product">
                    </div>
                    <div class="col-md-3">
                        <label for=""><b>Action</b></label><br>
                        <button class="btn btn-primary w-100"><i class="fa fa-filter"></i>Filter</button>
                    </div>
                  <div class="col-md-12 mt-3">
                    <div class="card">
                      <div class="card-body">

                      </div>
                    </div>

                </div>
                <!-- END TABLE -->
              </div>
            </div>
          </div>
        </div>
        <AddProductPopup
            :images="selectedImages"
            :heroImage="selectedHeroImage"
            @color="colorGallery( $event)"
        />
        <AddBrand />
        <AddColor />
        <AddCategory />
        <AddSize />
        <AddAttribute />
        <AddTag />
        <AddProductImage
            :selectedColor="selectedColor"
            @addSelectedImages="addSelectedImages( $event )"
            @addSelectedHeroImages="addSelectedHeroImages($event)"
        />
      </div>
</template>
<script>
import Vue from "vue";
import vSelect from "vue-select";

import "vue-select/dist/vue-select.css";
Vue.component("v-select", vSelect);

import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import AddProductPopup from "../../../components/inventory/product/AddProductPopup.vue";
import AddBrand from "../../../components/inventory/product/AddBrand.vue";
import AddColor from "../../../components/inventory/product/AddColor.vue";
import AddCategory from "../../../components/inventory/product/AddCategory.vue";
import AddSize from "../../../components/inventory/product/AddSize.vue";
import AddAttribute from "../../../components/inventory/product/AddAttribute.vue";
import AddTag from "../../../components/inventory/product/AddTag.vue";
import AddProductImage from "../../../components/inventory/product/AddProductImage.vue";

    export default {
        name : 'ProductListPage',
        components : {
            TableHeader,
            AddProductPopup,
            AddBrand,
            AddColor,
            AddCategory,
            AddSize,
            AddAttribute,
            AddTag,
            AddProductImage
        },
        data() {
            return {
                api_url : window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Product List",
                    link: "#",
                    target: "#createProduct",
                },
                th: ["Sr #","Name", "Email","Role","Allowed IP", "Action"],
                table_id: "product_list_table",
                categories : [],
                tags : [],
                product : '',
                filter : {
                    category : { code : 0, label : "Select from the following"},
                    tag : { code : 0, label : "Select from the following"}
                },
                selectedImages : [],
                selectedColor : '',
                selectedHeroImage : {}
            };
        },
        methods : {
            addSelectedImages( data ){
                this.selectedImages = data
            },
            addSelectedHeroImages( data ){
                this.selectedHeroImage = data
            },
            colorGallery( data ){
                this.selectedColor = data.color;
            }
        }
    }
</script>
