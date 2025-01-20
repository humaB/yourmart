<template>
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
              <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                  <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                      <div class="card-content">
                        <h5 class="font-15">Total Processed</h5>
                        <h2 class="mb-3 font-18">{{ allProcessedOrders?.processed?.current || 0 }}</h2>
                        <h2 class="mb-3 font-18">PKR {{formatPrice(allProcessedOrders?.processed?.processedAmount || 0) }}</h2>
                        <p class="mb-0"><span :class="allProcessedOrders?.processed?.trend == 'increase' ? 'col-green' : 'col-orange'">{{ allProcessedOrders?.processed?.percentage_change || 0}}% </span> {{ allProcessedOrders?.processed?.trend || '-'}}</p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                      <div class="banner-img">
                        <img src="assets/img/banner/1.png" alt="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
              <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                  <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                      <div class="card-content">
                        <h5 class="font-15"> COD</h5>
                        <h2 class="mb-3 font-18">{{ allProcessedOrders?.other?.Normal?.current || 0 }}</h2>
                        <h2 class="mb-3 font-18">PKR {{formatPrice(allProcessedOrders?.other?.Normal?.processedAmount || 0) }}</h2>
                        <p class="mb-0"><span :class="allProcessedOrders?.other?.Normal?.trend == 'increase' ? 'col-green' : 'col-orange'">{{ allProcessedOrders?.other?.Normal?.percentage_change || 0}}% </span> {{ allProcessedOrders?.other?.Normal?.trend || '-'}}</p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                      <div class="banner-img">
                        <img src="assets/img/banner/2.png" alt="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
              <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                  <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                      <div class="card-content">
                        <h5 class="font-15">Daraz</h5>
                        <h2 class="mb-3 font-18">{{ allProcessedOrders?.other?.Daraz?.current || 0 }}</h2>
                        <h2 class="mb-3 font-18">PKR {{formatPrice(allProcessedOrders?.other?.Daraz?.processedAmount || 0) }}</h2>
                        <p class="mb-0"><span :class="allProcessedOrders?.other?.Daraz?.trend == 'increase' ? 'col-green' : 'col-orange'">{{ allProcessedOrders?.other?.Daraz?.percentage_change || 0}}% </span> {{ allProcessedOrders?.other?.Daraz?.trend || '-'}}</p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                      <div class="banner-img">
                        <img src="assets/img/banner/3.png" alt="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
              <div class="card-statistic-4">
                <div class="align-items-center justify-content-between">
                  <div class="row ">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                      <div class="card-content">
                        <h5 class="font-15">Cash</h5>
                        <h2 class="mb-3 font-18">{{ allProcessedOrders?.other?.Cash?.current || 0 }}</h2>
                        <h2 class="mb-3 font-18">PKR {{formatPrice(allProcessedOrders?.other?.Cash?.processedAmount || 0) }}</h2>
                        <p class="mb-0"><span :class="allProcessedOrders?.other?.Cash?.trend == 'increase' ? 'col-green' : 'col-orange'">{{ allProcessedOrders?.other?.Cash?.percentage_change || 0}}% </span> {{ allProcessedOrders?.other?.Cash?.trend || '-'}}</p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                      <div class="banner-img">
                        <img src="assets/img/banner/4.png" alt="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


            <div class="col-xl-3 col-lg-6">
              <div class="card">
                <div class="card-body card-type-3">
                  <div class="row">
                    <div class="col">
                      <h6 class="text-muted mb-0">Delivered</h6>
                      <span class="font-weight-bold mb-0">{{ processOrders.delivered || 0 }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="card-circle l-bg-orange text-white">
                        <i class="fas fa-book-open"></i>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-xl-3 col-lg-6">
              <div class="card">
                <div class="card-body card-type-3">
                  <div class="row">
                    <div class="col">
                      <h6 class="text-muted mb-0">In Process</h6>
                      <span class="font-weight-bold mb-0">{{ processOrders.inProcess || 0 }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="card-circle l-bg-cyan text-white">
                        <i class="fas fa-briefcase"></i>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-xl-3 col-lg-6">
              <div class="card">
                <div class="card-body card-type-3">
                  <div class="row">
                    <div class="col">
                      <h6 class="text-muted mb-0">Return</h6>
                      <span class="font-weight-bold mb-0">{{ processOrders.returns || 0 }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="card-circle l-bg-green text-white">
                        <i class="fas fa-phone"></i>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-xl-3 col-lg-6">
              <div class="card">
                <div class="card-body card-type-3">
                  <div class="row">
                    <div class="col">
                      <h6 class="text-muted mb-0">Return Received</h6>
                      <span class="font-weight-bold mb-0">{{ formatPrice( processOrders.returnAmount || 0 ) }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="card-circle l-bg-purple text-white">
                        <i class="fas fa-dollar-sign"></i>
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
        name : 'DashboardSectionThree',
        props : ['allProcessedOrders', 'processOrders'],
        methods : {
            formatPrice(price) {
                var string = parseFloat(price).toString();
                return string
                    .replace(/,/g, "")
                    .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            },
        }
    }
</script>
