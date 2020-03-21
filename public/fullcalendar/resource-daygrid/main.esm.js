/*!
FullCalendar Resource Day Grid Plugin v4.3.0
Docs & License: https://fullcalendar.io/scheduler
(c) 2019 Adam Shaw
*/

import { mapHash, DateComponent, memoize, parseFieldSpecs, createPlugin } from '@fullcalendar/core';
import ResourceCommonPlugin, { VResourceSplitter, VResourceJoiner, flattenResources, ResourceDayHeader, DayResourceTable, ResourceDayTable } from '@fullcalendar/resource-common';
import DayGridPlugin, { DayGridSlicer, AbstractDayGridView, buildBasicDayTable } from '@fullcalendar/daygrid';

/*! *****************************************************************************
Copyright (c) Microsoft Corporation. All rights reserved.
Licensed under the Apache License, Version 2.0 (the "License"); you may not use
this file except in compliance with the License. You may obtain a copy of the
License at http://www.apache.org/licenses/LICENSE-2.0

THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
MERCHANTABLITY OR NON-INFRINGEMENT.

See the Apache Version 2.0 License for specific language governing permissions
and limitations under the License.
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

var ResourceDayGrid = /** @class */ (function (_super) {
    __extends(ResourceDayGrid, _super);
    function ResourceDayGrid(context, dayGrid) {
        var _this = _super.call(this, context, dayGrid.el) || this;
        _this.splitter = new VResourceSplitter();
        _this.slicers = {};
        _this.joiner = new ResourceDayGridJoiner();
        _this.dayGrid = dayGrid;
        context.calendar.registerInteractiveComponent(_this, {
            el: _this.dayGrid.el
        });
        return _this;
    }
    ResourceDayGrid.prototype.destroy = function () {
        _super.prototype.destroy.call(this);
        this.calendar.unregisterInteractiveComponent(this);
    };
    ResourceDayGrid.prototype.render = function (props) {
        var _this = this;
        var dayGrid = this.dayGrid;
        var dateProfile = props.dateProfile, resourceDayTable = props.resourceDayTable, nextDayThreshold = props.nextDayThreshold;
        var splitProps = this.splitter.splitProps(props);
        this.slicers = mapHash(splitProps, function (split, resourceId) {
            return _this.slicers[resourceId] || new DayGridSlicer();
        });
        var slicedProps = mapHash(this.slicers, function (slicer, resourceId) {
            return slicer.sliceProps(splitProps[resourceId], dateProfile, nextDayThreshold, dayGrid, resourceDayTable.dayTable);
        });
        dayGrid.allowAcrossResources = resourceDayTable.dayTable.colCnt === 1;
        dayGrid.receiveProps(__assign({}, this.joiner.joinProps(slicedProps, resourceDayTable), { dateProfile: dateProfile, cells: resourceDayTable.cells, isRigid: props.isRigid }));
    };
    ResourceDayGrid.prototype.buildPositionCaches = function () {
        this.dayGrid.buildPositionCaches();
    };
    ResourceDayGrid.prototype.queryHit = function (positionLeft, positionTop) {
        var rawHit = this.dayGrid.positionToHit(positionLeft, positionTop);
        if (rawHit) {
            return {
                component: this.dayGrid,
                dateSpan: {
                    range: rawHit.dateSpan.range,
                    allDay: rawHit.dateSpan.allDay,
                    resourceId: this.props.resourceDayTable.cells[rawHit.row][rawHit.col].resource.id
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
    return ResourceDayGrid;
}(DateComponent));
var ResourceDayGridJoiner = /** @class */ (function (_super) {
    __extends(ResourceDayGridJoiner, _super);
    function ResourceDayGridJoiner() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    ResourceDayGridJoiner.prototype.transformSeg = function (seg, resourceDayTable, resourceI) {
        var colRanges = resourceDayTable.computeColRanges(seg.firstCol, seg.lastCol, resourceI);
        return colRanges.map(function (colRange) {
            return __assign({}, seg, colRange, { isStart: seg.isStart && colRange.isStart, isEnd: seg.isEnd && colRange.isEnd });
        });
    };
    return ResourceDayGridJoiner;
}(VResourceJoiner));

var ResourceDayGridView = /** @class */ (function (_super) {
    __extends(ResourceDayGridView, _super);
    function ResourceDayGridView(context, viewSpec, dateProfileGenerator, parentEl) {
        var _this = _super.call(this, context, viewSpec, dateProfileGenerator, parentEl) || this;
        _this.flattenResources = memoize(flattenResources);
        _this.buildResourceDayTable = memoize(buildResourceDayTable);
        _this.resourceOrderSpecs = parseFieldSpecs(_this.opt('resourceOrder'));
        if (_this.opt('columnHeader')) {
            _this.header = new ResourceDayHeader(_this.context, _this.el.querySelector('.fc-head-container'));
        }
        _this.resourceDayGrid = new ResourceDayGrid(context, _this.dayGrid);
        return _this;
    }
    ResourceDayGridView.prototype.destroy = function () {
        _super.prototype.destroy.call(this);
        if (this.header) {
            this.header.destroy();
        }
        this.resourceDayGrid.destroy();
    };
    ResourceDayGridView.prototype.render = function (props) {
        _super.prototype.render.call(this, props); // for flags for updateSize
        var resources = this.flattenResources(props.resourceStore, this.resourceOrderSpecs);
        var resourceDayTable = this.buildResourceDayTable(this.props.dateProfile, this.dateProfileGenerator, resources, this.opt('datesAboveResources'));
        if (this.header) {
            this.header.receiveProps({
                resources: resources,
                dates: resourceDayTable.dayTable.headerDates,
                dateProfile: props.dateProfile,
                datesRepDistinctDays: true,
                renderIntroHtml: this.renderHeadIntroHtml
            });
        }
        this.resourceDayGrid.receiveProps({
            dateProfile: props.dateProfile,
            resourceDayTable: resourceDayTable,
            businessHours: props.businessHours,
            eventStore: props.eventStore,
            eventUiBases: props.eventUiBases,
            dateSelection: props.dateSelection,
            eventSelection: props.eventSelection,
            eventDrag: props.eventDrag,
            eventResize: props.eventResize,
            isRigid: this.hasRigidRows(),
            nextDayThreshold: this.nextDayThreshold
        });
    };
    ResourceDayGridView.needsResourceData = true; // for ResourceViewProps
    return ResourceDayGridView;
}(AbstractDayGridView));
function buildResourceDayTable(dateProfile, dateProfileGenerator, resources, datesAboveResources) {
    var dayTable = buildBasicDayTable(dateProfile, dateProfileGenerator);
    return datesAboveResources ?
        new DayResourceTable(dayTable, resources) :
        new ResourceDayTable(dayTable, resources);
}

var main = createPlugin({
    deps: [ResourceCommonPlugin, DayGridPlugin],
    defaultView: 'resourceDayGridDay',
    views: {
        resourceDayGrid: ResourceDayGridView,
        resourceDayGridDay: {
            type: 'resourceDayGrid',
            duration: { days: 1 }
        },
        resourceDayGridWeek: {
            type: 'resourceDayGrid',
            duration: { weeks: 1 }
        },
        resourceDayGridMonth: {
            type: 'resourceDayGrid',
            duration: { months: 1 },
            // TODO: wish we didn't have to C&P from dayGrid's file
            monthMode: true,
            fixedWeekCount: true
        }
    }
});

export default main;
export { ResourceDayGrid, ResourceDayGridView };
