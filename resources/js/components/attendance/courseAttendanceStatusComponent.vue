<template>
<tr>
    <td>
            <a v-if="this.attendanceEnabled" :href="courseattendanceroute">{{ coursename }}</a>
            <span v-else>{{ coursename }}</span>
    </td>

    <td>{{ teachername }}</td>

    <td>
        <span
            v-if="attendanceEnabled"
            class="badge badge-pill"
            v-bind:class="{ 'badge-success': count === 0, 'badge-warning': count > 0, 'badge-danger': count > 4 }"
        >{{ count }}</span>
    </td>

    <td>
        <label class="switch switch-label switch-pill switch-outline-primary-alt">
            <input class="switch-input" :disabled="!isadmin" type="checkbox" v-model="attendanceEnabled" @click="toggleAttendanceStatus(attendanceEnabled)">
            <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
        </label>
    </td>

</tr>
</template>

<script>
export default {
    props: ['exempted', 'count', 'toggleroute', 'coursename', 'teachername', 'courseattendanceroute', 'isadmin'],
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
                    text: this.$t('Your changes were successful'),
                }).show();
                })
                .catch(e => {
                    this.errors.push(e)
                    this.attendanceEnabled = status
                    new Noty({
                    type: "error",
                    text: this.$t('Your changes could not be saved'),
                }).show();
                })
        }
    }
}
</script>

<style>

</style>
