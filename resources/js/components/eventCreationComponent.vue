<template>
<div class="row">
  <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Créer une ou des nouvelle(s) classe(s)
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Répéter les :</label>
                        <div class="col-md-10 col-form-label">
                          <div v-for="day in days" :key="day.day" class="form-check form-check-inline mr-1">
                            <input @change="updateCreateList()" v-model="day.selected" class="form-check-input ml-3" :id="day.day" type="checkbox">
                            <label class="form-check-label" :for="day.day">{{ day.label }}</label>
                          </div>

                          <div class="form-check form-check-inline mr-1">
                            <input id="toggler" @change="toggleAllDays()" v-model="toggleAllDayStatus" class="form-check-input ml-3" type="checkbox">
                            <label for="toggler" class="form-check-label">un/select all</label>
                          </div>
                        </div>
                      </div>

                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="start_date">du</label>
                        <input @change="updateCreateList()" class="form-control" type="date" id="start_date" v-model="startdate">
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="end_date">au</label>
                        <input @change="updateCreateList()" class="form-control" type="date" id="end_date" v-model="enddate">
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="start_time">de</label>
                        <input @change="updateCreateList()" class="form-control" type="time" id="start_time" v-model="starttime">
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="end_time">à</label>
                        <input @change="updateCreateList()" class="form-control" type="time" id="end_time" v-model="endtime">
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" type="text" id="name" v-model="name">
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-form-label" for="teacher">Teacher</label>
                        <select class="form-control" id="teacher" name="teacher" v-model="teacher">
                        <option value="null">No teacher yet</option>
                        <option v-for="teacher in this.teachers" :value="teacher.id" v-bind:key="teacher.id">{{ teacher.name }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-form-label" for="room">Room</label>
                        <select class="form-control" id="room" name="room" v-model="room">
                        <option value="null">No room yet</option>
                        <option v-for="room in this.rooms" :value="room.id" v-bind:key="room.id">{{ room.name }}</option>
                    </select>
                </div>

            </div>
            </div>
        </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Les classes suivantes seront créées :
                <div class="card-header-actions">
                    <button @click="createEvents()" :disabled="this.createList.length == 0 || this.name == null || this.starttime == null || this.starttime == null" class="btn btn-primary">Submit</button>
                </div>
            </div>

            <div class="card-body">
                <ul>
                    <li v-for="item in this.createList" v-bind:key="item.start.id">le {{ item.start | moment('DD/MM/YY') }} de {{ item.start | moment('HH:mm') }} à {{ item.end | moment('HH:mm') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import moment from 'moment';

export default {
    props: ['teachers', 'rooms'],
    data() {
        return {
            days: [
                {selected: false, day: 1, label:'L'},
                {selected: false, day: 2, label:'M'},
                {selected: false, day: 3, label:'X'},
                {selected: false, day: 4, label:'J'},
                {selected: false, day: 5, label:'V'},
                {selected: false, day: 6, label:'S'},
            ],
            teacher: null,
            room: null,
            startdate: null,
            enddate: null,
            starttime: null,
            endtime: null,
            name: null,
            createList: [],
        }
    },
    computed: {
    	selectedDays: function() {
      	return this.days.filter(day => day.selected).map(day => day.day);
      },

      toggleAllDayStatus() {
          return this.selectedDays.length == 6;
    }

    },
    methods: {
        toggleAllDays() {
            this.days.forEach(day => {
                day.selected = !day.selected
            });
            this.updateCreateList()
        },
        updateCreateList() {
            this.createList = []
            var current_start = moment.utc(this.startdate)
            var current_end = moment.utc(this.startdate)
            while (current_start <= moment.utc(this.enddate)) {
                if(this.selectedDays.includes(current_start.day())) {
                    current_start.set({
                        hour: moment.utc(this.starttime, 'HH:mm').hour(),
                        minute: moment.utc(this.starttime, 'HH:mm').minute()
                    });
                    current_end.set({
                        hour: moment.utc(this.endtime, 'HH:mm').hour(),
                        minute: moment.utc(this.endtime, 'HH:mm').minute()
                    });
                    this.createList.push({
                        start: current_start,
                        end: current_end
                    });
                }
                current_start = moment.utc(current_start).add(1, 'days')
                current_end = moment.utc(current_start)
            }
        },
        createEvents() {
            axios
                .post('/event', {
                    name: this.name,
                    teacher: this.teacher,
                    room: this.room,
                    createList: this.createList
                })
                .then(response => {
                    window.location.href = '/event'
                })
        }
    },
    mounted() { }
}
</script>

<style>

</style>
