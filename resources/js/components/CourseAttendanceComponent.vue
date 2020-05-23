<template>
    <tr>
        <td>
            <a :href="studentdetailsroute">{{ student.student }}</a>
        </td>

        <td v-for="event in student" :key="event.id">
            <!-- todo clean -->

            <label
                v-if="
                    event.attendance && event.attendance.attendance_type_id == 1
                "
                class="badge badge-success"
            >
                P <i class="la la-user"></i>
            </label>

            <label
                v-if="
                    event.attendance && event.attendance.attendance_type_id == 2
                "
                class="badge badge-warning"
            >
                PP <i class="la la-clock-o"></i>
            </label>

            <label
                v-if="
                    event.attendance && event.attendance.attendance_type_id == 3
                "
                class="badge badge-info"
            >
                AJ <i class="la la-exclamation"></i>
            </label>

            <label
                v-if="
                    event.attendance && event.attendance.attendance_type_id == 4
                "
                class="badge badge-danger"
            >
                A <i class="la la-user-times"></i>
            </label>
        </td>
    </tr>
</template>

<script>
export default {
    props: ["student", "studentdetailsroute"],

    data() {
        return {};
    },

    mounted() {},

    methods: {},
};
</script>
