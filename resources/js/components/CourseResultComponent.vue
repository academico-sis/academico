<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                {{ $t('Course result') }}
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
                            v-bind:class="buttonClass(result)"
                            @click="saveResult(result)"
                            :disabled="loading || !writeaccess"
                        >
                            {{ result.translated_name }}
                        </button>
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
                        :disabled="loading"
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
        "enrollment",
        "results",
        "result",
        "stored_comments",
        "resultPostRoute",
        "commentPostRoute",
        "writeaccess"
    ],

    data() {
        return {
            newcomment: null,
            course_result: this.result,
            comments: this.stored_comments ?? [],
            loading: false,
            errors: []
        };
    },

    mounted() {},

    methods: {
        saveComment() {
            this.loading = true;
            axios
                .post(this.commentPostRoute, {
                    commentable_id: this.course_result.id,
                    commentable_type: "App\\Models\\Result",
                    body: this.newcomment,
                })
                .then((response) => {
                    this.loading = false;
                    this.comments.push(response.data);
                    this.newcomment = null
                    new Noty({
                        title: "Opération réussie",
                        text: "Commentaire sauvegardé !",
                        type: "success",
                    }).show();
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },

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
                    this.course_result = response.data
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },

        buttonClass(result_type) {
            if (this.course_result && this.course_result.result_type_id == result_type.id)
            {
                return "btn btn-"+result_type.class
            }
            else {
                return "btn btn-secondary"
            }
        }
    },
};
</script>
