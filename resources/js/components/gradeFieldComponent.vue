<template>
    <p>
        <a v-if="!this.editable" href="#" @click="editGrade()">{{ this.gradeFieldValue }}</a>
        <input style="width: 6em;" type="number" min="0" :max="grade_type.total" step="0.25" v-model="gradeFieldValue" v-else />
        <button class="btn btn-success" v-if="this.editable" @click="saveAndCloseField()"><i class="la la-save"></i></button>
        / {{ grade_type.total }}
    </p>
</template>

<script>

import {EventBus} from "./eventBus";

export default {
    props: ['grade', 'enrollment_id', 'grade_type'],
    data() {
        return {
            gradeFieldValue: this.grade ? this.grade.grade : 0,
            editable: false,
        }
    },
    methods: {
        editGrade() {
            this.editable = true;
        },
        saveAndCloseField() {
            // post grade to backend
            axios
                .post(`/grades`, {
                    grade_type_id: this.grade_type.id,
                    enrollment_id: this.enrollment_id,
                    value: this.gradeFieldValue,
                })
                .then(response => {
                    // close field
                    this.editable = false;

                    // propagate grade value update
                    EventBus.$emit("updateGradeValue", this.gradeFieldValue);
                })
                .catch(error =>
                    console.error(error) // TODO display error message.
                )
        }
    },
    mounted() {

    }
}
</script>

<style>

</style>
