<template>

    <!-- Modal -->
    <div class="modal fade" id="uploadProductImage" tabindex="-1" role="dialog" aria-labelledby="uploadProductImage"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Upload / Select from Gallery </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-5">
                        <label for=""><b>Upload File</b> <code> ( If image is not available in gallery )</code></label>
                        <input type="file" class="form-control" @change="setImage($event)">
                        <code>Maximum upload file size: 25 MB</code>
                        <code>Recommended Size for Size is 800 x 800 </code>

                    </div>
                    <div class="col-md-5">
                        <label for=""><b>ALT</b></label>
                        <input type="text" class="form-control" v-model="alt">
                        <code>( Attachment information )</code>
                    </div>
                    <div class="col-md-2">
                        <label for=""><b>Action</b></label><br>
                        <button type="button" class="btn btn-primary" v-if="!loader" @click="uploadAttachment()">Upload
                            Attachment</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Upload
                            Attachment</button>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-12 mt-3">
                        <div class="card">
                            <div class="card-header">
                                <h4>Select from Gallery</h4>
                            </div>
                            <div class="card-body">
                                <div class=" gutters-sm row" id="gallery-scroll">
                                    <div class="col-3 col-sm-2" v-for="(image, index) in attachments" :key="index">
                                        <label class="imagecheck mb-4">
                                            <input v-if="selectedColor != 'Hero'" type="checkbox" :value="image"
                                                class="imagecheck-input"
                                                v-model="selectedImagesByColor[selectedColor]" />
                                            <input v-else type="radio" :value="image" class="imagecheck-input"
                                                v-model="heroImage" />
                                            <span class="imagecheck-figure">
                                                <img :src="public_url + 'storage/uploads/inventory/products/media/'+ image.attachment"
                                                    :alt="image.alt" class="imagecheck-image" />
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="addSelectedImages()">Add Selected</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddProductImage',
    props: ['selectedColor', 'colors', 'loader', 'attachments' ,'type' , 'colorId'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            selectedImagesByColor: {},
            selectedImages: [],
            heroImage: {},
            image: '',
            alt: ''
        }
    },
    updated() {
        this.$nextTick(() => {
            $("#gallery-scroll").css({
                height: 500,
                overflow: 'auto'
            });
        });
    },
    mounted() {
        this.$parent.$on("attachmentSaved", (value) => {
            if (value) {
                this.close();
            }
        });
        this.$parent.$on("closeProduct", (value) => {
            if (value) {
                this.reset();
            }
        });
    },
    methods: {
        setImage(event) {
            this.image = event.target.files[0];
        },
        addSelectedImages() {
            if (this.selectedColor == 'Hero') {
                if( this.type && this.type == 'edit'){
                    this.$emit('changeSelectedHeroImage', this.heroImage);
                }else{
                    this.$emit('addSelectedHeroImages', this.heroImage);
                }
            } else {
                if( this.type && this.type == 'colorEdit'){
                    this.$emit('addMoreSelectedImages', { images : this.selectedImagesByColor, id : this.colorId});
                }else{
                    this.$emit('addSelectedImages', this.selectedImagesByColor);
                }
            }
            return swal({
                title: "Success",
                text: "Selected Images added",
                icon: "success",
                timer: 3000,
            });
        },
        uploadAttachment() {
            let vm = this;
            const fd = new FormData();
            if (vm.image == '') {
                return swal({
                    title: "Error",
                    text: "Please select image first, thanks",
                    icon: "error",
                    timer: 3000,
                });
            }
            fd.append('image', vm.image);
            fd.append('alt', vm.alt);

            vm.$emit('uploadAttachment', fd)
        },
        close() {
            this.image = '';
            this.alt = '';
            $("input[type=file]").val('');
        },
        reset() {
            this.selectedImagesByColor= {};
            this.heroImage= {};

            this.updateSelectedImagesByColor(this.colors);
        },
        updateSelectedImagesByColor(newColors) {
            // Reset the selectedImagesByColor object
            this.$set(this.selectedImagesByColor, 'Blank', []);

            // Populate selectedImagesByColor based on the new colors
            newColors.forEach((color) => {
                this.$set(this.selectedImagesByColor, color.name, []); // Use $set to ensure reactivity
            });
        }
    },
    watch: {
        colors(newColors) {
            // Call the method to handle color updates
            this.updateSelectedImagesByColor(newColors);
        }
    },

}
</script>
