<template>
    <div>
        <div v-if="editable">
            <input v-if="editable" class="input" type="text" v-model="price" />
            <button v-if="editable" class="btn btn-success" @click="savePrice">{{ $t('Save') }}</button>
        </div>
        <div v-else>
            {{ $t('Price') }}: ${{ price }}
            <a href="#" @click="editable = true"> {{ $t('Edit') }} </a>
        </div>

    </div>
</template>

<script>
export default {
    props: [
        "enrollment"
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
            })
            .catch(error => { console.error (error) })
        }
    },
};
</script>
