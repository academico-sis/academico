<template>
    <tr>
        <td>
            <a :href="studentdetailsroute">{{ student.student }}</a>
        </td>

        <td v-for="event in student" :key="event.id" style="text-align: center;">

            <label
                v-if="event.attendance"
                :class="`badge badge-${event.attendance.attendance_type.class}`"
            >
                <span v-html="event.attendance.attendance_type.icon"></span>
                {{ event.attendance.attendance_type.translated_name }}
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
