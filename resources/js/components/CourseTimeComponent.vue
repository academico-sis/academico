<template>
<div id="parent">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    New time
                </div>

                <div class="box-tools pull-right">
                </div>
            </div>
                    
            <div class="box-body">
                <table class="table">
                    <thead>
                        <th>
                            <select name="day" id="day" v-model="day">
                                <option value="1">Lundi</option>
                                <option value="2">Mardi</option>
                                <option value="3">Mercredi</option>
                                <option value="4">Jeudi</option>
                                <option value="5">Vendredi</option>
                                <option value="6">Samedi</option>
                            </select>
                        </th>

                        <th>
                            <input type="time" name="start" id="start" v-model="start">
                        </th>

                        <th>
                            <input type="time" name="end" id="end" v-model="end">
                        </th>

                        <th>
                            <button
                                type="button"
                                class="btn btn-xs btn-success"
                                @click="addTime()">
                                    <i class="fa fa-plus"></i>
                            </button>
                        </th>
                    </thead>

                    <tbody>
                        <tr v-for="time in times" v-bind:key="time.id">
                            <td>{{ time.day }}</td>
                            <td>{{ time.start }}</td>
                            <td>{{ time.end }}</td>
                            <td><a @click="removeTime(time)">(<i class="fa fa-times"></i>)</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>




<div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    Events
                </div>

                <div class="box-tools pull-right">
                </div>
            </div>
                    
            <div class="box-body">
                <table class="table">
                    <thead>
                        <th>Start</th>
                        <th>End</th>
                    </thead>

                    <tbody>
                        <tr v-for="event in events" v-bind:key="event.id">
                            <td>{{ event.start }}</td>
                            <td>{{ event.end }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
</template>

<script>
    
    export default {

        props: ['course'],

        data () {
            return {
                times: null,
                events: null,
                loading: true,
                errored: false,
                day: null,
                start: null,
                end: null
            }
        },

        mounted() {
            this.getTimes();
            this.getEvents();
        },

        methods: {

            getTimes() {
                axios
                    .get('/coursetime/'+this.course+'/get')
                    .then(response => {
                        this.times = response.data
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => this.loading = false)
            },

            getEvents() {
                axios
                    .get('/course/'+this.course+'/events/get')
                    .then(response => {
                        this.events = response.data
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => this.loading = false)
            },

            addTime() {
                axios
                    .post('/coursetime/'+this.course, {
                        day: this.day,
                        start: this.start,
                        end: this.end
                    })
                    .then(response => {
                        this.getTimes();
                        this.getEvents();
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },

            removeTime(time) {
                axios
                    .delete('/coursetime/'+time.id, {
                        time
                    })
                    .then(response => {
                        this.getTimes();
                        this.getEvents();
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            }
        }
    }
</script>
