<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                {{ $t("Skills") }}
            </div>

            <div class="card-body">
                <table id="skillsTable" class="table table-striped" v-for="category in skills" :key="category[0].skill_type_id">
                    <thead>
                    <tr>
                        <th>{{ category[0].skill_type_name }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="skill in category" :key="skill.id">
                            <td style="width: 50%;">{{ skill.name }}</td>

                            <td>
                                <div
                                    class="btn-group btn-group-justified"
                                    role="group"
                                    aria-label=""
                                >
                                    <!-- todo get styling from the model -->
                                    <div
                                        v-for="skillScale in skillScales"
                                        :key="skillScale.id"
                                        class="btn-group"
                                        role="group"
                                    >
                                        <button
                                            class="btn"
                                            :class="(skill.status === skillScale.id) ? 'btn-'+skillScale.classes : 'btn-secondary'"
                                            @click="
                                                saveSkillStatus(
                                                    skill,
                                                    skillScale.id
                                                )
                                            "
                                        >
                                            {{ skillScale.scale_name }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["saved_skills", "enrollment", "skill_scales", "route"],

    data() {
        return {
            skills: this.saved_skills,
            skillScales: this.skill_scales,
        };
    },

    mounted() {},

    methods: {
        saveSkillStatus(skill, status) {
            axios
                .post(this.route, {
                    skill: skill.id,
                    status,
                    enrollment_id: this.enrollment.id,
                })
                .then(response => {
                    skill.status = response.data;
                })
                .catch(e => this.errors.push(e));
        },
    },
};
</script>
