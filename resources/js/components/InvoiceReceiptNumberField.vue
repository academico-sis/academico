<template>
    <div>
        <div v-if="editable">
            <input v-if="editable" class="input" type="text" v-model="number" />
            <button v-if="editable" class="btn btn-success" @click="save()">{{ $t('Save') }}</button>
        </div>
        <div v-else>
            {{ $t('Receipt Number') }}: {{ this.number }}
            <a href="#" @click="editable = true"> {{ $t('Edit') }} </a>
        </div>

    </div>
</template>

<script>
export default {
    props: [
        "invoice"
    ],

    data() {
        return {
            editable: false,
            number: this.invoice.receipt_number,
        };
    },
    computed: {

    },

    mounted() {

    },

    methods: {
        save() {
            axios.post(`/invoice/${this.invoice.id}/receipt`, {
                number: this.number,
            })
            .then(response => {
                this.number = response.data.receipt_number;
                this.editable = false;
                new Noty({
                    title: this.$t("Operation successful"),
                    text: this.$t('Your changes were successful'),
                    type: "success",
                }).show();
            })
            .catch(error => {
                new Noty({
                    type: "error",
                    text: this.$t('Your changes could not be saved'),
                }).show();
            })
        }
    },
};
</script>
