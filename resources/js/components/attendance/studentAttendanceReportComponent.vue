<template>
<div>
<div class="row">

<div class="col-sm-6 col-md-3">

    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-user bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ attendanceratio }}%</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Attendance %')</div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-clock-o bg-warning p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ partialpresence() }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Number of partial presences')</div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-user-times bg-danger p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ absences() }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Number of Absences')</div>
            </div>
        </div>
    </div>
</div>


</div>

<div class="row">
<table class="table">
    <thead>
        <th>{{ $t('Event') }}</th>
        <th>{{ $t('Date') }}</th>
        <th>{{ $t('Attendance status') }}</th>
    </thead>
    <tbody v-for="attendance in attendances" v-bind:key="attendance.id">
        <absence-buttons :attendance="attendance" :route="storeattendanceroute" :event="attendance.event"></absence-buttons>
  </tbody>
</table>
</div>
</div>
</template>

<script>
export default {
    props: ['attendances', 'storeattendanceroute', 'attendanceratio'],
    data() {
        return {
        }
    },
    methods: {
        absences() {
            var count = 0;
            Object.keys(this.attendances).forEach(attendance => {
                if (this.attendances[attendance]['attendance_type_id'] == 3 || this.attendances[attendance]['attendance_type_id'] == 4) { count++ }
            });
            return count;
        },

        partialpresence()
        {
            var count = 0;
            Object.keys(this.attendances).forEach(attendance => {
                if (this.attendances[attendance]['attendance_type_id'] == 2) { count++ }
            });
            return count;
        },
    },
    created() {
       
    },
    computed: {
        
    }
}
</script>

<style>

</style>