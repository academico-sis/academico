var FullCalendarVue = (function (exports, Vue, core) {
    'use strict';

    Vue = Vue && Object.prototype.hasOwnProperty.call(Vue, 'default') ? Vue['default'] : Vue;

    /*! *****************************************************************************
    Copyright (c) Microsoft Corporation.

    Permission to use, copy, modify, and/or distribute this software for any
    purpose with or without fee is hereby granted.

    THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
    REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
    INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
    LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
    OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
    PERFORMANCE OF THIS SOFTWARE.
    ***************************************************************************** */

    var __assign = function() {
        __assign = Object.assign || function __assign(t) {
            for (var s, i = 1, n = arguments.length; i < n; i++) {
                s = arguments[i];
                for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
            }
            return t;
        };
        return __assign.apply(this, arguments);
    };

    var OPTION_IS_COMPLEX = {
        headerToolbar: true,
        footerToolbar: true,
        events: true,
        eventSources: true,
        resources: true
    };

    // TODO: add types!
    /*
    works with objects and arrays
    */
    function shallowCopy(val) {
        if (typeof val === 'object') {
            if (Array.isArray(val)) {
                val = Array.prototype.slice.call(val);
            }
            else if (val) { // non-null
                val = __assign({}, val);
            }
        }
        return val;
    }
    function mapHash(input, func) {
        var output = {};
        for (var key in input) {
            if (input.hasOwnProperty(key)) {
                output[key] = func(input[key], key);
            }
        }
        return output;
    }

    /*
    wrap it in an object with a `vue` key, which the custom content-type handler system will look for
    */
    function wrapVDomGenerator(vDomGenerator) {
        return function (props) {
            return { vue: vDomGenerator(props) };
        };
    }
    var VueContentTypePlugin = core.createPlugin({
        contentTypeHandlers: {
            vue: buildVDomHandler // looks for the `vue` key
        }
    });
    function buildVDomHandler() {
        var currentEl;
        var v; // the Vue instance
        return function (el, vDomContent) {
            if (currentEl !== el) {
                if (currentEl && v) { // if changing elements, recreate the vue
                    v.$destroy();
                }
                currentEl = el;
            }
            if (!v) {
                v = initVue(vDomContent);
                // vue's mount method *replaces* the given element. create an artificial inner el
                var innerEl = document.createElement('span');
                el.appendChild(innerEl);
                v.$mount(innerEl);
            }
            else {
                v.content = vDomContent;
            }
        };
    }
    function initVue(initialContent) {
        return new Vue({
            props: {
                content: Array
            },
            propsData: {
                content: initialContent
            },
            render: function (h) {
                var content = this.content;
                // the slot result can be an array, but the returned value of a vue component's
                // render method must be a single node.
                if (content.length === 1) {
                    return content[0];
                }
                else {
                    return h('span', {}, content);
                }
            }
        });
    }

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
            var calendar = new core.Calendar(this.$el, this.buildOptions(this.options));
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

    /*
    Registers the component globally if appropriate.
    This modules exposes the component AND an install function.

    Derived from:
    https://vuejs.org/v2/cookbook/packaging-sfc-for-npm.html
    */
    var installed = false;
    // declare install function executed by Vue.use()
    function install(Vue) {
        if (!installed) {
            installed = true;
            Vue.component('FullCalendar', FullCalendar);
        }
    }
    // detect a globally availble version of Vue (eg. in browser via <script> tag)
    var GlobalVue;
    if (typeof globalThis !== 'undefined') {
        GlobalVue = globalThis.Vue;
    }
    else {
        GlobalVue = window.Vue;
    }
    // auto-install if possible
    if (GlobalVue) {
        GlobalVue.use({
            install: install
        });
    }

    Object.keys(core).forEach(function (k) {
        if (k !== 'default') Object.defineProperty(exports, k, {
            enumerable: true,
            get: function () {
                return core[k];
            }
        });
    });
    exports.default = FullCalendar;
    exports.install = install;

    return exports;

}({}, Vue, FullCalendar));
