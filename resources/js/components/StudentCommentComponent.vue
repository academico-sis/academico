<template>

<div class="card">
    <div class="card-header">{{ $t('Comments') }}
        <div class="card-header-actions">
            <button type="button" class="btn btn-sm btn-primary" @click="showEditField = true">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <ul>
            <li v-for="(comment, index) in commentlist" v-bind:key="comment.id">
                {{ comment.body }} ({{comment.created_at | moment("D MMM YY") }})
                <button type="button" @click="deleteComment(comment.id, index)" class="btn btn-danger btn-sm">X</button>
                <!-- <button type="button" @click="editComment(comment.id)" class="btn btn-info btn-sm">Edit</button> -->
            </li>
        </ul>
    </div>
    <div class="card-footer" v-if="showEditField">
        <textarea name="comment" id="comment" style="width: 100%" rows="3" v-model="comment_body"></textarea>
        <div class="form-group">
            <label for="action">{{ $t('front.This comment requires an action') }}</label>
            <input name="action" id="action" type="checkbox" v-model="action"/>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-default" @click="showEditField = false">{{ $t('front.Cancel') }}</button>
            <button type="button" @click="addComment()" class="btn btn-primary">{{ $t('Save') }}</button>
        </div>
    </div>

</div>
</template>

<script>
    
    export default {

        props: ['comments', 'id', 'type', 'route'],

        data () {
            return {
                comment_body: null,
                action: false,
                showEditField: false,
                errors: [],
                commentlist: this.comments
            }
        },

        mounted() {

        },

        methods: {
            addComment()
            {
                axios
                    .post(this.route, {
                        body: this.comment_body,
                        commentable_id: this.id,
                        commentable_type: this.type,
                        action: this.action
                    })
                    .then(response => {
                        this.commentlist.push(response.data)
                        this.comment_body = null
                        this.showEditField = false
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },

            deleteComment(comment, index)
            {
                axios
                    .delete('/comment/'+comment)
                    .then(response => {
                        this.$delete(this.commentlist, index)
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            }
        }
    }
</script>
