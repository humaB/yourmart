<template>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card gradient-bottom">
          <div class="card-header">
            <h4>Top 5 Suppliers</h4>

          </div>
          <div class="card-body">
            <ul class="list-unstyled list-unstyled-border">
              <li class="media" v-for="supplier in topFiveSuppliers" :key="'dropshiper-'+supplier.id">
                <img class="mr-3 rounded" width="55" :src="`${web_url}public/storage/uploads/supplier/${supplier.supplier.profile_image}`" alt="product">
                <div class="media-body">
                  <div class="float-right">
                    <div class="font-weight-600 text-muted text-small">{{ formatPrice(supplier.total_amount_sum) }} Sales</div>
                  </div>
                  <div class="media-title">{{ supplier.supplier.full_name }}</div>
                  <div class="mt-1">
                    <div class="budget-price">
                      <div class="budget-price-square bg-primary" data-width="61%" style="width : 61%"></div>
                      <div class="budget-price-label">PKR {{ formatPrice(supplier.total_amount_sum - supplier.remaining_amount_sum) }}</div>
                    </div>
                    <div class="budget-price">
                      <div class="budget-price-square bg-danger" data-width="38%" style="width : 38%"></div>
                      <div class="budget-price-label">PKR {{ formatPrice(supplier.remaining_amount_sum) }}</div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-footer pt-3 d-flex justify-content-center">
            <div class="budget-price justify-content-center">
              <div class="budget-price-square bg-primary" data-width="20"></div>
              <div class="budget-price-label">Total Paid</div>
            </div>
            <div class="budget-price justify-content-center">
              <div class="budget-price-square bg-danger" data-width="20"></div>
              <div class="budget-price-label">Balance</div>
            </div>
          </div>
        </div>
      </div>
</template>
<script>
    export default {
        name : "DashboardTopFiveSupplier",
        props : ['topFiveSuppliers'],
        data() {
          return {
              web_url : process.env.MIX_WEB_URL,
          };
      },
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
