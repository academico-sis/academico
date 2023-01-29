<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                {{ $t("Course result") }}
            </div>

            <div class="card-body">
                <div
                    class="btn-group btn-group-justified"
                    role="group"
                    aria-label=""
                >
                    <button
                        v-for="result in results"
                        :key="result.id"
                        :class="buttonClass(result)"
                        :disabled="loading || !writeaccess"
                        @click="saveResult(result)"
                    >
                        {{ result.translated_name }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "enrollment",
        "results",
        "result",
        "resultPostRoute",
        "writeaccess",
    ],

    data() {
        return {
            course_result: this.result,
            loading: false,
            errors: [],
        };
    },

    mounted() {},

    methods: {
        saveResult(result) {
            this.loading = true;
            axios
                .post(this.resultPostRoute, {
                    result: result.id,
                    student: this.enrollment.student_id,
                    enrollment: this.enrollment.id,
                })
                .then((response) => {
                    this.loading = false;
                    window.location.reload();
                })
                .catch((e) => this.errors.push(e));
        },

        buttonClass(result_type) {
            if (
                this.course_result &&
                this.course_result.result_type_id === result_type.id
            ) {
                return `btn btn-${result_type.class}`;
            } else {
                return "btn btn-secondary";
            }
        },
    },
};
</script>
