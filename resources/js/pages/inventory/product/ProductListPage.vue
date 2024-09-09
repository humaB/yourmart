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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="product_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr #</th>
                                                            <th>Product Title</th>
                                                            <th>Short Description</th>
                                                            <th>Status</th>
                                                            <th>Added By</th>
                                                            <th>Added Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in products" :key="item.id">
                                                            <td>{{ index + 1 }}</td>
                                                            <td>{{ item.title }}</td>
                                                            <td>{{ item.short_description }}</td>
                                                            <td>
                                                                <span class="badge badge-success" v-if="item.status == 0">Published</span>
                                                                <span class="badge badge-warning" v-if="item.status == 1">Saved in Draft</span>
                                                            </td>
                                                            <td>{{ item.user.name }}</td>
                                                            <td>{{ formatDate(item.created_at) }}</td>
                                                            <td>
                                                                <button data-toggle="modal"
                                                                    data-target="#productDetailView"
                                                                    class="btn btn-info" @click="fetchDetail(item.id)"
                                                                    title="View Details"><i
                                                                        class="fa fa-eye"></i></button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
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
        <AddProductPopup
            :loader="btnLoader"
            :images="selectedImages"
            :heroImage="selectedHeroImage"
            :attributes="attributes"
            :brands="brandsDropDown"
            :categories="categoriesDropDown"
            :colors="colors"
            :sizes="sizes"
            :tags="tags"
            :shippingOptions="shippingOptions"
            :productOptions="productsDropDown"
            @color="colorGallery($event)"
            @submitProduct="submitProduct($event)"
            @searchProduct="searchProduct($event)"
            @close="closeProduct($event)"
        />

        <AddBrand :loader="btnLoader" :brands="brands" @addNewBrand="addNewBrand($event)"
            @editNewBrand="editNewBrand($event)" />
        <AddColor :loader="btnLoader" :colors="colors" @addNewColor="addNewColor($event)"
            @editColor="editColor($event)" />
        <AddCategory :loader="btnLoader" :categories="categories" :parentCategories="parentCategories"
            @addNewCategory="addNewCategory($event)" @editCategory="editCategory($event)" />
        <AddSize :loader="btnLoader" :sizes="sizes" @addNewSize="addNewSize($event)" @editSize="editSize($event)" />
        <AddAttribute :loader="btnLoader" :attributes="attributes" :parentAttributes="parentAttributes"
            @addNewAttribute="addNewAttribute($event)" @editAttribute="editAttribute($event)" />
        <AddTag :loader="btnLoader" :tags="tags" @addNewTag="addNewTag($event)" @editTag="editTag($event)" />


        <ProductDetailView
            :product="details"
            :attributes="attributesDropDown"
            :brands="brandsDropDown"
            :categories="categoriesDropDown"
            :tags="tagsDropDown"
            :shippingOptions="shippingOptions"
            :productNotUpdated="productNotUpdated"
            :productOptions="productsDropDown"
            @updateProduct="updateProduct( $event )"
            @editProductVariant="editProductVariantFun($event)"
            @updateDiscount="updateDiscount( $event )"
            @searchProduct="searchProduct($event)"
            @updateUpSell="updateUpSell( $event )"
            @updateTags="updateTags( $event )"
            @changeStatus="changeStatus( $event )"
            @changeImage="changeHeroImage( $event )"
        />

        <EditProductVariant
            :loader="btnLoader"
            :colors="colorsDropDown"
            :sizes="sizesDropDown"
            :details="editProductVariantData"
            :activeStatus="activeProductVariantStatus"
            @updateProductVariant="updateProductVariant($event)"
            @changeProductVariantStatus="changeProductVariantStatus( $event )"
        />

        <AddProductImage :loader="btnLoader" :colorId="colorId" :type='selectedType' :selectedColor="selectedColor" :colors="colors" :attachments="attachments"
            @addSelectedImages="addSelectedImages($event)"
            @addMoreSelectedImages="addMoreSelectedImages($event)"
            @addSelectedHeroImages="addSelectedHeroImages($event)"
            @uploadAttachment="uploadAttachment($event)"
            @changeSelectedHeroImage="changeSelectedHeroImage( $event )"
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
import ProductDetailView from "../../../components/inventory/product/setting/ProductDetailView.vue";
import EditProductVariant from "../../../components/inventory/product/setting/EditProductVariant.vue";

import moment from "moment";
export default {
    name: 'ProductListPage',
    components: {
        TableHeader,
        AddProductPopup,
        AddBrand,
        AddColor,
        AddCategory,
        AddSize,
        AddAttribute,
        AddTag,
        AddProductImage,
        ProductDetailView,
        EditProductVariant
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Product List",
                link: "#",
                target: "#createProduct",
            },
            th: ["Sr #", "Name", "Email", "Role", "Allowed IP", "Action"],
            table_id: "product_list_table",
            categories: [],
            product: '',
            filter: {
                category: { code: 0, label: "Select from the following" },
                tag: { code: 0, label: "Select from the following" }
            },
            selectedImages: [],
            selectedColor: '',
            selectedHeroImage: {},
            btnLoader: false,
            brands: [],
            brandsDropDown: [],
            colors: [],
            colorsDropDown: [],
            sizes: [],
            sizesDropDown: [],
            tags: [],
            tagsDropDown: [],
            categories: [],
            parentCategories: [],
            categoriesDropDown: [],
            attributes: [],
            parentAttributes: [],
            attributesDropDown: [],
            attachments: [],
            shippingOptions: [],
            products: [],
            productsDropDown: [],
            details: {
                // Replace this with actual data from your API
                title: null,
                slug: null,
                short_description: null,
                brand: { id: '0', name: null },
                category: { id: '0', name: null },
                shipping: { id: '0', name: null },
                hero_image: null,
                product_description: null,
                product_highlight: null,
                warranty: "",
                max_quantity: "",
                quantity_step: "",
                status: 0,
                variations: [
                    {
                        id: 0,
                        sku: "",
                        color: { name: "" },
                        size: { name: "" },
                        regular_price: "0",
                        sale_price: "0",
                        stock: "0",
                        images: [{ id: 0, image_id: "", attachment: { attachment: '' } }]
                    }
                    // More variations...
                ]
            },
            productNotUpdated : false,
            editProductVariantData : {},
            activeProductVariantStatus : '',
            selectedType : '',
            colorId : ''
        };
    },
    created() {
        this.fetchBrands();
        this.fetchCategories();
        this.fetchColors();
        this.fetchSizes();
        this.fetchTags();
        this.fetchAttributes();
        this.fetchAttachments();
        this.fetchShippingOptions();
        this.fetchProducts();
    },
    methods: {
        dataTable() {
            $("#product_table").DataTable();
        },
        clearDataTable() {
            const table = $("#product_table").DataTable();
            table.destroy();
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        addSelectedImages(data) {
            this.selectedImages = data
        },
        addMoreSelectedImages(data){
            let vm = this;
           if( !data ){
            return swal({
                    title: "Required",
                    text: 'Please select image first',
                    icon: "Success",
                    timer: 3000,
                });
           }
           axios
            .post(this.api_url + "inventory/products/color-images/changed", data )
            .then((response) => {
                vm.fetchDetail(this.details.id)
            }).catch((err) => {
                vm.btnLoader = false;
                return swal({
                    title: "Error",
                    text: 'Oops, Something went wrong please try again',
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        changeSelectedHeroImage(data) {
            let vm = this;
           if( !data ){
            return swal({
                    title: "Required",
                    text: 'Please select image first',
                    icon: "Success",
                    timer: 3000,
                });
           }

           const product = {
                'attachment' : data.attachment,
                'id'  : this.details.id
           }

           axios
            .post(this.api_url + "inventory/products/hero-image/changed", product )
            .then((response) => {
                vm.fetchDetail(this.details.id)
            }).catch((err) => {
                vm.btnLoader = false;
                return swal({
                    title: "Error",
                    text: 'Oops, Something went wrong please try again',
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        addSelectedHeroImages(data) {
            this.selectedHeroImage = data
        },
        colorGallery(data) {
            this.selectedColor = data.color;
        },
        changeHeroImage(data) {
            this.selectedColor = data.image;
            this.selectedType = data.type;
            this.colorId = data.id;
        },
        editProductVariantFun(data){
            this.editProductVariantData = data;
            this.activeProductVariantStatus = data.status;
            console.log(this.activeProductVariantStatus);

        },
        changeStatus( data ){
            let vm = this;
             axios
            .post(this.api_url + "inventory/products/status/changed", data )
            .then((response) => {
                vm.fetchProducts();
                return swal({
                    title: "Success",
                    text: 'Status Changed Successfully',
                    icon: "success",
                    timer: 3000,
                });
            }).catch((err) => {
                vm.btnLoader = false;
                return swal({
                    title: "Error",
                    text: 'Oops, Something went wrong please try again',
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        updateUpSell( data ){
            let vm = this;
             axios
            .post(this.api_url + "inventory/products/up-sells/changed", data )
            .then((response) => {
                vm.fetchDetail(data.id)
            }).catch((err) => {
                vm.btnLoader = false;
                return swal({
                    title: "Error",
                    text: 'Oops, Something went wrong please try again',
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        updateTags( data ){
            let vm = this;
             axios
            .post(this.api_url + "inventory/products/tags/changed", data )
            .then((response) => {
                vm.fetchDetail(data.id)
            }).catch((err) => {
                vm.btnLoader = false;
                return swal({
                    title: "Error",
                    text: 'Oops, Something went wrong please try again',
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        updateDiscount(data){
            let vm = this;
             axios
            .post(this.api_url + "inventory/products/discounts/changed", data )
            .then((response) => {

            }).catch((err) => {
                vm.btnLoader = false;
                return swal({
                    title: "Error",
                    text: 'Oops, Something went wrong please try again',
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        updateProductVariant(data){
            let vm = this;
            vm.btnLoader = true;
            vm.clearDataTable();
            axios
                .post(this.api_url + "inventory/products/variations/update", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.fetchDetail(data.details.product_id)
                    return swal({
                        title: "Success",
                        text: 'Product Updated Successfully',
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
        changeProductVariantStatus( data ){
                let vm = this;
                axios
                .post(this.api_url + "inventory/products/variations/change-status", data )
                .then((response) => {
                   vm.activeProductVariantStatus = !vm.activeProductVariantStatus;
                }).catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: 'Oops, Something went wrong please try again',
                        icon: "error",
                        timer: 3000,
                    });
                });
            },
        closeProduct( data ){
            if( data ){
                this.selectedHeroImage = {}
                this.$emit('closeProduct', true);
            }
        },
        fetchProducts() {
            let vm = this;
            axios
                .get(this.api_url + "inventory/products")
                .then((response) => {
                    const results = response.data.response;
                    vm.products = results;
                    setTimeout(() => {
                        vm.dataTable();
                    }, 300);
                }).catch((err) => this.fetchProducts());
        },
        searchProduct( data ) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/drop-down", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.productsDropDown = results;
                });
        },
        fetchDetail(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
        },
        submitProduct(data) {
            let vm = this;
            vm.btnLoader = true;
            vm.clearDataTable();
            axios
                .post(this.api_url + "inventory/products", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.fetchProducts();
                    vm.$emit('productAdded', true);
                    return swal({
                        title: "Success",
                        text: 'Product Added Successfully',
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
        updateProduct(data) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/update", data)
                .then((response) => {
                    vm.productNotUpdated = false
                }).catch((err) => {
                    vm.productNotUpdated = true;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        fetchShippingOptions() {
            let vm = this;
            axios
                .get(this.api_url + "inventory/products/settings/shipping-classes/drop-down")
                .then((response) => {
                    const results = response.data.response;
                    vm.shippingOptions = results;
                }).catch((err) => this.fetchShippingOptions());
        },
        fetchAttachments() {
            let vm = this;
            axios
                .get(this.api_url + "inventory/products/attachments")
                .then((response) => {
                    const results = response.data.response;
                    vm.attachments = results;
                }).catch((err) => this.fetchAttachments());
        },
        uploadAttachment(data) {
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "inventory/products/attachments", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.fetchAttachments();
                    vm.$emit('attachmentSaved', true);
                    return swal({
                        title: "Success",
                        text: 'Media File Uploaded',
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        fetchBrands() {
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
                }).catch((err) => this.fetchBrands());
        },
        addNewBrand(data) {
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
                        text: 'New Brand Added Successfully',
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
        editNewBrand(data) {
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
                        text: 'Brand Updated Successfully',
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
        fetchAttributes() {
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
                }).catch((err) => this.fetchAttributes());
        },
        addNewAttribute(data) {
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
                        text: 'New Attribute Added Successfully',
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
        editAttribute(data) {
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
                        text: 'Attribute Updated Successfully',
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
        fetchCategories() {
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
                }).catch((err) => this.fetchCategories());
        },
        addNewCategory(data) {
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
                .post(this.api_url + "inventory/products/categories/update", data)
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
        fetchColors() {
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
                }).catch((err) => this.fetchColors());
        },
        addNewColor(data) {
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
                        text: 'New Color Added Successfully',
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
        editColor(data) {
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
                        text: 'Color Updated Successfully',
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
        fetchSizes() {
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
                }).catch((err) => this.fetchSizes());
        },
        addNewSize(data) {
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
                        text: 'New Size Added Successfully',
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
        editSize(data) {
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
                        text: 'Size Updated Successfully',
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
        fetchTags() {
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
                }).catch((err) => this.fetchTags());
        },
        addNewTag(data) {
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
                        text: 'New Tag Added Successfully',
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
        editTag(data) {
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
                        text: 'Tag Updated Successfully',
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
        }
    },

}
</script>
