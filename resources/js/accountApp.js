/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue').default;
window.axios = require('axios');
import common from './common.js';

// loader
import { BulletListLoader } from 'vue-content-loader'
// vue select
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
// sweet alert 2
// import VueSweetalert2 from 'vue-sweetalert2';
// import 'sweetalert2/dist/sweetalert2.min.css';


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('account-group-page', require('./pages/account/AccountGroupPage.vue').default );
Vue.component('account-head-page', require('./pages/account/AccountHeadPage.vue').default );
Vue.component('account-head-bank-page', require('./pages/account/AccountHeadBankPage.vue').default );
Vue.component('account-head-cash-page', require('./pages/account/AccountHeadCashPage.vue').default );
Vue.component('account-bank-transaction-page', require('./pages/account/transaction/BankTransactionPage.vue').default );
Vue.component('account-cash-transaction-page', require('./pages/account/transaction/CashTransactionPage.vue').default );
Vue.component('account-journal-transaction-page', require('./pages/account/transaction/JournalTransactionPage.vue').default );
Vue.component('account-finance-report-page', require('./pages/account/report/AccountReportFinancePage.vue').default );

Vue.mixin(common);
Vue.component('BulletListLoader', BulletListLoader)
Vue.component("v-select", vSelect);


const app = new Vue({
    el: '#app',
});


