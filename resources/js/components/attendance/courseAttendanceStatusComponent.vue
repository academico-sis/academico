<template>
<tr>
    <td>
            <a v-if="this.attendanceEnabled" :href="courseattendanceroute">{{ course.name }}</a>
            <span v-else>{{ course.name }}</span>
    </td>

    <td>{{ course.course_teacher_name }}</td>

    <td>
        <span
            v-if="attendanceEnabled"
            class="badge badge-pill"
            v-bind:class="{ 'badge-success': count == 0, 'badge-warning': count > 0, 'badge-danger': count > 4 }"
        >{{ count }}</span>
    </td>

    <td>
        <label class="switch switch-label switch-pill switch-outline-primary-alt">
            <input class="switch-input" type="checkbox" v-model="attendanceEnabled" @click="toggleAttendanceStatus(attendanceEnabled)">
            <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
        </label>
    </td>

</tr>
</template>

<script>
export default {
    props: ['exempted', 'count', 'toggleroute', 'course', 'courseattendanceroute'],
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

</style>