require('../bootstrap');

window.Vue = require('vue');

Vue.use(require('vue-moment'));

import VueInternationalization from 'vue-i18n';
import Locale from './vue-i18n-locales';

Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);
// or however you determine your current app locale

const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

import { ValidationProvider } from 'vee-validate';

import { extend } from 'vee-validate';
import { required, email, min, length } from 'vee-validate/dist/rules';

import { configure } from 'vee-validate';

configure({
  classes: {
    valid: 'is-success', // one class
    invalid: 'is-danger' // multiple classes
  }
});


// Add the required rule
extend('required', required);

// Add the email rule
extend('email', email);
extend('min', min);

extend('length', length);

// Register vee-validate globally
Vue.component('ValidationProvider', ValidationProvider);


/**
 * Automatically register Vue components
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const app = new Vue({
    el: '#app',
    i18n,
});

