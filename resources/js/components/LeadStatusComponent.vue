<template>
    <div class="card">
        <div class="card-header">
            {{ $t("front.Lead Status") }}
        </div>

        <div class="card-body">
            <div
                v-for="leadtype in leadtypes"
                :key="leadtype.id"
                class="btn-group"
                role="group"
            >
                <button
                    class="btn btn-sm btn-secondary"
                    :class="{ 'btn-info': status && status == leadtype.id }"
                    @click="saveStatus(leadtype.id)"
                >
                    {{ leadtype.name }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["student", "route", "leadtypes"],

    data() {
        return {
            status: this.student.lead_type_id,
        };
    },

    mounted() {},

    methods: {
        saveStatus(status) {
            console.log("click");
            axios
                .post(this.route, {
                    student: this.student.id,
                    status: status,
                })
                .then((response) => {
                    this.status = response.data;
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },
    },
};
</script>
