<template>
<div>

 <table class="table table-striped" id="skillsTable">
    <thead>
        <th>Skill</th>
        <th></th>
        <th></th>
        <th></th>
    </thead>
    <tbody>
        <tr v-for="skill in skills" v-bind:key="skill.id">
            <td style="width: 50%">{{ skill.name }} {{skill.id}}</td>
            
            <td>
            <div class="btn-group btn-group-justified" role="group" aria-label="">

            <div class="btn-group" role="group">
                <button class="btn btn-secondary"
                v-bind:class="{ 'btn-success': skill.status == 3 }"
                v-on:click="saveSkillStatus(skill.id, 3)">
                    AQ <i class="fa fa-check"></i>
                </button>
            </div>
            
            <div class="btn-group" role="group">
                <button class="btn btn-secondary"
                    v-bind:class="{ 'btn-warning': skill.status == 2 }"
                    v-on:click="saveSkillStatus(skill.id, 2)">
                    EC <i class="fa fa-asterisk"></i>
                </button>
            </div>
            
            <div class="btn-group" role="group">
                <button class="btn btn-secondary"
                v-bind:class="{ 'btn-danger': skill.status == 1 }"
                v-on:click="saveSkillStatus(skill.id, 1)">
                    NA <i class="fa fa-times"></i>
                </button>
            </div>
            </div>
            </td>
        </tr>
    </tbody>
</table>

</div>
</template>



<script>

    export default {

        props: ['saved_skills', 'student', 'course'],
        
        data () {
            return {
                skills: this.saved_skills
            }
        },

        mounted() {

        },

        methods: {
            saveSkillStatus(skill, status)
            {
                axios
                    .post('/skillsevaluation/', {
                        skill,
                        status,
                        student: this.student.id,
                        course: this.course.id,
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
