/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue').default;
window.axios = require('axios');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('product-list-page', require('./pages/inventory/product/ProductListPage.vue').default );
Vue.component('product-minimum-order-quantity-page', require('./pages/inventory/product/setting/ProductMinimumOrderQuantityPage.vue').default );

Vue.component('product-shipping-classes-page', require('./pages/inventory/product/setting/ProductShippingClassesPage.vue').default );
Vue.component('product-order-page', require('./pages/inventory/product/order/ProductOrderPage.vue').default );


const app = new Vue({
    el: '#app',
});


