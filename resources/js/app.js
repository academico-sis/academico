
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
Vue.use(require('vue-moment'));

Vue.component('course-time-component', require('./components/CourseTimeComponent.vue').default);

Vue.component('cart-component', require('./components/CartComponent.vue').default);

Vue.component('event-attendance-component', require('./components/EventAttendanceComponent.vue').default);

Vue.component('student-attendance-component', require('./components/StudentAttendanceComponent.vue').default);

Vue.component('administrative-comments', require('./components/AdministrativeCommentComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
