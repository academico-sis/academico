<template>
    <div class="card">
        <div class="card-header">
            {{ $t("Comments") }}
            <div v-if="writeaccess" class="card-header-actions">
                <button
                    type="button"
                    class="btn btn-sm btn-primary"
                    @click="showCommentForm()"
                >
                    <i class="la la-plus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div v-if="isValidated" class="alert alert-success">
                {{ $t("Your comment has been saved") }}
            </div>
            <div
                v-if="!commentlist || commentlist.length === 0"
                style="font-style: italic"
            >
                {{ $t("No comment yet") }}
            </div>
            <ul>
                <li v-for="(comment, index) in commentlist" :key="comment.id">
                    <span v-if="comment.prefix">{{ comment.prefix }}</span>
                    {{ comment.body }}
                    <span v-if="!comment.prefix"
                        >({{ comment.created_at | moment("D MMM YY") }})</span
                    >
                    <button
                        v-if="writeaccess"
                        type="button"
                        class="btn btn-sm"
                        @click="deleteComment(comment.id, index)"
                    >
                        <i class="la la-trash"></i>
                    </button>

                    <button
                        v-if="writeaccess"
                        type="button"
                        class="btn btn-sm"
                        @click="editComment(comment)"
                    >
                        <i class="la la-pencil"></i>
                    </button>
                </li>
            </ul>
        </div>
        <div v-if="writeaccess && showEditField" class="card-footer">
            <div v-if="errors" class="alert alert-danger">
                {{ errors }}
            </div>

            <textarea
                id="comment"
                ref="comment"
                v-model="comment_body"
                name="comment"
                style="width: 100%"
                rows="3"
            ></textarea>
            <div class="btn-group">
                <button
                    type="button"
                    class="btn btn-xs btn-default"
                    @click="closeCommentForm()"
                >
                    {{ $t("Cancel") }}
                </button>
                <button
                    v-if="!selectedComment"
                    type="button"
                    class="btn btn-xs btn-primary"
                    @click="addComment()"
                >
                    {{ $t("Save") }}
                </button>

                <button
                    v-else
                    type="button"
                    class="btn btn-xs btn-primary"
                    @click="updateComment()"
                >
                    {{ $t("Save") }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["comments", "id", "type", "route", "writeaccess"],

    data() {
        return {
            comment_body: null,
            action: false,
            showEditField: false,
            errors: null,
            commentlist: this.comments,
            isValidated: false,
            selectedComment: null,
        };
    },

    mounted() {},

    methods: {
        showCommentForm() {
            this.showEditField = true;
            this.$nextTick(() => this.$refs.comment.focus());
        },

        closeCommentForm() {
            this.showEditField = false;
            this.comment_body = null;
        },

        addComment() {
            axios
                .post(this.route, {
                    body: this.comment_body,
                    commentable_id: this.id,
                    commentable_type: this.type,
                    action: this.action,
                })
                .then((response) => {
                    this.commentlist.push(response.data);
                    this.comment_body = null;
                    this.showEditField = false;
                    this.errors = null;
                    this.isValidated = true;
                    setTimeout(() => {
                        this.isValidated = false;
                    }, 3000);
                })
                .catch((e) => {
                    this.errors = e.response.data.errors.body[0];
                });
        },

        deleteComment(comment, index) {
            swal({
                title: this.$t("Warning"),
                text: this.$t("Do you really want to delete this comment?"),
                icon: "warning",
                buttons: {
                    cancel: {
                        text: this.$t("No"),
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    delete: {
                        text: this.$t("Delete"),
                        value: true,
                        visible: true,
                        className: "bg-danger",
                    },
                },
            }).then((value) => {
                if (value) {
                    axios
                        .delete(`/comment/${comment}`)
                        .then((response) =>
                            this.$delete(this.commentlist, index)
                        )
                        .catch((e) => this.errors.push(e));
                }
            });
        },

        editComment(comment) {
            this.selectedComment = comment;
            this.comment_body = comment.body;
            this.showCommentForm();
        },

        updateComment() {
            axios
                .put(`/edit-comment/${this.selectedComment.id}`, {
                    body: this.comment_body,
                })
                .then((response) => {
                    this.commentlist.filter(
                        (comment) => comment.id === this.selectedComment.id
                    )[0].body = this.comment_body;
                    this.selectedComment = null;
                    this.comment_body = null;
                    this.showEditField = false;
                    this.errors = null;
                    this.isValidated = true;
                    setTimeout(() => {
                        this.isValidated = false;
                    }, 3000);
                })
                .catch((e) => {
                    this.errors = e.response.data.errors.body[0];
                });
        },
    },
};
</script>
