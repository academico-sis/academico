import Vue from 'vue';
import { Calendar, CalendarOptions } from '@fullcalendar/core';
declare const FullCalendar: import("vue/types/vue").ExtendedVue<Vue, {
    renderId: number;
}, {
    getApi: typeof getApi;
    buildOptions: typeof buildOptions;
}, unknown, {
    options: CalendarOptions;
}>;
declare function buildOptions(this: {
    $options: any;
}, suppliedOptions: CalendarOptions): CalendarOptions;
declare function getApi(this: {
    $options: any;
}): Calendar;
export default FullCalendar;
