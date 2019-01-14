<template>
<div>

    <h2>Course result</h2>
    <label>{{ course_result.result_name.name }}</label>
    
    <button @click="editResult()"
    data-toggle="modal"
    data-target="#ResultModal"
    >
        Edit
    </button>

<h2>Comments</h2>
    <ul>
        <li v-for="comment in comments" v-bind:key="comment.id">{{ comment.body }}</li>
    </ul>




<!-- Modal -->
<div class="modal fade" id="ResultModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">Résultat pour le cours <span id="course_name"></span></h4>
            </div>

            <div class="modal-body">
              <h3>Apprenant(e) : <span id="student"></span></h3>

<!--               <input id="matricula_id" name ="matricula_id" type="hidden" value=""/>
              <input id="course_id" name ="course_id" type="hidden" value=""/> -->
              
              <div class="form-group">
              <select id="result" name="result" required v-model="result">
              <option value="">Choisir une décision</option>
                <option v-for="result_option in results"
                  :value="result_option.id"
                  v-bind:key="result_option.id">
                  {{ result_option.name }}
                  </option>
              </select>
              </div>

              <div class="form-group">
              <textarea id="comment"
                name="comment"
                cols="50"
                lines="5"
                required
                v-model="comment">
              </textarea>
              </div>

          </div>
          
        <div class="modal-footer">
            <button type="button" class="btn btn-success" @click="saveResult()">Enregistrer</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
        </div>

    </div>
    </div>


</div>
</template>



<script>

    export default {

        props: ['student', 'course', 'results', 'course_result', 'comments'],
        
        data () {
            return {
                skills: this.saved_skills,
                result: null,
                comment: null,
            }
        },

        mounted() {

        },

        methods: {
            saveResult()
            {
                axios
                    .post('/result/', {
                        result: this.result,
                        student: this.student.id,
                        enrollment: this.course_result.enrollment_id,
                        comment: this.comment,
                    })
                    .then(response => {
                        document.location.reload(true); // TODO improve this: do not reload the whole page
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            }
        }
    }
    
</script>
