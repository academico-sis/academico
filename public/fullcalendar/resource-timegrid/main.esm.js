/*!
FullCalendar Resource Time Grid Plugin v4.4.2
Docs & License: https://fullcalendar.io/scheduler
(c) 2019 Adam Shaw
*/

import { memoize, mapHash, DateComponent, parseFieldSpecs, createPlugin } from '@fullcalendar/core';
import ResourceCommonPlugin, { VResourceSplitter, VResourceJoiner, flattenResources, ResourceDayHeader, DayResourceTable, ResourceDayTable } from '@fullcalendar/resource-common';
import TimeGridPlugin, { buildDayRanges, TimeGridSlicer, AbstractTimeGridView, buildDayTable } from '@fullcalendar/timegrid';
import { ResourceDayGrid } from '@fullcalendar/resource-daygrid';

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
/* global Reflect, Promise */

var extendStatics = function(d, b) {
    extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return extendStatics(d, b);
};

function __extends(d, b) {
    extendStatics(d, b);
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

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

var ResourceTimeGrid = /** @class */ (function (_super) {
    __extends(ResourceTimeGrid, _super);
    function ResourceTimeGrid(timeGrid) {
        var _this = _super.call(this, timeGrid.el) || this;
        _this.buildDayRanges = memoize(buildDayRanges);
        _this.splitter = new VResourceSplitter();
        _this.slicers = {};
        _this.joiner = new ResourceTimeGridJoiner();
        _this.timeGrid = timeGrid;
        return _this;
    }
    ResourceTimeGrid.prototype.firstContext = function (context) {
        context.calendar.registerInteractiveComponent(this, {
            el: this.timeGrid.el
        });
    };
    ResourceTimeGrid.prototype.destroy = function () {
        this.context.calendar.unregisterInteractiveComponent(this);
    };
    ResourceTimeGrid.prototype.render = function (props, context) {
        var _this = this;
        var timeGrid = this.timeGrid;
        var dateEnv = context.dateEnv;
        var dateProfile = props.dateProfile, resourceDayTable = props.resourceDayTable;
        var dayRanges = this.dayRanges = this.buildDayRanges(resourceDayTable.dayTable, dateProfile, dateEnv);
        var splitProps = this.splitter.splitProps(props);
        this.slicers = mapHash(splitProps, function (split, resourceId) {
            return _this.slicers[resourceId] || new TimeGridSlicer();
        });
        timeGrid.receiveContext(context); // hack because sliceProps expects component to have context
        var slicedProps = mapHash(this.slicers, function (slicer, resourceId) {
            return slicer.sliceProps(splitProps[resourceId], dateProfile, null, context.calendar, timeGrid, dayRanges);
        });
        timeGrid.allowAcrossResources = dayRanges.length === 1;
        timeGrid.receiveProps(__assign({}, this.joiner.joinProps(slicedProps, resourceDayTable), { dateProfile: dateProfile, cells: resourceDayTable.cells[0] }), context);
    };
    ResourceTimeGrid.prototype.renderNowIndicator = function (date) {
        var timeGrid = this.timeGrid;
        var resourceDayTable = this.props.resourceDayTable;
        var nonResourceSegs = this.slicers[''].sliceNowDate(date, timeGrid, this.dayRanges);
        var segs = this.joiner.expandSegs(resourceDayTable, nonResourceSegs);
        timeGrid.renderNowIndicator(segs, date);
    };
    ResourceTimeGrid.prototype.buildPositionCaches = function () {
        this.timeGrid.buildPositionCaches();
    };
    ResourceTimeGrid.prototype.queryHit = function (positionLeft, positionTop) {
        var rawHit = this.timeGrid.positionToHit(positionLeft, positionTop);
        if (rawHit) {
            return {
                component: this.timeGrid,
                dateSpan: {
                    range: rawHit.dateSpan.range,
                    allDay: rawHit.dateSpan.allDay,
                    resourceId: this.props.resourceDayTable.cells[0][rawHit.col].resource.id
                },
                dayEl: rawHit.dayEl,
                rect: {
                    left: rawHit.relativeRect.left,
                    right: rawHit.relativeRect.right,
                    top: rawHit.relativeRect.top,
                    bottom: rawHit.relativeRect.bottom
                },
                layer: 0
            };
        }
    };
    return ResourceTimeGrid;
}(DateComponent));
var ResourceTimeGridJoiner = /** @class */ (function (_super) {
    __extends(ResourceTimeGridJoiner, _super);
    function ResourceTimeGridJoiner() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    ResourceTimeGridJoiner.prototype.transformSeg = function (seg, resourceDayTable, resourceI) {
        return [
            __assign({}, seg, { col: resourceDayTable.computeCol(seg.col, resourceI) })
        ];
    };
    return ResourceTimeGridJoiner;
}(VResourceJoiner));

var ResourceTimeGridView = /** @class */ (function (_super) {
    __extends(ResourceTimeGridView, _super);
    function ResourceTimeGridView() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this.processOptions = memoize(_this._processOptions);
        _this.flattenResources = memoize(flattenResources);
        _this.buildResourceDayTable = memoize(buildResourceDayTable);
        return _this;
    }
    ResourceTimeGridView.prototype._processOptions = function (options) {
        this.resourceOrderSpecs = parseFieldSpecs(options.resourceOrder);
    };
    ResourceTimeGridView.prototype.render = function (props, context) {
        _super.prototype.render.call(this, props, context); // for flags for updateSize. and will call _renderSkeleton/_unrenderSkeleton
        var options = context.options, nextDayThreshold = context.nextDayThreshold;
        this.processOptions(options);
        var splitProps = this.splitter.splitProps(props);
        var resources = this.flattenResources(props.resourceStore, this.resourceOrderSpecs);
        var resourceDayTable = this.buildResourceDayTable(props.dateProfile, props.dateProfileGenerator, resources, options.datesAboveResources);
        if (this.header) {
            this.header.receiveProps({
                resources: resources,
                dates: resourceDayTable.dayTable.headerDates,
                dateProfile: props.dateProfile,
                datesRepDistinctDays: true,
                renderIntroHtml: this.renderHeadIntroHtml
            }, context);
        }
        this.resourceTimeGrid.receiveProps(__assign({}, splitProps['timed'], { dateProfile: props.dateProfile, resourceDayTable: resourceDayTable }), context);
        if (this.resourceDayGrid) {
            this.resourceDayGrid.receiveProps(__assign({}, splitProps['allDay'], { dateProfile: props.dateProfile, resourceDayTable: resourceDayTable, isRigid: false, nextDayThreshold: nextDayThreshold }), context);
        }
        this.startNowIndicator(props.dateProfile, props.dateProfileGenerator);
    };
    ResourceTimeGridView.prototype._renderSkeleton = function (context) {
        _super.prototype._renderSkeleton.call(this, context);
        if (context.options.columnHeader) {
            this.header = new ResourceDayHeader(this.el.querySelector('.fc-head-container'));
        }
        this.resourceTimeGrid = new ResourceTimeGrid(this.timeGrid);
        if (this.dayGrid) {
            this.resourceDayGrid = new ResourceDayGrid(this.dayGrid);
        }
    };
    ResourceTimeGridView.prototype._unrenderSkeleton = function () {
        _super.prototype._unrenderSkeleton.call(this);
        if (this.header) {
            this.header.destroy();
        }
        this.resourceTimeGrid.destroy();
        if (this.resourceDayGrid) {
            this.resourceDayGrid.destroy();
        }
    };
    ResourceTimeGridView.prototype.renderNowIndicator = function (date) {
        this.resourceTimeGrid.renderNowIndicator(date);
    };
    ResourceTimeGridView.needsResourceData = true; // for ResourceViewProps
    return ResourceTimeGridView;
}(AbstractTimeGridView));
function buildResourceDayTable(dateProfile, dateProfileGenerator, resources, datesAboveResources) {
    var dayTable = buildDayTable(dateProfile, dateProfileGenerator);
    return datesAboveResources ?
        new DayResourceTable(dayTable, resources) :
        new ResourceDayTable(dayTable, resources);
}

var main = createPlugin({
    deps: [ResourceCommonPlugin, TimeGridPlugin],
    defaultView: 'resourceTimeGridDay',
    views: {
        resourceTimeGrid: {
            class: ResourceTimeGridView,
            // TODO: wish we didn't have to C&P from timeGrid's file
            allDaySlot: true,
            slotDuration: '00:30:00',
            slotEventOverlap: true // a bad name. confused with overlap/constraint system
        },
        resourceTimeGridDay: {
            type: 'resourceTimeGrid',
            duration: { days: 1 }
        },
        resourceTimeGridWeek: {
            type: 'resourceTimeGrid',
            duration: { weeks: 1 }
        }
    }
});

export default main;
export { ResourceTimeGrid, ResourceTimeGridView };
