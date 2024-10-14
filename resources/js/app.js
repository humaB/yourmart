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

Vue.component('dashboard-page', require('./pages/DashboardPage.vue').default );
Vue.component('users-page', require('./pages/admin/user/UserPage.vue').default );


Vue.component('dropshipper-requests', require('./pages/admin/request/DropShipperRequestPage.vue').default );
Vue.component('dropshipper-order-page', require('./pages/admin/user/DropShipperOrderPage.vue').default );
Vue.component('dropshipper-payouts-page', require('./pages/admin/user/DropShipperPayOutPage.vue').default );

Vue.component('supplier-requests', require('./pages/admin/request/SupplierRequestPage.vue').default );
Vue.component('courier-page', require('./pages/inventory/product/setting/CourierPage.vue').default );
Vue.component('page-setting-page', require('./pages/pages/PageSettingPage.vue').default );
Vue.component('library-setting-page', require('./pages/pages/LibrarySettingPage.vue').default );
Vue.component('help-center-setting-page', require('./pages/pages/HelpCenterSettingPage.vue').default );
Vue.component('ticket-page', require('./pages/TicketPage.vue').default );
Vue.component('profile-setting-page', require('./pages/setting/ProfileSettingPage.vue').default );


const app = new Vue({
    el: '#app',
});


