<template>
<div>

    <tr>
        <td>
            {{ attendance.student.firstname }}
        </td>
                                
        <td style="width: 50%;"> <!-- todo clean -->
                       
        <div class="btn-group btn-group-justified" role="group" aria-label="">

        <div class="btn-group" role="group">
            <button
                id=""
                onclick=""
                class="btn btn-secondary" 
                v-bind:class="{ 'btn-success': attendance.attendance.attendance_type_id == 1 }">
                P <i class="fa fa-user"></i>
            </button>
        </div>
        
        <div class="btn-group" role="group">
            <button
                id=""
                onclick=""
                class="btn btn-secondary"
                v-bind:class="{ 'btn-warning': attendance.attendance.attendance_type_id == 2 }">
                PP <i class="fa fa-clock-o"></i>
            </button>
        </div>
        
        <div class="btn-group" role="group">
            <button
                id=""
                onclick=""
                class="btn btn-secondary"
                v-bind:class="{ 'btn-info': attendance.attendance.attendance_type_id == 3 }">
                AJ <i class="fa fa-exclamation"></i>
            </button>
        </div>
        
        <div class="btn-group" role="group">
            <button
                id=""
                onclick=""
                class="btn btn-secondary"
                v-bind:class="{ 'btn-danger': attendance.attendance.attendance_type_id == 4 }">
                A <i class="fa fa-user-times"></i>
            </button>
        </div>
        
        </div>

        </td>

        </tr>
</div>
</template>



<script>

    export default {

        props: ['attendance'],
        
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
