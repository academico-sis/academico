<template>
    <!-- AF Loja specific feature -->
    <div class="card">

        <div class="card-header">Catégorie de prix pour cette inscription</div>
        <div class="card-body">

            <div class="form-group">
                <div class="btn-group-vertical btn-group-justified" role="group">
                    <button
                        v-for="(price, index) in pricecategories"
                        :key="index"
                        class="btn btn-secondary"
                        :class="{ 'btn-primary': selectedpricecategory === index }"
                        @click="changepricecategory(index, price)"
                    >
                        {{ index }} :
                        <span v-if="currencyposition === 'before'">{{ currency }} </span>
                        {{ price }}
                        <span v-if="currencyposition === 'after'">{{ currency }} </span>
                    </button>
                </div>

            </div>

        </div>
    </div>
</template>

<script>
import {EventBus} from "../eventBus";

export default {
    props: [
        "pricecategories",
        "studentpricecategory",
        "currency",
        "currencyposition",
    ],

    data() {
        return {
            selectedpricecategory: this.studentpricecategory,
        }
    },

    methods: {
        changepricecategory(index) {
            this.selectedpricecategory = index
            EventBus.$emit('setEnrollmentPrice', this.pricecategories[index]);
        },
    },
    mounted() {
        this.changepricecategory(this.selectedpricecategory, this.pricecategories[this.selectedpricecategory])
    }
}
</script>

<style scoped>

</style>
