<template>
    <div class="modal fade" id="dropShipperDetail" tabindex="-1" role="dialog" aria-labelledby="dropShipperDetailTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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
                          <p v-if="!editMode" class="text-muted">{{ details.full_name }}</p>
                          <input v-else type="text" v-model="details.full_name" class="form-control">
                        </div>

                        <div class="col-md-3 col-6 b-r">
                          <strong>Mobile</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.whatsapp_number }}</p>
                          <input v-else type="text" v-model="details.whatsapp_number" class="form-control">
                        </div>

                        <div class="col-md-3 col-6 b-r">
                          <strong>Email</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.email }}</p>
                          <input v-else type="email" v-model="details.email" class="form-control">
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Location</strong>
                          <br>
                          <p class="text-muted">{{ details.city ? details.city.name : '-' }}</p>

                        </div>

                        <div class="col-md-3 col-6">
                          <strong>CNIC</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.cnic_number }}</p>
                          <input v-else type="text" v-model="details.cnic_number" class="form-control">
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Address</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.address }}</p>
                          <input v-else type="text" v-model="details.address" class="form-control">
                        </div>

                        <div class="col-md-12">
                          <h5>Store Information</h5>
                          <hr>
                        </div>

                        <div class="col-md-12 row" v-for="(shop, index) in details.shops" :key="shop.id">
                          <div class="col-md-12">
                            <h5>Shop {{ index + 1 }} Details</h5>
                            <hr>
                          </div>

                          <div class="col-md-3 col-6">
                            <strong>Store Name:</strong>
                            <br>
                            <p class="text-muted">{{ shop.store_name || 'N/A' }}</p>
                          </div>

                          <div class="col-md-3 col-6">
                            <strong>Store URL:</strong>
                            <br>
                            <p v-if="!editMode" class="text-muted">{{ shop.store_url || 'N/A' }}</p>
                            <input v-else type="text" v-model="shop.store_url" class="form-control">
                          </div>

                          <div class="col-md-3 col-6">
                            <strong>Social Media Link:</strong>
                            <br>
                            <p v-if="!editMode" class="text-muted">{{ shop.social_media_profile_link || 'N/A' }}</p>
                            <input v-else type="text" v-model="shop.social_media_profile_link" class="form-control">
                          </div>

                          <div class="col-md-12 col-12">
                            <strong>Business Description:</strong>
                            <br>
                            <p v-if="!editMode" class="text-muted">{{ shop.business_description || 'N/A' }}</p>
                            <textarea v-else v-model="shop.business_description" class="form-control"></textarea>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <h5>Account Information</h5>
                          <hr>
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Bank Name:</strong>
                          <br>
                          <p class="text-muted" v-if="!editMode">{{ details.bank ? details.bank.name : '-' }}</p>
                          <v-select :options="banks" v-model="details.bank.name" v-else></v-select>
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Account Title</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.account_title || 'N/A' }}</p>
                          <input v-else type="text" v-model="details.account_title" class="form-control">
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Account Number:</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.account_number || 'N/A' }}</p>
                          <input v-else type="text" v-model="details.account_number" class="form-control">
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Account IBAN</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.account_iban || 'N/A' }}</p>
                          <input v-else type="text" v-model="details.account_iban" class="form-control">
                        </div>

                        <div class="col-md-3 col-6">
                          <strong>Payment Cycle</strong>
                          <br>
                          <p v-if="!editMode" class="text-muted">{{ details.payment_cycle || 'N/A' }}</p>
                          <select name="" id="" class="form-control" v-else v-model="details.payment_cycle" >
                            <option>Weekly</option>
                            <option>Bi-Weekly</option>
                            <option>Tri-Weekly</option>
                            <option>Monthly</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-12">
                        <h5>Incentives</h5>
                        <hr>
                      </div>
                      <div class="col-md-12" v-if="!editMode"
                        v-for="(requirement, key) in details?.level?.details.requirement"
                        :key="'level-'+key">
                          <p>
                            <strong>{{ key }}</strong> —
                            <span :class="requirement.filled ? 'text-success' : 'text-muted'">
                              {{ requirement.filled ? '✔️ Completed' : '⏳ Not Completed' }}
                            </span>
                          </p>
                        </div>

                      <div class="col-md-3" v-if="editMode"
                            v-for="(requirement, key) in details?.level?.details.requirement"
                            :key="key">
                        <div class="form-check">
                            <input class="form-check-input"
                                    type="checkbox"
                                    :id="`requirement-${key}`"
                                    v-model="requirement.filled" />
                            <label class="form-check-label" :for="`requirement-${key}`">{{ key }}</label>
                        </div>
                        </div>

                    <!-- You can also display uploaded images here if needed -->
                    <div class=row>
                       <div class="col-md-4">
                        <div v-if="details.profile_image" class="mt-5">
                          <p><strong>Profile Image:</strong></p>
                          <img :src="`${web_url}public/storage/uploads/dropshipper/${details.profile_image}`" alt="Profile Image" class="img-fluid">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div v-if="details.cnic_front_image" class="mt-5">
                          <p><strong>CNIC Front Image:</strong></p>
                          <img :src="`${web_url}public/storage/uploads/dropshipper/${details.cnic_front_image}`" alt="CNIC Front Image" class="img-fluid">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div v-if="details.cnic_back_image" class="mt-5">
                          <p><strong>CNIC Back Image:</strong></p>
                          <img :src="`${web_url}public/storage/uploads/dropshipper/${details.cnic_back_image}`" alt="CNIC Back Image" class="img-fluid">
                        </div>
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" :href="`${public_url}/dropshippers/preview?id=${details.id}&contact=${details.whatsapp_number}`" target="_blank">
                        <i class="fa fa-eye"></i> Preview
                    </a>

                    <button class="btn btn-primary" @click="editMode ? saveDetails() : editMode = true">
                        {{ editMode ? 'Update Information' : 'Edit Information' }}
                      </button>

                    <button v-if="details.status == 0" type="button" class="btn btn-success" :class="loader ? 'btn-progress disabled' : ''" @click="decision('approve')">Approve</button>
                    <button v-if="details.status == 0" type="button" class="btn btn-danger" :class="loader ? 'btn-progress disabled' : ''" @click="decision('reject')">Reject</button>

                    <button v-if="details.status == 1" type="button" class="btn btn-danger" :class="loader ? 'btn-progress disabled' : ''" @click="decision('deactivate')">Deactivate</button>
                    <button v-if="details.status == 3" type="button" class="btn btn-success" :class="loader ? 'btn-progress disabled' : ''" @click="decision('activate')">Re Activate</button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Banks } from '../../../data/banks';

export default {
    name: 'DropshipperDetails',
    props: ['details', 'loader'],
    data() {
          return {
              public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
              web_url : process.env.MIX_WEB_URL,
              editMode: false,  // This controls whether the user is in edit mode
              banks : Banks,
              selectedBank: 'Select from the following',
          };
      },
    methods : {
      decision(action){
        this.$emit('decision', { id : this.details.id , action })
      },
      saveDetails() {
            this.$emit('updateDropshipperInformation', this.details)
            this.editMode = false;  // Exit edit mode after saving
        }
    }
}
</script>

<style scoped>
/* Add any specific styling for the modal content here */
</style>
