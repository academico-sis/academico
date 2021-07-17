<template>
    <div class="card card-solid card-primary">
        <div class="card-header">
            {{ $t("Payment method") }}
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ $t("Payment method") }}</th>
                    <th>{{ $t("Amount received") }}</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="payment in payments" :key="payment.id">
                    <td>
                        <select v-model="payment.method" class="form-control" name="method">
                            <option v-for="paymentmethod in availablepaymentmethods" :key="paymentmethod.id" :value="paymentmethod.code">{{paymentmethod.name}}</option>
                        </select>
                    </td>

                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend" v-if="currencyposition === 'before'">
                                <span class="input-group-text">{{ currency }}</span>
                            </div>
                            <input v-model="payment.value" type="number" step="0.01" class="form-control" />
                            <div class="input-group-append" v-if="currencyposition === 'after'">
                                <span class="input-group-text">{{ currency }}</span>
                            </div>
                        </div>
                    </td>

                    <td>
                        <button class="btn btn-sm btn-ghost-danger" @click="removePayment(payment)">
                            <i class="la la-times"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button id="dropdownMenuButton" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ $t("Add") }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a v-for="paymentmethod in availablepaymentmethods" :key="paymentmethod.id" class="dropdown-item" href="#" @click="addPayment(paymentmethod.code)">
                                        {{ paymentmethod.name }}
                                    </a>
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
    name: "CartPaymentComponent",
    props: ['currency', 'currencyposition', 'shoppingCartTotal', 'availablepaymentmethods', 'totalPrice'],
    data() {
        return {
            paymentsCount: 1,
            firstPaymentDate: new Date().toISOString().substr(0, 10),
            paymentsDefaultValue: null,
            payments: [],
        };
    },
    mounted() {
        this.addPayment(this.totalPrice)
    },
    methods: {
        addPayment(value) {
            if (this.payments.length > 0) {
                let previousPaymentDate = new Date(this.payments[this.payments.length - 1].date);
                var nextPaymentDate = new Date(previousPaymentDate.setMonth(previousPaymentDate.getMonth()+1));
            }
            else {
                var nextPaymentDate = new Date(this.firstPaymentDate);
            }

            if (value === undefined) {
                value = this.totalPrice;
            }

            let payment = {
                value,
                date: nextPaymentDate.toISOString().substr(0, 10),
            };

            this.payments.push(payment);
        },
        removePayment(payment) {
            var index = this.payments.indexOf(payment);
            if (index !== -1) this.payments.splice(index, 1);
        },
    },
    watch: {
        payments: function () {
            this.paymentsCount = this.payments.length;
            EventBus.$emit("paymentsUpated", this.payments);
        },
    },
}
</script>

<style scoped>
.dropdown-menu {
    max-height: 400px;
    overflow-y: auto;
}
</style>
