<template>
<div>
<div class="row">

    <div class="col-md-4">
        <div class="row">
            <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ $t('Period') }}</div>
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
                    {{ $t('Teacher') }}
                    <button v-if="selectedTeacher !== ''" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedTeacher()">{{ $t('front.all') }}</button>
                    </div>
                <div class="card-body">
                    <select class="form-control" name="teacher" id="teacher" v-model="selectedTeacher" @change="getCoursesResults()">
                        <option value=''>{{ $t('front.All teachers') }}</option>
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
                {{ $t('Rhythm') }}
                <button v-if="selectedRhythms.length > 0" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedRhythms()">{{ $t('front.all') }}</button>
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

    </div>

    <div class="col-md-6">
        <div class="card" v-bind:class="{ ' border-primary': selectedLevels.length > 0 }">
            <div class="card-header">
                {{ $t('Level') }}
                <button v-if="selectedLevels.length > 0" class="btn btn-sm btn-pill btn-secondary float-right" @click="clearSelectedLevels()">{{ $t('front.all') }}</button>
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
        

<div class="col-md-8" v-if="isLoading == true && hasErrors == false">{{$t('front.Results are loading')}}</div>
<div class="col-md-8" v-if="isLoading == false && hasErrors == true">{{$t('front.errorfetchingcourses')}}</div>

    <div class="col-md-8" v-if="isLoading == false && hasErrors == false">
        <div class="row">
            <p v-if="sortedCourses.length == 0">{{$t('front.noresults')}}</p>
        <div class="col-md-4" v-for="course in sortedCourses" :key="course.id">
        <div class="card"
            @mouseover="highlightedSortableId = course.sortable_id"
            @mouseleave="highlightedSortableId = null"
            v-bind:class="{
                'border-danger': course.spots > 0 && course.course_enrollments_count == 0,
                'bg-secondary': highlightedSortableId == course.sortable_id,
                'border-warning': course.teacher_id == null || course.room_id == null,
            }">
            <div class="card-body">
                <div class="btn-group float-right">
                    <a class="btn" :href="'course/'+course.id+'/show'"><i class="fa fa-eye"></i></a>
                    <button class="btn dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-gear"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a v-if="course.events_count > 0 && course.exempt_attendance !== 1 && course.course_enrollments_count > 0" class="dropdown-item" :href="`attendance/course/${course.id}`"><i class="fa fa-calendar"></i> {{$t('Attendance')}}</a>
                        <a v-if="editable" class="dropdown-item" :href="`course/${course.id}/edit`"><i class="fa fa-edit"></i> {{$t('Edit')}}</a>
                        <a v-if="editable && course.children_count == 0" class="dropdown-item" :href="`coursetime/${course.id}/edit`"><i class="fa fa-clock-o"></i> {{ $t('front.Edit schedule') }}</a>
                        <button v-if="editable" class="dropdown-item" @click="createChildCourse(course.id)"><i class="fa fa-clone"></i> {{$t('front.Create subcourse') }}</button>
                        <button v-if="editable && course.course_enrollments_count == 0" class="dropdown-item text-danger" @click="deleteCourse(course.id)"><i class="fa fa-trash"></i> {{ $t('front.Delete') }}</button>
                    </div>
                </div>
                <h5 class="coursename">{{ course.name }}</h5>
                <div v-if="course.teacher"><i class="fa fa-user"></i> {{ course.course_teacher_name }}</div>
                <div v-if="course.room"><i class="fa fa-home"></i> {{ course.room.name }}</div>
                <div><i class="fa fa-clock-o"></i> {{ course.course_times }}</div>
                <div><i class="fa fa-calendar"></i> {{ course.start_date | moment("D MMM") }} - {{ course.end_date | moment("D MMM") }} ({{ course.volume }}h)</div>
                <div v-bind:class="{ ' text-danger': course.spots > 0 && course.course_enrollments_count == 0 }"><i class="fa fa-users"></i> {{ course.course_enrollments_count }} {{$t('students')}}, {{ Math.max(0, course.spots - course.course_enrollments_count) }} {{$t('front.spots left')}}</div>
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
    props: ['periods', 'defaultperiod', 'teachers', 'rhythms', 'levels', 'editable'],

    data() {
        return {
            selectedPeriod: this.defaultperiod.id,
            selectedTeacher: '',
            courses: [],
            selectedRhythms: [],
            selectedLevels: [],
            highlightedSortableId: null,
            isLoading: true,
            hasErrors: false,
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
            this.isLoading = true;
            axios
            .get('/courselist/search', {
                params: {
                    "filter[period_id]": this.selectedPeriod,
                    "filter[searchable_levels]": this.selectedLevels.join(),
                    "filter[rhythm_id]": this.selectedRhythms.join(),
                    "filter[teacher_id]": this.selectedTeacher
                }
            })
            .then(response => {
                this.courses = response.data
                this.isLoading = false
                this.hasErrors = false
            })
            .catch(errors => {
                this.isLoading = false
                this.hasErrors = true
            });
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
        },

        createChildCourse(id)
        {
        swal({
		  title: "Warning",
		  text: "Realmente quiere crear un curso hijo para este curso?",
		  icon: "warning",
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
			}
		  },
		}).then((value) => {
			if (value) {
            $.ajax({
              url: `course/${id}/clone`,
              type: 'POST',
              success: function(result) {
                  // Show an alert with the result
                  new Noty({
                    type: "success",
                    text: "<strong>Course cloned</strong><br>A new course has been created as a sub-course of this one."
                  }).show();

                  // Hide the modal, if any
                  $('.modal').modal('hide');

                  // open new course edit page
                  window.location.href = '/course/'+result+'/edit'
              },
              error: function(result) {
                  // Show an alert with the result
                  new Noty({
                    type: "warning",
                    text: "<strong>Cloning failed</strong><br>The new course could not be created. Please try again."
                  }).show();
              }
          });
            }
        });
        },

        deleteCourse(id)
        {
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
			}
		  },
		}).then((value) => {
			if (value) {
				$.ajax({
			      url: 'course/'+id,
			      type: 'DELETE',
			      success: function(result) {
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
			      error: function(result) {
			          // Show an alert with the result
			          swal({
                        title: "Error",
                        text: "Impossible to delete this course",
		              	icon: "error",
		              	timer: 4000,
		              	buttons: false,
		              });
                  }
                });
            }
            });
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