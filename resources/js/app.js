require('./bootstrap');

window.Vue = require('vue');

Vue.use(require('vue-moment'));

import VueInternationalization from 'vue-i18n';
const messages = {
    en: require("../lang/en.json"),
    fr: require("../lang/fr.json"),
    es: require("../lang/es.json"),
};

Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);
// or however you determine your current app locale

const i18n = new VueInternationalization({
    locale: lang,
    messages
});

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

Vue.component('course-list-component', require('./components/CourseListComponent.vue').default);

Vue.component('event-creation-component', require('./components/eventCreationComponent.vue').default);

Vue.component('scholarship-modal-component', require('./components/ScholarshipModalComponent.vue').default);

Vue.component('grade-field-component', require('./components/gradeFieldComponent.vue').default);
Vue.component('enrollment-grades-component', require('./components/enrollmentGradesComponent.vue').default);

const app = new Vue({
    el: '#app',
    i18n,
});

