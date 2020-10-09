// TODO: add types!
import { __assign } from "tslib";
/*
works with objects and arrays
*/
export function shallowCopy(val) {
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
export function mapHash(input, func) {
    var output = {};
    for (var key in input) {
        if (input.hasOwnProperty(key)) {
            output[key] = func(input[key], key);
        }
    }
    return output;
}
//# sourceMappingURL=utils.js.map