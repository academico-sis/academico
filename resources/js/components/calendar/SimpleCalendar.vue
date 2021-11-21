<script>
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue'
import timeGridPlugin from '@fullcalendar/timegrid'


export default {
  props: ['leaves', 'events', 'locale'],
  components: {
    FullCalendar,
  },
  data() {
    return {
      calendarOptions: {
        plugins: [ timeGridPlugin ],
        initialView: 'timeGridWeek',
        nowIndicator: true,
        hiddenDays: [ 0 ], // TODO make this customizable
        firstDay: 1,
        locale: this.locale,
        eventSources: [
          {
              events: this.events,
          },
          {
              events: this.leaves,
              color: 'red',
              textColor: 'white',
          },
        ],
      }
    }
  }
}
</script>
<template>
  <FullCalendar :options="calendarOptions" />
</template>