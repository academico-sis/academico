<template>

<tr>
    <td>{{ event.name }}</td>
    <td>{{ event.start }}</td>
    <td>
        <div class="btn-group" role="group">
            <button
                id=""
                disabled
                class="btn btn-secondary"
                v-bind:class="{ 'btn-success': studentAttendance == 1 }">
                {{ $t('front.Presence') }} <i class="fa fa-user"></i>
            </button>

            <button
                id=""
                disabled
                class="btn btn-secondary"
                v-bind:class="{ 'btn-warning': studentAttendance == 2 }">
                {{ $t('front.Partial Presence') }} <i class="fa fa-clock-o"></i>
            </button>


        </div>
        
        <div class="btn-group" role="group">
            <button
                id=""
                @click="saveAttendance(3)"
                class="btn btn-secondary"
                :disabled="studentAttendance !== 4"
                v-bind:class="{ 'btn-info': studentAttendance == 3 }">
                {{ $t('front.Justified Absence') }} <i class="fa fa-check"></i>
            </button>

            <button
                id=""
                @click="saveAttendance(4)"
                class="btn btn-secondary"
                :disabled="studentAttendance !== 3"
                v-bind:class="{ 'btn-danger': studentAttendance == 4 }">
                {{ $t('front.Unjustified Absence') }} <i class="fa fa-user-times"></i>
            </button>
        </div>
        </td>
</tr>

</template>



<script>

    export default {

        props: ['attendance', 'route', 'event'],
        
        data () {
            return {
                studentAttendance: this.attendance.attendance_type_id
            }
        },

        mounted() {

        },

        methods: {
            saveAttendance(attendance) {
                axios
                    .post(this.route, {
                        event_id: this.attendance.event.id,
                        student_id: this.attendance.student_id,
                        attendance_type_id: attendance
                    })
                    .then(response => {
                        this.studentAttendance = attendance /* todo fix this and update according to server response */
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
        }
    }
    
</script>
