<template>
    <!-- Modal -->
    <div class="modal fade" id="createProduct" tabindex="-1" role="dialog" aria-labelledby="createProduct"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7 card mx-auto">
                            <div class="row">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <h5>Product Title <span class="text-danger">*</span></h5>
                                        <input type="text" class="form-control" placeholder="Add Product Title"
                                            v-model="title">
                                        <code>Length ( {{ titleLength }} / 150 )</code>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <h5>Short Description <span class="text-danger">*</span></h5>
                                        <input type="text" class="form-control"
                                            placeholder="Please enter short description of product"
                                            v-model="shortDescription">
                                        <code>Length ( {{ shortDescriptionLength }} / 150 )</code>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <h5>Product Description <span class="text-danger">*</span></h5>
                                        <textarea class="summernote"></textarea>
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-12 mt-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Product Gallery</h4>
                                                <a href="#" data-toggle="modal" data-target="#uploadProductImage"
                                                    @click="addImage('Blank')" class="btn btn-outline-primary"
                                                    style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                            <div class="card-body">
                                                <div class="gallery">
                                                    <div v-for="(image, index) in images['Blank']" :key="index"
                                                        class="gallery-item" :data-image="public_url + image.src"
                                                        :data-title="image.alt" :href="public_url + image.src"
                                                        :title="image.alt"
                                                        :style="{ backgroundImage: 'url(' + public_url + image.src + ')' }">
                                                        <!-- Remove Icon -->
                                                        <button @click="removeImage(index)" class="remove-icon">
                                                            &#10006;
                                                            <!-- This represents an "X" icon; you can use an actual icon from a library like FontAwesome -->
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cloned Product Galleries -->
                                    <div v-for="color in selectedColors" :key="color"
                                        class="col-12 col-sm-6 col-lg-12 mt-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>{{ color }} Product Gallery</h4>
                                                <a href="#" data-toggle="modal" data-target="#uploadProductImage"
                                                    @click="addImage(color)" class="btn btn-outline-primary"
                                                    style="height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                            <div class="card-body">
                                                <div class="gallery" v-if="images[color]">
                                                    <div v-for="(image, index) in images[color]" :key="index"
                                                        class="gallery-item" :data-image="public_url + image.src"
                                                        :data-title="image.alt" :href="public_url + image.src"
                                                        :title="image.alt"
                                                        :style="{ backgroundImage: 'url(' + public_url + image.src + ')' }">
                                                        <!-- Remove Icon -->
                                                        <button @click="removeImage(color, index)" class="remove-icon">
                                                            &#10006;
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <h5>Product Highlights <span class="text-danger">( optional )</span></h5>
                                        <textarea class="summernote"></textarea>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <h5>Warranty <span class="text-danger">( optional )</span></h5>
                                        <input type="text" class="form-control">
                                    </div>
                                        <div class="col-md-12 row mt-3">
                                           <div class="col-md-3">
                                            <h5>Weight (kg)</h5>
                                        </div>
                                         <div class="col-md-9">
                                            <input type="text" class="form-control" v-model="weight">
                                        </div>
                                        </div>
                                         <div class="col-md-12 row">
                                           <div class="col-md-3">
                                            <h5>Dimension (cm)</h5>
                                        </div>
                                         <div class="col-md-9 d-flex justify-content-between">
                                            <input type="text" class="form-control" placeholder="length">
                                            <input type="text" class="form-control" placeholder="width">
                                            <input type="text" class="form-control" placeholder="height">
                                        </div>
                                        </div>

                                       <div class="col-md-12 mt-4">
                                        <h5>Shipping Class <span class="text-danger">( optional )</span></h5>
                                        <v-select :options="shippingOptions" v-model="selectedShipping">
                                        </v-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 card mx-auto">
                            <div class="row">
                                <div class="card-body">

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label"><b>Brand</b> <span class="text-danger">(
                                                        optional )</span></label>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#" data-toggle="modal" data-target="#addBrand"
                                                    class="btn btn-outline-primary"
                                                    style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                        </div>
                                        <v-select :options="brands" v-model="brand">
                                        </v-select>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label"><b>Category</b> <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#" data-toggle="modal" data-target="#addCategory"
                                                    class="btn btn-outline-primary"
                                                    style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                        </div>
                                        <v-select :options="categories" v-model="category">
                                        </v-select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div id="accordion">
                                            <div class="accordion">
                                                <div class="accordion-header" @click="toggleAttribute('selectColor')"
                                                    role="button" data-toggle="collapse" data-target="#panel-body-1"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectColor ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectColor ? 'Hide' : 'Show' }} Colors
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-1"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addColor"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="color in colors" :key="color">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox" :value="color"
                                                                    v-model="selectedColors"> <label>{{ color }}</label>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="accordion">
                                                <div class="accordion-header" @click="toggleAttribute('selectSize')"
                                                    role="button" data-toggle="collapse" data-target="#panel-body-2"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectSize ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectSize ? 'Hide' : 'Show' }} Sizes
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-2"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addSize"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="item in sizes" :key="item">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox"> <label>{{ item }}</label>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="accordion">
                                                <div class="accordion-header" @click="toggleAttribute('selectTag')"
                                                    role="button" data-toggle="collapse" data-target="#panel-body-3"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectTag ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectTag ? 'Hide' : 'Show' }} Tags
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-3"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addTag"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="item in tags" :key="item">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox"> <label>{{ item }}</label>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="accordion">
                                                <div class="accordion-header"
                                                    @click="toggleAttribute('selectOtherAttributes')" role="button"
                                                    data-toggle="collapse" data-target="#panel-body-4"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectOtherAttributes ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectOtherAttributes ? 'Hide' : 'Show' }} Other Attributes
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-4"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addAttribute"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="(item, index) in attributes" :key="item">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox"
                                                                    v-model="selectedAttributes[index].checked">
                                                                <label>{{ item }}</label>
                                                            </div>
                                                            <!-- Conditionally render input field if checkbox is checked -->
                                                            <div v-if="selectedAttributes[index].checked">
                                                                <input type="text"
                                                                    v-model="selectedAttributes[index].value"
                                                                    class="form-control"
                                                                    :placeholder="'Enter details for ' + item">
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 my-3">
                                            <div class="card card-primary row">
                                                <div class="col-md-9 mx-auto pt-3">
                                                    <label for="" v-if="!heroImage.src"><b>Select Product
                                                            Image</b></label>
                                                    <img v-else :src="public_url + heroImage.src" style="width: 100%"
                                                        alt="">
                                                </div>
                                                <div class="col-md-12 my-2">
                                                    <button class="btn btn-primary w-100" data-toggle="modal"
                                                        data-target="#uploadProductImage"
                                                        @click="addImage('Hero')">Select Product Image</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <h5>Regular Price <span class="text-danger">*</span></h5>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <h5>Sale Price</h5>
                                            <input type="text" class="form-control">
                                            <code><a href="#" @click="schedule.status = !schedule.status">click to add schedule</a></code>
                                        </div>
                                       <div class="col-md-12 row border p-3" v-if="schedule.status">
                                        <div class="col-md-6">
                                            <label for=""><b>Valid From</b></label>
                                            <input type="date" class="form-control" v-model="schedule.from">
                                        </div>
                                        <div class="col-md-6">
                                            <label for=""><b>Valid Till</b></label>
                                            <input type="date" class="form-control"  v-model="schedule.to">
                                        </div>
                                        </div>
                                        <div class="col-md-12 mt-5">
                                            <h5>Discount per Quantity <code>( optional )</code></h5>
                                        </div>
                                        <div class="form-group form-float col-md-12 row"
                                            v-for="(item, index) in discountPerQty" :key="index">

                                            <div class="col-md-5">
                                                <label for="">Quantity</label>
                                                <input type="text" class="form-control">
                                                <code>Example : 20 - 50</code>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="">Price</label>
                                                <input type="text" class="form-control">
                                                <code>1,463</code>
                                            </div>
                                            <div class="col-md-2 ">
                                                <label for="">Action</label><br>
                                                <div class="d-flex align-item-center">
                                                    <i class="btn btn-primary fa fa-plus" style="height:35px"
                                                        @click="addDiscountQuantityOneRow(index)"></i>
                                                    <i class="btn btn-danger fa fa-trash ml-1" style="height:35px"
                                                        @click="removeDiscountQuantityRow(index)"
                                                        v-if="discountPerQty.length > 1"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                 <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon"><i class="fas fa-calendar"></i> Scheduled Publish Date
                      </a>
                    <button type="button" class="btn btn-primary"><i class="fa fa-paper-plane" aria-hidden="true"></i>
                        Publish</button>
                    <button type="button" class="btn btn-warning text-dark">
                        <i class="fas fa-save"></i>
                        Save in draft
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddProductPopup',
    props: ["images", "heroImage"],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            brands: ["Apple", "Nike", "Samsung", "Gucci"],
            brand: { code: 0, label: "Select from the following" },
            categories: ["Electronics", "Wireless", "Bluetooth", "Apple", "Modern", "Office", "Unisex", "Black", "Plastic"],
            category: { code: 0, label: "Select from the following" },
            title: '',
            shortDescription: '',
            selectOtherAttributes: false,
            selectColor: false,
            selectSize: false,
            selectTag: false,
            shippingOptions: [
                "Standard Shipping: Lightweight items with regular packing.",
                "Fragile Shipping: Delicate items needing extra care.",
                "Oversized Shipping: Large items requiring special packing.",
                "Express Shipping: Fast delivery for urgent items.",
                "Economy Shipping: Cost-effective, slower delivery.",
                "Perishable Shipping: Items needing temperature control.",
                "Heavy-Duty Shipping: Very heavy items with special handling.",
                "Hazardous Shipping: Items requiring specific regulations.",
                "International Shipping: Overseas shipments with customs.",
                "Luxury Shipping: High-value items with secure, premium packaging."
            ],
            selectedShipping : { code : 0 , label : 'Select from the following'},
            colors: [
                "Red",
                "Orange",
                "Yellow",
                "Green",
                "Blue",
                "Indigo",
                "Violet",
                "Black",
                "White",
                "Gray",
                "Pink",
                "Brown",
                "Turquoise",
                "Silver",
                "Gold"
            ],
            sizes: ['XL', 'X', 'M', 'S'],
            tags: ['Outdoor', 'Travel', 'Office', 'Workout'],
            attributes: ["Battery Type", "Battery Capacity", "Battery Life", "Screen Size", "Resolution", "Screen Type"],
            selectedAttributes: [],
            discountPerQty: [{ quantity: 0, price: 0 }],
            selectedColors: [], // Tracks selected colors
            colorImages: {}, // Stores images by color
            schedule : {
                status :  false,
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
            weight : 0
        }
    },
    created() {
        this.selectedAttributes = this.attributes.map(() => ({
            checked: false,
            value: ''
        }));
    },
    computed: {
        titleLength() {
            return this.title.length > 150 ? 150 : this.title.length;
        },
        shortDescriptionLength() {
            return this.shortDescription.length > 150 ? 150 : this.shortDescription.length;
        }
    },
    methods: {
        addDiscountQuantityOneRow(index) {
            if (this.discountPerQty.length == 3) {
                return swal({
                    title: "Error",
                    text: "Can't add more than 3",
                    icon: "error",
                    timer: 3000,
                });
            }
            this.discountPerQty.push({ quantity: 0, price: 0 });
        },
        removeDiscountQuantityRow(index) {
            this.discountPerQty.splice(index, 1); // Remove the item at the given index
        },
        toggleAttribute(attribute) {
            this[attribute] = !this[attribute];
        },
        addImage(color) {
            this.$emit('color', { color })
        },
        removeImage(color, index) {
            if (this.images[color]) {
                this.images[color].splice(index, 1);
            }
        },
    },
}
</script>
<style scoped>
.remove-icon {
    position: absolute;
    top: 3px;
    right: 3px;
    background-color: rgba(255, 255, 255, 0.7);
    border: none;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    font-size: 10px;
    line-height: 1;
    color: #ff0000;
    /* red color for the icon */
}
</style>
