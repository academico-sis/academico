<template>
    <table id="skillsTable" class="table table-striped">
        <thead>
            <td>{{ $t("front.Skill Type") }}</td>
            <td>{{ $t("front.Level") }}</td>
            <td>{{ $t("front.Skill") }}</td>
        </thead>
        <tbody>
            <tr v-for="skill in skills" :key="skill.id">
                <td>{{ skill.skill_type.shortname }}</td>
                <td>{{ skill.level.name }}</td>
                <td>{{ skill.name }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ["course"],
    data() {
        return {
            skills: [],
        };
    },

    mounted() {
        this.getSkills();
    },

    methods: {
        getSkills() {
            axios
                .get("/course/" + this.course + "/getskills")
                .then((response) => {
                    this.skills = response.data;
                });
        },

        update() {
            this.skills.map((skill, index) => {
                skill.order = index + 1;
                axios.patch("/course/" + this.course + "/setskills", {
                    skills: this.skills,
                });
            });
        },
    },
};
</script>
