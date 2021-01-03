<template>
<tr>
        <td>{{ enrollment.student.name }}</td>

        <td v-for="grade_type in course_grade_types">
            <grade-field-component
                :enrollment_id=enrollment.id
                :grade_type=grade_type
                :grade="enrollmentGradesForGradeType(grade_type.id)"
            ></grade-field-component>
        </td>

        <td v-if="!loading">
            {{ enrollmentTotal }} / {{ courseTotal }}
        </td>
        <td v-else>...</td>

        <td>
            {{ enrollment.result_name }}
            <a :href="`/result/${enrollment.id}/show`"><i class="la la-edit"></i></a>
        </td>

        <td>
            <div v-if="enrollment.result && enrollment.result.comments.length > 0">
                <p v-for="comment in enrollment.result.comments">{{ comment.body }}</p>
            </div>
        </td>
</tr>
</template>

<script>

import gradeFieldComponent from "./gradeFieldComponent";
import {EventBus} from "./eventBus";
import _ from "lodash";

export default {
    props: ['enrollment', 'course_grade_types', 'grades'],
    components: {
        gradeFieldComponent,
    },
    data() {
        return {
            enrollmentTotal: 0,
            loading: false,
        }
    },
    computed: {
        courseTotal() {
            var sum = 0;
            Object.values(this.course_grade_types).forEach(gradetype => {
                sum += parseFloat( gradetype.total );
            });

            return sum;
        },
    },
    methods: {
        enrollmentGradesForGradeType: function (gradeTypeId) {
            return Object.values(this.grades).find(grade => grade.grade_type_id === gradeTypeId)
        },
        refreshEnrollmentTotal()
        {
            this.loading = true;
            axios.post('/grades/enrollment-total', {
                enrollment_id: this.enrollment.id,
            })
            .then(response => {
                this.enrollmentTotal = response.data
                this.loading = false;
            })
            .catch(error => console.error(error)) // todo display errors to user.
        }
    },
    created() {
        EventBus.$on("updateGradeValue", (grade_type, value) => {
            this.refreshEnrollmentTotal()
        });
    },
    mounted() {
        this.refreshEnrollmentTotal()
    }
}
</script>

<style>

</style>
