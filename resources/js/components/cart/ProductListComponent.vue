<template>
    <div class="card">
        <div class="card-header">
            {{ $t("Products") }}
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ $t("Product") }}</th>
                    <th>{{ $t("Price") }}</th>
                    <th>{{ $t("Actions") }}</th>
                </tr>
                </thead>
                <tbody>

                    <tr v-for="(product, index) in products" :key="index">
                        <td>{{ product.name }}</td>
                        <td>
                            {{ product.price_with_currency }}
                            <span v-if="product.type === 'enrollment' && discount(product.price) > 0" class="badge badge-info"> - <span v-if="currencyposition === 'before'">{{ currency }} </span> {{ discount(product.price) }} <span v-if="currencyposition === 'after'">{{ currency }} </span></span>
                        </td>
                        <td>
                            <button class="btn btn-xs btn-danger" @click="removeFromCart(index)">
                                <i class="la la-trash"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import {EventBus} from "../eventBus";

export default {
    props: ['products', 'totalDiscount', 'currency', 'currencyposition'],
    methods: {
        removeFromCart(index) {
            EventBus.$emit("removeFromCart", index);
        },
        discount(price) {
            return price * (this.totalDiscount / 100);
        },
    }

}
</script>

<style scoped>

</style>
