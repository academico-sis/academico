require('./bootstrap');

window.Vue = require('vue');

Vue.use(require('vue-moment'));

import VueInternationalization from 'vue-i18n';
import Locale from './vue-i18n-locales.generated';

Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);
// or however you determine your current app locale

const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

Vue.component('course-time-component', require('./components/CourseTimeComponent.vue').default);

Vue.component('cart-component', require('./components/CartComponent.vue').default);

Vue.component('event-attendance-component', require('./components/EventAttendanceComponent.vue').default);

Vue.component('course-attendance-component', require('./components/CourseAttendanceComponent.vue').default);

Vue.component('student-comments', require('./components/StudentCommentComponent.vue').default);

Vue.component('student-skills-component', require('./components/StudentSkillEvaluationComponent.vue').default);

Vue.component('course-result-component', require('./components/CourseResultComponent.vue').default);

Vue.component('lead-status-component', require('./components/LeadStatusComponent.vue').default);

Vue.component('skills-list', require('./components/SkillsListComponent.vue').default);

Vue.component('phone-number-update-component', require('./components/PhoneNumberUpdateComponent.vue').default);
Vue.component('contact-phone-number-update-component', require('./components/ContactPhoneNumberUpdateComponent.vue').default);

Vue.component('course-attendance-status-component', require('./components/attendance/courseAttendanceStatusComponent.vue').default);
Vue.component('event-attendance-status-component', require('./components/attendance/eventAttendanceStatusComponent.vue').default);
Vue.component('student-contacts-component', require('./components/student/studentContactsComponent.vue').default);

const app = new Vue({
    el: '#app',
    i18n,
});

