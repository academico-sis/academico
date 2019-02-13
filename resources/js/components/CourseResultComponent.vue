<template>

  <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    Résultat final <!-- todo translate -->
                </div>

                <div class="box-tools pull-right">
                </div>  
            </div>
            
            <div class="box-body">
            <div class="btn-group btn-group-justified" role="group" aria-label="">
                <!-- todo get styling from the model -->
                <div class="btn-group" role="group"
                v-for="result in results" v-bind:key="result.id">
                    <button
                    class="btn btn-secondary"
                    v-bind:class="{
                        'btn-success': course_result && course_result.result_type_id == result.id && result.id == 1,
                        'btn-danger': course_result && course_result.result_type_id == result.id && result.id == 2,
                        'btn-info': course_result && course_result.result_type_id == result.id && result.id == 3,
                        }"
                    v-on:click="saveResult(result)"
                    >
                        {{ result.name.fr }}
                    </button>
                </div>
            </div>

            <div v-if="this.course_result">
                <h4>Commentaires</h4><!-- todo translate -->
                <ul>
                    <li v-for="comment in this.comments" v-bind:key="comment.id">{{ comment.body }}</li>
                </ul>

                <textarea name="comment" id="comment" style="width:100%" rows="4" v-model="newcomment"></textarea>
                <button type="button" @click="saveComment(newcomment)">Enregistrer</button>
            </div>

        </div>
    </div>
  </div>
</template>



<script>

    export default {

        props: ['student', 'enrollment', 'results', 'result', 'stored_comments'],
        
        data () {
            return {
                newcomment: null,
                course_result: this.result,
                comments: this.stored_comments,
            }
        },

        mounted() {

        },

        methods: {

            saveComment()
            {
                axios
                    .post('resultcomment/', {
                        enrollment: this.enrollment.id,
                        comment: this.newcomment
                    })
                    .then(response => {
                        this.comments.push(response.data);
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
                
            },

            saveResult(result)
            {
                axios
                    .post('result/', {
                        result: result.id,
                        student: this.student.id,
                        enrollment: this.enrollment.id
                    })
                    .then(response => {
                        this.course_result = response.data;
                        this.comments = []; // todo get existing comments if any
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            }
        }
    }
    
</script>
