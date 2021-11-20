<script>
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import resourceTimelineDay from '@fullcalendar/daygrid'
import interaction from '@fullcalendar/interaction'
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

export default {
  props: ['resources', 'events', 'unassignedEvents', 'leaves','locale'],
  components: {
    FullCalendar,
  },
  data() {
    return {
      calendarOptions: {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        plugins: [ resourceTimelinePlugin, resourceTimelineDay, interaction ],
        initialView: 'week',
        headerToolbar: {
            center: 'week,twoDay,day'
        },
        views: {
            week: {
            type: 'resourceTimelineWeek',
            buttonText: 'Semaine'
            },
            twoDay: {
            type: 'resourceTimeline',
            duration: { days: 2 },
            buttonText: '2 jours'
            },
            day: {
            type: 'resourceTimelineDay',
            buttonText: '1 jour'
            }
        },
        resources: this.resources,
        stickyHeaderDates: true,
        height: "auto",
        slotLabelInterval: {hours:4},
        slotMinTime: "05:00:00",
        slotMaxTime: "23:00:00",
        locale: this.locale,
        nowIndicator: true,
        hiddenDays: [ 0 ], // TODO make this customizable
        firstDay: 1,
        slotLabelFormat: [
            { weekday: 'short'},
            { hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short',
                hour12: false,
            }
        ],
        slotMinWidth: 50,
        eventDidMount: function (info) {
            tippy(info.el, {
                content: info.event.title,
            });
        },
        resourceAreaWidth: 150,
        eventSources: [
          {
              events: this.events,
          },
          {
              events: this.unassignedEvents,
          },
          {
              events: this.leaves,
              color: 'red',
              textColor: 'white',
          },
        ],

        editable:true,
        eventDurationEditable: false,
        eventStartEditable: false,

        eventDrop: info =>
            axios.patch(window.location.href, {
                course_id: info.event.groupId,
                resource_id: info.newResource.id,
            })

                .then(response =>
                    new Noty({
                        title: this.$t("Operation successful"),
                        text: this.$t('Your changes were successful'),
                        type: "success"
                    }).show())

                .catch(error =>
                    new Noty({
                      type: "error",
                      text: this.$t('Your changes could not be saved'),
                    }).show())
      }
    }
  }
}
</script>
<template>
  <FullCalendar :options="calendarOptions" />
</template>