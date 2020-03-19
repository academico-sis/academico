<template>
<div>
<div class="row">

    <div class="col-md-4">
        <div class="row">
            <div class="col-md-6">
            <div class="card">
                <div class="card-header">Period</div>
                <div class="card-body">
                    <select class="form-control" name="period" id="period" v-model="selectedPeriod" @change="getCoursesResults()">
                        <option :value=period.id v-for="period in this.periods" :key="period.id">{{ period.name }}</option>
                    </select>
                </div>
            </div>
            </div>

            <div class="col-md-6">
            <div class="card" v-bind:class="{ ' border-primary': selectedTeacher !== '' }">
                <div class="card-header">
                    Teacher
                    <button v-if="selectedTeacher !== ''" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedTeacher()">Remove filter</button>
                    </div>
                <div class="card-body">
                    <select class="form-control" name="teacher" id="teacher" v-model="selectedTeacher" @change="getCoursesResults()">
                        <option value=''>All teachers</option>
                        <option :value=teacher.id v-for="teacher in this.teachers" :key="teacher.id">{{ teacher.user.firstname }} {{ teacher.user.lastname }}</option>
                    </select>
                </div>
            </div>
            </div>
        </div>

<div class="row">
    <div class="col-md-6">
        <div class="card" v-bind:class="{ ' border-primary': selectedRhythms.length > 0 }">
            <div class="card-header">
                Rhythm
                <button v-if="selectedRhythms.length > 0" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedRhythms()">Remove filter</button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-check checkbox" v-for="rhythm in this.rhythms" :key="rhythm.id">
                    <input class="form-check-input" type="checkbox" :id="rhythm.id" :value="rhythm.id" v-model="selectedRhythms" @change="getCoursesResults()">
                    <label class="form-check-label" :for="rhythm.id">{{ rhythm.name }}</label>
                </div>
                </div>
            </div>
        </div>

        <div class="card">
            <label class="switch switch-pill switch-primary">
                <input class="switch-input" type="checkbox" checked=""><span class="switch-slider"></span>
            </label>
            Show children
        </div>

        <div class="card">
            Search by name
        </div>

        <button @click="getCoursesResults()">Filter</button>
    </div>

    <div class="col-md-6">
        <div class="card" v-bind:class="{ ' border-primary': selectedLevels.length > 0 }">
            <div class="card-header">
                Level
                <button v-if="selectedLevels.length > 0" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedLevels()">Remove filter</button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-check checkbox" v-for="level in this.levels" :key="level.id">
                    <input class="form-check-input" type="checkbox" :id="level.id" :value="level.id" v-model="selectedLevels" @change="getCoursesResults()">
                    <label class="form-check-label" :for="level.id">{{ level.name }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div><!-- filters col -->
        


    <div class="col-md-8">
        <div class="row">
        <div class="col-md-4" v-for="course in sortedCourses" :key="course.id">
        <div class="card" v-bind:class="{ ' border-danger': course.spots > 0 && course.course_enrollments_count == 0 }">
            <div class="card-body">
                <h5 class="coursename">{{ course.name }} {{ course.sortable_id }}</h5>
                <!-- <span v-if="course.level">Level: {{ course.level.name }}</span>
                <span v-if="course.rhythm">Rhythm: {{ course.rhythm.name }}</span> -->
                <div v-if="course.teacher"><i class="fa fa-user"></i> {{ course.course_teacher_name }}</div>
                <div v-if="course.room"><i class="fa fa-home"></i> {{ course.room.name }}</div>
                <div><i class="fa fa-clock-o"></i> {{ course.course_times }}</div>
                <div><i class="fa fa-calendar"></i> {{ course.start_date | moment("D MMM") }} - {{ course.end_date | moment("D MMM") }}</div>
                <div v-bind:class="{ ' text-danger': course.spots > 0 && course.course_enrollments_count == 0 }"><i class="fa fa-users"></i> {{ course.course_enrollments_count }} ({{ Math.max(0, course.spots - course.course_enrollments_count) }} available)</div>
            </div>
        </div>
        </div>
        </div>
    </div><!-- main col -->


  </div><!-- row -->
  </div>
</template>

<script>
import _ from 'lodash';

export default {
    props: ['periods', 'defaultperiod', 'teachers', 'rhythms', 'levels'],

    data() {
        return {
            selectedPeriod: this.defaultperiod.id,
            selectedTeacher: '',
            courses: [],
            selectedRhythms: [],
            selectedLevels: []
        }
    },

    mounted() {
        this.getCoursesResults();
    },

    computed: {
        sortedCourses() {
            return _.orderBy(this.courses, ['sortable_id', 'id'], 'asc');
        }
    },

    methods: {
        getCoursesResults()
        {
            axios
            .get('/courselist/search', {
                params: {
                    "filter[period_id]": this.selectedPeriod,
                    "filter[level_id]": this.selectedLevels.join(),
                    "filter[rhythm_id]": this.selectedRhythms.join(),
                    "filter[teacher_id]": this.selectedTeacher
                }
            })
            .then(response => { this.courses = response.data });
        },

        clearSelectedRhythms()
        {
            this.selectedRhythms = [];
            this.getCoursesResults();
        },

        clearSelectedLevels()
        {
            this.selectedLevels = [];
            this.getCoursesResults();
        },

        clearSelectedTeacher()
        {
            this.selectedTeacher = '';
            this.getCoursesResults();
        }
    }
}
</script>

<style scoped>
.coursename {
    text-transform: uppercase;
    font-weight: bold;
}
</style>