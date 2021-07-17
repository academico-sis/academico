<template>
    <table class="table">
        <thead>
        <tr>
            <th>{{ $t("Payment") }}</th>
            <th>{{ $t("Date") }}</th>
            <th>{{ $t("Value") }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="payment in payments" :key="payment.id">
            <td v-if="editable">
                <select v-model="payment.payment_method" class="form-control" name="method">
                    <option v-for="paymentmethod in availablepaymentmethods" :key="paymentmethod.id" :value="paymentmethod.code" :selected="paymentmethod.code === payment.payment_method">{{paymentmethod.name}}</option>
                </select>
            </td>

            <td v-else>{{ payment.payment_method }}</td>

            <td v-if="editable">
                <input type="date" class="form-control" v-model="payment.date">
            </td>
            <td v-else>
                {{ payment.date_for_humans }}
            </td>

            <td v-if="editable">
                <div class="input-group" v-if="currencyposition === 'before'">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ currency }}</span>
                    </div>
                    <input v-model="payment.value" type="number" step="0.01" class="form-control" />
                </div>
                <div class="input-group" v-else>
                    <input v-model="payment.value" type="number" step="0.01" class="form-control" />
                    <div class="input-group-append">
                        <span class="input-group-text">{{ currency }}</span>
                    </div>
                </div>
            </td>

            <td v-else>
                <span v-if="currencyposition === 'before'">{{ currency }} </span>
                {{ payment.value }}
                <span v-if="currencyposition === 'after'"> {{ currency }}</span>
            </td>

            <td>
                <button v-if="editable" class="btn btn-sm btn-ghost-danger" @click="removePayment(payment)">
                    <i class="la la-times"></i>
                </button>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>{{ $t("Total received amount") }}</td>
            <td>
                <span v-if="currencyposition === 'before'">{{ currency }} </span>
                {{ paidTotal }}
                <span v-if="currencyposition === 'after'">{{ currency }} </span>
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr v-if="editable">
            <td>
                <div class="btn-group">
                    <button class="btn btn-secondary" type="button" @click="addPayment()">
                    {{ $t("Add") }}
                    </button>
                </div>

                <button class="btn btn-primary" @click="savePayments()" type="button" aria-haspopup="true" aria-expanded="false">
                    {{ $t("Save") }}
                </button>
            </td>
        </tr>

        </tbody>
    </table>
</template>

<script>
export default {
    props: [
        "invoice",
        "availablepaymentmethods",
        "editable",
        "currency",
        "currencyposition",
    ],

    data() {
        return {
            payments: this.invoice.payments,
            errors: [],
        };
    },
    computed: {
        paidTotal() {
            let total = 0;
            if (this.payments) {
                this.payments.forEach(payment => {
                    total += parseFloat(payment.value);
                });
            }
            return total;
        },
    },

    mounted() {

    },

    methods: {
        addPayment() {
            let payment = {
                value: 0,
            };

            this.payments.push(payment);
        },

        removePayment(payment) {
            var index = this.payments.indexOf(payment);
            if (index !== -1) this.payments.splice(index, 1);
        },

        savePayments() {
            this.loading = true;

            axios
                .post(`/invoice/${this.invoice.id}/payments`, {
                    payments: this.payments,
                })
                .then(response => {
                    // handle success
                    this.payments = response.data
                    new Noty({
                        title: this.$t("Operation successful"),
                        text: this.$t("The payment has been saved"),
                        type: "success",
                    }).show();
                })
                .catch(e => {
                    this.loading = false;
                    this.errors.push(e);
                    new Noty({
                        title: this.$t("Error"),
                        text: this.$t("The payment couldn't be saved"),
                        type: "error",
                    }).show();
                });
        },
    },
};
</script>
