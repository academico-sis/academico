<template>
    <div>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">{{ $t("Period") }}</div>
                            <div class="card-body">
                                <select
                                    id="period"
                                    v-model="selectedPeriod"
                                    class="form-control"
                                    name="period"
                                    @change="getCoursesResults()"
                                >
                                    <option
                                        v-for="period in this.periods"
                                        :key="period.id"
                                        :value="period.id"
                                        >{{ period.name }}</option
                                    >
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div
                            class="card"
                            :class="{
                                ' border-primary': selectedTeacher !== '',
                            }"
                        >
                            <div class="card-header">
                                {{ $t("Teacher") }}
                                <button
                                    v-if="selectedTeacher !== ''"
                                    class="btn btn-sm btn-pill btn-secondary float-right"
                                    @click="clearSelectedTeacher()"
                                >
                                    {{ $t("all") }}
                                </button>
                            </div>
                            <div class="card-body">
                                <select
                                    id="teacher"
                                    v-model="selectedTeacher"
                                    class="form-control"
                                    name="teacher"
                                    @change="getCoursesResults()"
                                >
                                    <option value="">{{
                                        $t("All teachers")
                                    }}</option>
                                    <option
                                        v-for="teacher in this.teachers"
                                        :key="teacher.id"
                                        :value="teacher.id"
                                        >{{ teacher.user.firstname }}
                                        {{ teacher.user.lastname }}</option
                                    >
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div
                            class="card"
                            :class="{
                                ' border-primary': selectedRhythms.length > 0,
                            }"
                        >
                            <div class="card-header">
                                {{ $t("Rhythm") }}
                                <button
                                    v-if="selectedRhythms.length > 0"
                                    class="btn btn-sm btn-pill btn-secondary float-right"
                                    @click="clearSelectedRhythms()"
                                >
                                    {{ $t("all") }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div
                                        v-for="rhythm in this.rhythms"
                                        :key="rhythm.id"
                                        class="form-check checkbox"
                                    >
                                        <input
                                            :id="rhythm.id"
                                            v-model="selectedRhythms"
                                            class="form-check-input"
                                            type="checkbox"
                                            :value="rhythm.id"
                                            @change="getCoursesResults()"
                                        />
                                        <label
                                            class="form-check-label"
                                            :for="rhythm.id"
                                            >{{ rhythm.name }}</label
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div
                            class="card"
                            :class="{
                                ' border-primary': selectedLevels.length > 0,
                            }"
                        >
                            <div class="card-header">
                                {{ $t("Level") }}
                                <button
                                    v-if="selectedLevels.length > 0"
                                    class="btn btn-sm btn-pill btn-secondary float-right"
                                    @click="clearSelectedLevels()"
                                >
                                    {{ $t("all") }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div
                                        v-for="level in this.levels"
                                        :key="level.id"
                                        class="form-check checkbox"
                                    >
                                        <input
                                            :id="level.id"
                                            v-model="selectedLevels"
                                            class="form-check-input"
                                            type="checkbox"
                                            :value="level.id"
                                            @change="getCoursesResults()"
                                        />
                                        <label
                                            class="form-check-label"
                                            :for="level.id"
                                            >{{ level.name }}</label
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- filters col -->

            <div
                v-if="isLoading == true && hasErrors == false"
                class="col-md-8"
            >
                {{ $t("Results are loading") }}
            </div>
            <div
                v-if="isLoading == false && hasErrors == true"
                class="col-md-8"
            >
                {{ $t("errorfetchingcourses") }}
            </div>

            <div
                v-if="isLoading == false && hasErrors == false"
                class="col-md-8"
            >
            <div class="row" v-if="this.mode == 'enroll'">
                    <div class="col-md-6">
                        <div class="card">
                        <div class="card-header">
                            {{ $t("Student") }}
                        </div>
                        <div class="card-body">
                            {{ this.student.name }}
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                        <div class="card-header">
                            {{ $t("Last enrollment") }}
                        </div>
                        <div class="card-body" v-if="this.student.enrollments.length > 0">
                            <p>{{ this.student.enrollments.slice(-1)[0].course.name }} ({{ this.student.enrollments.slice(-1)[0].course.course_period_name }})</p>
                            <label class="label-info">{{ this.student.enrollments.slice(-1)[0].result_name }}</label>
                        </div>
                        </div>
                    </div>

            </div>
                <div class="row">
                    <p v-if="sortedCourses.length == 0">
                        {{ $t("noresults") }}
                    </p>
                    <div
                        v-for="course in sortedCourses"
                        :key="course.id"
                        class="col-md-4"
                    >
                        <div
                            class="card"
                            :class="{
                                'border-danger':
                                    course.spots > 0 &&
                                    course.enrollments_count == 0,
                                'bg-secondary':
                                    highlightedSortableId == course.sortable_id,
                                'border-warning':
                                    course.teacher_id == null ||
                                    course.room_id == null,
                            }"
                            @mouseover="
                                highlightedSortableId = course.sortable_id
                            "
                            @mouseleave="highlightedSortableId = null"
                        >
                            <div class="card-body">
                                <div v-if="mode == 'view'" class="btn-group float-right">
                                    <a
                                        class="btn"
                                        :href="`course/${course.id}/show`"
                                        ><i class="la la-eye"></i
                                    ></a>
                                    <button
                                        class="btn dropdown-toggle p-0"
                                        type="button"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        <i class="la la-gear"></i>
                                    </button>
                                    <div
                                        class="dropdown-menu dropdown-menu-right"
                                    >
                                        <a
                                            v-if="
                                                course.events_count > 0 &&
                                                course.exempt_attendance !==
                                                    1 &&
                                                course.course_enrollments_count >
                                                    0
                                            "
                                            class="dropdown-item"
                                            :href="`attendance/course/${course.id}`"
                                            ><i class="la la-calendar"></i>
                                            {{ $t("Attendance") }}</a
                                        >
                                        <a
                                            v-if="editable == 1"
                                            class="dropdown-item"
                                            :href="`course/${course.id}/edit`"
                                            ><i class="la la-edit"></i>
                                            {{ $t("Edit") }}</a
                                        >
                                        <a
                                            v-if="
                                                editable == 1 &&
                                                course.children_count == 0
                                            "
                                            class="dropdown-item"
                                            :href="`coursetime/${course.id}/edit`"
                                            ><i class="la la-clock-o"></i>
                                            {{ $t("Edit schedule") }}</a
                                        >
                                        <button
                                            v-if="
                                                editable == 1 &&
                                                course.course_enrollments_count ==
                                                    0
                                            "
                                            class="dropdown-item text-danger"
                                            @click="deleteCourse(course.id)"
                                        >
                                            <i class="la la-trash"></i>
                                            {{ $t("Delete") }}
                                        </button>
                                    </div>
                                </div>

                                <div v-if="mode == 'enroll' && course.spots - course.course_enrollments_count > 0" class="btn-group float-right">
                                    <a
                                        class="btn"
                                        href='#'
                                        @click="enrollStudent(course.id)"
                                        ><i class="la la-user-plus"></i
                                    ></a>
                                </div>

                                <div v-if="mode == 'update'" class="btn-group float-right">
                                    <a
                                        class="btn"
                                        href='#'
                                        @click="updateEnrollment(course.id)"
                                        ><i class="la la-user-plus"></i
                                    ></a>
                                </div>
                                <h5 class="coursename">{{ course.name }}</h5>
                                <div v-if="course.teacher">
                                    <i class="la la-user"></i>
                                    {{ course.course_teacher_name }}
                                </div>
                                <div v-if="course.room">
                                    <i class="la la-home"></i>
                                    {{ course.room.name }}
                                </div>
                                <div>
                                    <i class="la la-clock-o"></i>
                                    {{ course.course_times }}
                                </div>
                                <div>
                                    <i class="la la-calendar"></i>
                                    {{ course.start_date | moment("D MMM") }} -
                                    {{ course.end_date | moment("D MMM") }} ({{
                                        course.volume
                                    }}h)
                                </div>
                                <div
                                    :class="{
                                        ' text-danger':
                                            course.spots > 0 &&
                                            course.course_enrollments_count ==
                                                0,
                                    }"
                                >
                                    <i class="la la-users"></i>
                                    {{ course.course_enrollments_count }}
                                    {{ $t("students") }},
                                    {{
                                        Math.max(
                                            0,
                                            course.spots -
                                                course.course_enrollments_count
                                        )
                                    }}
                                    {{ $t("spots left") }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- main col -->
        </div>
        <!-- row -->
    </div>
</template>

<script>
import _ from "lodash";

export default {
    props: [
        "periods",
        "defaultperiod",
        "teachers",
        "rhythms",
        "levels",
        "editable",
        "mode",
        "student",
        "enrollment_id"
    ],

    data() {
        return {
            selectedPeriod: this.defaultperiod.id,
            selectedTeacher: "",
            courses: [],
            selectedRhythms: [],
            selectedLevels: [],
            highlightedSortableId: null,
            isLoading: true,
            hasErrors: false,
            showChildren: true,
        };
    },

    computed: {
        sortedCourses() {
            return _.orderBy(this.courses, ["sortable_id", "id"], "asc");
        },
    },

    mounted() {
        this.getCoursesResults();
    },

    methods: {
        getCoursesResults() {
            this.isLoading = true;
            axios
                .get("/courselist/search", {
                    params: {
                        "filter[period_id]": this.selectedPeriod,
                        "filter[searchable_levels]": this.selectedLevels.join(),
                        "filter[rhythm_id]": this.selectedRhythms.join(),
                        "filter[teacher_id]": this.selectedTeacher,
                    },
                })
                .then(response => {
                    this.courses = response.data;
                    this.isLoading = false;
                    this.hasErrors = false;
                })
                .catch(errors => {
                    this.isLoading = false;
                    this.hasErrors = true;
                });
        },

        clearSelectedRhythms() {
            this.selectedRhythms = [];
            this.getCoursesResults();
        },

        clearSelectedLevels() {
            this.selectedLevels = [];
            this.getCoursesResults();
        },

        clearSelectedTeacher() {
            this.selectedTeacher = "";
            this.getCoursesResults();
        },

        deleteCourse(id) {
            swal({
                title: "DANGER",
                text: "Realmente quiere eliminar este curso?",
                icon: "danger",
                buttons: {
                    cancel: {
                        text: "No",
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    delete: {
                        text: "Si",
                        value: true,
                        visible: true,
                        className: "bg-danger",
                    },
                },
            }).then(value => {
                if (value) {
                    $.ajax({
                        url: `course/${id}`,
                        type: "DELETE",
                        success: result => {
                            if (result != 1) {
                                // Show an error alert
                                swal({
                                    title: "Error",
                                    text: "Impossible to delete this course",
                                    icon: "error",
                                    timer: 2000,
                                    buttons: false,
                                });
                            } else {
                                // Show a success message
                                swal({
                                    title: "Success",
                                    text: "The course has been deleted",
                                    icon: "success",
                                    timer: 4000,
                                    buttons: false,
                                });
                                location.reload();
                            }
                        },
                        error: result => {
                            // Show an alert with the result
                            swal({
                                title: "Error",
                                text: "Impossible to delete this course",
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        },
                    });
                }
            });
        },
        enrollStudent(course_id)
        {
            this.mode = 'blocked'
            new Noty({
                type: "info",
                text: 'Matricula en curso...',
            }).show();
            axios.post('/student/enroll', {
                student_id: this.student.id,
                course_id: course_id
            })
            .then(response => {
                window.location.href=response.data
            })
        },
        updateEnrollment(course_id)
        {
            this.mode = 'blocked'
            new Noty({
                type: "info",
                text: 'Cambiando de curso...',
            }).show();
            axios.post(`/enrollment/${this.enrollment_id}/changeCourse`, {
                student_id: this.student.id,
                course_id: course_id
            })
            .then(response => {
                window.location.href=response.data
            })
        },
    },
};
</script>

<style scoped>
.coursename {
    text-transform: uppercase;
    font-weight: bold;
}
</style>
