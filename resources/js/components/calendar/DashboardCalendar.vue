<script>
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import resourceTimeGrid from '@fullcalendar/resource-timegrid'

export default {
  props: ['resources', 'events', 'locale'],
  components: {
    FullCalendar,
  },
  data() {
    return {
      calendarOptions: {
        plugins: [ dayGridPlugin, resourceTimeGrid ],
        initialView: 'resourceTimeGridDay',
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        resources: this.resources,
        height: "auto",
        nowIndicator: true,
        locale: this.locale,
        events: this.events,
        titleFormat: { // will produce something like "Tuesday, September 18, 2018"
            month: 'long',
            year: 'numeric',
            day: 'numeric',
            weekday: 'long'
        }
      }
    }
  }
}
</script>
<template>
  <FullCalendar :options="calendarOptions" />
</template>