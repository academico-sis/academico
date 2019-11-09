require('./bootstrap');

window.Vue = require('vue');

Vue.use(require('vue-moment'));



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

const app = new Vue({
    el: '#app',
});

