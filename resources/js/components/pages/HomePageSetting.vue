<template>
<!-- Modal -->
<div class="modal fade" id="homePageSetting" tabindex="-1" role="dialog" aria-labelledby="homePageSetting" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Home Page Settings</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body row">
            <div class="container mt-4">
                <form>
                    <h3>Headline Settings</h3>
                  <div class="form-group">
                    <label for="button_link">Please HeadLine Text</label>
                    <input type="text" v-model="form.headline.text" class="form-control" placeholder="Enter Headline Text" required>
                  </div>

                  <h3>Shopify Video</h3>
                  <div class="form-group">
                    <label for="button_link">Please add embedded Youtube Link </label>
                    <input type="text" v-model="form.video.text" class="form-control" placeholder="Enter embedded Youtube Link " required>
                  </div>

                  <!-- Image Section -->
                <h3>Banner Image Settings</h3>
                <div class="form-group border border-1 p-2" v-for="(img, index) in form.imageSettings" :key="'banner-'+index + 1">
                    <h4>Banner Image {{ index + 1 }}</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Image Upload -->
                            <label :for="'image_' + index">Upload Image {{ index + 1 }}</label>
                            <input type="file" class="form-control" @change="setImage($event, index)">
                            <code>Dimensions 835 x 415</code>
                        </div>
                        <div class="col-md-6">
                            <img :src="getImage(img.href)" alt="" width="20%">
                        </div>
                    </div>


                    <!-- Button Link -->
                    <label :for="'button_link_' + index">Button Link</label>
                    <input type="text" v-model="img.button_link" class="form-control" placeholder="Enter Button Link" required>

                    <!-- Button Label -->
                    <label :for="'button_label_' + index">Button Label</label>
                    <input type="text" v-model="img.button_label" class="form-control" placeholder="Enter Button Label" required>
                </div>

                <!-- Advertisement Image Section -->
                <h3>Advertisement Image Settings</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Preview</th>
                            <th>Product (Shop Now Redirect )</th>
                            <th>Button Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(advertise, index) in form.advertiseImages" :key="'adv-'+advertise.index">
                            <td>
                                Image {{ advertise.index }}
                            </td>
                            <!-- Image Upload -->
                            <td>
                                <input type="file" class="form-control" @change="setAdvertiseImage($event, index)" accept=".png, .jpg, .jpeg">
                                <code>Dimensions 493 x 316</code>
                            </td>
                            <td>
                                <img :src="getImage(advertise.preview)" alt="" width="20%">
                            </td>
                            <!-- Category Dropdown -->
                            <td>
                                <v-select :options="products" v-model="advertise.category">

                                </v-select>
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="advertise.label">
                            </td>
                        </tr>
                    </tbody>
                </table>

                  <!-- Tag Section -->
                  <h3>Tags Settings</h3>
                  <div v-for="(tag, index) in form.tags" :key="index + 1" class="border p-3 mb-3">
                    <div class="row">
                      <!-- Tag Select -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label :for="'tag_link_' + index">Select Tag</label>
                            <v-select :options="tags" v-model="tag.link">

                            </v-select>
                        </div>
                      </div>

                      <!-- Tag Position -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label :for="'tag_position_' + index">Position</label>
                          <input type="text" v-model="tag.position" class="form-control" placeholder="Enter Position" required>
                        </div>
                      </div>

                      <!-- Remove Tag Button -->
                      <div class="col-md-4 d-flex align-items-center">
                        <button type="button" class="btn btn-primary" @click="addTag"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-danger ml-2" @click="removeTag(index)"><i class="fa fa-trash"></i></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="submitForm()" v-if="!loader" >Update Settings</button>
            <button type="button" class="btn btn-primary btn-progress disabled" v-else>Update Settings</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    export default {
        name :  'HomePageSetting',
        props : ['tags', 'loader', 'settings', 'categories', 'products'],
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
                form: {
                    headline: {
                        text: ''
                    },
                    video: {
                        text: ''
                    },
                    imageSettings: [
                        { index: 1, link: '', button_link: '', button_label: '', href : '' },
                        { index: 2, link: '', button_link: '', button_label: '', href : '' },
                        { index: 3, link: '', button_link: '', button_label: '', href : '' }
                    ],
                    tags: [
                        { link: { code: 0, label: 'Select from the following' }, position: '' } // Default tag
                    ],
                    advertiseImages: [
                        { index : 1, image: '', category: { code: 0, label: 'Select from the following' }, label: '', preview : '' },
                        { index : 2, image: '', category: { code: 0, label: 'Select from the following' }, label: '', preview : '' },
                        { index : 3, image: '', category: { code: 0, label: 'Select from the following' }, label: '', preview : '' },
                        { index : 4, image: '', category: { code: 0, label: 'Select from the following' }, label: '', preview : '' },
                        { index : 5, image: '', category: { code: 0, label: 'Select from the following' }, label: '', preview : '' },
                        { index : 6, image: '', category: { code: 0, label: 'Select from the following' }, label: '', preview : '' }
                    ]
                }
            };
        },
        methods: {
            getImage(imageId) {
            // Check if the image is null
                if (!imageId) {
                    return this.public_url + 'assets/img/blank_image.jpg';
                }
                return this.public_url + 'public/storage/uploads/pages/home/banners/' + imageId;
            },
            setAdvertiseImage(event, index) {
                const file = event.target.files[0];
                if (file) {
                    this.$set(this.form.advertiseImages, index, {
                        index : this.form.advertiseImages[index].index,
                        image: file, // Set the image link
                        category: this.form.advertiseImages[index].category, // Keep the existing button link
                        label: this.form.advertiseImages[index].label,
                    });
                }
            },
            setImage(event, index) {
                const file = event.target.files[0]; // Get the uploaded file
                if (file) {
                        // Update the specific image link in the form
                        this.$set(this.form.imageSettings, index, {
                            index : index + 1,
                            link: file, // Set the image link
                            button_link: this.form.imageSettings[index].button_link, // Keep the existing button link
                            button_label: this.form.imageSettings[index].button_label,
                        });
                }
            },
            addTag() {
                this.form.tags.push( { link: { code : 0 , label : 'Select from the following'}, position: '' });
            },
            removeTag(index) {
            this.form.tags.splice(index, 1);
            },
            submitForm() {
                // Perform form validation and submit form data to the backend
                const formData = new FormData();

                // Add image data
                formData.append('head_line', this.form.headline.text);
                formData.append('video_link', this.form.video.text);

                // Add image data
                this.form.imageSettings.forEach((img, index) => {
                    formData.append(`image[${index}][index]`,img.index );
                    formData.append(`image[${index}][link]`, img.link); // Append the image link
                    formData.append(`image[${index}][button_link]`, img.button_link); // Append the button link
                    formData.append(`image[${index}][button_label]`, img.button_label);
                });


                // Add Advertisement  image data
                this.form.advertiseImages.forEach((img, index) => {
                    formData.append(`advertiseImage[${index}][index]`,img.index );
                    formData.append(`advertiseImage[${index}][image]`, img.image); // Append the image link
                    formData.append(`advertiseImage[${index}][category]`, img.category.code); // Append the button link
                    formData.append(`advertiseImage[${index}][label]`, img.label); // Append the button link
                });

                // Add tags data
                this.form.tags.forEach((tag, index) => {
                    formData.append(`tags[${index}][link]`, tag.link.code);
                    formData.append(`tags[${index}][position]`, tag.position);
                });

                this.$emit('updateHomePage' , formData)
            },
            updateImages(newSettings) {
                const images = [];

                // Iterate through 3 image types: image-1, image-2, image-3
                for (let i = 1; i <= 3; i++) {
                    const imageType = `image-${i}`;

                    // Try to find the corresponding image setting
                    const imageSetting = newSettings.find(setting => setting.type === imageType);

                    // If an image is found, use its data; otherwise, fill with empty values
                    images.push({
                        index : i - 1,
                        button_link: imageSetting ? imageSetting.position : '', // Button link or empty string
                        button_label: imageSetting ? imageSetting.label : '', // Button link or empty string
                        href :  imageSetting ? imageSetting.attachment : ''
                    });
                }

                // Update the form's images field
                this.$set(this.form, 'imageSettings', images);
            },
            updateAdvertismentImages(newSettings) {
                const images = [];

                // Iterate through 3 image types: image-1, image-2, image-3
                for (let i = 1; i <= 6; i++) {
                    const imageType = `adv-image-${i}`;

                    // Try to find the corresponding image setting
                    const imageSetting = newSettings.find(setting => setting.type === imageType);

                    // Find the corresponding category name by tag_id
                    const category = this.products.find(category => category.code === imageSetting?.tag_id) || { code: 0, label: 'Select from the following' };


                    // If an image is found, use its data; otherwise, fill with empty values
                    images.push({
                        index : i,
                        image : '',
                        preview : imageSetting ? imageSetting.attachment : '',
                        label: imageSetting ? imageSetting.label : '', // Button link or empty string
                        category: {
                            code: category.code, // Category code from categories array or 0
                            label: category.label // Category label from categories array or default
                        }
                    });
                }


                // Update the form's images field
                this.$set(this.form, 'advertiseImages', images);
            },
            updateTags(newSettings) {

                if (newSettings.length > 0) {
                    const tags = newSettings
                        .filter(setting => setting.type === 'tag')
                        .map(setting => {
                            const tagName = this.tags.find(tag => tag.code === setting.tag_id)?.label;

                            return {
                                link: {
                                    code: setting.tag_id,
                                    label: tagName || `Tag ${setting.tag_id}` // Fallback to default label if not found
                                },
                                position: setting.position
                            };
                        });

                    // Update the form's tags
                    if(tags.length > 0){
                        this.$set(this.form, 'tags', tags);
                    }
                }
            },
        },
        watch: {
            settings(newSettings) {
                if (newSettings.length > 0) {
                    // Check if there is at least one setting of type 'headline'
                    const headLine = newSettings
                        .filter(setting => setting.type === 'headline');

                    // Proceed only if there are headline settings
                    if (headLine.length > 0) {
                        const mappedHeadlines = headLine.map(setting => {
                            return {
                                text: setting.position
                            };
                        });

                        // Use this.$set to update the form
                        this.$set(this.form, 'headline', ...mappedHeadlines);
                    }
                }
                if (newSettings.length > 0) {
                    // Check if there is at least one setting of type 'headline'
                    const headLine = newSettings
                        .filter(setting => setting.type === 'video');

                    // Proceed only if there are headline settings
                    if (headLine.length > 0) {
                        const mappedHeadlines = headLine.map(setting => {
                            return {
                                text: setting.position
                            };
                        });

                        // Use this.$set to update the form
                        this.$set(this.form, 'video', ...mappedHeadlines);
                    }
                }
                this.updateTags(newSettings);
                this.updateImages(newSettings);
                this.updateAdvertismentImages(newSettings)
            }
        }
    }
</script>
