/*!
FullCalendar Resource Day Grid Plugin v4.4.2
Docs & License: https://fullcalendar.io/scheduler
(c) 2019 Adam Shaw
*/

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('@fullcalendar/core'), require('@fullcalendar/resource-common'), require('@fullcalendar/daygrid')) :
    typeof define === 'function' && define.amd ? define(['exports', '@fullcalendar/core', '@fullcalendar/resource-common', '@fullcalendar/daygrid'], factory) :
    (global = global || self, factory(global.FullCalendarResourceDayGrid = {}, global.FullCalendar, global.FullCalendarResourceCommon, global.FullCalendarDayGrid));
}(this, function (exports, core, ResourceCommonPlugin, DayGridPlugin) { 'use strict';

    var ResourceCommonPlugin__default = 'default' in ResourceCommonPlugin ? ResourceCommonPlugin['default'] : ResourceCommonPlugin;
    var DayGridPlugin__default = 'default' in DayGridPlugin ? DayGridPlugin['default'] : DayGridPlugin;

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

    var ResourceDayGrid = /** @class */ (function (_super) {
        __extends(ResourceDayGrid, _super);
        function ResourceDayGrid(dayGrid) {
            var _this = _super.call(this, dayGrid.el) || this;
            _this.splitter = new ResourceCommonPlugin.VResourceSplitter();
            _this.slicers = {};
            _this.joiner = new ResourceDayGridJoiner();
            _this.dayGrid = dayGrid;
            return _this;
        }
        ResourceDayGrid.prototype.firstContext = function (context) {
            context.calendar.registerInteractiveComponent(this, {
                el: this.dayGrid.el
            });
        };
        ResourceDayGrid.prototype.destroy = function () {
            _super.prototype.destroy.call(this);
            this.context.calendar.unregisterInteractiveComponent(this);
        };
        ResourceDayGrid.prototype.render = function (props, context) {
            var _this = this;
            var dayGrid = this.dayGrid;
            var dateProfile = props.dateProfile, resourceDayTable = props.resourceDayTable, nextDayThreshold = props.nextDayThreshold;
            var splitProps = this.splitter.splitProps(props);
            this.slicers = core.mapHash(splitProps, function (split, resourceId) {
                return _this.slicers[resourceId] || new DayGridPlugin.DayGridSlicer();
            });
            dayGrid.receiveContext(context); // hack because sliceProps expects component to have context
            var slicedProps = core.mapHash(this.slicers, function (slicer, resourceId) {
                return slicer.sliceProps(splitProps[resourceId], dateProfile, nextDayThreshold, context.calendar, dayGrid, resourceDayTable.dayTable);
            });
            dayGrid.allowAcrossResources = resourceDayTable.dayTable.colCnt === 1;
            dayGrid.receiveProps(__assign({}, this.joiner.joinProps(slicedProps, resourceDayTable), { dateProfile: dateProfile, cells: resourceDayTable.cells, isRigid: props.isRigid }), context);
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
    }(core.DateComponent));
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
    }(ResourceCommonPlugin.VResourceJoiner));

    var ResourceDayGridView = /** @class */ (function (_super) {
        __extends(ResourceDayGridView, _super);
        function ResourceDayGridView() {
            var _this = _super !== null && _super.apply(this, arguments) || this;
            _this.flattenResources = core.memoize(ResourceCommonPlugin.flattenResources);
            _this.buildResourceDayTable = core.memoize(buildResourceDayTable);
            return _this;
        }
        ResourceDayGridView.prototype._processOptions = function (options) {
            _super.prototype._processOptions.call(this, options);
            this.resourceOrderSpecs = core.parseFieldSpecs(options.resourceOrder);
        };
        ResourceDayGridView.prototype.render = function (props, context) {
            _super.prototype.render.call(this, props, context); // for flags for updateSize. also _renderSkeleton/_unrenderSkeleton
            var options = context.options, nextDayThreshold = context.nextDayThreshold;
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
                nextDayThreshold: nextDayThreshold
            }, context);
        };
        ResourceDayGridView.prototype._renderSkeleton = function (context) {
            _super.prototype._renderSkeleton.call(this, context);
            if (context.options.columnHeader) {
                this.header = new ResourceCommonPlugin.ResourceDayHeader(this.el.querySelector('.fc-head-container'));
            }
            this.resourceDayGrid = new ResourceDayGrid(this.dayGrid);
        };
        ResourceDayGridView.prototype._unrenderSkeleton = function () {
            _super.prototype._unrenderSkeleton.call(this);
            if (this.header) {
                this.header.destroy();
            }
            this.resourceDayGrid.destroy();
        };
        ResourceDayGridView.needsResourceData = true; // for ResourceViewProps
        return ResourceDayGridView;
    }(DayGridPlugin.AbstractDayGridView));
    function buildResourceDayTable(dateProfile, dateProfileGenerator, resources, datesAboveResources) {
        var dayTable = DayGridPlugin.buildBasicDayTable(dateProfile, dateProfileGenerator);
        return datesAboveResources ?
            new ResourceCommonPlugin.DayResourceTable(dayTable, resources) :
            new ResourceCommonPlugin.ResourceDayTable(dayTable, resources);
    }

    var main = core.createPlugin({
        deps: [ResourceCommonPlugin__default, DayGridPlugin__default],
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

    exports.ResourceDayGrid = ResourceDayGrid;
    exports.ResourceDayGridView = ResourceDayGridView;
    exports.default = main;

    Object.defineProperty(exports, '__esModule', { value: true });

}));
