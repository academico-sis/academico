<template>
<div>
    <div class="form-group">
        <a data-toggle="collapse" href="#collapseScholarships" aria-expanded="false" aria-controls="collapseScholarships" class="btn btn-sm btn-warning">{{ $t('Add scholarship') }}</a>
    </div>

    <div class="collapse" id="collapseScholarships">
        <div class="input-group">
            <select v-model="selectedScholarship" required name="scholarship" id="scholarship" class="form-control">
                <option v-for=" scholarship in scholarships" v-bind:key="scholarship.id" :value="scholarship.id">{{ scholarship.name }}</option>
            </select>
            <span class="input-group-append">
                <button :disabled="this.selectedScholarship == null || this.loading" @click="addScholarship()" class="btn btn-sm btn-primary" type="submit"><i class="fa fa-dot-circle-o"></i> {{ $t('Save') }}</button>
            </span>
        </div>
    </div>

  </div>
</template>

<script>
export default {
    props: ['scholarships', 'enrollment_id'],
    data() {
        return {
            selectedScholarship: null,
            loading: false,
        }
    },
    methods: {
        addScholarship()
        {
            this.loading = true,
            axios
            .post(`/enrollment/${this.enrollment_id}/scholarships/add`, {
                scholarship_id: this.selectedScholarship
            })
            .then(function (response) {
                new Noty({
                        title: this.$t("Operation successful"),
                        text: this.$t("The scholarship has been successfully added"),
                        type: "success",
                    }).show();
                window.location.reload()
            })
            .catch(function (error) {
                console.log(error);
                this.loading = false
            });
        }
    },
    mounted() { }
}
</script>

<style>

</style>
