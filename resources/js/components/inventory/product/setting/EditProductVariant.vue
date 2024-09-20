<template>
    <!-- Modal -->
    <div class="modal fade" id="editProductVariant" tabindex="-1" role="dialog"
        aria-labelledby="EditProductVariantTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product Variant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6">
                        <label for="color">Color </label>
                        <v-select :options="colors" v-model="color">
                        </v-select>
                    </div>
                    <div class="col-md-6">
                        <label for="color">Size</label>
                        <v-select :options="sizes" v-model="size">
                        </v-select>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label for="regularPrice">Regular Price <span class="text-danger">*</span></label>
                        <input v-model="details.regular_price" type="number" class="form-control" />
                    </div>
                    <div class="col-md-6 mt-2">
                        <label for="salePrice">Sale Price</label>
                        <input v-model="details.sale_price" type="number" class="form-control" />
                    </div>

                    <div class="col-md-12 mt-5">
                        <table class="table table-stried">
                            <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(image, index) in details.images" :key="image.attachment.attachment">
                                    <td>{{ index + 1 }}</td>
                                    <td><img style="width: 10%;" :src="getImageUrl(image.attachment.attachment)"
                                            alt=""></td>
                                    <td><button class="btn btn-danger"
                                            @click="removeVariationImage(index , image.image_id, image.product_variation_id, image.product_id)">Remove</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" :class="activeStatus == 0 ? 'btn btn-danger' : 'btn btn-success'"
                        @click="toggleActivation">
                        {{ activeStatus == 0 ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button type="button" class="btn btn-primary" v-if="!loader"
                        @click="updateProductVariant()">Updated</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Updated</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'EditProductVariant',
    props: ['details', "colors", "sizes", "loader", "activeStatus"],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            color: { code: 0, label: 'Select from the following' },
            size: { code: 0, label: 'Select from the following' },
        }
    },
    methods: {
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + 'assets/img/blank_image.jpg';
            }
            return this.public_url + 'storage/uploads/inventory/products/media/' + imageId;
        },
        updateProductVariant() {
            const data = {
                color: this.color.code,
                size: this.size.code,
                details: this.details
            }

            this.$emit('updateProductVariant', data);
        },
        removeVariationImage(index, attachment, variation, product) {
            this.details.images.splice(index, 1);
            this.$emit('removeVariationImage', { attachment, variation, product })
        },
        toggleActivation() {
            if (this.status) {
                // Logic to deactivate
                this.deactivate();
            } else {
                // Logic to activate
                this.activate();
            }
        },
        activate() {
            // Update the status and/or make an API call to activate
            this.$emit('changeProductVariantStatus', { id: this.details.id, status: 1 });
        },
        deactivate() {
            // Update the status and/or make an API call to deactivate
            this.$emit('changeProductVariantStatus', { id: this.details.id, status: 0 });
        }
    },
    watch: {
        details: {
            immediate: true, // Watch the details prop as soon as it's available
            handler(newDetails) {
                if (newDetails.color_id) {
                    const selectedColor = this.colors.find(color => color.code === newDetails.color_id);

                    if (selectedColor) {
                        this.color = { code: selectedColor.code, label: selectedColor.label };
                    }
                }

                if (newDetails.size_id) {
                    const selectedSize = this.sizes.find(size => size.code === newDetails.size_id);
                    if (selectedSize) {
                        this.size = { code: selectedSize.code, label: selectedSize.label };
                    }
                }
            }
        }
    }

}
</script>
