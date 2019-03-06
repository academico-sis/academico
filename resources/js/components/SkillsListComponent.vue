<template>

<table class="table table-striped" id="skillsTable">
    <thead>
        <td>Skill Type</td>
        <td>Level</td>
        <td>Skill</td>
        <td>Order</td>
    </thead>
    <draggable :list="skills" :options="{}" :element="'tbody'" @change="update">
        <tr v-for="skill in skills" :key="skill.id">
            <td>{{ skill.skill_type.shortname }}</td>
            <td>{{ skill.level.name }}</td>
            <td>{{ skill.name }}</td>
            <td>{{ skill.order }}</td>
        </tr>
    </draggable>
</table>

</template>

<script>

import draggable from 'vuedraggable'

    export default {
        
        props: ['course'],
        data () {
            return {
                skills: []
            }
        },

        mounted() {
            this.getSkills();
        },

        methods: {
            getSkills() {
                axios.get('/course/' + this.course + '/getskills')
                .then(response => {
                    this.skills = response.data
                    })
                {
                }
            },

            update() {
                this.skills.map((skill, index) => {
                    skill.order = index + 1
                    axios.patch('/course/' + this.course + '/setskills', {
                        skills: this.skills
                    })
                })
            }
 
        }
    }
</script>
