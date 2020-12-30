<template>
    <tr>
        <td>
            {{ attendance.student }}
        </td>

        <td>
            <div class="btn-group" role="group" aria-label="">
                <div class="btn-group" role="group">
                    <button
                        v-for="attendance_type in attendance_types"
                        v-bind:key="attendance_type.id"
                        v-bind:class="buttonClass(attendance_type)"
                        @click="saveAttendance(attendance_type.id)"
                    >
                        <span v-html="attendance_type.icon"></span>
                        {{ attendance_type.translated_name }}
                    </button>

                </div>

            </div>
        </td>
    </tr>
</template>

<script>
export default {
    props: ["attendance", "event", "route", "attendance_types"],

    data() {
        return {
            studentAttendance: this.attendance.attendance,
        };
    },

    mounted() {},

    methods: {
        saveAttendance(attendance_type_id) {
            axios
                .post(this.route, {
                    event_id: this.event.id,
                    student_id: this.attendance.student_id,
                    attendance_type_id,
                })
                .then(response => {
                    this.studentAttendance = response.data;
                })
                .catch(e => this.errors.push(e));
        },

        buttonClass(attendance_type) {
            if (this.studentAttendance.attendance_type_id === attendance_type.id)
            {
                return `btn btn-${attendance_type.class}`
            }
            else {
                return "btn btn-secondary"
            }
        }
    }
};
</script>
