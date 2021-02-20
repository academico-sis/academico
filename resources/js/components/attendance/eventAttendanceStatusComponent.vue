<template>
<div class="attendance-toolbox">
<p>
<a :href="eventattendanceroute" v-if="this.attendanceEnabled">
    {{ eventdate }}
</a>
<span v-else>{{ eventdate }}</span>
</p>
<p>
<label class="switch switch-label switch-pill switch-outline-primary-alt attendance-switch">
    <input class="switch-input" :disabled="!isadmin" type="checkbox" v-model="attendanceEnabled" @click="toggleAttendanceStatus(attendanceEnabled)">
    <span class="switch-slider" data-checked="On" data-unchecked="Off"></span>
</label>
</p>

</div>
</template>

<script>
export default {
    props: ['exempted', 'toggleroute', 'eventattendanceroute', 'eventdate', 'isadmin'],
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
