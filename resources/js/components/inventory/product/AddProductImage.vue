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
                    <div class="col-md-8">
                        <label for=""><b>Upload File</b> <code>If image is not available in gallery</code></label>
                        <input type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for=""><b>Action</b></label><br>
                        <button class="btn btn-primary">Upload Attachment</button>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-12 mt-3">
                        <div class="card">
                          <div class="card-header">
                            <h4>Select from Gallery</h4>
                          </div>
                          <div class="card-body">
                            <div class=" gutters-sm row">
                                <div class="col-3 col-sm-2" v-for="(image, index) in images" :key="index">
                                    <label class="imagecheck mb-4">
                                      <input
                                        v-if="selectedColor != 'Hero'"
                                        type="checkbox"
                                        :value="image"
                                        class="imagecheck-input"
                                        v-model="selectedImagesByColor[selectedColor]"
                                      />
                                      <input
                                      v-else
                                        type="radio"
                                        :value="image"
                                        class="imagecheck-input"
                                        v-model="heroImage"
                                        />
                                      <span class="imagecheck-figure">
                                        <img :src="public_url + image.src" :alt="image.alt" class="imagecheck-image" />
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
    props : ['selectedColor'],
    data () {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            images: [
                { src: 'assets/img/blog/img08.png', alt: 'Image 1' },
                { src: 'assets/img/blog/img01.png', alt: 'Image 2' }, // Add more images as needed
                { src: 'assets/img/blog/img02.png', alt: 'Image 3' }, // Add more images as needed
                { src: 'assets/img/blog/img03.png', alt: 'Image 4' }, // Add more images as needed
                { src: 'assets/img/blog/img04.png', alt: 'Image 5' }, // Add more images as needed
                { src: 'assets/img/blog/img05.png', alt: 'Image 6' }, // Add more images as needed
                { src: 'assets/img/blog/img06.png', alt: 'Image 7' }, // Add more images as needed
            ],
            selectedImagesByColor: {
                Hero : [],
                Blank: [],
                Black: [],
                Blue: [],
                Brown: [],
                Gold: [],
                Gray: [],
                Green: [],
                Indigo: [],
                Orange: [],
                Pink: [],
                Red: [],
                Silver: [],
                Turquoise: [],
                Violet: [],
                White: [],
                Yellow: [],
            },
            selectedImages: [],
            heroImage : {}
        }
    },
    methods : {
        addSelectedImages(){
            if( this.selectedColor == 'Hero'){
                this.$emit('addSelectedHeroImages', this.heroImage);
            }else{
                this.$emit('addSelectedImages', this.selectedImagesByColor);
            }
            return swal({
              title: "Success",
              text:  "Selected Images added",
              icon: "success",
              timer: 3000,
            });
        }
    },

}
</script>
