<template>
<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ $t("Period") }}</div>
                    <div class="card-body">
                        <select id="period" v-model="selectedPeriod" class="form-control" name="period" @change="getCoursesResults()">
                            <option v-for="period in this.periods" :key="period.id" :value="period.id">{{ period.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div :class="{'border-primary': selectedTeacher !== ''}" class="card">
                    <div class="card-header">
                        {{ $t("Teacher") }}
                        <button v-if="selectedTeacher !== ''" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedTeacher()">
                            {{ $t("all") }}
                        </button>
                    </div>
                    <div class="card-body">
                        <select id="teacher" v-model="selectedTeacher" class="form-control" name="teacher" @change="getCoursesResults()">
                            <option value="">{{ $t("All teachers") }}</option>
                            <option v-for="teacher in this.teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div
                    :class="{'border-primary': selectedRhythms.length > 0}" class="card">
                    <div class="card-header">
                        {{ $t("Rhythm") }}
                        <button v-if="selectedRhythms.length > 0" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedRhythms()">
                            {{ $t("all") }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div v-for="rhythm in this.rhythms" :key="rhythm.id" class="form-check checkbox">
                                <input
                                    :id="rhythm.id"
                                    v-model="selectedRhythms"
                                    :value="rhythm.id"
                                    class="form-check-input"
                                    type="checkbox"
                                    @change="getCoursesResults()"
                                />
                                <label :for="rhythm.id" class="form-check-label">{{ rhythm.name }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div
                    :class="{'border-primary': selectedLevels.length > 0}" class="card">
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
                                    :value="level.id"
                                    class="form-check-input"
                                    type="checkbox"
                                    @change="getCoursesResults()"
                                />
                                <label
                                    :for="level.id"
                                    class="form-check-label"
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
        v-if="isLoading === true && hasErrors === false"
        class="col-md-8"
    >
        {{ $t("Results are loading") }}
    </div>
    <div
        v-if="isLoading === false && hasErrors === true"
        class="col-md-8"
    >
        {{ $t("errorfetchingcourses") }}
    </div>

    <div
        v-if="isLoading === false && hasErrors === false"
        class="col-md-8"
    >
        <div v-if="this.mode === 'enroll'" class="row">
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
                <div class="card" v-if="this.student.enrollments.length > 0">
                    <div class="card-header">
                        {{ $t("Last enrollment") }}
                    </div>
                    <div class="card-body">
                        <p>{{ this.student.enrollments.slice(-1)[0].course.name }}
                            ({{ this.student.enrollments.slice(-1)[0].course.course_period_name }})</p>
                        <label class="label-info">{{
                                this.student.enrollments.slice(-1)[0].result_name
                            }}</label>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <p v-if="sortedCourses.length === 0">
                {{ $t("noresults") }}
            </p>
            <div
                v-for="course in sortedCourses"
                :key="course.id"
                class="col-md-4"
            >
                <div
                    :class="{
                        'border-danger':
                            course.spots > 0 &&
                            course.enrollments_count === 0,
                        'bg-secondary':
                            highlightedSortableId === course.sortable_id,
                        'border-warning':
                            course.teacher_id == null ||
                            course.room_id == null,
                    }"
                    class="card"
                    @mouseleave="highlightedSortableId = null"
                    @mouseover="
                        highlightedSortableId = course.sortable_id
                    "
                >
                    <div class="card-body">
                        <div v-if="mode === 'view'" class="btn-group float-right">
                            <a :href="`enrollment?course_id=${course.id}`" class="btn">
                                <i class="la la-eye"></i>
                            </a>
                            <button aria-expanded="false" aria-haspopup="true" class="btn dropdown-toggle p-0" data-toggle="dropdown" type="button">
                                <i class="la la-gear"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a v-if="course.takes_attendance" :href="`attendance/course/${course.id}`" class="dropdown-item">
                                    <i class="la la-calendar"></i> {{ $t("Attendance") }}
                                </a>

                                <a v-if="editable === 1" :href="`course/${course.id}/edit`" class="dropdown-item">
                                    <i class="la la-edit"></i> {{ $t("Edit") }}</a>

                                <a v-if="course.evaluation_type && course.evaluation_type.skills.length > 0 && course.course_enrollments_count > 0"
                                    :href="`course/${course.id}/skillsevaluation`"
                                    class="dropdown-item">
                                    <i class="la la-th"></i> {{ $t('Evaluate skills') }}
                                </a>

                                <a
                                    v-if="course.evaluation_type && course.evaluationType.gradeTypes.length > 0 && course.course_enrollments_count > 0"
                                    :href="`course/${course.id}/grades`"
                                    class="dropdown-item">
                                    <i class="la la-th"></i> {{ $t('Manage grades') }}
                                </a>

                                <button v-if=" editable === 1 && course.course_enrollments_count === 0" class="dropdown-item text-danger" @click="deleteCourse(course.id)">
                                    <i class="la la-trash"></i> {{ $t("Delete") }}
                                </button>
                            </div>
                        </div>

                        <div v-if="mode === 'enroll' && course.accepts_new_students"
                             class="btn-group float-right">
                            <a class="btn" href='#' @click="enrollStudent(course.id)"><i class="la la-user-plus"></i></a>
                        </div>

                        <div v-if="mode === 'update'" class="btn-group float-right">
                            <a class="btn" href='#' @click="updateEnrollment(course.id)"><i class="la la-user-plus"></i></a>
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

                        <div :class="{'text-danger': course.spots > 0 && course.course_enrollments_count === 0 }">
                            <i class="la la-users"></i>
                            {{ course.course_enrollments_count }}
                            {{ $t("students") }}
                            <span v-if="course.spots > 0">, {{ Math.max(0, course.spots - course.course_enrollments_count) }} {{ $t("spots left") }}</span>
                        </div>

                        <div v-if="course.evaluation_type">
                            <i class="la la-th"></i>
                            {{ course.evaluation_type.translated_name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- main col -->
</div>
<!-- row -->
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
                title: this.$t('Warning'),
                text: this.$t('Do you really want to delete this course?'),
                icon: "warning",
                buttons: {
                    cancel: {
                        text: this.$t('Cancel'),
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    delete: {
                        text: this.$t('Delete'),
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
                            if (result !== 1) {
                                // Show an error alert
                                swal({
                                    title: this.$t('Error'),
                                    text: this.$t('Your changes could not be saved'),
                                    icon: "error",
                                    timer: 2000,
                                    buttons: false,
                                });
                            } else {
                                // Show a success message
                                swal({
                                    title: this.$t('Success'),
                                    text: this.$t('The course has been deleted'),
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
                                title: this.$t('Error'),
                                text: this.$t('Impossible to delete this course'),
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        },
                    });
                }
            });
        },
        enrollStudent(course_id) {
            this.mode = 'blocked'
            new Noty({
                type: "info",
                text: this.$t('Enrollment in progress...'),
            }).show();
            axios.post('/student/enroll', {
                student_id: this.student.id,
                course_id
            })
                .then(response => {
                    window.location.href = response.data
                })
        },
        updateEnrollment(course_id) {
            this.mode = 'blocked'
            new Noty({
                type: "info",
                text: this.$t('Enrollment in progress...'),
            }).show();
            axios.post(`/enrollment/${this.enrollment_id}/changeCourse`, {
                student_id: this.student.id,
                course_id
            })
                .then(response => {
                    window.location.href = response.data
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

.dropdown-menu {
    max-height: 400px;
    overflow-y: auto;
}
</style>
