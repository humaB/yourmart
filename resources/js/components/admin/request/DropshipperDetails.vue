<template>
    <div class="modal fade" id="dropShipperDetail" tabindex="-1" role="dialog" aria-labelledby="dropShipperDetailTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dropShipperDetailTitle">Details of {{ details.full_name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Basic Information</h5>
                            <hr>
                        </div>
                        <div class="col-md-3 col-6 b-r">
                          <strong>Full Name</strong>
                          <br>
                          <p class="text-muted">{{ details.full_name }}</p>
                        </div>
                        <div class="col-md-3 col-6 b-r">
                          <strong>Mobile</strong>
                          <br>
                          <p class="text-muted">{{ details.whatsapp_number }}</p>
                        </div>
                        <div class="col-md-3 col-6 b-r">
                          <strong>Email</strong>
                          <br>
                          <p class="text-muted">{{ details.email }}</p>
                        </div>
                        <div class="col-md-3 col-6">
                          <strong>Location</strong>
                          <br>
                          <p class="text-muted">{{ details.city ? details.city.name : '-' }}</p>
                        </div>

                        <div class="col-md-3 col-6">
                            <strong>CNIC</strong>
                            <br>
                            <p class="text-muted">{{ details.cnic_number }}</p>
                          </div>

                          <div class="col-md-3 col-6">
                            <strong>Address</strong>
                            <br>
                            <p class="text-muted">{{ details.address }}</p>
                          </div>

                          <div class="col-md-12">
                            <h5>Store Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-3 col-6">
                            <strong>Store Name:</strong>
                            <br>
                            <p class="text-muted">{{ details.store_name || 'N/A' }}</p>
                          </div>

                          <div class="col-md-3 col-6">
                              <strong>Store URL:</strong>
                              <br>
                              <p class="text-muted">{{ details.store_url || 'N/A' }}</p>
                            </div>

                            <div class="col-md-3 col-6">
                              <strong>Social Media Link:</strong>
                              <br>
                              <p class="text-muted">{{ details.social_media_profile_link || 'N/A' }}</p>
                            </div>

                            <div class="col-md-12 col-12">
                                <strong>Business Description:</strong>
                                <br>
                                <p class="text-muted">{{ details.business_description || 'N/A' }}</p>
                              </div>

                              <div class="col-md-12">
                                <h5>Account Information</h5>
                                <hr>
                            </div>

                            <div class="col-md-3 col-6">
                                <strong>Bank Name:</strong>
                                <br>
                                <p class="text-muted">{{ details.bank ? details.bank.name : '-' }}</p>
                              </div>

                              <div class="col-md-3 col-6">
                                  <strong>Account Number:</strong>
                                  <br>
                                  <p class="text-muted">{{ details.account_number || 'N/A' }}</p>
                                </div>

                                <div class="col-md-3 col-6">
                                  <strong>Account Title</strong>
                                  <br>
                                  <p class="text-muted">{{ details.account_title || 'N/A' }}</p>
                                </div>

                      </div>


                    <!-- You can also display uploaded images here if needed -->
                    <div v-if="details.profile_image" class="mt-5">
                        <p><strong>Profile Image:</strong></p>
                        <img :src="`http://localhost/dropshipping-frontend/public/storeage/uploads/dropshipper/${details.profile_image}`" alt="Profile Image" class="img-fluid">
                    </div>
                    <div v-if="details.cnic_front_image" class="mt-5">
                        <p><strong>CNIC Front Image:</strong></p>
                        <img :src="`http://localhost/dropshipping-frontend/public/storeage/uploads/dropshipper/${details.cnic_front_image}`" alt="CNIC Front Image" class="img-fluid">
                    </div>
                    <div v-if="details.cnic_back_image" class="mt-5">
                        <p><strong>CNIC Back Image:</strong></p>
                        <img :src="`http://localhost/dropshipping-frontend/public/storeage/uploads/dropshipper/${details.cnic_back_image}`" alt="CNIC Back Image" class="img-fluid">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="decision('approve')" v-if="!loader">Approve</button>
                    <button type="button" class="btn btn-success btn-progress disabled"  v-else>Approve</button>


                    <button type="button" class="btn btn-danger" @click="decision('reject')">Reject</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DropshipperDetails',
    props: ['details', 'loader'],
    methods : {
      decision(action){
        this.$emit('decision', { id : this.details.id , action })
      }
    }
}
</script>

<style scoped>
/* Add any specific styling for the modal content here */
</style>
