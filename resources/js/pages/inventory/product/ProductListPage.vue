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
            :attributes="attributes"
            :brands="brandsDropDown"
            :categories="categoriesDropDown"
            :colors="colors"
            :sizes="sizes"
            :tags="tags"
            @color="colorGallery( $event)"
        />
        <AddBrand
            :loader="btnLoader"
            :brands="brands"
            @addNewBrand="addNewBrand( $event )"
            @editNewBrand="editNewBrand( $event )"
        />
        <AddColor
            :loader="btnLoader"
            :colors="colors"
            @addNewColor="addNewColor( $event )"
            @editColor="editColor( $event )"
        />
        <AddCategory
            :loader="btnLoader"
            :categories="categories"
            :parentCategories="parentCategories"
            @addNewCategory="addNewCategory( $event )"
            @editCategory="editCategory( $event )"
        />
        <AddSize
            :loader="btnLoader"
            :sizes="sizes"
            @addNewSize="addNewSize( $event )"
            @editSize="editSize( $event )"
        />
        <AddAttribute
            :loader="btnLoader"
            :attributes="attributes"
            :parentAttributes="parentAttributes"
            @addNewAttribute="addNewAttribute( $event )"
            @editAttribute="editAttribute( $event )"
        />
        <AddTag
            :loader="btnLoader"
            :tags="tags"
            @addNewTag="addNewTag( $event )"
            @editTag="editTag( $event )"
        />
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
                selectedHeroImage : {},
                btnLoader : false,
                brands : [],
                brandsDropDown : [],
                colors : [],
                colorsDropDown : [],
                sizes : [],
                sizesDropDown : [],
                tags : [],
                tagsDropDown : [],
                categories : [],
                parentCategories : [],
                categoriesDropDown : [],
                attributes : [],
                parentAttributes : [],
                attributesDropDown : []
            };
        },
        created(){
            this.fetchBrands();
            this.fetchCategories();
            this.fetchColors();
            this.fetchSizes();
            this.fetchTags();
            this.fetchAttributes();
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
            },
            fetchBrands(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/brands")
                .then((response) => {
                    vm.brands = response.data.response.record.map(item => ({
                        ...item,
                        editable: false, // Add the editable property here,
                        originalData: { ...item } // Keep a copy of the original data
                    }));
                    vm.brandsDropDown = response.data.response.dropdown;
                }).catch((err) => this.fetchBrands() );
            },
            addNewBrand( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/brands", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchBrands();
                    vm.$emit('brandSaved', true);
                    return swal({
                        title: "Success",
                        text:  'New Brand Added Successfully',
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
            editNewBrand( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/brands/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchBrands();
                    vm.$emit('brandSaved', true);
                    return swal({
                        title: "Success",
                        text:  'Brand Updated Successfully',
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
            fetchAttributes(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/attributes")
                .then((response) => {
                    vm.attributesDropDown = response.data.response.dropdown;
                    vm.attributes = response.data.response.record.map(item => ({
                        ...item,
                        editable: false, // Add the editable property here,
                        originalData: { ...item } // Keep a copy of the original data
                    }));
                    vm.parentAttributes = response.data.response.parent;
                }).catch((err) => this.fetchAttributes() );
            },
            addNewAttribute( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/attributes", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchAttributes();
                    vm.$emit('attributeSaved', true);
                    return swal({
                        title: "Success",
                        text:  'New Attribute Added Successfully',
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
            editAttribute( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/attributes/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchAttributes();
                    vm.$emit('attributeSaved', true);
                    return swal({
                        title: "Success",
                        text:  'Attribute Updated Successfully',
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
            fetchCategories(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/categories")
                .then((response) => {
                    vm.categoriesDropDown = response.data.response.dropdown;
                    vm.categories = response.data.response.record.map(item => ({
                        ...item,
                        editable: false, // Add the editable property here,
                        originalData: { ...item } // Keep a copy of the original data
                    }));
                    vm.parentCategories = response.data.response.parent;
                }).catch((err) => this.fetchCategories() );
            },
            addNewCategory( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/categories", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchCategories();
                    vm.$emit('categorySaved', true);
                    return swal({
                        title: "Success",
                        text:  'New Category Added Successfully',
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
            editCategory( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/categories/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchCategories();
                    vm.$emit('categorySaved', true);
                    return swal({
                        title: "Success",
                        text:  'Category Updated Successfully',
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
            fetchColors(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/colors")
                .then((response) => {
                    vm.colorsDropDown = response.data.response.dropdown;
                    vm.colors = response.data.response.record.map(item => ({
                        ...item,
                        editable: false, // Add the editable property here,
                        originalData: { ...item } // Keep a copy of the original data
                    }));
                }).catch((err) => this.fetchColors() );
            },
            addNewColor( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/colors", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchColors();
                    vm.$emit('colorSaved', true);
                    return swal({
                        title: "Success",
                        text:  'New Color Added Successfully',
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
            editColor( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/colors/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchColors();
                    vm.$emit('colorSaved', true);
                    return swal({
                        title: "Success",
                        text:  'Color Updated Successfully',
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
            fetchSizes(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/sizes")
                .then((response) => {
                    vm.sizesDropDown = response.data.response.dropdown;
                    vm.sizes = response.data.response.record.map(item => ({
                        ...item,
                        editable: false, // Add the editable property here,
                        originalData: { ...item } // Keep a copy of the original data
                    }));
                }).catch((err) => this.fetchSizes() );
            },
            addNewSize( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/sizes", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchSizes();
                    vm.$emit('sizeSaved', true);
                    return swal({
                        title: "Success",
                        text:  'New Size Added Successfully',
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
            editSize( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/sizes/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchSizes();
                    vm.$emit('sizeSaved', true);
                    return swal({
                        title: "Success",
                        text:  'Size Updated Successfully',
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
            fetchTags(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/tags")
                .then((response) => {
                    vm.tagsDropDown = response.data.response.dropdown;
                    vm.tags = response.data.response.record.map(item => ({
                        ...item,
                        editable: false, // Add the editable property here,
                        originalData: { ...item } // Keep a copy of the original data
                    }));
                }).catch((err) => this.fetchTags() );
            },
            addNewTag( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/tags", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchTags();
                    vm.$emit('tagSaved', true);
                    return swal({
                        title: "Success",
                        text:  'New Tag Added Successfully',
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
            editTag( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/tags/update", data)
                .then((response) => {
                    vm.btnLoader = false;

                    vm.fetchTags();
                    vm.$emit('tagSaved', true);
                    return swal({
                        title: "Success",
                        text:  'Tag Updated Successfully',
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
        },

    }
</script>
