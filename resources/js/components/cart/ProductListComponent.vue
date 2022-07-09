<template>
    <div class="card">
        <div class="card-header">
            {{ $t("Products") }}
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ $t("Quantity") }}</th>
                    <th>{{ $t("Product") }}</th>
                    <th>{{ $t("Comment") }}</th>
                    <th>{{ $t("Price") }}</th>
                    <th v-if="editsallowed">{{ $t("Actions") }}</th>
                </tr>
                </thead>
                <tbody>

                    <tr v-for="(product, index) in products" :key="index">
                        <td>
                            <div v-if="editable && product.type !== 'enrollment'" class="form-group">
                                <input class="form-control" type="number" min="1" step="1" v-model="product.quantity" />
                            </div>

                            <div v-else-if="product.type !== 'enrollment'">
                                {{ product.quantity }}
                                <button v-if="editsallowed" class="btn btn-seconday btn-xs" @click="editable=true"><i class="la la-pencil"></i></button>
                            </div>
                        </td>

                        <td>
                            <div v-if="editable" class="form-group">
                                <input class="form-control" type="text" v-model="product.name" />
                            </div>
                            <div v-else>
                                {{ product.name }}
                                <button v-if="editsallowed" class="btn btn-seconday btn-xs" @click="editable=true"><i class="la la-pencil"></i></button>
                            </div>
                        </td>

                        <td>
                          <div v-if="editable" class="form-group">
                            <input class="form-control" type="text" v-model="product.comment" />
                          </div>
                          <div v-else>
                            {{ product.comment }}
                            <button v-if="editsallowed" class="btn btn-seconday btn-xs" @click="editable=true"><i class="la la-pencil"></i></button>
                          </div>
                        </td>

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

                                <button v-if="editsallowed" class="btn btn-seconday btn-xs" @click="editable=true"><i class="la la-pencil"></i></button>

                                <span v-for="(discount, index) in product.discounts" :key="index">
                                    <span v-if="editsallowed" class="badge badge-info" @click="removeDiscount(product, index)">{{ discount.name }} (- {{ discount.value }}%) (X)</span>
                                    <span v-else class="badge badge-info">{{ discount.name }} (- {{ discount.value }}%)</span>
                                </span>

                                <span v-for="(tax, index) in product.taxes" :key="index">
                                    <span v-if="editsallowed" class="badge badge-secondary" @click="removeTax(product, index)">{{ tax.name }} ({{ tax.value }}%) (X)</span>
                                    <span v-else class="badge badge-secondary">{{ tax.name }} ({{ tax.value }}%)</span>
                                </span>
                            </div>

                        </td>
                        <td v-if="editsallowed">
                            <div class="btn-group">

                                <button v-if="products.length > 1" class="btn btn-sm btn-danger" @click="removeFromCart(index)">
                                    <i class="la la-trash"></i>
                                </button>

                                <div class="dropdown" v-if="! invoicesContainEnrollmentsOnly">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        {{ $t("Add discount") }}
                                    </button>

                                    <div class="dropdown-menu">
                                        <button v-for="availableDiscount in availablediscounts" :key="availableDiscount.id" class="dropdown-item" @click="addDiscount(product, availableDiscount)">
                                            {{ availableDiscount.name }}
                                        </button>
                                    </div>
                                </div>

                                <div class="dropdown" v-if="! invoicesContainEnrollmentsOnly">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        {{ $t("Add tax") }}
                                    </button>
                                    <div class="dropdown-menu">
                                        <button v-for="availableTax in availabletaxes" :key="availableTax.id" class="dropdown-item" @click="addTax(product, availableTax)">
                                            {{ availableTax.name }}
                                        </button>
                                    </div>
                                </div>

                            </div>
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
    props: ['products', 'currency', 'currencyposition', 'availablediscounts', 'availabletaxes', 'editsallowed', 'invoicesContainEnrollmentsOnly'],
    data() {
        return {
            editable: false,
        }
    },
    methods: {
        removeFromCart(index) {
            EventBus.$emit("removeFromCart", index);
        },

        addDiscount(product, discount) {
            var discounts = product.discounts || [];
            discounts.push(discount);
            product.discounts = discounts;

            EventBus.$emit("productsUpated");
        },

        removeDiscount(product, index) {
            product.discounts.splice(index, 1);
            EventBus.$emit("productsUpated");
        },

        addTax(product, tax) {
            var taxes = product.taxes || [];
            taxes.push(tax);
            product.taxes = taxes;

            EventBus.$emit("productsUpated");
        },

        removeTax(product, index) {
            product.taxes.splice(index, 1);
            EventBus.$emit("productsUpated");
        },
    },
    created() {
        EventBus.$on("setEnrollmentPrice", (price) => {
            this.products.forEach((product) => {
                if (product.type === 'enrollment') {
                    product.price = price
                }
            });
        });
    },

}
</script>

<style scoped>
.dropdown-menu {
    max-height: 400px;
    overflow-y: auto;
}
</style>
