require('./bootstrap');

import Vue from 'vue'

window.Vue = require('vue');
Vue.use(require('vue-moment'));

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


extend('cedula', {
    validate: function(ced) {
    let [suma, mul, index] = [0, 1, ced.length];
        while (index--) {
        let num = ced[index] * mul;
        suma += num - (num > 9) * 9;
        mul = 1 << index % 2;
        }

        if ((suma % 10 === 0) && (suma > 0) && (ced.length == 10)) {
            return true
        } else {
            return false
        }
    }
});

// Register vee-validate globally
Vue.component('ValidationProvider', ValidationProvider);




Vue.component('course-time-component', require('./components/CourseTimeComponent.vue').default);

Vue.component('cart-component', require('./components/CartComponent.vue').default);

Vue.component('event-attendance-component', require('./components/EventAttendanceComponent.vue').default);

Vue.component('course-attendance-component', require('./components/CourseAttendanceComponent.vue').default);

Vue.component('student-comments', require('./components/StudentCommentComponent.vue').default);

Vue.component('student-skills-component', require('./components/StudentSkillEvaluationComponent.vue').default);

Vue.component('course-result-component', require('./components/CourseResultComponent.vue').default);

Vue.component('lead-status-component', require('./components/LeadStatusComponent.vue').default);

Vue.component('absence-buttons', require('./components/AbsenceButtonsComponent.vue').default);

Vue.component('skills-list', require('./components/SkillsListComponent.vue').default);

Vue.component('phone-number-update-component', require('./components/PhoneNumberUpdateComponent.vue').default);
Vue.component('contact-phone-number-update-component', require('./components/ContactPhoneNumberUpdateComponent.vue').default);


/**
 * Automatically register Vue components
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const app = new Vue({
    el: '#app',
});

