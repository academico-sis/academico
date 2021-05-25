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
                            <div v-if="editable">
                                <div class="input-group" v-if="currencyposition === 'before'">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ currency }}</span>
                                    </div>
                                    <input v-if="editable" step="0.01" class="form-control" type="number" v-model="product.price" />
                                </div>

                                <div class="input-group" v-else>
                                    <input v-if="editable" step="0.01" class="form-control" type="number" v-model="product.price" />
                                    <div v-if="currencyposition === 'after'" class="input-group-append">
                                        <span class="input-group-text">{{ currency }}</span>
                                    </div>

                                </div>
                                <button class="btn btn-success" @click="editable = false">{{ $t('Save') }}</button>
                            </div>
                            <div v-else>
                                <span v-if="currencyposition === 'before'">{{ currency }} </span>
                                {{ product.price }}
                                <span v-if="currencyposition === 'after'">{{ currency }} </span>

                                <button class="btn btn-seconday btn-xs" @click="editable=true"><i class="la la-pencil"></i></button>

                                <span v-if="product.type === 'enrollment' && discount(product.price) > 0" class="badge badge-info"> - <span v-if="currencyposition === 'before'">{{ currency }} </span> {{ discount(product.price) }} <span v-if="currencyposition === 'after'">{{ currency }} </span></span>
                            </div>

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
    data() {
        return {
            editable: false,
        }
    },
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
