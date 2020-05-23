<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Résultat final
            </div>

            <div class="card-body">
                <div
                    class="btn-group btn-group-justified"
                    role="group"
                    aria-label=""
                >
                    <!-- todo get styling from the model -->
                    <div
                        v-for="result in results"
                        :key="result.id"
                        class="btn-group"
                        role="group"
                    >
                        <button
                            class="btn btn-secondary"
                            :class="{
                                'btn-success':
                                    course_result &&
                                    course_result.result_type_id == result.id &&
                                    result.id == 1,
                                'btn-danger':
                                    course_result &&
                                    course_result.result_type_id == result.id &&
                                    result.id == 2,
                                'btn-info':
                                    course_result &&
                                    course_result.result_type_id == result.id &&
                                    result.id == 3,
                            }"
                            @click="saveResult(result)"
                        >
                            {{ result.name.fr }}
                        </button>
                    </div>
                </div>

                <div v-if="this.course_result">
                    <h4>{{ $t("Comments") }}</h4>
                    <ul>
                        <li v-for="comment in this.comments" :key="comment.id">
                            {{ comment.body }}
                        </li>
                    </ul>

                    <textarea
                        id="comment"
                        v-model="newcomment"
                        name="comment"
                        style="width: 100%;"
                        rows="4"
                    ></textarea>
                    <button
                        v-if="newcomment != null"
                        type="button"
                        class="btn btn-primary"
                        @click="saveComment(newcomment)"
                    >
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "student",
        "enrollment",
        "results",
        "result",
        "stored_comments",
        "resultPostRoute",
        "commentPostRoute",
    ],

    data() {
        return {
            newcomment: null,
            course_result: this.result,
            comments: this.stored_comments,
        };
    },

    mounted() {},

    methods: {
        saveComment() {
            axios
                .post(this.commentPostRoute, {
                    enrollment: this.enrollment.id,
                    comment: this.newcomment,
                })
                .then((response) => {
                    this.comments.push(response.data);
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },

        saveResult(result) {
            axios
                .post(this.resultPostRoute, {
                    result: result.id,
                    student: this.student.id,
                    enrollment: this.enrollment.id,
                })
                .then((response) => {
                    document.location.reload(true); // todo improve
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },
    },
};
</script>
