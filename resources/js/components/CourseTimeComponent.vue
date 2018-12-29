<template>
<div id="parent">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    TODO - new time
                </div>

                <div class="box-tools pull-right">
                    <button class="btn btn-primary" @click="addTime()">Add</button>
                </div>
            </div>
                    
            <div class="box-body">
                <!-- todo convert to checkboxes with multiselect option -->
                <div class="form-group">
                    <select required name="day" v-model="day">
                    <option value="1">Lundi</option>
                    <option value="2">Mardi</option>
                    <option value="3">Mercredi</option>
                    <option value="4">Jeudi</option>
                    <option value="5">Vendredi</option>
                    <option value="6">Samedi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start">Heure de début</label>
                    <input type="time" name="start" required v-model="start">
                </div>

                <div class="form-group">
                    <label for="end">Heure de fin</label>
                    <input type="time" name="end" required v-model="end">
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    TODO - existing times
                </div>

                <div class="box-tools pull-right">
                        
                </div>
            </div>
                    
            <div class="box-body">
                <ul>
                    <li v-for="time in times" v-bind:key="time.id">
                        {{ time.day }} de {{ time.start }} à {{ time.end }}
                            <a @click="removeTime(time)">(<i class="fa fa-times"></i>)</a>
                    </li>
                </ul>
                
            </div>
        </div>
    </div>

</div>
</template>

<script>

    export default {

        data () {
            return {
                times: null,
                loading: true,
                errored: false,
                day: null,
                start: null,
                end: null
            }
        },

        mounted() {
            this.getTimes();
        },

        methods: {

            getTimes() {
                axios
                    .get('/course/1489/time/get')
                    .then(response => {
                        this.times = response.data
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => this.loading = false)
            },

            addTime() {
                axios
                    .post('/course/1489/time', {
                        day: this.day,
                        start: this.start,
                        end: this.end
                    })
                    .then(response => {
                        this.getTimes();
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
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            }
        }
    }
</script>
