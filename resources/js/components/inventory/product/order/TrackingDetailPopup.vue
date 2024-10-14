<template>
    <div class="modal fade" id="trackingInformation" tabindex="-1" role="dialog"
    aria-labelledby="trackingInformationTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tracking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Status</th>
                                <th>Date Time</th>
                                <th>Remarks</th>
                                <th>Receiver Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in trackingDetails" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.leopard_label }}</td>
                                <td>{{ item.time }}</td>
                                <td>{{ item.reason }}</td>
                                <td>{{ item.receiver_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</template>
<script>
    export default {
        name : 'TrackingDetailPopup',
        props : ['trackingDetails']
    }
</script>
