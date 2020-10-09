import { __assign } from "tslib";
import Vue from 'vue';
import { Calendar } from '@fullcalendar/core';
import { OPTION_IS_COMPLEX } from './options';
import { shallowCopy, mapHash } from './utils';
import { wrapVDomGenerator, VueContentTypePlugin } from './custom-content-type';
var FullCalendar = Vue.extend({
    props: {
        options: Object
    },
    data: initData,
    render: function (createElement) {
        return createElement('div', {
            // when renderId is changed, Vue will trigger a real-DOM async rerender, calling beforeUpdate/updated
            attrs: { 'data-fc-render-id': this.renderId }
        });
    },
    mounted: function () {
        var internal = this.$options;
        internal.scopedSlotOptions = mapHash(this.$scopedSlots, wrapVDomGenerator); // needed for buildOptions
        var calendar = new Calendar(this.$el, this.buildOptions(this.options));
        internal.calendar = calendar;
        calendar.render();
    },
    methods: {
        getApi: getApi,
        buildOptions: buildOptions,
    },
    beforeUpdate: function () {
        this.getApi().resumeRendering(); // the watcher handlers paused it
    },
    beforeDestroy: function () {
        this.getApi().destroy();
    },
    watch: buildWatchers()
});
function initData() {
    return {
        renderId: 0
    };
}
function buildOptions(suppliedOptions) {
    var internal = this.$options;
    suppliedOptions = suppliedOptions || {};
    return __assign(__assign(__assign({}, internal.scopedSlotOptions), suppliedOptions), { plugins: (suppliedOptions.plugins || []).concat([
            VueContentTypePlugin
        ]) });
}
function getApi() {
    var internal = this.$options;
    return internal.calendar;
}
function buildWatchers() {
    var watchers = {
        // watches changes of ALL options and their nested objects,
        // but this is only a means to be notified of top-level non-complex options changes.
        options: {
            deep: true,
            handler: function (options) {
                var calendar = this.getApi();
                calendar.pauseRendering();
                calendar.resetOptions(this.buildOptions(options));
                this.renderId++; // will queue a rerender
            }
        }
    };
    var _loop_1 = function (complexOptionName) {
        // handlers called when nested objects change
        watchers["options." + complexOptionName] = {
            deep: true,
            handler: function (val) {
                var _a;
                // unfortunately the handler is called with undefined if new props were set, but the complex one wasn't ever set
                if (val !== undefined) {
                    var calendar = this.getApi();
                    calendar.pauseRendering();
                    calendar.resetOptions((_a = {},
                        // the only reason we shallow-copy is to trick FC into knowing there's a nested change.
                        // TODO: future versions of FC will more gracefully handle event option-changes that are same-reference.
                        _a[complexOptionName] = shallowCopy(val),
                        _a), true);
                    this.renderId++; // will queue a rerender
                }
            }
        };
    };
    for (var complexOptionName in OPTION_IS_COMPLEX) {
        _loop_1(complexOptionName);
    }
    return watchers;
}
export default FullCalendar;
//# sourceMappingURL=FullCalendar.js.map