<template>
<div class="attendance-toolbox">

<span
    v-if="attendanceEnabled"
    class="badge badge-pill attendance-count"
    v-bind:class="{ 'badge-success': count == 0, 'badge-warning': count > 0, 'badge-danger': count > 4 }"
>{{ count }}</span>
&nbsp;
<label class="switch switch-label switch-pill switch-outline-primary-alt attendance-switch">
    <input class="switch-input" type="checkbox" v-model="attendanceEnabled" @click="toggleAttendanceStatus(attendanceEnabled)">
    <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
</label>

</div>
</template>

<script>
export default {
    props: ['exempted', 'count', 'toggleroute'],
    data () {
        return {
            errors: [],
            attendanceEnabled: true
        }
    },
    mounted() {
        this.attendanceEnabled = !this.exempted;
    },
    methods: {
        toggleAttendanceStatus(status)
        {
            axios
                .post(this.toggleroute, {
                    status
                })
                .then(response => {
                    this.attendanceEnabled = !response.data
                    new Noty({
                    type: "success",
                    text: 'Attendance feature status has been saved',
                }).show();
                })
                .catch(e => {
                    this.errors.push(e)
                    this.attendanceEnabled = status
                    new Noty({
                    type: "error",
                    text: 'Unable to save your change. The course has not been modified',
                }).show();
                })
        }
    }
}
</script>

<style>
.attendance-toolbox {
    display: table;
}

.attendance-count {
    display: table-cell;
    vertical-align: middle;
}

.attendance-switch {
    display: table-cell;
    vertical-align: middle;
}
</style>