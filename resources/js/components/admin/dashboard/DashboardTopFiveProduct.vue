<template>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card gradient-bottom">
          <div class="card-header">
            <h4>Top 5 Products</h4>

          </div>
          <div class="card-body">
            <ul class="list-unstyled list-unstyled-border">
              <li class="media" v-for="product in topFiveProduct" :key="'product-'+product.id">
                <img class="mr-3 rounded" width="55"  :src="getImageUrl(product?.variation?.product?.hero_image)" alt="product">
                <div class="media-body">
                  <div class="float-right">
                    <div class="font-weight-600 text-muted text-small">{{ formatPrice(product.total_quantity) }} Sales</div>
                  </div>
                  <div class="media-title">{{ product?.variation?.product?.title }}</div>
                  <div class="mt-1">
                    <div class="budget-price">
                      <div class="budget-price-square bg-primary" data-width="61%" style="width : 61%"></div>
                      <div class="budget-price-label">PKR {{ formatPrice(product.selling_price) }}</div>
                    </div>
                    <div class="budget-price">
                      <div class="budget-price-square bg-danger" data-width="38%" style="width : 38%"></div>
                      <div class="budget-price-label">PKR {{ formatPrice(product.total_quantity * (product?.variation?.avg_price || 0) ) }}</div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-footer pt-3 d-flex justify-content-center">
            <div class="budget-price justify-content-center">
              <div class="budget-price-square bg-primary" data-width="20"></div>
              <div class="budget-price-label">Selling Price</div>
            </div>
            <div class="budget-price justify-content-center">
              <div class="budget-price-square bg-danger" data-width="20"></div>
              <div class="budget-price-label">Product Cost</div>
            </div>
          </div>
        </div>
      </div>
</template>
<script>
    export default {
        name : "DashboardTopFiveProduct",
        props : ['topFiveProduct'],
        data() {
          return {
              web_url : process.env.MIX_WEB_URL,
              public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
          };
      },
        methods : {
            formatPrice(price) {
                var string = parseFloat(price).toString();
                return string
                    .replace(/,/g, "")
                    .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            },
            getImageUrl(imageId) {
                // Check if the image is null
                if (!imageId) {
                    return this.public_url + 'assets/img/blank_image.jpg';
                }
                return this.public_url + 'storage/uploads/inventory/products/media/' + imageId;
            },
        }
    }
</script>
