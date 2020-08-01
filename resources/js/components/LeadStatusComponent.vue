<template>
    <div class="card">
        <div class="card-header">
            {{ $t("Lead Status") }}
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
                    :disabled="isLoading || student.is_enrolled"
                    @click="saveStatus(leadtype.id)"
                >
                    {{ leadtype.translated_name }}
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
            lang: document.documentElement.lang.substr(0, 2),
            status: this.student.lead_status,
            isLoading: false,
        };
    },

    mounted() {},

    methods: {
        saveStatus(status) {
            this.isLoading = true;
            axios
                .post(this.route, {
                    student: this.student.id,
                    status: status,
                })
                .then((response) => {
                    this.status = response.data;
                    this.isLoading = false;
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },
    },
};
</script>
