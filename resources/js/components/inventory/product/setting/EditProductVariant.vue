<template>
<!-- Modal -->
<div class="modal fade" id="editProductVariant" tabindex="-1" role="dialog" aria-labelledby="EditProductVariantTitle" aria-hidden="true">
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
        </div>
        <div class="modal-footer">
            <button
                type="button"
                :class="activeStatus == 0 ? 'btn btn-danger' : 'btn btn-success'"
                @click="toggleActivation">
                {{ activeStatus == 0 ? 'Deactivate' : 'Activate' }}
            </button>
            <button type="button" class="btn btn-primary" v-if="!loader" @click="updateProductVariant()">Updated</button>
            <button type="button" class="btn btn-primary btn-progress disabled" v-else>Updated</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    export default {
        name : 'EditProductVariant',
        props : ['details', "colors", "sizes", "loader", "activeStatus"],
        data(){
            return {
                color : { code : 0 , label : 'Select from the following'},
                size : { code : 0 , label : 'Select from the following'},
            }
        },
        methods : {
        updateProductVariant(){
            const data = {
                color : this.color.code,
                size : this.size.code,
                details : this.details
            }

            this.$emit('updateProductVariant', data);
        },
        toggleActivation() {
        if (this.status) {
            // Logic to deactivate
            console.log("here D");

            this.deactivate();
        } else {
            // Logic to activate
            console.log("here A");
            this.activate();
        }
        },
        activate() {
            // Update the status and/or make an API call to activate
            this.$emit('changeProductVariantStatus', { id : this.details.id,  status : 1 });
        },
        deactivate() {
            // Update the status and/or make an API call to deactivate
            this.$emit('changeProductVariantStatus', { id : this.details.id,  status : 0 });
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
