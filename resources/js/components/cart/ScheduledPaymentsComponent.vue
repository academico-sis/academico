<template>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-solid card-primary">
                <div class="card-body">

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label" for="firstPaymentDate">{{ $t('First payment date') }}</label>
                        <div class="col-sm-8">
                            <input id="firstPaymentDate" v-model="firstPaymentDate" class="form-control" type="date">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label" for="paymentsCount">{{ $t('Number of payments') }}</label>
                        <div class="col-sm-8">
                            <input id="paymentsCount" v-model="paymentsCount" class="form-control" type="number" step="1" min="1" decimal="0">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label" for="firstPaymentDate">{{ $t('Value') }}</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend" v-if="currencyposition === 'before'">
                                    <span class="input-group-text">{{ currency }}</span>
                                </div>
                                <input v-model="paymentsDefaultValue" type="number" step="0.01" class="form-control" />
                                <div class="input-group-append" v-if="currencyposition === 'after'">
                                    <span class="input-group-text">{{ currency }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-solid card-primary">
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ $t("Date") }}</th>
                            <th>{{ $t("Value") }}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="payment in payments" :key="payment.id">
                            <td>
                                <div class="form-group">
                                    <input class="input-group-text" type="date" v-model="payment.date">
                                </div>
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
                                <button @click="addPayment()" class="btn btn-secondary" type="button">
                                    {{ $t("Add") }}
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <cart-total-price-component :value="totalValue" :currency="currency" :currencyposition="currencyposition"></cart-total-price-component>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <button :disabled="loading" class="btn btn-success" @click="save()">
                        <i class="la la-check"></i>{{ $t("Save") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['currency', 'currencyposition', 'enrollment'],

    data() {
        return {
            loading: false,
            paymentsCount: 1,
            firstPaymentDate: new Date().toISOString().substr(0, 10),
            paymentsDefaultValue: null,
            payments: [],
        };
    },

    mounted() {
        this.addPayment(this.enrollment.price)
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

        save() {
            this.loading = true;
            axios.post(`/enrollment/${this.enrollment.id}/scheduled-payments`, {
                enrollmentId: this.enrollment.id,
                payments: this.payments,
            })
                .then(response => {
                    new Noty({
                        title: this.$t('Success'),
                        text: this.$t("Your changes were successful"),
                        type: "success",
                    }).show();
                    window.location.href = `/enrollment/${this.enrollment.id}/show`;
                })
                .catch(errors => {
                    this.loading = false;
                    new Noty({
                        title: this.$t("Error"),
                        text: this.$t("Your changes could not be saved"),
                        type: "error",
                    }).show();
                });
        }
    },

    computed: {
        totalValue() {
            let total = 0;
            this.payments.forEach(payment => {
                total += payment.value * 100;
            })
            return total / 100;
        }
    },

    watch: {

        paymentsCount: function () {
            // empty payments array
            this.payments.length = 0;

            var i = 0;
            while (i < this.paymentsCount) {
                this.addPayment(this.paymentsDefaultValue);
                i++
            }
        },

        paymentsDefaultValue: function () {
            // empty payments array
            this.payments.length = 0;

            var i = 0;
            while (i < this.paymentsCount) {
                this.addPayment(this.paymentsDefaultValue);
                i++
            }
        },
    },

}
</script>

<style scoped>

</style>
