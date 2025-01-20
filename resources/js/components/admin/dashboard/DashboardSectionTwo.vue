<template>
    <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon l-bg-purple">
                    <i class="fa fa-hand-holding-usd"></i>
                </div>
                <div class="card-wrap">
                    <div class="padding-20">
                        <div class="text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(dropshipper.total_payable) }}
                            </h3>
                            <span class="text-muted">Total Payouts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon l-bg-green">
                    <i class="fa fa-thumbs-up"></i>
                </div>
                <div class="card-wrap">
                    <div class="padding-20">
                        <div class="text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(dropshipper.total_paid) }}
                            </h3>
                            <span class="text-muted">Total Paid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon l-bg-cyan">
                    <i class="fa fa-calculator"></i>
                </div>
                <div class="card-wrap">
                    <div class="padding-20">
                        <div class="text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(dropshipper.total_remaining)
                                }}
                            </h3>
                            <span class="text-muted">Total Remaining</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon l-bg-orange">
                    <i class="fa fa-clipboard-list"></i>
                </div>
                <div class="card-wrap">
                    <div class="padding-20">
                        <div class="text-right">
                            <h3 class="font-light mb-0">

                                <i class="ti-arrow-up text-success"></i> {{ dropshipper.remaining_dropshippers }}
                            </h3>
                            <span class="text-muted">Total Sellers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">

            <table style="table-layout: fixed; width: 100%;">
                <tr>
                    <td style="width: 20%; padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-cyan">
                                <i class="fa fa-shopping-bag"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                           {{ pendingRequests.tickets || 0 }}
                                        </h3>
                                        <span class="text-muted">Open Support Ticket</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ pendingRequests.pendingDropshippers || 0 }}
                                        </h3>
                                        <span class="text-muted">Pending DS Application</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 25%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-purple">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ pendingRequests.pendingSuppliers || 0 }}
                                        </h3>
                                        <span class="text-muted">Pending Suppliers Applications</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-green">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ pendingRequests.pendingReceivable || 0 }}
                                        </h3>
                                        <span class="text-muted">Returns Receivable</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-cyan">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ pendingRequests.pendingPO || 0 }}
                                        </h3>
                                        <span class="text-muted">Pending PO</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            </table>
        </div>

        <div class="col-lg-6 col-md-6 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>DS Applications Status</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.dropshippers.total }}</div>
                        <div class="font-weight-bold">Applications</div>
                        <div class="progress" data-height="15">
                            <div class="progress-bar l-bg-purple" role="progressbar"
                                :style="{ width: pendingRequests.dropshippers.total + '%' }"
                                :aria-valuenow="pendingRequests.dropshippers.total" aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.dropshippers.approved }}</div>
                        <div class="font-weight-bold">Approved</div>
                        <div class="progress" data-height="15">
                            <div class="progress-bar bg-success"
                                :style="{ width: getApplicationPercentage(pendingRequests.dropshippers.approved, pendingRequests.dropshippers.total) + '%' }"
                                :aria-valuenow="getApplicationPercentage(pendingRequests.dropshippers.approved, pendingRequests.dropshippers.total)"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ getApplicationPercentage(pendingRequests.dropshippers.approved,
                                pendingRequests.dropshippers.total) }}% </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.dropshippers.reject }}</div>
                        <div class="font-weight-bold">Rejected</div>

                        <div class="progress" data-height="15">
                            <div class="progress-bar bg-orange"
                                :style="{ width: getApplicationPercentage(pendingRequests.dropshippers.reject, pendingRequests.dropshippers.total) + '%' }"
                                :aria-valuenow="getApplicationPercentage(pendingRequests.dropshippers.reject, pendingRequests.dropshippers.total)"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ getApplicationPercentage(pendingRequests.dropshippers.reject,
                                pendingRequests.dropshippers.total) }}% </div>
                        </div>

                    </div>
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.dropshippers.pending }}</div>
                        <div class="font-weight-bold">In-Process</div>
                        <div class="progress" data-height="15">
                            <div class="progress-bar l-bg-yellow" role="progressbar"
                                :style="{ width: getApplicationPercentage(pendingRequests.dropshippers.pending, pendingRequests.dropshippers.total) + '%' }"
                                :aria-valuenow="getApplicationPercentage(pendingRequests.dropshippers.pending, pendingRequests.dropshippers.total)"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ getApplicationPercentage(pendingRequests.dropshippers.pending,
                                pendingRequests.dropshippers.total) }}%
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-lg-6 col-md-6 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Supplier Application Status</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.supplier.total }}</div>
                        <div class="font-weight-bold">Applications</div>
                        <div class="progress" data-height="15">
                            <div class="progress-bar l-bg-purple" role="progressbar"
                                data-width="100%"
                                aria-valuenow="100" aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.supplier.approved }}</div>
                        <div class="font-weight-bold">Approved</div>
                        <div class="progress" data-height="15">
                            <div class="progress-bar bg-success"
                                :style="{ width: getApplicationPercentage(pendingRequests.supplier.approved, pendingRequests.supplier.total) + '%' }"
                                :aria-valuenow="getApplicationPercentage(pendingRequests.supplier.approved, pendingRequests.supplier.total)"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ getApplicationPercentage(pendingRequests.supplier.approved,
                                pendingRequests.supplier.total) }}% </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.supplier.reject }}</div>
                        <div class="font-weight-bold">Rejected</div>

                        <div class="progress" data-height="15">
                            <div class="progress-bar bg-orange"
                                :style="{ width: getApplicationPercentage(pendingRequests.supplier.reject, pendingRequests.supplier.total) + '%' }"
                                :aria-valuenow="getApplicationPercentage(pendingRequests.supplier.reject, pendingRequests.supplier.total)"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ getApplicationPercentage(pendingRequests.supplier.reject,
                                pendingRequests.supplier.total) }}% </div>
                        </div>

                    </div>
                    <div class="mb-4">
                        <div class="text-small float-right font-weight-bold text-muted">{{
                            pendingRequests.supplier.pending }}</div>
                        <div class="font-weight-bold">In-Process</div>
                        <div class="progress" data-height="15">
                            <div class="progress-bar l-bg-yellow" role="progressbar"
                                :style="{ width: getApplicationPercentage(pendingRequests.supplier.pending, pendingRequests.supplier.total) + '%' }"
                                :aria-valuenow="getApplicationPercentage(pendingRequests.supplier.pending, pendingRequests.supplier.total)"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ getApplicationPercentage(pendingRequests.supplier.pending,
                                pendingRequests.supplier.total) }}%
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>
<script>
    export default {
        name : 'DashboardSectionTwo',
        props : ['dropshipper', 'applications', 'orders', 'pendingRequests'],
        methods : {
            getApplicationPercentage(count, total) {
                if (count === 0) return 0; // Correct check to prevent division by zero
                const percentage = Math.round((count / total) * 100);
                return percentage;
            },
            formatPrice(price) {
                var string = parseFloat(price).toString();
                return string
                    .replace(/,/g, "")
                    .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            },
        }
    }
</script>
