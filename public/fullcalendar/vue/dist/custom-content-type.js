import Vue from 'vue';
import { createPlugin } from '@fullcalendar/core';
/*
wrap it in an object with a `vue` key, which the custom content-type handler system will look for
*/
export function wrapVDomGenerator(vDomGenerator) {
    return function (props) {
        return { vue: vDomGenerator(props) };
    };
}
export var VueContentTypePlugin = createPlugin({
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
//# sourceMappingURL=custom-content-type.js.map