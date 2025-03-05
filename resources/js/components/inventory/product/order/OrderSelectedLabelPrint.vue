<template>
    <!-- Modal -->
    <div class="modal fade" id="selectedOrderPrints" tabindex="-1" role="dialog" aria-labelledby="selectedOrderPrints" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Selected Order Prints</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Tracking Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in labels" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.tracking_number }}</td>
                                <td>
                                    <a :href="item.slip_link"
                                    target="_blank">Press to Print</a>
                                </td>
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
        name : 'OrderSelectedLabelPrint',
        props : ['labels'],
        methods : {

        },
    }
</script>
