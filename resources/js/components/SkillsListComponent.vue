<template>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{$t('Available skills')}}
                <div class="card-header-actions">
                    <a class="btn btn-sm btn-primary" href="#" @click="addSkill('all')">
                        {{ $t('Add all') }}
                    </a>
                </div>
            </div>

            <div class="card-body" v-if="loading">{{$t('Loading...')}}</div>
            <div class="card-body" v-else>
                <table class="table table-responsive-sm table-sm" v-for="category in availableskills" :key="category[0].skillType.id">
                    <thead>
                        <tr>
                            <th>{{ category[0].skillType.name }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="skill in category" :key="skill.id">
                            <td>{{ skill.name }}</td>
                            <th>
                                <button type="button" class="btn btn-sm btn-square btn-primary" :disabled="loading" @click="addSkill(skill.id)">
                                    <i class="la la-plus"></i>
                                </button>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{$t('Course skills')}}
                <div class="card-header-actions">
                    <a class="btn btn-sm btn-danger" href="#" @click="removeSkill('all')">
                        {{ $t('Remove all') }}
                    </a>
                </div>
            </div>
            <div class="card-body" v-if="loading">{{$t('Loading...')}}</div>
            <div class="card-body" v-else>
                <table class="table table-responsive-sm table-sm" v-for="(category, index) in courseskills" :key="category[0].skillType.id">
                    <thead>
                        <tr>
                            <th>{{ category[0].skillType.name }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <draggable v-model="courseskills[index]" :group="{ name: index, pull: false, put: false }" @update="saveOrder(index)" @start="drag=true" @end="drag=false">
                    <tr v-for="skill in category" :key="skill.id">
                        <td>{{skill.name}}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-square btn-danger" :disabled="loading" @click="removeSkill(skill.id)">
                                <i class="la la-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </draggable>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>
</template>

<script>
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    props: ["course"],
    data() {
        return {
            loading: false,
            availableskills: [],
            courseskills: [],
        };
    },

    mounted() {
        this.getCourseSkills();
        this.getAvailableSkills();
    },

    methods: {
        getCourseSkills() {
            this.loading = true;
            axios
                .get(`/course/${this.course}/getcourseskills`)
                .then(response => {
                    this.courseskills = response.data;
                    this.loading = false;
                });
        },
        getAvailableSkills() {
            this.loading = true;
            axios
                .get(`/course/${this.course}/getavailableskills`)
                .then(response => {
                    this.availableskills = response.data;
                    this.loading = false;
                });
        },

        addSkill(skill) {
            this.loading = true;
            axios
                .post(`/course/${this.course}/skills/add`, {
                    skill_id: skill
                })
                .then(response => {
                    this.courseskills = response.data;
                    this.getAvailableSkills()
                });
        },

        removeSkill(skill) {
            this.loading = true;
            axios
                .post(`/course/${this.course}/skills/remove`, {
                    skill_id: skill
                })
                .then(response => {
                    this.courseskills = response.data;
                    this.getAvailableSkills()
                });
        },

        saveOrder(index)
        {
            this.courseskills[index].map((skill, index) => {
                skill.order = index + 1;
            })

            axios.put(`/course/${this.course}/setskills`, {
                skills: this.courseskills[index]
            })
        }
    },
};
</script>
