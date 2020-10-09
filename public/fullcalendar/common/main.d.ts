
import { Ref, ComponentChildren, createElement, Context, RefObject, ComponentType, VUIEvent, VNode, Component } from './vdom';
export * from './vdom';
export { VUIEvent } from './vdom';

declare type DurationInput = DurationObjectInput | string | number;
interface DurationObjectInput {
    years?: number;
    year?: number;
    months?: number;
    month?: number;
    weeks?: number;
    week?: number;
    days?: number;
    day?: number;
    hours?: number;
    hour?: number;
    minutes?: number;
    minute?: number;
    seconds?: number;
    second?: number;
    milliseconds?: number;
    millisecond?: number;
    ms?: number;
}
interface Duration {
    years: number;
    months: number;
    days: number;
    milliseconds: number;
    specifiedWeeks?: boolean;
}
declare function createDuration(input: DurationInput, unit?: string): Duration | null;
declare function asCleanDays(dur: Duration): number;
declare function addDurations(d0: Duration, d1: Duration): {
    years: number;
    months: number;
    days: number;
    milliseconds: number;
};
declare function multiplyDuration(d: Duration, n: number): {
    years: number;
    months: number;
    days: number;
    milliseconds: number;
};
declare function asRoughMinutes(dur: Duration): number;
declare function asRoughSeconds(dur: Duration): number;
declare function asRoughMs(dur: Duration): number;
declare function wholeDivideDurations(numerator: Duration, denominator: Duration): number;
declare function greatestDurationDenominator(dur: Duration): {
    unit: string;
    value: number;
};


declare type ClassNamesInput = string | string[];
declare function parseClassNames(raw: ClassNamesInput): string[];


declare type DateMarker = Date;
declare function addWeeks(m: DateMarker, n: number): Date;
declare function addDays(m: DateMarker, n: number): Date;
declare function addMs(m: DateMarker, n: number): Date;
declare function diffWeeks(m0: any, m1: any): number;
declare function diffDays(m0: any, m1: any): number;
declare function diffDayAndTime(m0: DateMarker, m1: DateMarker): Duration;
declare function diffWholeWeeks(m0: DateMarker, m1: DateMarker): number;
declare function diffWholeDays(m0: DateMarker, m1: DateMarker): number;
declare function startOfDay(m: DateMarker): DateMarker;
declare function isValidDate(m: DateMarker): boolean;

interface CalendarSystem {
    getMarkerYear(d: DateMarker): number;
    getMarkerMonth(d: DateMarker): number;
    getMarkerDay(d: DateMarker): number;
    arrayToMarker(arr: number[]): DateMarker;
    markerToArray(d: DateMarker): number[];
}

interface DateRangeInput {
    start?: DateInput;
    end?: DateInput;
}
interface OpenDateRange {
    start: DateMarker | null;
    end: DateMarker | null;
}
interface DateRange {
    start: DateMarker;
    end: DateMarker;
}
declare function intersectRanges(range0: OpenDateRange, range1: OpenDateRange): OpenDateRange;
declare function rangesEqual(range0: OpenDateRange, range1: OpenDateRange): boolean;
declare function rangesIntersect(range0: OpenDateRange, range1: OpenDateRange): boolean;
declare function rangeContainsRange(outerRange: OpenDateRange, innerRange: OpenDateRange): boolean;
declare function rangeContainsMarker(range: OpenDateRange, date: DateMarker | number): boolean;

interface PointerDragEvent {
    origEvent: UIEvent;
    isTouch: boolean;
    subjectEl: EventTarget;
    pageX: number;
    pageY: number;
    deltaX: number;
    deltaY: number;
}


interface EventInteractionState {
    affectedEvents: EventStore;
    mutatedEvents: EventStore;
    isEvent: boolean;
}


declare function guid(): string;
declare function disableCursor(): void;
declare function enableCursor(): void;
declare function preventSelection(el: HTMLElement): void;
declare function allowSelection(el: HTMLElement): void;
declare function preventContextMenu(el: HTMLElement): void;
declare function allowContextMenu(el: HTMLElement): void;
interface OrderSpec<Subject> {
    field?: string;
    order?: number;
    func?: FieldSpecInputFunc<Subject>;
}
declare type FieldSpecInput<Subject> = string | string[] | FieldSpecInputFunc<Subject> | FieldSpecInputFunc<Subject>[];
declare type FieldSpecInputFunc<Subject> = (a: Subject, b: Subject) => number;
declare function parseFieldSpecs<Subject>(input: FieldSpecInput<Subject>): OrderSpec<Subject>[];
declare function compareByFieldSpecs<Subject>(obj0: Subject, obj1: Subject, fieldSpecs: OrderSpec<Subject>[]): number;
declare function compareByFieldSpec<Subject>(obj0: Subject, obj1: Subject, fieldSpec: OrderSpec<Subject>): number;
declare function flexibleCompare(a: any, b: any): number;
declare function padStart(val: any, len: any): string;
declare function compareNumbers(a: any, b: any): number;
declare function isInt(n: any): boolean;
declare function computeSmallestCellWidth(cellEl: HTMLElement): number;


interface EventMutation {
    datesDelta?: Duration;
    startDelta?: Duration;
    endDelta?: Duration;
    standardProps?: any;
    extendedProps?: any;
}
declare function applyMutationToEventStore(eventStore: EventStore, eventConfigBase: EventUiHash, mutation: EventMutation, context: CalendarContext): EventStore;
declare type eventDefMutationApplier = (eventDef: EventDef, mutation: EventMutation, context: CalendarContext) => void;


interface DateSelectionApi extends DateSpanApi {
    jsEvent: UIEvent;
    view: ViewApi;
}
declare type DatePointTransform = (dateSpan: DateSpan, context: CalendarContext) => any;
declare type DateSpanTransform = (dateSpan: DateSpan, context: CalendarContext) => any;
declare type CalendarInteraction = {
    destroy(): any;
};
declare type CalendarInteractionClass = {
    new (context: CalendarContext): CalendarInteraction;
};
declare type OptionChangeHandler = (propValue: any, context: CalendarContext) => void;
declare type OptionChangeHandlerMap = {
    [propName: string]: OptionChangeHandler;
};
interface DateSelectArg extends DateSpanApi {
    jsEvent: MouseEvent | null;
    view: ViewApi;
}
declare function triggerDateSelect(selection: DateSpan, pev: PointerDragEvent | null, context: CalendarContext & {
    viewApi?: ViewApi;
}): void;
interface DateUnselectArg {
    jsEvent: MouseEvent;
    view: ViewApi;
}
declare function getDefaultEventEnd(allDay: boolean, marker: DateMarker, context: CalendarContext): DateMarker;


interface RenderHookProps<ContentArg> {
    hookProps: ContentArg;
    classNames: ClassNamesGenerator<ContentArg>;
    content: CustomContentGenerator<ContentArg>;
    defaultContent?: DefaultContentGenerator<ContentArg>;
    didMount: DidMountHandler<MountArg<ContentArg>>;
    willUnmount: WillUnmountHandler<MountArg<ContentArg>>;
    children: RenderHookPropsChildren;
    elRef?: Ref<any>;
}
declare type RenderHookPropsChildren = (rootElRef: Ref<any>, classNames: string[], innerElRef: Ref<any>, innerContent: ComponentChildren) => ComponentChildren;
interface ContentTypeHandlers {
    [contentKey: string]: () => (el: HTMLElement, contentVal: any) => void;
}
declare class RenderHook<HookProps> extends BaseComponent<RenderHookProps<HookProps>> {
    private rootElRef;
    render(): createElement.JSX.Element;
    handleRootEl: (el: HTMLElement | null) => void;
}
interface ObjCustomContent {
    html: string;
    domNodes: any[];
    [custom: string]: any;
}
declare type CustomContent = ComponentChildren | ObjCustomContent;
declare type CustomContentGenerator<HookProps> = CustomContent | ((hookProps: HookProps) => CustomContent);
declare type DefaultContentGenerator<HookProps> = (hookProps: HookProps) => ComponentChildren;
declare const CustomContentRenderContext: Context<number>;
interface ContentHookProps<HookProps> {
    hookProps: HookProps;
    content: CustomContentGenerator<HookProps>;
    defaultContent?: DefaultContentGenerator<HookProps>;
    children: (innerElRef: Ref<any>, innerContent: ComponentChildren) => ComponentChildren;
    backupElRef?: RefObject<any>;
}
declare function ContentHook<HookProps>(props: ContentHookProps<HookProps>): createElement.JSX.Element;
declare type MountArg<ContentArg> = ContentArg & {
    el: HTMLElement;
};
declare type DidMountHandler<MountArg extends {
    el: HTMLElement;
}> = (mountArg: MountArg) => void;
declare type WillUnmountHandler<MountArg extends {
    el: HTMLElement;
}> = (mountArg: MountArg) => void;
interface MountHookProps<ContentArg> {
    hookProps: ContentArg;
    didMount: DidMountHandler<MountArg<ContentArg>>;
    willUnmount: WillUnmountHandler<MountArg<ContentArg>>;
    children: (rootElRef: Ref<any>) => ComponentChildren;
    elRef?: Ref<any>;
}
declare class MountHook<ContentArg> extends BaseComponent<MountHookProps<ContentArg>> {
    rootEl: HTMLElement;
    render(): ComponentChildren;
    componentDidMount(): void;
    componentWillUnmount(): void;
    private handleRootEl;
}
declare function buildClassNameNormalizer<HookProps>(): (generator: ClassNamesGenerator<HookProps>, hookProps: HookProps) => string[];
declare type ClassNamesGenerator<HookProps> = ClassNamesInput | ((hookProps: HookProps) => ClassNamesInput);


declare type ViewComponentType = ComponentType<ViewProps>;
declare type ViewConfigInput = ViewComponentType | ViewOptions;
declare type ViewConfigInputHash = {
    [viewType: string]: ViewConfigInput;
};
interface SpecificViewContentArg extends ViewProps {
    nextDayThreshold: Duration;
}
declare type SpecificViewMountArg = MountArg<SpecificViewContentArg>;


declare abstract class Interaction {
    component: DateComponent<any>;
    constructor(settings: InteractionSettings);
    destroy(): void;
}
declare type InteractionClass = {
    new (settings: InteractionSettings): Interaction;
};
interface InteractionSettingsInput {
    el: HTMLElement;
    useEventCenter?: boolean;
}
interface InteractionSettings {
    component: DateComponent<any>;
    el: HTMLElement;
    useEventCenter: boolean;
}
declare type InteractionSettingsStore = {
    [componenUid: string]: InteractionSettings;
};
declare function interactionSettingsToStore(settings: InteractionSettings): {
    [x: string]: InteractionSettings;
};
declare let interactionSettingsStore: InteractionSettingsStore;


declare class DelayedRunner {
    private drainedOption?;
    private isRunning;
    private isDirty;
    private pauseDepths;
    private timeoutId;
    constructor(drainedOption?: () => void);
    request(delay?: number): void;
    pause(scope?: string): void;
    resume(scope?: string, force?: boolean): void;
    isPaused(): number;
    tryDrain(): void;
    clear(): void;
    private clearTimeout;
    protected drained(): void;
}

interface CalendarContentProps extends CalendarData {
    forPrint: boolean;
    isHeightAuto: boolean;
}
declare class CalendarContent extends PureComponent<CalendarContentProps> {
    context: never;
    private buildViewContext;
    private buildViewPropTransformers;
    private buildToolbarProps;
    private handleNavLinkClick;
    private headerRef;
    private footerRef;
    private interactionsStore;
    private calendarInteractions;
    render(): createElement.JSX.Element;
    componentDidMount(): void;
    componentDidUpdate(prevProps: CalendarContentProps): void;
    componentWillUnmount(): void;
    _handleNavLinkClick(ev: VUIEvent, anchorEl: HTMLElement): void;
    buildAppendContent(): VNode;
    renderView(props: CalendarContentProps): createElement.JSX.Element;
    registerInteractiveComponent: (component: DateComponent<any>, settingsInput: InteractionSettingsInput) => void;
    unregisterInteractiveComponent: (component: DateComponent<any>) => void;
    resizeRunner: DelayedRunner;
    handleWindowResize: (ev: UIEvent) => void;
}


interface Point {
    left: number;
    top: number;
}
interface Rect {
    left: number;
    right: number;
    top: number;
    bottom: number;
}
declare function pointInsideRect(point: Point, rect: Rect): boolean;
declare function intersectRects(rect1: Rect, rect2: Rect): Rect | false;
declare function translateRect(rect: Rect, deltaX: number, deltaY: number): Rect;
declare function constrainPoint(point: Point, rect: Rect): Point;
declare function getRectCenter(rect: Rect): Point;
declare function diffPoints(point1: Point, point2: Point): Point;


interface Hit {
    component: DateComponent<any>;
    dateSpan: DateSpan;
    dayEl: HTMLElement;
    rect: Rect;
    layer: number;
}


declare type eventDragMutationMassager = (mutation: EventMutation, hit0: Hit, hit1: Hit) => void;
declare type EventDropTransformers = (mutation: EventMutation, context: CalendarContext) => Dictionary;
declare type eventIsDraggableTransformer = (val: boolean, eventDef: EventDef, eventUi: EventUi, context: CalendarContext) => boolean;


declare type dateSelectionJoinTransformer = (hit0: Hit, hit1: Hit) => any;


declare type EventResizeJoinTransforms = (hit0: Hit, hit1: Hit) => false | Dictionary;


declare const DRAG_META_REFINERS: {
    startTime: typeof createDuration;
    duration: typeof createDuration;
    create: BooleanConstructor;
    sourceId: StringConstructor;
};
declare type DragMetaInput = RawOptionsFromRefiners<typeof DRAG_META_REFINERS> & {
    [otherProp: string]: any;
};
interface DragMeta {
    startTime: Duration | null;
    duration: Duration | null;
    create: boolean;
    sourceId: string;
    leftoverProps: Dictionary;
}
declare function parseDragMeta(raw: DragMetaInput): DragMeta;

declare type ExternalDefTransform = (dateSpan: DateSpan, dragMeta: DragMeta) => any;


declare class Theme {
    classes: any;
    iconClasses: any;
    rtlIconClasses: any;
    baseIconClass: string;
    iconOverrideOption: any;
    iconOverrideCustomButtonOption: any;
    iconOverridePrefix: string;
    constructor(calendarOptions: CalendarOptionsRefined);
    setIconOverride(iconOverrideHash: any): void;
    applyIconOverridePrefix(className: any): any;
    getClass(key: any): any;
    getIconClass(buttonName: any, isRtl?: boolean): string;
    getCustomButtonIconClass(customButtonProps: any): string;
}
declare type ThemeClass = {
    new (calendarOptions: any): Theme;
};


declare type EventSourceFunc = (arg: {
    start: Date;
    end: Date;
    startStr: string;
    endStr: string;
    timeZone: string;
}, successCallback: (events: EventInput[]) => void, failureCallback: (error: EventSourceError) => void) => (void | PromiseLike<EventInput[]>);

declare const JSON_FEED_EVENT_SOURCE_REFINERS: {
    method: StringConstructor;
    extraParams: Identity<Record<string, any> | (() => Dictionary)>;
    startParam: StringConstructor;
    endParam: StringConstructor;
    timeZoneParam: StringConstructor;
};


declare const EVENT_SOURCE_REFINERS: {
    id: StringConstructor;
    defaultAllDay: BooleanConstructor;
    url: StringConstructor;
    events: Identity<EventInput[] | EventSourceFunc>;
    eventDataTransform: Identity<EventInputTransformer>;
    success: Identity<EventSourceSuccessResponseHandler>;
    failure: Identity<EventSourceErrorResponseHandler>;
};
declare type BuiltInEventSourceRefiners = typeof EVENT_SOURCE_REFINERS & typeof JSON_FEED_EVENT_SOURCE_REFINERS;
interface EventSourceRefiners extends BuiltInEventSourceRefiners {
}
declare type EventSourceInputObject = EventUiInput & RawOptionsFromRefiners<Required<EventSourceRefiners>>;
declare type EventSourceInput = EventSourceInputObject | 
EventInput[] | EventSourceFunc | 
string;
declare type EventSourceRefined = EventUiRefined & RefinedOptionsFromRefiners<Required<EventSourceRefiners>>;

interface EventSourceDef<Meta> {
    ignoreRange?: boolean;
    parseMeta: (refined: EventSourceRefined) => Meta | null;
    fetch: EventSourceFetcher<Meta>;
}


interface ZonedMarker {
    marker: DateMarker;
    timeZoneOffset: number;
}
interface ExpandedZonedMarker extends ZonedMarker {
    array: number[];
    year: number;
    month: number;
    day: number;
    hour: number;
    minute: number;
    second: number;
    millisecond: number;
}

interface VerboseFormattingArg {
    date: ExpandedZonedMarker;
    start: ExpandedZonedMarker;
    end?: ExpandedZonedMarker;
    timeZone: string;
    localeCodes: string[];
    defaultSeparator: string;
}
declare type CmdFormatterFunc = (cmd: string, arg: VerboseFormattingArg) => string;
interface DateFormattingContext {
    timeZone: string;
    locale: Locale;
    calendarSystem: CalendarSystem;
    computeWeekNumber: (d: DateMarker) => number;
    weekText: string;
    cmdFormatter?: CmdFormatterFunc;
    defaultSeparator: string;
}
interface DateFormatter {
    format(date: ZonedMarker, context: DateFormattingContext): string;
    formatRange(start: ZonedMarker, end: ZonedMarker, context: DateFormattingContext, betterDefaultSeparator?: string): string;
}


interface ParsedRecurring<RecurringData> {
    typeData: RecurringData;
    allDayGuess: boolean | null;
    duration: Duration | null;
}
interface RecurringType<RecurringData> {
    parse: (refined: EventRefined, dateEnv: DateEnv) => ParsedRecurring<RecurringData> | null;
    expand: (typeData: any, framingRange: DateRange, dateEnv: DateEnv) => DateMarker[];
}

declare abstract class NamedTimeZoneImpl {
    timeZoneName: string;
    constructor(timeZoneName: string);
    abstract offsetForArray(a: number[]): number;
    abstract timestampToArray(ms: number): number[];
}
declare type NamedTimeZoneImplClass = {
    new (timeZoneName: string): NamedTimeZoneImpl;
};


interface HandlerFuncTypeHash {
    [eventName: string]: (...args: any[]) => any;
}
declare class Emitter<HandlerFuncs extends HandlerFuncTypeHash> {
    private handlers;
    private options;
    private thisContext;
    setThisContext(thisContext: any): void;
    setOptions(options: Partial<HandlerFuncs>): void;
    on<Prop extends keyof HandlerFuncs>(type: Prop, handler: HandlerFuncs[Prop]): void;
    off<Prop extends keyof HandlerFuncs>(type: Prop, handler?: HandlerFuncs[Prop]): void;
    trigger<Prop extends keyof HandlerFuncs>(type: Prop, ...args: Parameters<HandlerFuncs[Prop]>): void;
    hasHandlers(type: keyof HandlerFuncs): number | Partial<HandlerFuncs>[keyof HandlerFuncs];
}


declare abstract class ElementDragging {
    emitter: Emitter<any>;
    constructor(el: HTMLElement, selector?: string);
    destroy(): void;
    abstract setIgnoreMove(bool: boolean): void;
    setMirrorIsVisible(bool: boolean): void;
    setMirrorNeedsRevert(bool: boolean): void;
    setAutoScrollEnabled(bool: boolean): void;
}
declare type ElementDraggingClass = {
    new (el: HTMLElement, selector?: string): ElementDragging;
};


declare type CssDimValue = string | number;
interface ColProps {
    width?: CssDimValue;
    minWidth?: CssDimValue;
    span?: number;
}
interface SectionConfig {
    outerContent?: VNode;
    type: 'body' | 'header' | 'footer';
    className?: string;
    maxHeight?: number;
    liquid?: boolean;
    expandRows?: boolean;
    syncRowHeights?: boolean;
    isSticky?: boolean;
}
declare type ChunkConfigContent = (contentProps: ChunkContentCallbackArgs) => VNode;
declare type ChunkConfigRowContent = VNode | ChunkConfigContent;
interface ChunkConfig {
    outerContent?: VNode;
    content?: ChunkConfigContent;
    rowContent?: ChunkConfigRowContent;
    scrollerElRef?: Ref<HTMLDivElement>;
    elRef?: Ref<HTMLTableCellElement>;
    tableClassName?: string;
}
interface ChunkContentCallbackArgs {
    tableColGroupNode: VNode;
    tableMinWidth: CssDimValue;
    clientWidth: number | null;
    clientHeight: number | null;
    expandRows: boolean;
    syncRowHeights: boolean;
    rowSyncHeights: number[];
    reportRowHeightChange: (rowEl: HTMLElement, isStable: boolean) => void;
}
declare function computeShrinkWidth(chunkEls: HTMLElement[]): number;
interface ScrollerLike {
    needsYScrolling(): boolean;
    needsXScrolling(): boolean;
}
declare function getSectionHasLiquidHeight(props: {
    liquid: boolean;
}, sectionConfig: SectionConfig): boolean;
declare function getAllowYScrolling(props: {
    liquid: boolean;
}, sectionConfig: SectionConfig): boolean;
declare function renderChunkContent(sectionConfig: SectionConfig, chunkConfig: ChunkConfig, arg: ChunkContentCallbackArgs): VNode;
declare function isColPropsEqual(cols0: ColProps[], cols1: ColProps[]): boolean;
declare function renderMicroColGroup(cols: ColProps[], shrinkWidth?: number): VNode;
declare function sanitizeShrinkWidth(shrinkWidth?: number): number;
declare function hasShrinkWidth(cols: ColProps[]): boolean;
declare function getScrollGridClassNames(liquid: boolean, context: ViewContext): any[];
declare function getSectionClassNames(sectionConfig: SectionConfig, wholeTableVGrow: boolean): string[];
declare function renderScrollShim(arg: ChunkContentCallbackArgs): createElement.JSX.Element;
declare function getStickyHeaderDates(options: BaseOptionsRefined): boolean;
declare function getStickyFooterScrollbar(options: BaseOptionsRefined): boolean;


interface ScrollGridProps {
    colGroups?: ColGroupConfig[];
    sections: ScrollGridSectionConfig[];
    liquid: boolean;
    elRef?: Ref<any>;
}
interface ScrollGridSectionConfig extends SectionConfig {
    key: string;
    chunks?: ScrollGridChunkConfig[];
}
interface ScrollGridChunkConfig extends ChunkConfig {
    key: string;
}
interface ColGroupConfig {
    width?: CssDimValue;
    cols: ColProps[];
}
declare type ScrollGridImpl = {
    new (props: ScrollGridProps, context: ViewContext): Component<ScrollGridProps>;
};


interface PluginDefInput {
    deps?: PluginDef[];
    reducers?: ReducerFunc[];
    contextInit?: (context: CalendarContext) => void;
    eventRefiners?: GenericRefiners;
    eventDefMemberAdders?: EventDefMemberAdder[];
    eventSourceRefiners?: GenericRefiners;
    isDraggableTransformers?: eventIsDraggableTransformer[];
    eventDragMutationMassagers?: eventDragMutationMassager[];
    eventDefMutationAppliers?: eventDefMutationApplier[];
    dateSelectionTransformers?: dateSelectionJoinTransformer[];
    datePointTransforms?: DatePointTransform[];
    dateSpanTransforms?: DateSpanTransform[];
    views?: ViewConfigInputHash;
    viewPropsTransformers?: ViewPropsTransformerClass[];
    isPropsValid?: isPropsValidTester;
    externalDefTransforms?: ExternalDefTransform[];
    eventResizeJoinTransforms?: EventResizeJoinTransforms[];
    viewContainerAppends?: ViewContainerAppend[];
    eventDropTransformers?: EventDropTransformers[];
    componentInteractions?: InteractionClass[];
    calendarInteractions?: CalendarInteractionClass[];
    themeClasses?: {
        [themeSystemName: string]: ThemeClass;
    };
    eventSourceDefs?: EventSourceDef<any>[];
    cmdFormatter?: CmdFormatterFunc;
    recurringTypes?: RecurringType<any>[];
    namedTimeZonedImpl?: NamedTimeZoneImplClass;
    initialView?: string;
    elementDraggingImpl?: ElementDraggingClass;
    optionChangeHandlers?: OptionChangeHandlerMap;
    scrollGridImpl?: ScrollGridImpl;
    contentTypeHandlers?: ContentTypeHandlers;
    listenerRefiners?: GenericListenerRefiners;
    optionRefiners?: GenericRefiners;
    propSetHandlers?: {
        [propName: string]: (val: any, context: CalendarData) => void;
    };
}
interface PluginHooks {
    reducers: ReducerFunc[];
    contextInit: ((context: CalendarContext) => void)[];
    eventRefiners: GenericRefiners;
    eventDefMemberAdders: EventDefMemberAdder[];
    eventSourceRefiners: GenericRefiners;
    isDraggableTransformers: eventIsDraggableTransformer[];
    eventDragMutationMassagers: eventDragMutationMassager[];
    eventDefMutationAppliers: eventDefMutationApplier[];
    dateSelectionTransformers: dateSelectionJoinTransformer[];
    datePointTransforms: DatePointTransform[];
    dateSpanTransforms: DateSpanTransform[];
    views: ViewConfigInputHash;
    viewPropsTransformers: ViewPropsTransformerClass[];
    isPropsValid: isPropsValidTester | null;
    externalDefTransforms: ExternalDefTransform[];
    eventResizeJoinTransforms: EventResizeJoinTransforms[];
    viewContainerAppends: ViewContainerAppend[];
    eventDropTransformers: EventDropTransformers[];
    componentInteractions: InteractionClass[];
    calendarInteractions: CalendarInteractionClass[];
    themeClasses: {
        [themeSystemName: string]: ThemeClass;
    };
    eventSourceDefs: EventSourceDef<any>[];
    cmdFormatter?: CmdFormatterFunc;
    recurringTypes: RecurringType<any>[];
    namedTimeZonedImpl?: NamedTimeZoneImplClass;
    initialView: string;
    elementDraggingImpl?: ElementDraggingClass;
    optionChangeHandlers: OptionChangeHandlerMap;
    scrollGridImpl: ScrollGridImpl | null;
    contentTypeHandlers: ContentTypeHandlers;
    listenerRefiners: GenericListenerRefiners;
    optionRefiners: GenericRefiners;
    propSetHandlers: {
        [propName: string]: (val: any, context: CalendarData) => void;
    };
}
interface PluginDef extends PluginHooks {
    id: string;
    deps: PluginDef[];
}
declare type ViewPropsTransformerClass = new () => ViewPropsTransformer;
interface ViewPropsTransformer {
    transform(viewProps: ViewProps, calendarProps: CalendarContentProps): any;
}
declare type ViewContainerAppend = (context: CalendarContext) => ComponentChildren;


interface ViewSpec {
    type: string;
    component: ViewComponentType;
    duration: Duration;
    durationUnit: string;
    singleUnit: string;
    optionDefaults: ViewOptions;
    optionOverrides: ViewOptions;
    buttonTextOverride: string;
    buttonTextDefault: string;
}
declare type ViewSpecHash = {
    [viewType: string]: ViewSpec;
};

interface CalendarDataManagerState {
    dynamicOptionOverrides: CalendarOptions;
    currentViewType: string;
    currentDate: DateMarker;
    dateProfile: DateProfile;
    businessHours: EventStore;
    eventSources: EventSourceHash;
    eventUiBases: EventUiHash;
    loadingLevel: number;
    eventStore: EventStore;
    renderableEventStore: EventStore;
    dateSelection: DateSpan | null;
    eventSelection: string;
    eventDrag: EventInteractionState | null;
    eventResize: EventInteractionState | null;
    selectionConfig: EventUi;
}
interface CalendarOptionsData {
    localeDefaults: CalendarOptions;
    calendarOptions: CalendarOptionsRefined;
    toolbarConfig: any;
    availableRawLocales: any;
    dateEnv: DateEnv;
    theme: Theme;
    pluginHooks: PluginHooks;
    viewSpecs: ViewSpecHash;
}
interface CalendarCurrentViewData {
    viewSpec: ViewSpec;
    options: ViewOptionsRefined;
    viewApi: ViewApi;
    dateProfileGenerator: DateProfileGenerator;
}
declare type CalendarDataBase = CalendarOptionsData & CalendarCurrentViewData & CalendarDataManagerState;
interface CalendarData extends CalendarDataBase {
    viewTitle: string;
    calendarApi: CalendarApi;
    dispatch: (action: Action) => void;
    emitter: Emitter<CalendarListeners>;
    getCurrentData(): CalendarData;
}

declare class ViewApi {
    type: string;
    private getCurrentData;
    private dateEnv;
    constructor(type: string, getCurrentData: () => CalendarData, dateEnv: DateEnv);
    get calendar(): CalendarApi;
    get title(): string;
    get activeStart(): Date;
    get activeEnd(): Date;
    get currentStart(): Date;
    get currentEnd(): Date;
    getOption(name: string): any;
}


interface ScrollRequest {
    time?: Duration;
    [otherProp: string]: any;
}
declare type ScrollRequestHandler = (request: ScrollRequest) => boolean;
declare class ScrollResponder {
    private execFunc;
    private emitter;
    private scrollTime;
    queuedRequest: ScrollRequest;
    constructor(execFunc: ScrollRequestHandler, emitter: Emitter<CalendarListeners>, scrollTime: Duration);
    detach(): void;
    update(isDatesNew: boolean): void;
    private fireInitialScroll;
    private handleScrollRequest;
    private drain;
}


declare const ViewContextType: Context<ViewContext>;
declare type ResizeHandler = (force: boolean) => void;
interface ViewContext extends CalendarContext {
    options: ViewOptionsRefined;
    theme: Theme;
    isRtl: boolean;
    dateProfileGenerator: DateProfileGenerator;
    viewSpec: ViewSpec;
    viewApi: ViewApi;
    addResizeHandler: (handler: ResizeHandler) => void;
    removeResizeHandler: (handler: ResizeHandler) => void;
    createScrollResponder: (execFunc: ScrollRequestHandler) => ScrollResponder;
    registerInteractiveComponent: (component: DateComponent<any>, settingsInput: InteractionSettingsInput) => void;
    unregisterInteractiveComponent: (component: DateComponent<any>) => void;
}

declare function filterHash(hash: any, func: any): {};
declare function mapHash<InputItem, OutputItem>(hash: {
    [key: string]: InputItem;
}, func: (input: InputItem, key: string) => OutputItem): {
    [key: string]: OutputItem;
};
declare function buildHashFromArray<Item, ItemRes>(a: Item[], func: (item: Item, index: number) => [string, ItemRes]): {
    [key: string]: ItemRes;
};
declare function isPropsEqual(obj0: any, obj1: any): boolean;
declare function getUnequalProps(obj0: any, obj1: any): string[];
declare type EqualityFunc<T> = (a: T, b: T) => boolean;
declare type EqualityThing<T> = EqualityFunc<T> | true;
declare type EqualityFuncs<ObjType> = {
    [K in keyof ObjType]?: EqualityThing<ObjType[K]>;
};
declare function compareObjs(oldProps: any, newProps: any, equalityFuncs?: EqualityFuncs<any>): boolean;
declare function collectFromHash<Item>(hash: {
    [key: string]: Item;
}, startIndex?: number, endIndex?: number, step?: number): Item[];


declare abstract class PureComponent<Props = Dictionary, State = Dictionary> extends Component<Props, State> {
    static addPropsEquality: typeof addPropsEquality;
    static addStateEquality: typeof addStateEquality;
    static contextType: Context<ViewContext>;
    context: ViewContext;
    propEquality: EqualityFuncs<Props>;
    stateEquality: EqualityFuncs<State>;
    debug: boolean;
    shouldComponentUpdate(nextProps: Props, nextState: State): boolean;
}
declare abstract class BaseComponent<Props = Dictionary, State = Dictionary> extends PureComponent<Props, State> {
    static contextType: Context<ViewContext>;
    context: ViewContext;
}
declare function addPropsEquality(this: {
    prototype: {
        propEquality: any;
    };
}, propEquality: any): void;
declare function addStateEquality(this: {
    prototype: {
        stateEquality: any;
    };
}, stateEquality: any): void;
declare function setRef<RefType>(ref: Ref<RefType> | void, current: RefType): void;

interface EventInstance {
    instanceId: string;
    defId: string;
    range: DateRange;
    forcedStartTzo: number | null;
    forcedEndTzo: number | null;
}
declare type EventInstanceHash = {
    [instanceId: string]: EventInstance;
};
declare function createEventInstance(defId: string, range: DateRange, forcedStartTzo?: number, forcedEndTzo?: number): EventInstance;


interface Seg {
    component?: DateComponent<any, any>;
    isStart: boolean;
    isEnd: boolean;
    eventRange?: EventRenderRange;
    [otherProp: string]: any;
    el?: never;
}
interface EventSegUiInteractionState {
    affectedInstances: EventInstanceHash;
    segs: Seg[];
    isEvent: boolean;
}
declare abstract class DateComponent<Props = Dictionary, State = Dictionary> extends BaseComponent<Props, State> {
    uid: string;
    largeUnit: any;
    prepareHits(): void;
    queryHit(positionLeft: number, positionTop: number, elWidth: number, elHeight: number): Hit | null;
    isInteractionValid(interaction: EventInteractionState): boolean;
    isDateSelectionValid(selection: DateSpan): boolean;
    isValidSegDownEl(el: HTMLElement): boolean;
    isValidDateDownEl(el: HTMLElement): boolean;
    isPopover(): boolean;
    isInPopover(el: HTMLElement): boolean;
}


interface NativeFormatterOptions extends Intl.DateTimeFormatOptions {
    week?: 'short' | 'narrow' | 'numeric';
    meridiem?: 'lowercase' | 'short' | 'narrow' | boolean;
    omitZeroMinute?: boolean;
    omitCommas?: boolean;
    separator?: string;
}

declare type FuncFormatterFunc = (arg: VerboseFormattingArg) => string;

declare type FormatterInput = NativeFormatterOptions | string | FuncFormatterFunc;
declare function createFormatter(input: FormatterInput): DateFormatter;


declare type BusinessHoursInput = boolean | EventInput | EventInput[];
declare function parseBusinessHours(input: BusinessHoursInput, context: CalendarContext): EventStore;


interface NowIndicatorRootProps {
    isAxis: boolean;
    date: DateMarker;
    children: RenderHookPropsChildren;
}
interface NowIndicatorContentArg {
    isAxis: boolean;
    date: Date;
    view: ViewApi;
}
declare type NowIndicatorMountArg = MountArg<NowIndicatorContentArg>;
declare const NowIndicatorRoot: (props: NowIndicatorRootProps) => createElement.JSX.Element;


interface WeekNumberRootProps {
    date: DateMarker;
    defaultFormat: DateFormatter;
    children: RenderHookPropsChildren;
}
interface WeekNumberContentArg {
    num: number;
    text: string;
    date: Date;
}
declare type WeekNumberMountArg = MountArg<WeekNumberContentArg>;
declare const WeekNumberRoot: (props: WeekNumberRootProps) => createElement.JSX.Element;


interface DateMeta {
    dow: number;
    isDisabled: boolean;
    isOther: boolean;
    isToday: boolean;
    isPast: boolean;
    isFuture: boolean;
}
declare function getDateMeta(date: DateMarker, todayRange?: DateRange, nowDate?: DateMarker, dateProfile?: DateProfile): DateMeta;
declare function getDayClassNames(meta: DateMeta, theme: Theme): string[];
declare function getSlotClassNames(meta: DateMeta, theme: Theme): string[];


interface SlotLaneContentArg extends Partial<DateMeta> {
    time?: Duration;
    date?: Date;
    view: ViewApi;
}
declare type SlotLaneMountArg = MountArg<SlotLaneContentArg>;
interface SlotLabelContentArg {
    level: number;
    time: Duration;
    date: Date;
    view: ViewApi;
    text: string;
}
declare type SlotLabelMountArg = MountArg<SlotLabelContentArg>;
interface AllDayContentArg {
    text: string;
    view: ViewApi;
}
declare type AllDayMountArg = MountArg<AllDayContentArg>;
interface DayHeaderContentArg extends DateMeta {
    date: Date;
    view: ViewApi;
    text: string;
    [otherProp: string]: any;
}
declare type DayHeaderMountArg = MountArg<DayHeaderContentArg>;


interface DayCellHookPropsInput {
    date: DateMarker;
    dateProfile: DateProfile;
    todayRange: DateRange;
    dateEnv: DateEnv;
    viewApi: ViewApi;
    showDayNumber?: boolean;
    extraProps?: Dictionary;
}
interface DayCellContentArg extends DateMeta {
    date: DateMarker;
    view: ViewApi;
    dayNumberText: string;
    [extraProp: string]: any;
}
declare type DayCellMountArg = MountArg<DayCellContentArg>;
interface DayCellRootProps {
    elRef?: Ref<any>;
    date: DateMarker;
    dateProfile: DateProfile;
    todayRange: DateRange;
    showDayNumber?: boolean;
    extraHookProps?: Dictionary;
    children: (rootElRef: Ref<any>, classNames: string[], rootDataAttrs: any, isDisabled: boolean) => ComponentChildren;
}
declare class DayCellRoot extends BaseComponent<DayCellRootProps> {
    refineHookProps: (arg: DayCellHookPropsInput) => DayCellContentArg;
    normalizeClassNames: (generator: ClassNamesGenerator<DayCellContentArg>, hookProps: DayCellContentArg) => string[];
    render(): createElement.JSX.Element;
}
interface DayCellContentProps {
    date: DateMarker;
    dateProfile: DateProfile;
    todayRange: DateRange;
    showDayNumber?: boolean;
    extraHookProps?: Dictionary;
    defaultContent?: (hookProps: DayCellContentArg) => ComponentChildren;
    children: (innerElRef: Ref<any>, innerContent: ComponentChildren) => ComponentChildren;
}
declare class DayCellContent extends BaseComponent<DayCellContentProps> {
    render(): createElement.JSX.Element;
}

interface ViewRootProps {
    viewSpec: ViewSpec;
    children: (rootElRef: Ref<any>, classNames: string[]) => ComponentChildren;
    elRef?: Ref<any>;
}
interface ViewContentArg {
    view: ViewApi;
}
declare type ViewMountArg = MountArg<ViewContentArg>;
declare class ViewRoot extends BaseComponent<ViewRootProps> {
    normalizeClassNames: (generator: ClassNamesGenerator<ViewContentArg>, hookProps: ViewContentArg) => string[];
    render(): createElement.JSX.Element;
}


interface EventClickArg {
    el: HTMLElement;
    event: EventApi;
    jsEvent: MouseEvent;
    view: ViewApi;
}

interface EventHoveringArg {
    el: HTMLElement;
    event: EventApi;
    jsEvent: MouseEvent;
    view: ViewApi;
}

interface ToolbarInput {
    left?: string;
    center?: string;
    right?: string;
    start?: string;
    end?: string;
}
interface CustomButtonInput {
    text?: string;
    icon?: string;
    themeIcon?: string;
    bootstrapFontAwesome?: string;
    click?(ev: MouseEvent, element: HTMLElement): void;
}
interface ButtonIconsInput {
    prev?: string;
    next?: string;
    prevYear?: string;
    nextYear?: string;
    today?: string;
    [viewOrCustomButton: string]: string | undefined;
}
interface ButtonTextCompoundInput {
    prev?: string;
    next?: string;
    prevYear?: string;
    nextYear?: string;
    today?: string;
    month?: string;
    week?: string;
    day?: string;
    [viewOrCustomButton: string]: string | undefined;
}


declare type DatesSetArg = RangeApiWithTimeZone & {
    view: ViewApi;
};

interface EventAddArg {
    event: EventApi;
    relatedEvents: EventApi[];
    revert: () => void;
}
interface EventChangeArg {
    oldEvent: EventApi;
    event: EventApi;
    relatedEvents: EventApi[];
    revert: () => void;
}
interface EventRemoveArg {
    event: EventApi;
    relatedEvents: EventApi[];
    revert: () => void;
}

declare class EventApi {
    _context: CalendarContext;
    _def: EventDef;
    _instance: EventInstance | null;
    constructor(context: CalendarContext, def: EventDef, instance?: EventInstance);
    setProp(name: string, val: string): void;
    setExtendedProp(name: string, val: any): void;
    setStart(startInput: DateInput, options?: {
        granularity?: string;
        maintainDuration?: boolean;
    }): void;
    setEnd(endInput: DateInput | null, options?: {
        granularity?: string;
    }): void;
    setDates(startInput: DateInput, endInput: DateInput | null, options?: {
        allDay?: boolean;
        granularity?: string;
    }): void;
    moveStart(deltaInput: DurationInput): void;
    moveEnd(deltaInput: DurationInput): void;
    moveDates(deltaInput: DurationInput): void;
    setAllDay(allDay: boolean, options?: {
        maintainDuration?: boolean;
    }): void;
    formatRange(formatInput: FormatterInput): string;
    mutate(mutation: EventMutation): void;
    remove(): void;
    get source(): EventSourceApi | null;
    get start(): Date | null;
    get end(): Date | null;
    get startStr(): string;
    get endStr(): string;
    get id(): string;
    get groupId(): string;
    get allDay(): boolean;
    get title(): string;
    get url(): string;
    get display(): string;
    get startEditable(): boolean;
    get durationEditable(): boolean;
    get constraint(): string | EventStore;
    get overlap(): boolean;
    get allow(): AllowFunc;
    get backgroundColor(): string;
    get borderColor(): string;
    get textColor(): string;
    get classNames(): string[];
    get extendedProps(): Record<string, any>;
    toPlainObject(settings?: {
        collapseExtendedProps?: boolean;
        collapseColor?: boolean;
    }): Dictionary;
    toJSON(): Record<string, any>;
}
declare function buildEventApis(eventStore: EventStore, context: CalendarContext, excludeInstance?: EventInstance): EventApi[];


interface EventRenderRange extends EventTuple {
    ui: EventUi;
    range: DateRange;
    isStart: boolean;
    isEnd: boolean;
}
declare function sliceEventStore(eventStore: EventStore, eventUiBases: EventUiHash, framingRange: DateRange, nextDayThreshold?: Duration): {
    bg: EventRenderRange[];
    fg: EventRenderRange[];
};
declare function hasBgRendering(def: EventDef): boolean;
declare function setElSeg(el: HTMLElement, seg: Seg): void;
declare function getElSeg(el: HTMLElement): Seg | null;
declare function sortEventSegs(segs: any, eventOrderSpecs: OrderSpec<EventApi>[]): Seg[];
declare function buildSegCompareObj(seg: Seg): {
    id: string;
    start: number;
    end: number;
    duration: number;
    allDay: number;
    _seg: Seg;
    defId: string;
    sourceId: string;
    publicId: string;
    groupId: string;
    hasEnd: boolean;
    recurringDef: {
        typeId: number;
        typeData: any;
        duration: Duration;
    };
    title: string;
    url: string;
    ui: EventUi;
    extendedProps: Record<string, any>;
};
interface EventContentArg {
    event: EventApi;
    timeText: string;
    backgroundColor: string;
    borderColor: string;
    textColor: string;
    isDraggable: boolean;
    isStartResizable: boolean;
    isEndResizable: boolean;
    isMirror: boolean;
    isStart: boolean;
    isEnd: boolean;
    isPast: boolean;
    isFuture: boolean;
    isToday: boolean;
    isSelected: boolean;
    isDragging: boolean;
    isResizing: boolean;
    view: ViewApi;
}
declare type EventMountArg = MountArg<EventContentArg>;
declare function computeSegDraggable(seg: Seg, context: ViewContext): boolean;
declare function computeSegStartResizable(seg: Seg, context: ViewContext): boolean;
declare function computeSegEndResizable(seg: Seg, context: ViewContext): boolean;
declare function buildSegTimeText(seg: Seg, timeFormat: DateFormatter, context: ViewContext, defaultDisplayEventTime?: boolean, 
defaultDisplayEventEnd?: boolean, 
startOverride?: DateMarker, endOverride?: DateMarker): string;
declare function getSegMeta(seg: Seg, todayRange: DateRange, nowDate?: DateMarker): {
    isPast: boolean;
    isFuture: boolean;
    isToday: boolean;
};
declare function getEventClassNames(props: EventContentArg): string[];
declare function buildEventRangeKey(eventRange: EventRenderRange): string;


interface OpenDateSpanInput {
    start?: DateInput;
    end?: DateInput;
    allDay?: boolean;
    [otherProp: string]: any;
}
interface DateSpanInput extends OpenDateSpanInput {
    start: DateInput;
    end: DateInput;
}
interface OpenDateSpan {
    range: OpenDateRange;
    allDay: boolean;
    [otherProp: string]: any;
}
interface DateSpan extends OpenDateSpan {
    range: DateRange;
}
interface RangeApi {
    start: Date;
    end: Date;
    startStr: string;
    endStr: string;
}
interface DateSpanApi extends RangeApi {
    allDay: boolean;
}
interface RangeApiWithTimeZone extends RangeApi {
    timeZone: string;
}
interface DatePointApi {
    date: Date;
    dateStr: string;
    allDay: boolean;
}
declare function isDateSpansEqual(span0: DateSpan, span1: DateSpan): boolean;

declare type Action = {
    type: 'NOTHING';
} | 
{
    type: 'SET_OPTION';
    optionName: string;
    rawOptionValue: any;
} | 
{
    type: 'PREV';
} | {
    type: 'NEXT';
} | {
    type: 'CHANGE_DATE';
    dateMarker: DateMarker;
} | {
    type: 'CHANGE_VIEW_TYPE';
    viewType: string;
    dateMarker?: DateMarker;
} | {
    type: 'SELECT_DATES';
    selection: DateSpan;
} | {
    type: 'UNSELECT_DATES';
} | {
    type: 'SELECT_EVENT';
    eventInstanceId: string;
} | {
    type: 'UNSELECT_EVENT';
} | {
    type: 'SET_EVENT_DRAG';
    state: EventInteractionState;
} | {
    type: 'UNSET_EVENT_DRAG';
} | {
    type: 'SET_EVENT_RESIZE';
    state: EventInteractionState;
} | {
    type: 'UNSET_EVENT_RESIZE';
} | {
    type: 'ADD_EVENT_SOURCES';
    sources: EventSource<any>[];
} | {
    type: 'REMOVE_EVENT_SOURCE';
    sourceId: string;
} | {
    type: 'REMOVE_ALL_EVENT_SOURCES';
} | {
    type: 'FETCH_EVENT_SOURCES';
    sourceIds?: string[];
} | 
{
    type: 'RECEIVE_EVENTS';
    sourceId: string;
    fetchId: string;
    fetchRange: DateRange | null;
    rawEvents: EventInput[];
} | {
    type: 'RECEIVE_EVENT_ERROR';
    sourceId: string;
    fetchId: string;
    fetchRange: DateRange | null;
    error: EventSourceError;
} | 
{
    type: 'ADD_EVENTS';
    eventStore: EventStore;
} | {
    type: 'MERGE_EVENTS';
    eventStore: EventStore;
} | {
    type: 'REMOVE_EVENTS';
    eventStore: EventStore;
} | {
    type: 'REMOVE_ALL_EVENTS';
};


interface CalendarDataManagerProps {
    optionOverrides: CalendarOptions;
    calendarApi: CalendarApi;
    onAction?: (action: Action) => void;
    onData?: (data: CalendarData) => void;
}
declare type ReducerFunc = (
currentState: Dictionary | null, action: Action | null, context: CalendarContext & CalendarDataManagerState) => Dictionary;
declare class CalendarDataManager {
    private computeOptionsData;
    private computeCurrentViewData;
    private organizeRawLocales;
    private buildLocale;
    private buildPluginHooks;
    private buildDateEnv;
    private buildTheme;
    private parseToolbars;
    private buildViewSpecs;
    private buildDateProfileGenerator;
    private buildViewApi;
    private buildViewUiProps;
    private buildEventUiBySource;
    private buildEventUiBases;
    private parseContextBusinessHours;
    private buildTitle;
    emitter: Emitter<Required<RefinedOptionsFromRefiners<Required<CalendarListenerRefiners>>>>;
    private actionRunner;
    private props;
    private state;
    private data;
    currentCalendarOptionsInput: CalendarOptions;
    private currentCalendarOptionsRefined;
    private currentViewOptionsInput;
    private currentViewOptionsRefined;
    currentCalendarOptionsRefiners: any;
    constructor(props: CalendarDataManagerProps);
    getCurrentData: () => CalendarData;
    dispatch: (action: Action) => void;
    resetOptions(optionOverrides: CalendarOptions, append?: boolean): void;
    _handleAction(action: Action): void;
    updateData(): void;
    _computeOptionsData(optionOverrides: CalendarOptions, dynamicOptionOverrides: CalendarOptions, calendarApi: CalendarApi): CalendarOptionsData;
    processRawCalendarOptions(optionOverrides: CalendarOptions, dynamicOptionOverrides: CalendarOptions): {
        rawOptions: CalendarOptions;
        refinedOptions: CalendarOptionsRefined;
        pluginHooks: PluginHooks;
        availableLocaleData: RawLocaleInfo;
        localeDefaults: CalendarOptionsRefined;
        extra: {};
    };
    _computeCurrentViewData(viewType: string, optionsData: CalendarOptionsData, optionOverrides: CalendarOptions, dynamicOptionOverrides: CalendarOptions): CalendarCurrentViewData;
    processRawViewOptions(viewSpec: ViewSpec, pluginHooks: PluginHooks, localeDefaults: CalendarOptions, optionOverrides: CalendarOptions, dynamicOptionOverrides: CalendarOptions): {
        rawOptions: ViewOptions;
        refinedOptions: ViewOptionsRefined;
        extra: {};
    };
}


declare class CalendarApi {
    currentDataManager?: CalendarDataManager;
    getCurrentData(): CalendarData;
    dispatch(action: Action): void;
    get view(): ViewApi;
    batchRendering(callback: () => void): void;
    updateSize(): void;
    setOption<OptionName extends keyof CalendarOptions>(name: OptionName, val: CalendarOptions[OptionName]): void;
    getOption<OptionName extends keyof CalendarOptions>(name: OptionName): CalendarOptions[OptionName];
    getAvailableLocaleCodes(): string[];
    on<ListenerName extends keyof CalendarListeners>(handlerName: ListenerName, handler: CalendarListeners[ListenerName]): void;
    off<ListenerName extends keyof CalendarListeners>(handlerName: ListenerName, handler: CalendarListeners[ListenerName]): void;
    trigger<ListenerName extends keyof CalendarListeners>(handlerName: ListenerName, ...args: Parameters<CalendarListeners[ListenerName]>): void;
    changeView(viewType: string, dateOrRange?: DateRangeInput | DateInput): void;
    zoomTo(dateMarker: DateMarker, viewType?: string): void;
    private getUnitViewSpec;
    prev(): void;
    next(): void;
    prevYear(): void;
    nextYear(): void;
    today(): void;
    gotoDate(zonedDateInput: any): void;
    incrementDate(deltaInput: any): void;
    getDate(): Date;
    formatDate(d: DateInput, formatter: any): string;
    formatRange(d0: DateInput, d1: DateInput, settings: any): string;
    formatIso(d: DateInput, omitTime?: boolean): string;
    select(dateOrObj: DateInput | any, endDate?: DateInput): void;
    unselect(pev?: PointerDragEvent): void;
    addEvent(eventInput: EventInput, sourceInput?: EventSourceApi | string | boolean): EventApi | null;
    private triggerEventAdd;
    getEventById(id: string): EventApi | null;
    getEvents(): EventApi[];
    removeAllEvents(): void;
    getEventSources(): EventSourceApi[];
    getEventSourceById(id: string): EventSourceApi | null;
    addEventSource(sourceInput: EventSourceInput): EventSourceApi;
    removeAllEventSources(): void;
    refetchEvents(): void;
    scrollToTime(timeInput: DurationInput): void;
}


interface DateProfile {
    currentRange: DateRange;
    currentRangeUnit: string;
    isRangeAllDay: boolean;
    validRange: OpenDateRange;
    activeRange: DateRange | null;
    renderRange: DateRange;
    slotMinTime: Duration;
    slotMaxTime: Duration;
    isValid: boolean;
    dateIncrement: Duration;
}
interface DateProfileGeneratorProps extends DateProfileOptions {
    dateProfileGeneratorClass: DateProfileGeneratorClass;
    duration: Duration;
    durationUnit: string;
    usesMinMaxTime: boolean;
    dateEnv: DateEnv;
    calendarApi: CalendarApi;
}
interface DateProfileOptions {
    slotMinTime: Duration;
    slotMaxTime: Duration;
    showNonCurrentDates?: boolean;
    dayCount?: number;
    dateAlignment?: string;
    dateIncrement?: Duration;
    hiddenDays?: number[];
    weekends?: boolean;
    nowInput?: DateInput | (() => DateInput);
    validRangeInput?: DateRangeInput | ((this: CalendarApi, nowDate: Date) => DateRangeInput);
    visibleRangeInput?: DateRangeInput | ((this: CalendarApi, nowDate: Date) => DateRangeInput);
    monthMode?: boolean;
    fixedWeekCount?: boolean;
}
declare type DateProfileGeneratorClass = {
    new (props: DateProfileGeneratorProps): DateProfileGenerator;
};
declare class DateProfileGenerator {
    protected props: DateProfileGeneratorProps;
    nowDate: DateMarker;
    isHiddenDayHash: boolean[];
    constructor(props: DateProfileGeneratorProps);
    buildPrev(currentDateProfile: DateProfile, currentDate: DateMarker, forceToValid?: boolean): DateProfile;
    buildNext(currentDateProfile: DateProfile, currentDate: DateMarker, forceToValid?: boolean): DateProfile;
    build(currentDate: DateMarker, direction?: any, forceToValid?: boolean): DateProfile;
    buildValidRange(): OpenDateRange;
    buildCurrentRangeInfo(date: DateMarker, direction: any): {
        duration: any;
        unit: any;
        range: any;
    };
    getFallbackDuration(): Duration;
    adjustActiveRange(range: DateRange): {
        start: Date;
        end: Date;
    };
    buildRangeFromDuration(date: DateMarker, direction: any, duration: Duration, unit: any): any;
    buildRangeFromDayCount(date: DateMarker, direction: any, dayCount: any): {
        start: Date;
        end: Date;
    };
    buildCustomVisibleRange(date: DateMarker): DateRange;
    buildRenderRange(currentRange: DateRange, currentRangeUnit: any, isRangeAllDay: any): DateRange;
    buildDateIncrement(fallback: any): Duration;
    refineRange(rangeInput: DateRangeInput | undefined): DateRange | null;
    initHiddenDays(): void;
    trimHiddenDays(range: DateRange): DateRange | null;
    isHiddenDay(day: any): boolean;
    skipHiddenDays(date: DateMarker, inc?: number, isExclusive?: boolean): Date;
}


interface ViewProps {
    dateProfile: DateProfile;
    businessHours: EventStore;
    eventStore: EventStore;
    eventUiBases: EventUiHash;
    dateSelection: DateSpan | null;
    eventSelection: string;
    eventDrag: EventInteractionState | null;
    eventResize: EventInteractionState | null;
    isHeightAuto: boolean;
    forPrint: boolean;
}
declare function sliceEvents(props: ViewProps & {
    dateProfile: DateProfile;
    nextDayThreshold: Duration;
}, allDay?: boolean): EventRenderRange[];


declare const BASE_OPTION_REFINERS: {
    navLinkDayClick: Identity<string | ((this: CalendarApi, date: Date, jsEvent: VUIEvent) => void)>;
    navLinkWeekClick: Identity<string | ((this: CalendarApi, weekStart: Date, jsEvent: VUIEvent) => void)>;
    duration: typeof createDuration;
    bootstrapFontAwesome: Identity<false | ButtonIconsInput>;
    buttonIcons: Identity<false | ButtonIconsInput>;
    customButtons: Identity<{
        [name: string]: CustomButtonInput;
    }>;
    defaultAllDayEventDuration: typeof createDuration;
    defaultTimedEventDuration: typeof createDuration;
    nextDayThreshold: typeof createDuration;
    scrollTime: typeof createDuration;
    slotMinTime: typeof createDuration;
    slotMaxTime: typeof createDuration;
    dayPopoverFormat: typeof createFormatter;
    slotDuration: typeof createDuration;
    snapDuration: typeof createDuration;
    headerToolbar: Identity<false | ToolbarInput>;
    footerToolbar: Identity<false | ToolbarInput>;
    defaultRangeSeparator: StringConstructor;
    titleRangeSeparator: StringConstructor;
    forceEventDuration: BooleanConstructor;
    dayHeaders: BooleanConstructor;
    dayHeaderFormat: typeof createFormatter;
    dayHeaderClassNames: Identity<ClassNamesGenerator<DayHeaderContentArg>>;
    dayHeaderContent: Identity<CustomContentGenerator<DayHeaderContentArg>>;
    dayHeaderDidMount: Identity<DidMountHandler<MountArg<DayHeaderContentArg>>>;
    dayHeaderWillUnmount: Identity<WillUnmountHandler<MountArg<DayHeaderContentArg>>>;
    dayCellClassNames: Identity<ClassNamesGenerator<DayCellContentArg>>;
    dayCellContent: Identity<CustomContentGenerator<DayCellContentArg>>;
    dayCellDidMount: Identity<DidMountHandler<MountArg<DayCellContentArg>>>;
    dayCellWillUnmount: Identity<WillUnmountHandler<MountArg<DayCellContentArg>>>;
    initialView: StringConstructor;
    aspectRatio: NumberConstructor;
    weekends: BooleanConstructor;
    weekNumberCalculation: Identity<WeekNumberCalculation>;
    weekNumbers: BooleanConstructor;
    weekNumberClassNames: Identity<ClassNamesGenerator<WeekNumberContentArg>>;
    weekNumberContent: Identity<CustomContentGenerator<WeekNumberContentArg>>;
    weekNumberDidMount: Identity<DidMountHandler<MountArg<WeekNumberContentArg>>>;
    weekNumberWillUnmount: Identity<WillUnmountHandler<MountArg<WeekNumberContentArg>>>;
    editable: BooleanConstructor;
    viewClassNames: Identity<ClassNamesGenerator<ViewContentArg>>;
    viewDidMount: Identity<DidMountHandler<MountArg<ViewContentArg>>>;
    viewWillUnmount: Identity<WillUnmountHandler<MountArg<ViewContentArg>>>;
    nowIndicator: BooleanConstructor;
    nowIndicatorClassNames: Identity<ClassNamesGenerator<NowIndicatorContentArg>>;
    nowIndicatorContent: Identity<CustomContentGenerator<NowIndicatorContentArg>>;
    nowIndicatorDidMount: Identity<DidMountHandler<MountArg<NowIndicatorContentArg>>>;
    nowIndicatorWillUnmount: Identity<WillUnmountHandler<MountArg<NowIndicatorContentArg>>>;
    showNonCurrentDates: BooleanConstructor;
    lazyFetching: BooleanConstructor;
    startParam: StringConstructor;
    endParam: StringConstructor;
    timeZoneParam: StringConstructor;
    timeZone: StringConstructor;
    locales: Identity<LocaleInput[]>;
    locale: Identity<LocaleSingularArg>;
    themeSystem: Identity<string>;
    dragRevertDuration: NumberConstructor;
    dragScroll: BooleanConstructor;
    allDayMaintainDuration: BooleanConstructor;
    unselectAuto: BooleanConstructor;
    dropAccept: Identity<string | ((this: CalendarApi, draggable: any) => boolean)>;
    eventOrder: typeof parseFieldSpecs;
    handleWindowResize: BooleanConstructor;
    windowResizeDelay: NumberConstructor;
    longPressDelay: NumberConstructor;
    eventDragMinDistance: NumberConstructor;
    expandRows: BooleanConstructor;
    height: Identity<string | number>;
    contentHeight: Identity<string | number>;
    direction: Identity<"ltr" | "rtl">;
    weekNumberFormat: typeof createFormatter;
    eventResizableFromStart: BooleanConstructor;
    displayEventTime: BooleanConstructor;
    displayEventEnd: BooleanConstructor;
    weekText: StringConstructor;
    progressiveEventRendering: BooleanConstructor;
    businessHours: Identity<BusinessHoursInput>;
    initialDate: Identity<DateInput>;
    now: Identity<string | number | Date | number[] | ((this: CalendarApi) => DateInput)>;
    eventDataTransform: Identity<EventInputTransformer>;
    stickyHeaderDates: Identity<boolean | "auto">;
    stickyFooterScrollbar: Identity<boolean | "auto">;
    viewHeight: Identity<string | number>;
    defaultAllDay: BooleanConstructor;
    eventSourceFailure: Identity<(this: CalendarApi, error: any) => void>;
    eventSourceSuccess: Identity<(this: CalendarApi, eventsInput: EventInput[], xhr?: XMLHttpRequest) => EventInput[] | void>;
    eventDisplay: StringConstructor;
    eventStartEditable: BooleanConstructor;
    eventDurationEditable: BooleanConstructor;
    eventOverlap: Identity<boolean | OverlapFunc>;
    eventConstraint: Identity<ConstraintInput>;
    eventAllow: Identity<AllowFunc>;
    eventBackgroundColor: StringConstructor;
    eventBorderColor: StringConstructor;
    eventTextColor: StringConstructor;
    eventColor: StringConstructor;
    eventClassNames: Identity<ClassNamesGenerator<EventContentArg>>;
    eventContent: Identity<CustomContentGenerator<EventContentArg>>;
    eventDidMount: Identity<DidMountHandler<MountArg<EventContentArg>>>;
    eventWillUnmount: Identity<WillUnmountHandler<MountArg<EventContentArg>>>;
    selectConstraint: Identity<ConstraintInput>;
    selectOverlap: Identity<boolean | OverlapFunc>;
    selectAllow: Identity<AllowFunc>;
    droppable: BooleanConstructor;
    unselectCancel: StringConstructor;
    slotLabelFormat: Identity<string | NativeFormatterOptions | FuncFormatterFunc | FormatterInput[]>;
    slotLaneClassNames: Identity<ClassNamesGenerator<SlotLaneContentArg>>;
    slotLaneContent: Identity<CustomContentGenerator<SlotLaneContentArg>>;
    slotLaneDidMount: Identity<DidMountHandler<MountArg<SlotLaneContentArg>>>;
    slotLaneWillUnmount: Identity<WillUnmountHandler<MountArg<SlotLaneContentArg>>>;
    slotLabelClassNames: Identity<ClassNamesGenerator<SlotLabelContentArg>>;
    slotLabelContent: Identity<CustomContentGenerator<SlotLabelContentArg>>;
    slotLabelDidMount: Identity<DidMountHandler<MountArg<SlotLabelContentArg>>>;
    slotLabelWillUnmount: Identity<WillUnmountHandler<MountArg<SlotLabelContentArg>>>;
    dayMaxEvents: Identity<number | boolean>;
    dayMaxEventRows: Identity<number | boolean>;
    dayMinWidth: NumberConstructor;
    slotLabelInterval: typeof createDuration;
    allDayText: StringConstructor;
    allDayClassNames: Identity<ClassNamesGenerator<AllDayContentArg>>;
    allDayContent: Identity<CustomContentGenerator<AllDayContentArg>>;
    allDayDidMount: Identity<DidMountHandler<MountArg<AllDayContentArg>>>;
    allDayWillUnmount: Identity<WillUnmountHandler<MountArg<AllDayContentArg>>>;
    slotMinWidth: NumberConstructor;
    navLinks: BooleanConstructor;
    eventTimeFormat: typeof createFormatter;
    rerenderDelay: NumberConstructor;
    moreLinkText: Identity<string | ((this: CalendarApi, num: number) => string)>;
    selectMinDistance: NumberConstructor;
    selectable: BooleanConstructor;
    selectLongPressDelay: NumberConstructor;
    eventLongPressDelay: NumberConstructor;
    selectMirror: BooleanConstructor;
    eventMinHeight: NumberConstructor;
    slotEventOverlap: BooleanConstructor;
    plugins: Identity<PluginDef[]>;
    firstDay: NumberConstructor;
    dayCount: NumberConstructor;
    dateAlignment: StringConstructor;
    dateIncrement: typeof createDuration;
    hiddenDays: Identity<number[]>;
    monthMode: BooleanConstructor;
    fixedWeekCount: BooleanConstructor;
    validRange: Identity<DateRangeInput | ((this: CalendarApi, nowDate: Date) => DateRangeInput)>;
    visibleRange: Identity<DateRangeInput | ((this: CalendarApi, currentDate: Date) => DateRangeInput)>;
    titleFormat: Identity<FormatterInput>;
    noEventsText: StringConstructor;
};
declare type BuiltInBaseOptionRefiners = typeof BASE_OPTION_REFINERS;
interface BaseOptionRefiners extends BuiltInBaseOptionRefiners {
}
declare type BaseOptions = RawOptionsFromRefiners<
Required<BaseOptionRefiners>>;
declare const BASE_OPTION_DEFAULTS: {
    eventDisplay: string;
    defaultRangeSeparator: string;
    titleRangeSeparator: string;
    defaultTimedEventDuration: string;
    defaultAllDayEventDuration: {
        day: number;
    };
    forceEventDuration: boolean;
    nextDayThreshold: string;
    dayHeaders: boolean;
    initialView: string;
    aspectRatio: number;
    headerToolbar: {
        start: string;
        center: string;
        end: string;
    };
    weekends: boolean;
    weekNumbers: boolean;
    weekNumberCalculation: WeekNumberCalculation;
    editable: boolean;
    nowIndicator: boolean;
    scrollTime: string;
    slotMinTime: string;
    slotMaxTime: string;
    showNonCurrentDates: boolean;
    lazyFetching: boolean;
    startParam: string;
    endParam: string;
    timeZoneParam: string;
    timeZone: string;
    locales: any[];
    locale: string;
    themeSystem: string;
    dragRevertDuration: number;
    dragScroll: boolean;
    allDayMaintainDuration: boolean;
    unselectAuto: boolean;
    dropAccept: string;
    eventOrder: string;
    dayPopoverFormat: {
        month: string;
        day: string;
        year: string;
    };
    handleWindowResize: boolean;
    windowResizeDelay: number;
    longPressDelay: number;
    eventDragMinDistance: number;
    expandRows: boolean;
    navLinks: boolean;
    selectable: boolean;
};
declare type BaseOptionsRefined = DefaultedRefinedOptions<RefinedOptionsFromRefiners<Required<BaseOptionRefiners>>, 
keyof typeof BASE_OPTION_DEFAULTS>;
declare const CALENDAR_LISTENER_REFINERS: {
    datesSet: Identity<(arg: DatesSetArg) => void>;
    eventsSet: Identity<(events: EventApi[]) => void>;
    eventAdd: Identity<(arg: EventAddArg) => void>;
    eventChange: Identity<(arg: EventChangeArg) => void>;
    eventRemove: Identity<(arg: EventRemoveArg) => void>;
    windowResize: Identity<(arg: {
        view: ViewApi;
    }) => void>;
    eventClick: Identity<(arg: EventClickArg) => void>;
    eventMouseEnter: Identity<(arg: EventHoveringArg) => void>;
    eventMouseLeave: Identity<(arg: EventHoveringArg) => void>;
    select: Identity<(arg: DateSelectArg) => void>;
    unselect: Identity<(arg: DateUnselectArg) => void>;
    loading: Identity<(isLoading: boolean) => void>;
    _unmount: Identity<() => void>;
    _beforeprint: Identity<() => void>;
    _afterprint: Identity<() => void>;
    _noEventDrop: Identity<() => void>;
    _noEventResize: Identity<() => void>;
    _resize: Identity<(forced: boolean) => void>;
    _scrollRequest: Identity<(arg: any) => void>;
};
declare type BuiltInCalendarListenerRefiners = typeof CALENDAR_LISTENER_REFINERS;
interface CalendarListenerRefiners extends BuiltInCalendarListenerRefiners {
}
declare type CalendarListenersLoose = RefinedOptionsFromRefiners<Required<CalendarListenerRefiners>>;
declare type CalendarListeners = Required<CalendarListenersLoose>;
declare const CALENDAR_OPTION_REFINERS: {
    buttonText: Identity<ButtonTextCompoundInput>;
    views: Identity<{
        [viewId: string]: ViewOptions;
    }>;
    plugins: Identity<PluginDef[]>;
    initialEvents: Identity<EventSourceInput>;
    events: Identity<EventSourceInput>;
    eventSources: Identity<EventSourceInput[]>;
};
declare type BuiltInCalendarOptionRefiners = typeof CALENDAR_OPTION_REFINERS;
interface CalendarOptionRefiners extends BuiltInCalendarOptionRefiners {
}
declare type CalendarOptions = BaseOptions & CalendarListenersLoose & RawOptionsFromRefiners<Required<CalendarOptionRefiners>>;
declare type CalendarOptionsRefined = BaseOptionsRefined & CalendarListenersLoose & RefinedOptionsFromRefiners<Required<CalendarOptionRefiners>>;
declare const VIEW_OPTION_REFINERS: {
    type: StringConstructor;
    component: Identity<ComponentType<ViewProps>>;
    buttonText: StringConstructor;
    buttonTextKey: StringConstructor;
    dateProfileGeneratorClass: Identity<DateProfileGeneratorClass>;
    usesMinMaxTime: BooleanConstructor;
    classNames: Identity<ClassNamesGenerator<SpecificViewContentArg>>;
    content: Identity<CustomContentGenerator<SpecificViewContentArg>>;
    didMount: Identity<DidMountHandler<MountArg<SpecificViewContentArg>>>;
    willUnmount: Identity<WillUnmountHandler<MountArg<SpecificViewContentArg>>>;
};
declare type BuiltInViewOptionRefiners = typeof VIEW_OPTION_REFINERS;
interface ViewOptionRefiners extends BuiltInViewOptionRefiners {
}
declare type ViewOptions = BaseOptions & CalendarListenersLoose & RawOptionsFromRefiners<Required<ViewOptionRefiners>>;
declare type ViewOptionsRefined = BaseOptionsRefined & CalendarListenersLoose & RefinedOptionsFromRefiners<Required<ViewOptionRefiners>>;
declare function refineProps<Refiners extends GenericRefiners, Raw extends RawOptionsFromRefiners<Refiners>>(input: Raw, refiners: Refiners): {
    refined: RefinedOptionsFromRefiners<Refiners>;
    extra: Dictionary;
};
declare type GenericRefiners = {
    [propName: string]: (input: any) => any;
};
declare type GenericListenerRefiners = {
    [listenerName: string]: Identity<(this: CalendarApi, ...args: any[]) => void>;
};
declare type RawOptionsFromRefiners<Refiners extends GenericRefiners> = {
    [Prop in keyof Refiners]?: Refiners[Prop] extends ((input: infer RawType) => infer RefinedType) ? (any extends RawType ? RefinedType : RawType) : never;
};
declare type RefinedOptionsFromRefiners<Refiners extends GenericRefiners> = {
    [Prop in keyof Refiners]?: Refiners[Prop] extends ((input: any) => infer RefinedType) ? RefinedType : never;
};
declare type DefaultedRefinedOptions<RefinedOptions extends Dictionary, DefaultKey extends keyof RefinedOptions> = Required<Pick<RefinedOptions, DefaultKey>> & Partial<Omit<RefinedOptions, DefaultKey>>;
declare type Dictionary = Record<string, any>;
declare type Identity<T = any> = (raw: T) => T;
declare function identity<T>(raw: T): T;

declare type LocaleCodeArg = string | string[];
declare type LocaleSingularArg = LocaleCodeArg | LocaleInput;
interface Locale {
    codeArg: LocaleCodeArg;
    codes: string[];
    week: {
        dow: number;
        doy: number;
    };
    simpleNumberFormat: Intl.NumberFormat;
    options: CalendarOptionsRefined;
}
interface LocaleInput extends CalendarOptions {
    code: string;
}
declare type LocaleInputMap = {
    [code: string]: LocaleInput;
};
interface RawLocaleInfo {
    map: LocaleInputMap;
    defaultCode: string;
}

declare type WeekNumberCalculation = 'local' | 'ISO' | ((m: Date) => number);
interface DateEnvSettings {
    timeZone: string;
    namedTimeZoneImpl?: NamedTimeZoneImplClass;
    calendarSystem: string;
    locale: Locale;
    weekNumberCalculation?: WeekNumberCalculation;
    firstDay?: number;
    weekText?: string;
    cmdFormatter?: CmdFormatterFunc;
    defaultSeparator?: string;
}
declare type DateInput = Date | string | number | number[];
interface DateMarkerMeta {
    marker: DateMarker;
    isTimeUnspecified: boolean;
    forcedTzo: number | null;
}
declare class DateEnv {
    timeZone: string;
    namedTimeZoneImpl: NamedTimeZoneImpl;
    canComputeOffset: boolean;
    calendarSystem: CalendarSystem;
    locale: Locale;
    weekDow: number;
    weekDoy: number;
    weekNumberFunc: any;
    weekText: string;
    cmdFormatter?: CmdFormatterFunc;
    defaultSeparator: string;
    constructor(settings: DateEnvSettings);
    createMarker(input: DateInput): DateMarker;
    createNowMarker(): DateMarker;
    createMarkerMeta(input: DateInput): DateMarkerMeta;
    parse(s: string): {
        marker: Date;
        isTimeUnspecified: boolean;
        forcedTzo: any;
    };
    getYear(marker: DateMarker): number;
    getMonth(marker: DateMarker): number;
    add(marker: DateMarker, dur: Duration): DateMarker;
    subtract(marker: DateMarker, dur: Duration): DateMarker;
    addYears(marker: DateMarker, n: number): Date;
    addMonths(marker: DateMarker, n: number): Date;
    diffWholeYears(m0: DateMarker, m1: DateMarker): number;
    diffWholeMonths(m0: DateMarker, m1: DateMarker): number;
    greatestWholeUnit(m0: DateMarker, m1: DateMarker): {
        unit: string;
        value: number;
    };
    countDurationsBetween(m0: DateMarker, m1: DateMarker, d: Duration): number;
    startOf(m: DateMarker, unit: string): Date;
    startOfYear(m: DateMarker): DateMarker;
    startOfMonth(m: DateMarker): DateMarker;
    startOfWeek(m: DateMarker): DateMarker;
    computeWeekNumber(marker: DateMarker): number;
    format(marker: DateMarker, formatter: DateFormatter, dateOptions?: {
        forcedTzo?: number;
    }): string;
    formatRange(start: DateMarker, end: DateMarker, formatter: DateFormatter, dateOptions?: {
        forcedStartTzo?: number;
        forcedEndTzo?: number;
        isEndExclusive?: boolean;
        defaultSeparator?: string;
    }): string;
    formatIso(marker: DateMarker, extraOptions?: any): string;
    timestampToMarker(ms: number): Date;
    offsetForMarker(m: DateMarker): number;
    toDate(m: DateMarker, forcedTzo?: number): Date;
}


interface CalendarContext {
    dateEnv: DateEnv;
    options: BaseOptionsRefined;
    pluginHooks: PluginHooks;
    emitter: Emitter<CalendarListeners>;
    dispatch(action: Action): void;
    getCurrentData(): CalendarData;
    calendarApi: CalendarApi;
}


declare const EVENT_UI_REFINERS: {
    display: StringConstructor;
    editable: BooleanConstructor;
    startEditable: BooleanConstructor;
    durationEditable: BooleanConstructor;
    constraint: Identity<any>;
    overlap: Identity<boolean>;
    allow: Identity<AllowFunc>;
    className: typeof parseClassNames;
    classNames: typeof parseClassNames;
    color: StringConstructor;
    backgroundColor: StringConstructor;
    borderColor: StringConstructor;
    textColor: StringConstructor;
};
declare type BuiltInEventUiRefiners = typeof EVENT_UI_REFINERS;
interface EventUiRefiners extends BuiltInEventUiRefiners {
}
declare type EventUiInput = RawOptionsFromRefiners<Required<EventUiRefiners>>;
declare type EventUiRefined = RefinedOptionsFromRefiners<Required<EventUiRefiners>>;
interface EventUi {
    display: string | null;
    startEditable: boolean | null;
    durationEditable: boolean | null;
    constraints: Constraint[];
    overlap: boolean | null;
    allows: AllowFunc[];
    backgroundColor: string;
    borderColor: string;
    textColor: string;
    classNames: string[];
}
declare type EventUiHash = {
    [defId: string]: EventUi;
};
declare function createEventUi(refined: EventUiRefined, context: CalendarContext): EventUi;
declare function combineEventUis(uis: EventUi[]): EventUi;

interface EventDef {
    defId: string;
    sourceId: string;
    publicId: string;
    groupId: string;
    allDay: boolean;
    hasEnd: boolean;
    recurringDef: {
        typeId: number;
        typeData: any;
        duration: Duration | null;
    } | null;
    title: string;
    url: string;
    ui: EventUi;
    extendedProps: Dictionary;
}
declare type EventDefHash = {
    [defId: string]: EventDef;
};


interface EventStore {
    defs: EventDefHash;
    instances: EventInstanceHash;
}
declare function eventTupleToStore(tuple: EventTuple, eventStore?: EventStore): EventStore;
declare function getRelevantEvents(eventStore: EventStore, instanceId: string): EventStore;
declare function createEmptyEventStore(): EventStore;
declare function mergeEventStores(store0: EventStore, store1: EventStore): EventStore;
declare function filterEventStoreDefs(eventStore: EventStore, filterFunc: (eventDef: EventDef) => boolean): EventStore;

interface SplittableProps {
    businessHours: EventStore | null;
    dateSelection: DateSpan | null;
    eventStore: EventStore;
    eventUiBases: EventUiHash;
    eventSelection: string;
    eventDrag: EventInteractionState | null;
    eventResize: EventInteractionState | null;
}
declare abstract class Splitter<PropsType extends SplittableProps = SplittableProps> {
    private getKeysForEventDefs;
    private splitDateSelection;
    private splitEventStore;
    private splitIndividualUi;
    private splitEventDrag;
    private splitEventResize;
    private eventUiBuilders;
    abstract getKeyInfo(props: PropsType): {
        [key: string]: {
            ui?: EventUi;
            businessHours?: EventStore;
        };
    };
    abstract getKeysForDateSpan(dateSpan: DateSpan): string[];
    abstract getKeysForEventDef(eventDef: EventDef): string[];
    splitProps(props: PropsType): {
        [key: string]: SplittableProps;
    };
    private _splitDateSpan;
    private _getKeysForEventDefs;
    private _splitEventStore;
    private _splitIndividualUi;
    private _splitInteraction;
}


declare type ConstraintInput = 'businessHours' | string | EventInput | EventInput[];
declare type Constraint = 'businessHours' | string | EventStore | false;
declare type OverlapFunc = ((stillEvent: EventApi, movingEvent: EventApi | null) => boolean);
declare type AllowFunc = (span: DateSpanApi, movingEvent: EventApi | null) => boolean;
declare type isPropsValidTester = (props: SplittableProps, context: CalendarContext) => boolean;

declare const EVENT_REFINERS: {
    extendedProps: Identity<Record<string, any>>;
    start: Identity<DateInput>;
    end: Identity<DateInput>;
    date: Identity<DateInput>;
    allDay: BooleanConstructor;
    id: StringConstructor;
    groupId: StringConstructor;
    title: StringConstructor;
    url: StringConstructor;
};
declare type BuiltInEventRefiners = typeof EVENT_REFINERS;
interface EventRefiners extends BuiltInEventRefiners {
}
declare type EventInput = EventUiInput & RawOptionsFromRefiners<Required<EventRefiners>> & 
{
    [extendedProp: string]: any;
};
declare type EventRefined = EventUiRefined & RefinedOptionsFromRefiners<Required<EventRefiners>>;
interface EventTuple {
    def: EventDef;
    instance: EventInstance | null;
}
declare type EventInputTransformer = (input: EventInput) => EventInput;
declare type EventDefMemberAdder = (refined: EventRefined) => Partial<EventDef>;
declare function refineEventDef(raw: EventInput, context: CalendarContext, refiners?: {
    extendedProps: Identity<Record<string, any>>;
    start: Identity<DateInput>;
    end: Identity<DateInput>;
    date: Identity<DateInput>;
    allDay: BooleanConstructor;
    id: StringConstructor;
    groupId: StringConstructor;
    title: StringConstructor;
    url: StringConstructor;
    display: StringConstructor;
    editable: BooleanConstructor;
    startEditable: BooleanConstructor;
    durationEditable: BooleanConstructor;
    constraint: Identity<any>;
    overlap: Identity<boolean>;
    allow: Identity<AllowFunc>;
    className: typeof parseClassNames;
    classNames: typeof parseClassNames;
    color: StringConstructor;
    backgroundColor: StringConstructor;
    borderColor: StringConstructor;
    textColor: StringConstructor;
}): {
    refined: RefinedOptionsFromRefiners<{
        extendedProps: Identity<Record<string, any>>;
        start: Identity<DateInput>;
        end: Identity<DateInput>;
        date: Identity<DateInput>;
        allDay: BooleanConstructor;
        id: StringConstructor;
        groupId: StringConstructor;
        title: StringConstructor;
        url: StringConstructor;
        display: StringConstructor;
        editable: BooleanConstructor;
        startEditable: BooleanConstructor;
        durationEditable: BooleanConstructor;
        constraint: Identity<any>;
        overlap: Identity<boolean>;
        allow: Identity<AllowFunc>;
        className: typeof parseClassNames;
        classNames: typeof parseClassNames;
        color: StringConstructor;
        backgroundColor: StringConstructor;
        borderColor: StringConstructor;
        textColor: StringConstructor;
    }>;
    extra: Record<string, any>;
};
declare function parseEventDef(refined: EventRefined, extra: Dictionary, sourceId: string, allDay: boolean, hasEnd: boolean, context: CalendarContext): EventDef;

declare type EventSourceError = {
    message: string;
    response?: any;
    [otherProp: string]: any;
};
declare type EventSourceSuccessResponseHandler = (this: CalendarApi, rawData: any, response: any) => EventInput[] | void;
declare type EventSourceErrorResponseHandler = (error: EventSourceError) => void;
interface EventSource<Meta> {
    _raw: any;
    sourceId: string;
    sourceDefId: number;
    meta: Meta;
    publicId: string;
    isFetching: boolean;
    latestFetchId: string;
    fetchRange: DateRange | null;
    defaultAllDay: boolean | null;
    eventDataTransform: EventInputTransformer;
    ui: EventUi;
    success: EventSourceSuccessResponseHandler | null;
    failure: EventSourceErrorResponseHandler | null;
    extendedProps: Dictionary;
}
declare type EventSourceHash = {
    [sourceId: string]: EventSource<any>;
};
declare type EventSourceFetcher<Meta> = (arg: {
    eventSource: EventSource<Meta>;
    range: DateRange;
    context: CalendarContext;
}, success: (res: {
    rawEvents: EventInput[];
    xhr?: XMLHttpRequest;
}) => void, failure: (error: EventSourceError) => void) => (void | PromiseLike<EventInput[]>);


declare class EventSourceApi {
    private context;
    internalEventSource: EventSource<any>;
    constructor(context: CalendarContext, internalEventSource: EventSource<any>);
    remove(): void;
    refetch(): void;
    get id(): string;
    get url(): string;
}


interface FormatDateOptions extends NativeFormatterOptions {
    locale?: string;
}
interface FormatRangeOptions extends FormatDateOptions {
    separator?: string;
    isEndExclusive?: boolean;
}
declare function formatDate(dateInput: DateInput, options?: FormatDateOptions): string;
declare function formatRange(startInput: DateInput, endInput: DateInput, options: FormatRangeOptions): string;


declare function computeVisibleDayRange(timedRange: OpenDateRange, nextDayThreshold?: Duration): OpenDateRange;
declare function isMultiDayRange(range: DateRange): boolean;
declare function diffDates(date0: DateMarker, date1: DateMarker, dateEnv: DateEnv, largeUnit?: string): Duration;


declare function removeExact(array: any, exactVal: any): number;
declare function isArraysEqual(a0: any, a1: any, equalityFunc?: (v0: any, v1: any) => boolean): boolean;


declare function memoize<Args extends any[], Res>(workerFunc: (...args: Args) => Res, resEquality?: (res0: Res, res1: Res) => boolean, teardownFunc?: (res: Res) => void): (...args: Args) => Res;
declare function memoizeObjArg<Arg extends Dictionary, Res>(workerFunc: (arg: Arg) => Res, resEquality?: (res0: Res, res1: Res) => boolean, teardownFunc?: (res: Res) => void): (arg: Arg) => Res;
declare function memoizeArraylike<Args extends any[], Res>(
workerFunc: (...args: Args) => Res, resEquality?: (res0: Res, res1: Res) => boolean, teardownFunc?: (res: Res) => void): (argSets: Args[]) => Res[];
declare function memoizeHashlike<Args extends any[], Res>(
workerFunc: (...args: Args) => Res, resEquality?: (res0: Res, res1: Res) => boolean, teardownFunc?: (res: Res) => void): (argHash: {
    [key: string]: Args;
}) => {
    [key: string]: Res;
};


declare function removeElement(el: HTMLElement): void;
declare function elementClosest(el: HTMLElement, selector: string): HTMLElement;
declare function elementMatches(el: HTMLElement, selector: string): HTMLElement;
declare function findElements(container: HTMLElement[] | HTMLElement | NodeListOf<HTMLElement>, selector: string): HTMLElement[];
declare function findDirectChildren(parent: HTMLElement[] | HTMLElement, selector?: string): HTMLElement[];
declare function applyStyle(el: HTMLElement, props: Dictionary): void;
declare function applyStyleProp(el: HTMLElement, name: string, val: any): void;


declare function getCanVGrowWithinCell(): boolean;


declare function buildNavLinkData(date: DateMarker, type?: string): string;


declare function preventDefault(ev: any): void;
declare function listenBySelector(container: HTMLElement, eventType: string, selector: string, handler: (ev: Event, matchedTarget: HTMLElement) => void): () => void;
declare function whenTransitionDone(el: HTMLElement, callback: (ev: Event) => void): void;


interface EdgeInfo {
    borderLeft: number;
    borderRight: number;
    borderTop: number;
    borderBottom: number;
    scrollbarLeft: number;
    scrollbarRight: number;
    scrollbarBottom: number;
    paddingLeft?: number;
    paddingRight?: number;
    paddingTop?: number;
    paddingBottom?: number;
}
declare function computeEdges(el: HTMLElement, getPadding?: boolean): EdgeInfo;
declare function computeInnerRect(el: any, goWithinPadding?: boolean, doFromWindowViewport?: boolean): {
    left: any;
    right: number;
    top: any;
    bottom: number;
};
declare function computeRect(el: any): Rect;
declare function computeHeightAndMargins(el: HTMLElement): number;
declare function getClippingParents(el: HTMLElement): HTMLElement[];


declare function unpromisify(func: any, success: any, failure?: any): void;


declare class PositionCache {
    els: HTMLElement[];
    originClientRect: ClientRect;
    lefts: any;
    rights: any;
    tops: any;
    bottoms: any;
    constructor(originEl: HTMLElement, els: HTMLElement[], isHorizontal: boolean, isVertical: boolean);
    buildElHorizontals(originClientLeft: number): void;
    buildElVerticals(originClientTop: number): void;
    leftToIndex(leftPosition: number): any;
    topToIndex(topPosition: number): any;
    getWidth(leftIndex: number): number;
    getHeight(topIndex: number): number;
}


declare abstract class ScrollController {
    abstract getScrollTop(): number;
    abstract getScrollLeft(): number;
    abstract setScrollTop(top: number): void;
    abstract setScrollLeft(left: number): void;
    abstract getClientWidth(): number;
    abstract getClientHeight(): number;
    abstract getScrollWidth(): number;
    abstract getScrollHeight(): number;
    getMaxScrollTop(): number;
    getMaxScrollLeft(): number;
    canScrollVertically(): boolean;
    canScrollHorizontally(): boolean;
    canScrollUp(): boolean;
    canScrollDown(): boolean;
    canScrollLeft(): boolean;
    canScrollRight(): boolean;
}
declare class ElementScrollController extends ScrollController {
    el: HTMLElement;
    constructor(el: HTMLElement);
    getScrollTop(): number;
    getScrollLeft(): number;
    setScrollTop(top: number): void;
    setScrollLeft(left: number): void;
    getScrollWidth(): number;
    getScrollHeight(): number;
    getClientHeight(): number;
    getClientWidth(): number;
}
declare class WindowScrollController extends ScrollController {
    getScrollTop(): number;
    getScrollLeft(): number;
    setScrollTop(n: number): void;
    setScrollLeft(n: number): void;
    getScrollWidth(): number;
    getScrollHeight(): number;
    getClientHeight(): number;
    getClientWidth(): number;
}


interface CalendarDataProviderProps {
    optionOverrides: any;
    calendarApi: CalendarApi;
    children?: (data: CalendarData) => ComponentChildren;
}
declare class CalendarDataProvider extends Component<CalendarDataProviderProps, CalendarData> {
    dataManager: CalendarDataManager;
    constructor(props: CalendarDataProviderProps);
    handleData: (data: CalendarData) => void;
    render(): ComponentChildren;
    componentDidUpdate(prevProps: CalendarDataProviderProps): void;
}


interface ViewDef {
    type: string;
    component: ViewComponentType;
    overrides: ViewOptions;
    defaults: ViewOptions;
}

declare function formatDayString(marker: DateMarker): string;
declare function formatIsoTimeString(marker: DateMarker): string;

declare function parse(str: any): {
    marker: Date;
    isTimeUnspecified: boolean;
    timeZoneOffset: any;
};


declare const config: any;


declare const globalLocales: LocaleInput[];


declare function createPlugin(input: PluginDefInput): PluginDef;

interface CalendarRootProps {
    options: CalendarOptions;
    theme: Theme;
    emitter: Emitter<CalendarListeners>;
    children: (classNames: string[], height: CssDimValue, isHeightAuto: boolean, forPrint: boolean) => ComponentChildren;
}
interface CalendarRootState {
    forPrint: boolean;
}
declare class CalendarRoot extends BaseComponent<CalendarRootProps, CalendarRootState> {
    state: {
        forPrint: boolean;
    };
    render(): ComponentChildren;
    componentDidMount(): void;
    componentWillUnmount(): void;
    handleBeforePrint: () => void;
    handleAfterPrint: () => void;
}

interface DayHeaderProps {
    dateProfile: DateProfile;
    dates: DateMarker[];
    datesRepDistinctDays: boolean;
    renderIntro?: () => VNode;
}
declare class DayHeader extends BaseComponent<DayHeaderProps> {
    createDayHeaderFormatter: (explicitFormat: DateFormatter, datesRepDistinctDays: any, dateCnt: any) => DateFormatter;
    render(): createElement.JSX.Element;
}


declare function computeFallbackHeaderFormat(datesRepDistinctDays: boolean, dayCnt: number): DateFormatter;


interface TableDateCellProps {
    date: DateMarker;
    dateProfile: DateProfile;
    todayRange: DateRange;
    colCnt: number;
    dayHeaderFormat: DateFormatter;
    colSpan?: number;
    isSticky?: boolean;
    extraDataAttrs?: Dictionary;
    extraHookProps?: Dictionary;
}
declare class TableDateCell extends BaseComponent<TableDateCellProps> {
    render(): createElement.JSX.Element;
}
interface TableDowCellProps {
    dow: number;
    dayHeaderFormat: DateFormatter;
    colSpan?: number;
    isSticky?: boolean;
    extraHookProps?: Dictionary;
    extraDataAttrs?: Dictionary;
    extraClassNames?: string[];
}
declare class TableDowCell extends BaseComponent<TableDowCellProps> {
    render(): createElement.JSX.Element;
}


interface DaySeriesSeg {
    firstIndex: number;
    lastIndex: number;
    isStart: boolean;
    isEnd: boolean;
}
declare class DaySeriesModel {
    cnt: number;
    dates: DateMarker[];
    indices: number[];
    constructor(range: DateRange, dateProfileGenerator: DateProfileGenerator);
    sliceRange(range: DateRange): DaySeriesSeg | null;
    private getDateDayIndex;
}


interface DayTableSeg extends Seg {
    row: number;
    firstCol: number;
    lastCol: number;
}
interface DayTableCell {
    key: string;
    date: DateMarker;
    extraHookProps?: Dictionary;
    extraDataAttrs?: Dictionary;
    extraClassNames?: string[];
}
declare class DayTableModel {
    rowCnt: number;
    colCnt: number;
    cells: DayTableCell[][];
    headerDates: DateMarker[];
    private daySeries;
    constructor(daySeries: DaySeriesModel, breakOnWeeks: boolean);
    private buildCells;
    private buildCell;
    private buildHeaderDates;
    sliceRange(range: DateRange): DayTableSeg[];
}


interface SliceableProps {
    dateSelection: DateSpan;
    businessHours: EventStore;
    eventStore: EventStore;
    eventDrag: EventInteractionState | null;
    eventResize: EventInteractionState | null;
    eventSelection: string;
    eventUiBases: EventUiHash;
}
interface SlicedProps<SegType extends Seg> {
    dateSelectionSegs: SegType[];
    businessHourSegs: SegType[];
    fgEventSegs: SegType[];
    bgEventSegs: SegType[];
    eventDrag: EventSegUiInteractionState | null;
    eventResize: EventSegUiInteractionState | null;
    eventSelection: string;
}
declare abstract class Slicer<SegType extends Seg, ExtraArgs extends any[] = []> {
    private sliceBusinessHours;
    private sliceDateSelection;
    private sliceEventStore;
    private sliceEventDrag;
    private sliceEventResize;
    abstract sliceRange(dateRange: DateRange, ...extraArgs: ExtraArgs): SegType[];
    protected forceDayIfListItem: boolean;
    sliceProps(props: SliceableProps, dateProfile: DateProfile, nextDayThreshold: Duration | null, context: CalendarContext, ...extraArgs: ExtraArgs): SlicedProps<SegType>;
    sliceNowDate(
    date: DateMarker, context: CalendarContext, ...extraArgs: ExtraArgs): SegType[];
    private _sliceBusinessHours;
    private _sliceEventStore;
    private _sliceInteraction;
    private _sliceDateSpan;
    private sliceEventRanges;
    private sliceEventRange;
}


declare function isInteractionValid(interaction: EventInteractionState, context: CalendarContext): boolean;
declare function isPropsValid(state: SplittableProps, context: CalendarContext, dateSpanMeta?: {}, filterConfig?: any): boolean;


declare function requestJson(method: string, url: string, params: Dictionary, successCallback: any, failureCallback: any): void;


declare type OverflowValue = 'auto' | 'hidden' | 'scroll' | 'visible';
interface ScrollerProps {
    overflowX: OverflowValue;
    overflowY: OverflowValue;
    overcomeLeft?: number;
    overcomeRight?: number;
    overcomeBottom?: number;
    maxHeight?: CssDimValue;
    liquid?: boolean;
    liquidIsAbsolute?: boolean;
    children?: ComponentChildren;
    elRef?: Ref<HTMLElement>;
}
declare class Scroller extends BaseComponent<ScrollerProps> implements ScrollerLike {
    private el;
    render(): createElement.JSX.Element;
    handleEl: (el: HTMLElement) => void;
    needsXScrolling(): boolean;
    needsYScrolling(): boolean;
    getXScrollbarWidth(): number;
    getYScrollbarWidth(): number;
}


declare class RefMap<RefType> {
    masterCallback?: (val: RefType | null, key: string) => void;
    currentMap: {
        [key: string]: RefType;
    };
    private depths;
    private callbackMap;
    constructor(masterCallback?: (val: RefType | null, key: string) => void);
    createRef(key: string | number): (val: RefType) => void;
    handleValue: (val: RefType | null, key: string) => void;
    collect(startIndex?: number, endIndex?: number, step?: number): RefType[];
    getAll(): RefType[];
}


interface SimpleScrollGridProps {
    cols: ColProps[];
    sections: SimpleScrollGridSection[];
    liquid: boolean;
    height?: CssDimValue;
}
interface SimpleScrollGridSection extends SectionConfig {
    key: string;
    chunk?: ChunkConfig;
}
interface SimpleScrollGridState {
    shrinkWidth: number | null;
    forceYScrollbars: boolean;
    scrollerClientWidths: {
        [index: string]: number;
    };
    scrollerClientHeights: {
        [index: string]: number;
    };
}
declare class SimpleScrollGrid extends BaseComponent<SimpleScrollGridProps, SimpleScrollGridState> {
    processCols: (a: any) => any;
    renderMicroColGroup: (cols: ColProps[], shrinkWidth?: number) => VNode;
    scrollerRefs: RefMap<Scroller>;
    scrollerElRefs: RefMap<HTMLElement>;
    state: SimpleScrollGridState;
    render(): VNode;
    renderSection(sectionConfig: SimpleScrollGridSection, sectionI: number, microColGroupNode: VNode): createElement.JSX.Element;
    renderChunkTd(sectionConfig: SimpleScrollGridSection, sectionI: number, microColGroupNode: VNode, chunkConfig: ChunkConfig): createElement.JSX.Element;
    _handleScrollerEl(scrollerEl: HTMLElement | null, key: string): void;
    handleSizing: () => void;
    componentDidMount(): void;
    componentDidUpdate(): void;
    componentWillUnmount(): void;
    computeShrinkWidth(): number;
    computeScrollerDims(): {
        forceYScrollbars: boolean;
        scrollerClientWidths: {
            [index: string]: number;
        };
        scrollerClientHeights: {
            [index: string]: number;
        };
    };
}

interface ScrollbarWidths {
    x: number;
    y: number;
}
declare function getScrollbarWidths(): ScrollbarWidths;

declare function getIsRtlScrollbarOnLeft(): boolean;


interface NowTimerProps {
    unit: string;
    children: (now: DateMarker, todayRange: DateRange) => ComponentChildren;
}
interface NowTimerState {
    nowDate: DateMarker;
    todayRange: DateRange;
}
declare class NowTimer extends Component<NowTimerProps, NowTimerState> {
    static contextType: Context<ViewContext>;
    context: ViewContext;
    initialNowDate: DateMarker;
    initialNowQueriedMs: number;
    timeoutId: any;
    constructor(props: NowTimerProps, context: ViewContext);
    render(): ComponentChildren;
    componentDidMount(): void;
    componentDidUpdate(prevProps: NowTimerProps): void;
    componentWillUnmount(): void;
    private computeTiming;
    private setTimeout;
    private clearTimeout;
}

declare let globalPlugins: PluginDef[];

interface MinimalEventProps {
    seg: Seg;
    isDragging: boolean;
    isResizing: boolean;
    isDateSelecting: boolean;
    isSelected: boolean;
    isPast: boolean;
    isFuture: boolean;
    isToday: boolean;
}
interface EventRootProps extends MinimalEventProps {
    timeText: string;
    disableDragging?: boolean;
    disableResizing?: boolean;
    defaultContent: (hookProps: EventContentArg) => ComponentChildren;
    children: (rootElRef: Ref<any>, classNames: string[], innerElRef: Ref<any>, innerContent: ComponentChildren, hookProps: EventContentArg) => ComponentChildren;
}
declare class EventRoot extends BaseComponent<EventRootProps> {
    elRef: RefObject<HTMLElement>;
    render(): createElement.JSX.Element;
    componentDidMount(): void;
    componentDidUpdate(prevProps: EventRootProps): void;
}


interface StandardEventProps extends MinimalEventProps {
    extraClassNames: string[];
    defaultTimeFormat: DateFormatter;
    defaultDisplayEventTime?: boolean;
    defaultDisplayEventEnd?: boolean;
    disableDragging?: boolean;
    disableResizing?: boolean;
    defaultContent?: (hookProps: EventContentArg) => ComponentChildren;
}
declare class StandardEvent extends BaseComponent<StandardEventProps> {
    render(): createElement.JSX.Element;
}


declare function renderFill(fillType: string): createElement.JSX.Element;
interface BgEventProps {
    seg: Seg;
    isPast: boolean;
    isFuture: boolean;
    isToday: boolean;
}
declare const BgEvent: (props: BgEventProps) => createElement.JSX.Element;


declare const version: string;

export { Action, AllDayContentArg, AllDayMountArg, AllowFunc, BASE_OPTION_DEFAULTS, BASE_OPTION_REFINERS, BaseComponent, BaseOptionRefiners, BaseOptionsRefined, BgEvent, BgEventProps, BusinessHoursInput, ButtonIconsInput, ButtonTextCompoundInput, CalendarApi, CalendarContent, CalendarContentProps, CalendarContext, CalendarData, CalendarDataManager, CalendarDataProvider, CalendarDataProviderProps, CalendarListenerRefiners, CalendarListeners, CalendarOptionRefiners, CalendarOptions, CalendarOptionsRefined, CalendarRoot, ChunkConfig, ChunkConfigContent, ChunkConfigRowContent, ChunkContentCallbackArgs, ClassNamesGenerator, ColGroupConfig, ColProps, Constraint, ConstraintInput, ContentHook, CssDimValue, CustomButtonInput, CustomContentGenerator, CustomContentRenderContext, DateComponent, DateEnv, DateFormatter, DateInput, DateMarker, DateMarkerMeta, DateMeta, DatePointApi, DatePointTransform, DateProfile, DateProfileGenerator, DateRange, DateRangeInput, DateSelectArg, DateSelectionApi, DateSpan, DateSpanApi, DateSpanInput, DateSpanTransform, DateUnselectArg, DatesSetArg, DayCellContent, DayCellContentArg, DayCellContentProps, DayCellMountArg, DayCellRoot, DayCellRootProps, DayHeader, DayHeaderContentArg, DayHeaderMountArg, DaySeriesModel, DayTableCell, DayTableModel, DayTableSeg, DelayedRunner, Dictionary, DidMountHandler, DragMeta, DragMetaInput, Duration, DurationInput, ElementDragging, ElementScrollController, Emitter, EventAddArg, EventApi, EventChangeArg, EventClickArg, EventContentArg, EventDef, EventDefHash, EventDropTransformers, EventHoveringArg, EventInput, EventInputTransformer, EventInstance, EventInstanceHash, EventInteractionState, EventMountArg, EventMutation, EventRefined, EventRefiners, EventRemoveArg, EventRenderRange, EventResizeJoinTransforms, EventRoot, EventSegUiInteractionState, EventSource, EventSourceApi, EventSourceDef, EventSourceFunc, EventSourceHash, EventSourceInput, EventSourceRefined, EventSourceRefiners, EventStore, EventTuple, EventUi, EventUiHash, FormatDateOptions, FormatRangeOptions, FormatterInput, Hit, Identity, Interaction, InteractionSettings, InteractionSettingsStore, LocaleInput, LocaleSingularArg, MinimalEventProps, MountArg, MountHook, MountHookProps, NamedTimeZoneImpl, NowIndicatorContentArg, NowIndicatorMountArg, NowIndicatorRoot, NowIndicatorRootProps, NowTimer, OrderSpec, OverflowValue, OverlapFunc, ParsedRecurring, PluginDef, PluginDefInput, Point, PointerDragEvent, PositionCache, RawOptionsFromRefiners, Rect, RecurringType, RefMap, RefinedOptionsFromRefiners, RenderHook, RenderHookProps, RenderHookPropsChildren, ScrollController, ScrollGridChunkConfig, ScrollGridImpl, ScrollGridProps, ScrollGridSectionConfig, ScrollRequest, ScrollResponder, Scroller, ScrollerLike, ScrollerProps, SectionConfig, Seg, SimpleScrollGrid, SimpleScrollGridSection, SlicedProps, Slicer, SlotLabelContentArg, SlotLabelMountArg, SlotLaneContentArg, SlotLaneMountArg, SpecificViewContentArg, SpecificViewMountArg, SplittableProps, Splitter, StandardEvent, StandardEventProps, TableDateCell, TableDowCell, Theme, ToolbarInput, VerboseFormattingArg, ViewApi, ViewComponentType, ViewContainerAppend, ViewContentArg, ViewContext, ViewContextType, ViewDef, ViewMountArg, ViewOptionRefiners, ViewOptionsRefined, ViewProps, ViewPropsTransformer, ViewRoot, ViewRootProps, ViewSpec, WeekNumberCalculation, WeekNumberContentArg, WeekNumberMountArg, WeekNumberRoot, WeekNumberRootProps, WillUnmountHandler, WindowScrollController, addDays, addDurations, addMs, addWeeks, allowContextMenu, allowSelection, applyMutationToEventStore, applyStyle, applyStyleProp, asCleanDays, asRoughMinutes, asRoughMs, asRoughSeconds, buildClassNameNormalizer, buildEventApis, buildEventRangeKey, buildHashFromArray, buildNavLinkData, buildSegCompareObj, buildSegTimeText, collectFromHash, combineEventUis, compareByFieldSpec, compareByFieldSpecs, compareNumbers, compareObjs, computeEdges, computeFallbackHeaderFormat, computeHeightAndMargins, computeInnerRect, computeRect, computeSegDraggable, computeSegEndResizable, computeSegStartResizable, computeShrinkWidth, computeSmallestCellWidth, computeVisibleDayRange, config, constrainPoint, createDuration, createEmptyEventStore, createEventInstance, createEventUi, createFormatter, createPlugin, dateSelectionJoinTransformer, diffDates, diffDayAndTime, diffDays, diffPoints, diffWeeks, diffWholeDays, diffWholeWeeks, disableCursor, elementClosest, elementMatches, enableCursor, eventDragMutationMassager, eventTupleToStore, filterEventStoreDefs, filterHash, findDirectChildren, findElements, flexibleCompare, formatDate, formatDayString, formatIsoTimeString, formatRange, getAllowYScrolling, getCanVGrowWithinCell, getClippingParents, getDateMeta, getDayClassNames, getDefaultEventEnd, getElSeg, getEventClassNames, getIsRtlScrollbarOnLeft, getRectCenter, getRelevantEvents, getScrollGridClassNames, getScrollbarWidths, getSectionClassNames, getSectionHasLiquidHeight, getSegMeta, getSlotClassNames, getStickyFooterScrollbar, getStickyHeaderDates, getUnequalProps, globalLocales, globalPlugins, greatestDurationDenominator, guid, hasBgRendering, hasShrinkWidth, identity, interactionSettingsStore, interactionSettingsToStore, intersectRanges, intersectRects, isArraysEqual, isColPropsEqual, isDateSpansEqual, isInt, isInteractionValid, isMultiDayRange, isPropsEqual, isPropsValid, isValidDate, listenBySelector, mapHash, memoize, memoizeArraylike, memoizeHashlike, memoizeObjArg, mergeEventStores, multiplyDuration, padStart, parseBusinessHours, parseClassNames, parseDragMeta, parseEventDef, parseFieldSpecs, parse as parseMarker, pointInsideRect, preventContextMenu, preventDefault, preventSelection, rangeContainsMarker, rangeContainsRange, rangesEqual, rangesIntersect, refineEventDef, refineProps, removeElement, removeExact, renderChunkContent, renderFill, renderMicroColGroup, renderScrollShim, requestJson, sanitizeShrinkWidth, setElSeg, setRef, sliceEventStore, sliceEvents, sortEventSegs, startOfDay, translateRect, triggerDateSelect, unpromisify, version, whenTransitionDone, wholeDivideDurations };
