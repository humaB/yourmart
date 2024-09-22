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

                  <!-- Image Section -->
                  <h3>Image Settings</h3>
                  <div class="form-group">
                    <label for="image_link">Upload Image</label>
                    <input type="file"  class="form-control" @change="setImage">
                    <code>Dimensions 835 x 415</code>
                  </div>
                  <div class="form-group">
                    <label for="button_link">Button Link</label>
                    <input type="text" v-model="form.image.button_link" class="form-control" placeholder="Enter Button Link" required>
                  </div>

                  <!-- Tag Section -->
                  <h3>Tags Settings</h3>
                  <div v-for="(tag, index) in form.tags" :key="index" class="border p-3 mb-3">
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
        props : ['tags', 'loader', 'settings'],
        data() {
            return {
            form: {
                headline : {
                    text : ''
                },
                image: {
                    link: '',
                    button_link: ''
                },
                tags: [
                    { link: { code : 0 , label : 'Select from the following'}, position: '' } // Default tag
                ]
            }
            };
        },

        methods: {
            setImage(event) {
                this.form.image.link = event.target.files[0];
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
                formData.append('image_link', this.form.image.link);
                formData.append('button_link', this.form.image.button_link);
                formData.append('head_line', this.form.headline.text);

                // Add tags data
                this.form.tags.forEach((tag, index) => {
                    formData.append(`tags[${index}][link]`, tag.link.code);
                    formData.append(`tags[${index}][position]`, tag.position);
                });

                this.$emit('updateHomePage' , formData)
            }
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
                        this.$set(this.form, 'headline', mappedHeadlines);
                    }
                }
                if(newSettings.length > 0){
                    setTimeout(() => {
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

                        this.$set(this.form, 'tags', tags);
                    }, 300);
                }
            }
        }
    }
</script>
