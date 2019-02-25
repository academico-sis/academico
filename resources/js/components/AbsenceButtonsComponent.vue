<template>
<div>
        <div class="btn-group" role="group">
            <button
                id=""
                @click="saveAttendance(3)"
                class="btn btn-secondary"
                v-bind:class="{ 'btn-info': studentAttendance == 3 }">
                Absence justifiée <i class="fa fa-check"></i>
            </button>
        </div>
        
        <div class="btn-group" role="group">
            <button
                id=""
                @click="saveAttendance(4)"
                class="btn btn-secondary"
                v-bind:class="{ 'btn-danger': studentAttendance == 4 }">
                Absence non justifiée <i class="fa fa-user-times"></i>
            </button>
        </div>
        
        </div>

</template>



<script>

    export default {

        props: ['attendance', 'route'],
        
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
                        this.studentAttendance = attendance
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
        }
    }
    
</script>
