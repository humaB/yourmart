<template>

    <!-- Modal -->
    <div class="modal fade" id="uploadProductImage" tabindex="-1" role="dialog" aria-labelledby="uploadProductImage"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 90%;">
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
                        <input type="file" class="form-control" @change="setImage($event)" multiple>
                        <code>Maximum upload file size: 25 MB</code><br>
                        <code>Recommended dimension for Size is 800 x 800 </code><br>
                        <code>Recommended Size for Image is 0.5MB </code>

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
                                    <!-- Search Form -->
                                <form class="card-header-form">
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search by title or alt"
                                        v-model="searchQuery"
                                    />
                                </form>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-9">
                                    <div class=" gutters-sm row" id="gallery-scroll">
                                        <div class="col-3 col-sm-2" v-for="(image, index) in filteredImages" :key="index">
                                            <label class="imagecheck mb-4">
                                                <input
                                                    v-if="selectedColor != 'Hero' && selectedColor != 'Video'"
                                                    type="checkbox"
                                                    :value="image"
                                                    class="imagecheck-input"
                                                    v-model="selectedImagesByColor[selectedColor]"
                                                    @change="setSelectedImage(image)"
                                                />
                                                <input
                                                    v-else
                                                    type="radio"
                                                    :value="image"
                                                    class="imagecheck-input"
                                                    v-model="heroImage"
                                                    @change="setSelectedImage(image)"
                                                />
                                                <span class="imagecheck-figure">
                                                    <video v-if="isVideo(image.attachment)"
                                                    :src="public_url + 'storage/uploads/inventory/products/media/' + image.attachment"
                                                    controls
                                                    class="media-video"
                                                >
                                                    Your browser does not support the video tag.
                                                </video>
                                                    <img v-else
                                                        :src="public_url + 'storage/uploads/inventory/products/media/' + image.attachment"
                                                        :alt="image.alt"
                                                        class="imagecheck-image"
                                                    />

                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 border-1 py-2">
                                    <h5>Selected Image</h5>
                                    <p v-if="selectedImage">
                                        <strong>Image:</strong>
                                        <img
                                            :src="public_url + 'storage/uploads/inventory/products/media/' + selectedImage.attachment"
                                            :alt="selectedImage.alt"
                                            class="img-thumbnail"
                                            width="100"
                                            @load="getImageDimensions"
                                            ref="selectedImage"
                                        />
                                               <!-- Display Image Dimensions -->
                                    </p>
                                    <p v-if="imageDimensions"><strong>Dimensions:</strong> {{ imageDimensions.width }} x {{ imageDimensions.height }} pixels</p>
                                     <!-- Display Image Size -->
                                    <p v-if="imageSize"><strong>Size:</strong> {{ imageSize }}</p>
                                    <p v-else>No image selected</p>

                                    <p v-if="selectedImage"><strong>Title:</strong> <input v-if="selectedImage" type="text" name="" id="" v-model="selectedImage.title" class="form-control">
                                    <p v-if="selectedImage"><strong>ALT:</strong> </p><input v-if="selectedImage" type="text" name="" id="" v-model="selectedImage.alt" class="form-control">
                                    <p v-if="selectedImage"><strong>Caption:</strong> <textarea v-if="selectedImage"  v-model="selectedImage.caption" class="form-control"></textarea>
                                    <p v-if="selectedImage"><strong>Description:</strong> </p><textarea v-if="selectedImage"  v-model="selectedImage.description" class="form-control"></textarea>

                                    <!-- Delete Button -->
                                     <div class="text-right mt-2" v-if="selectedImage">
                                         <button v-if="!loader" class="btn btn-primary" @click="updateImageData()">
                                             Update
                                         </button>
                                         <button v-else class="btn btn-primary btn-progress disabled">
                                            Update
                                        </button>
                                         <button v-if="!loader" class="btn btn-danger" @click="deleteImage()">
                                             Delete
                                         </button>
                                         <button v-else class="btn btn-danger btn-progress disabled">
                                            Delete
                                        </button>
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
    props: ['selectedColor', 'colors', 'loader', 'attachments', 'type', 'colorId', 'imageAlt'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            selectedImagesByColor: {},
            selectedImages: [],
            heroImage: {},
            images: [],
            alt: '',
            selectedImage: null,
            imageDimensions: null, // To store the image dimensions
            imageSize: null, // To store the image size
            searchQuery: '', // Search input query
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
    computed: {
        filteredImages() {
            if (!this.searchQuery) {
                // If no search query, return all images
                return this.attachments;
            }
            // Convert search query to lowercase for case-insensitive search
            const query = this.searchQuery.toLowerCase();

            // Filter images based on alt or attachment (title)
            return this.attachments.filter(image => {
                 // Ensure image properties are not null and handle undefined values
                const alt = (image.alt || '').toLowerCase();
                const attachment = (image.title || '').toLowerCase();

                return (
                    alt.includes(query) ||
                    attachment.includes(query)
                );
            });
        }
    },
    methods: {
        updateImageData(){
            if( !this.selectedImage ){
                return swal({
                    title: "Required",
                    text: "Please select image first",
                    icon: "error",
                    timer: 3000,
                });
            }
            this.$emit('updateImageData', this.selectedImage);
        },
        deleteImage(){
            if( !this.selectedImage ){
                return swal({
                    title: "Required",
                    text: "Please select image first",
                    icon: "error",
                    timer: 3000,
                });
            }
            this.$emit('deleteImage', this.selectedImage);
        },
        setSelectedImage(image) {
            this.selectedImage = image;
            this.imageDimensions = null; // Reset dimensions when a new image is selected
            this.imageSize = null; // Reset size when a new image is selected
            this.getImageSize(); // Fetch image size
        },
        getImageDimensions() {
            const img = this.$refs.selectedImage; // Reference to the image element
            if (img) {
                this.imageDimensions = {
                    width: img.naturalWidth,
                    height: img.naturalHeight
                };
            }
        },
        async getImageSize() {
            const imageUrl = this.public_url + 'storage/uploads/inventory/products/media/' + this.selectedImage.attachment;

            // Fetch image metadata
            try {
                const response = await fetch(imageUrl, { method: 'HEAD' });
                const contentLength = response.headers.get('content-length'); // Get the file size in bytes

                if (contentLength) {
                    this.imageSize = this.formatBytes(parseInt(contentLength, 10));
                }
            } catch (error) {
                console.error('Error fetching image size:', error);
            }
        },
        isVideo(fileName) {
            // Define video file extensions
            const videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'mkv', 'flv', 'webm'];

            // Extract the file extension
            const extension = fileName.split('.').pop().toLowerCase();

            // Check if the extension matches any video extension
            return videoExtensions.includes(extension);
        },
        formatBytes(bytes) {
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes === 0) return '0 Bytes';
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
        },
        addSelectedImages() {
            if (this.selectedColor == 'Hero') {
                if (this.type && this.type == 'edit') {
                    this.$emit('changeSelectedHeroImage', this.heroImage);
                } else {
                    this.$emit('addSelectedHeroImages', this.heroImage);
                }
            }else if (this.selectedColor == 'Video') {
                if (this.type && this.type == 'edit') {
                    this.$emit('changeSelectedVideo', this.heroImage);
                }
            }
             else {
                if (this.type && this.type == 'colorEdit') {
                    this.$emit('addMoreSelectedImages', { images: this.selectedImagesByColor, id: this.colorId });
                } else {
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
        setImage(event) {
            // Clear the existing images array to prevent appending on multiple selects
            this.images = [];
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                this.images.push(files[i]); // Store each selected file in the images array
            }
        },

        uploadAttachment() {
            const vm = this;
            const fd = new FormData();

            // Check if any images are selected
            if (vm.images.length === 0) {
                return swal({
                    title: "Error",
                    text: "Please select image first, thanks",
                    icon: "error",
                    timer: 3000,
                });
            }

            // Append each image to FormData as 'images[]'
            vm.images.forEach((image, index) => {
                fd.append('images[]', image); // 'images[]' allows multiple files to be sent as an array
            });

            // Append the alt text to FormData
            fd.append('alt', vm.alt);

            // Emit the FormData to the parent component or handle it with an API request
            vm.$emit('uploadAttachment', fd);
        },
        close() {
            this.image = '';
            this.alt = '';
            $("input[type=file]").val('');
        },
        reset() {
            this.selectedImagesByColor = {};
            this.heroImage = {};

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
            this.alt = this.imageAlt;
            console.log(this.imageAlt);

            // Call the method to handle color updates
            this.updateSelectedImagesByColor(newColors);
        },
        imageAlt(newAlt) {
            this.alt = this.imageAlt;
        }
    },

}
</script>

<style scoped>
.media-video {
    width: 100%;
    height: auto;
    border-radius: 5px;
}
</style>
