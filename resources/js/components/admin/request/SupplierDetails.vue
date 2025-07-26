<template>
  <div class="modal fade" id="supplierDetail" tabindex="-1" role="dialog" aria-labelledby="suppliersDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="suppliersDetailTitle">Details of {{ details.full_name }}</h5>
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
              <strong>Full Name</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.full_name" />
              </div>
              <p v-else class="text-muted">{{ details.full_name }}</p>
            </div>

            <div class="col-md-3 col-6 b-r">
              <strong>Mobile</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.whatsapp_number" />
              </div>
              <p v-else class="text-muted">{{ details.whatsapp_number }}</p>
            </div>

            <div class="col-md-3 col-6 b-r">
              <strong>Email</strong><br>
              <div v-if="isEditing">
                <input type="email" class="form-control" v-model="details.email" />
              </div>
              <p v-else class="text-muted">{{ details.email }}</p>
            </div>


            <div class="col-md-3 col-6 b-r">
                <strong>Change Password</strong>
                <br>
                <p v-if="!isEditing" class="text-muted">*****</p>
                <input v-else type="password" v-model="details.changedPassword" class="form-control">
            </div>

            <div class="col-md-3 col-6">
              <strong>Location</strong><br>
              <p class="text-muted">{{ details.city ? details.city.name : '-' }}</p>
            </div>

            <div class="col-md-3 col-6">
              <strong>CNIC</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.cnic_number" />
              </div>
              <p v-else class="text-muted">{{ details.cnic_number }}</p>
            </div>

            <div class="col-md-3 col-6">
              <strong>Address</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.address" />
              </div>
              <p v-else class="text-muted">{{ details.address }}</p>
            </div>

            <div class="col-md-12">
              <h5>Account Information</h5>
              <hr>
            </div>

            <div class="col-md-3 col-6">
              <strong>Bank Name:</strong><br>
              <p class="text-muted">{{ details.bank ? details.bank.name : '-' }}</p>
            </div>

            <div class="col-md-3 col-6">
              <strong>Account Number:</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.account_number" />
              </div>
              <p v-else class="text-muted">{{ details.account_number || 'N/A' }}</p>
            </div>

            <div class="col-md-3 col-6">
              <strong>Account Title:</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.account_title" />
              </div>
              <p v-else class="text-muted">{{ details.account_title || 'N/A' }}</p>
            </div>

            <div class="col-md-3 col-6">
              <strong>Account IBAN:</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.account_iban" />
              </div>
              <p v-else class="text-muted">{{ details.account_iban || 'N/A' }}</p>
            </div>

            <div class="col-md-3 col-6">
              <strong>Payment Cycle:</strong><br>
              <div v-if="isEditing">
                <input type="text" class="form-control" v-model="details.payment_cycle" />
              </div>
              <p v-else class="text-muted">{{ details.payment_cycle || 'N/A' }}</p>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4" v-if="details.profile_image">
              <p class="mt-5"><strong>Profile Image:</strong></p>
              <img :src="`${web_url}public/storage/uploads/supplier/${details.profile_image}`" alt="Profile Image" class="img-fluid">
            </div>
            <div class="col-md-4" v-if="details.cnic_front_image">
              <p class="mt-5"><strong>CNIC Front Image:</strong></p>
              <img :src="`${web_url}public/storage/uploads/supplier/${details.cnic_front_image}`" alt="CNIC Front Image" class="img-fluid">
            </div>
            <div class="col-md-4" v-if="details.cnic_back_image">
              <p class="mt-5"><strong>CNIC Back Image:</strong></p>
              <img :src="`${web_url}public/storage/uploads/supplier/${details.cnic_back_image}`" alt="CNIC Back Image" class="img-fluid">
            </div>
          </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary" @click="isEditing ? saveDetails() : isEditing = true">
                {{ isEditing ? 'Update Information' : 'Edit Information' }}
            </button>

          <button v-if="details.status == 0 && !isEditing" type="button" class="btn btn-success" :class="loader ? 'btn-progress disabled' : ''" @click="decision('approve')">Approve</button>
          <button v-if="details.status == 0 && !isEditing" type="button" class="btn btn-danger" :class="loader ? 'btn-progress disabled' : ''" @click="decision('reject')">Reject</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
    name: 'SupplierDetails',
    props: ['details', 'loader'],
    data() {
          return {
            web_url : process.env.MIX_WEB_URL,
            isEditing: false,
          };
      },
    methods : {
      decision(action){
        this.$emit('decision', { id : this.details.id , action })
      },
        toggleEdit() {
            this.isEditing = !this.isEditing;
        },
        saveDetails() {
            this.$emit('updateInformation', this.details); // parent will handle API call
            this.isEditing = false;
        }
    }
}
</script>
