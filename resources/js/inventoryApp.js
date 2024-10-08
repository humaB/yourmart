/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue').default;
window.axios = require('axios');

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

Vue.component("v-select", vSelect);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('purchase-order-page', require('./pages/inventory/purchase_order/PurchaseOrderPage.vue').default );
Vue.component('admin-purchase-order-page', require('./pages/admin/request/AdminPurchaseOrderPage.vue').default );

//Gate
Vue.component('gate-pending-purchase-order-page', require('./pages/gate/GatePendingPurchaseOrderPage.vue').default );
Vue.component('gate-inward-record-page', require('./pages/gate/GateInWardRecordPage.vue').default );


//Store
Vue.component('store-pending-purchase-order-page', require('./pages/store/StorePendingPurchaseOrderPage.vue').default );
Vue.component('store-inward-record-page', require('./pages/store/StoreInWardRecordPage.vue').default );
Vue.component('store-stock-page', require('./pages/store/StoreStockPage.vue').default );

//Returns
Vue.component('store-courier-return-page', require('./pages/store/return/StorePendingCourierReturnPage.vue').default );
Vue.component('store-courier-return-record-page', require('./pages/store/return/StorePendingCourierReturnRecordPage.vue').default );

const app = new Vue({
    el: '#app',
});


