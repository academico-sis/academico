<template>


    <tr>
        <td>
            {{ student.student }}
        </td>
                                
        <td v-bind:key="event.id" v-for="event in student"> <!-- todo clean -->


            <label
                v-if="event.attendance && event.attendance.attendance_type_id == 1"
                class="label-success">
                P <i class="fa fa-user"></i>
            </label>
        
            <label
                v-if="event.attendance && event.attendance.attendance_type_id == 2"
                class="label-warning">
                PP <i class="fa fa-clock-o"></i>
            </label>
        
            <label
                v-if="event.attendance && event.attendance.attendance_type_id == 3"
                class="label-info">
                AJ <i class="fa fa-exclamation"></i>
            </label>
        
            <label
                v-if="event.attendance && event.attendance.attendance_type_id == 4"
                class="label-danger">
                A <i class="fa fa-user-times"></i>
            </label>

        </td>

        </tr>

</template>



<script>

    export default {

        props: ['student'],
        
        data () {
            return {
                
            }
        },

        mounted() {

        },

        methods: {

        }
    }
    
</script>
