<template>
    <div>
        <div v-if="editable">
            <div class="input-group" v-if="this.currencyposition === 'before'">
                <div class="input-group-append">
                    <span class="input-group-text">{{ this.currency }}</span>
                </div>
                <input v-if="editable" step="0.01" class="form-control" type="number" v-model="price" />
            </div>

            <div class="input-group" v-else>
                <input v-if="editable" step="0.01" class="form-control" type="number" v-model="price" />
                <div v-if="this.currencyposition === 'after'" class="input-group-append">
                    <span class="input-group-text">{{ this.currency }}</span>
                </div>

                <button v-if="editable" class="btn btn-success" @click="savePrice">{{ $t('Save') }}</button>
            </div>
        </div>
        <div v-else>
            {{ $t('Price') }}:
            <span v-if="this.currencyposition === 'before'">{{ this.currency }} </span>
            {{ price }}
            <span v-if="this.currencyposition === 'after'">{{ this.currency }} </span>
            <a v-if="writeaccess" href="#" @click="editable = true"> {{ $t('Edit') }} </a>
        </div>

    </div>
</template>

<script>
export default {
    props: [
        "enrollment",
        "currency",
        "currencyposition",
        "writeaccess",
    ],

    data() {
        return {
            editable: false,
            price: this.enrollment.price,
        };
    },
    computed: {

    },

    mounted() {

    },

    methods: {
        savePrice() {
            axios.put(`/enrollment/${this.enrollment.id}/price`, {
                price: this.price,
            })
            .then(response => {
                this.price = response.data.total_price;
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
