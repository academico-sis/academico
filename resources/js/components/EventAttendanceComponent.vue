<template>
    <tr>
        <td>
            {{ attendance.student }}
        </td>

        <td>
            <div class="btn-group" role="group" aria-label="">
                <div class="btn-group" role="group">
                    <button
                        id=""
                        class="btn btn-secondary"
                        :class="{ 'btn-success': studentAttendance == 1 }"
                        @click="saveAttendance(1)"
                    >
                        P <i class="la la-user"></i>
                    </button>
                </div>

                <div class="btn-group" role="group">
                    <button
                        id=""
                        class="btn btn-secondary"
                        :class="{ 'btn-warning': studentAttendance == 2 }"
                        @click="saveAttendance(2)"
                    >
                        PP <i class="la la-clock-o"></i>
                    </button>
                </div>

                <div class="btn-group" role="group">
                    <button
                        id=""
                        class="btn btn-secondary"
                        :class="{ 'btn-info': studentAttendance == 3 }"
                        @click="saveAttendance(3)"
                    >
                        AJ <i class="la la-exclamation"></i>
                    </button>
                </div>

                <div class="btn-group" role="group">
                    <button
                        id=""
                        class="btn btn-secondary"
                        :class="{ 'btn-danger': studentAttendance == 4 }"
                        @click="saveAttendance(4)"
                    >
                        A <i class="la la-user-times"></i>
                    </button>
                </div>
            </div>
        </td>
    </tr>
</template>

<script>
export default {
    props: ["attendance", "event", "route"],

    data() {
        return {
            studentAttendance: this.attendance.attendance.attendance_type_id,
        };
    },

    mounted() {},

    methods: {
        saveAttendance(attendance) {
            axios
                .post(this.route, {
                    event_id: this.event.id,
                    student_id: this.attendance.student_id,
                    attendance_type_id: attendance,
                })
                .then((response) => {
                    this.studentAttendance = attendance;
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },
    },
};
</script>
