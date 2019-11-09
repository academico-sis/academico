<template>

<div class="card">
    <div class="card-header">Commentaires
        <div class="card-header-actions">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
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

    <!-- Modal / todo translate buttons -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Nouveau commentaire</h4>
      </div>

      <div class="modal-body">
        <textarea name="comment" id="comment" style="width: 100%" rows="5" v-model="comment_body"></textarea>
        <label for="action">@lang("This comment requires an action")</label>
        <input name="action" id="action" type="checkbox" v-model="action"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        <button type="button" @click="addComment()" class="btn btn-primary">Enregister</button>
      </div>
    </div>
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
                        this.commentlist.push(response.data);
                        $('#myModal').modal('hide');
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
