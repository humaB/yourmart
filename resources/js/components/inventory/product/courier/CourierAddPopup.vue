<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="addCourier" tabindex="-1" role="dialog" aria-labelledby="addCourierTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add New Courier Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <h5>Courier Service Name <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Courier Service Name"
                                v-model="courierName">
                        </div>

                        <div class="col-md-6 mt-3">
                            <h5>Courier Contact Person Name <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Courier Contact Person Name"
                                v-model="contactPerson">
                        </div>


                        <div class="col-md-6 mt-3">
                            <h5>Courier Contact Person Number <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Courier Contact Person Number"
                                v-model="contactPersonNumber">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="handleSubmit()" v-if="!loader" class="btn btn-primary">Add
                            Courier Service</button>
                        <button type="button" v-else class="btn btn-primary btn-progress disabled">Add Shipping
                            Class</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddShippingClass',
    props: ['loader'],
    data() {
        return {
            courierName: '',
            contactPerson: '',
            contactPersonNumber: '',
        }
    },
    mounted() {
        this.$parent.$on("saved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    methods: {
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        handleSubmit() {
            let vm = this;
            if (vm.courierName == '') {
                return swal({
                    title: "Error",
                    text: "Please add some class name, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.contactPerson == '') {
                return swal({
                    title: "Error",
                    text: "Please add contact person name, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.contactPersonNumber == '') {
                return swal({
                    title: "Error",
                    text: "Please add contact person number, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }
            const data = {
                name: vm.courierName,
                contactPerson: vm.contactPerson,
                contactPersonNumber: vm.contactPersonNumber,
            };

            vm.$emit('addNewCourier', data);
        },
        close() {
            this.courierName = '';
            this.contactPersonNumber = '';
            this.contactPerson = '';

        }
    },
}
</script>
