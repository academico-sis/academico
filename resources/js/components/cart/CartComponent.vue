<template>
    <div>
        <cart-breadcrumbs-component :step="step"></cart-breadcrumbs-component>


        <div v-if="step === 1" class="row">

            <div class="col col-md-8">

                <cart-product-list-component :products="products" :totalDiscount="totalDiscount" :currency="currency" :currencyposition="currencyposition"></cart-product-list-component>

                <cart-total-price-component :value="shoppingCartTotal" :currency="currency" :currencyposition="currencyposition"></cart-total-price-component>

                <!-- Button to move forward-->
                <div class="card">
                    <div class="card-body text-center">
                    <button :disabled="products.length === 0" class="btn btn-success" @click="step = 2">
                        <i class="la la-check"></i>{{ $t("Confirm") }}
                    </button>
                    </div>
                </div>

            </div>

            <!-- Add more products or discounts -->
            <div class="col col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{ $t("Add products") }}
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <div class="dropdown">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    {{ $t("Books") }}
                                </button>
                                <div class="dropdown-menu">
                                    <button v-for="availableBook in this.availablebooks" :key="availableBook.id" class="dropdown-item" @click="addBook(availableBook)">
                                        {{ availableBook.name }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="dropdown">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span> {{ $t("Fees") }}
                                </button>
                                <div class="dropdown-menu">
                                    <button v-for="availableFee in this.availablefees" :key="availableFee.id" class="dropdown-item" @click="addFee(availableFee)">
                                        {{ availableFee.name }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        {{ $t("Discounts") }}
                    </div>

                    <div class="card-body">
                        <ul>
                            <li v-for="(discount, index) in discounts" :key="discount.id">
                                {{ discount.name }} ({{ discount.value }}%)
                                <button class="btn btn-xs btn-warning" @click="removeDiscount(index)">
                                    <i class="la la-times"></i>
                                </button>
                            </li>
                        </ul>

                        <div class="form-group">
                            <div class="dropdown">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    {{ $t("Add discount") }}
                                </button>
                                <div class="dropdown-menu">
                                    <button v-for="availableDiscount in this.availablediscounts" :key="availableDiscount.id" class="dropdown-item" @click="addDiscount(availableDiscount)">
                                        {{ availableDiscount.name }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="step === 2" class="row">

            <div class="col-md-4">
                <client-data-component :clients="clients"></client-data-component>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ $t("Invoice Data") }}
                        <div class="card-header-actions">
                            <button :disabled="! checkForm()" class="btn btn-success" @click="confirmInvoiceData()">
                                <i class="la la-check"></i>{{ $t("Select") }}
                            </button>
                            <span v-if ="! checkForm()">{{ $t('You need to fill out all fields to continue') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="clientname">{{ $t("Client name") }}</label>
                            <input id="clientname" v-model="clientname" :class="{'is-invalid': !clientname || clientname == ''}" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientphone">{{ $t("Client Phone Number") }}</label>
                            <input id="clientphone" v-model="clientphone" :class="{'is-invalid': !clientphone || clientphone == ''}" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientaddress">{{ $t("Client address") }}</label>
                            <input id="clientaddress" v-model="clientaddress" :class="{'is-invalid': !clientaddress || clientaddress == ''}" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientidnumber">{{ $t("Client ID Number") }}</label>
                            <input v-model="clientidnumber" id="clientidnumber" :class="{'is-invalid': !clientidnumber || clientidnumber == ''}" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientemail">{{ $t("Client email") }}</label>
                            <input v-model="clientemail" id="clientemail" :class="{'is-invalid': !clientemail || clientemail == ''}" required type="text" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="step === 3" class="row">
            <div class="col col-md-6">
                <cart-product-list-component :products="products" :totalDiscount="totalDiscount" :currency="currency" :currencyposition="currencyposition"></cart-product-list-component>
            </div>
            <div class="col-md-6">
                <div class="card card-solid card-primary">
                    <div class="card-header">
                        {{ $t("Invoice Data") }}
                    </div>
                    <div class="card-body">
                        <div><strong>{{ $t('name') }}:</strong> {{ clientname }}</div>
                        <div v-if="clientphone"><strong>{{ $t('Phone') }}:</strong> {{ clientphone }}</div>
                        <div v-if="clientaddress"><strong>{{ $t('Address') }}:</strong> {{ clientaddress }}</div>
                        <div v-if="clientidnumber"><strong>{{ $t('ID Number') }}:</strong> {{ clientidnumber }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <total-price-component :value="shoppingCartTotal" :currency="currency" :currencyposition="currencyposition"></total-price-component>

                <cart-scheduled-payments-component :payments="payments" :currency="currency" :currencyposition="currencyposition"></cart-scheduled-payments-component>
            </div>

            <div class="col-md-12">
                <div class="card card-solid card-primary">
                    <div class="card-body text-center">

                        <div class="form-group">
                            <h4>
                                {{ $t("Total received amount") }}: <span v-if="currencyposition === 'before'">{{ currency }} </span>
                                {{ paidTotal }}
                                <span v-if="currencyposition === 'after'">{{ currency }} </span>
                            </h4>
                        </div>

                        <div class="form-group">
                            <label for="comment" class="form-label">{{ $t("Comment") }}</label>
                            <textarea
                                class="form-control"
                                id="comment"
                                v-model="comment"
                                name="comment"
                                rows="2"
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-lg btn-success" :disabled="loading || payments.length === 0 || ! paidTotal > 0" @click="checkTotal()">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i class="la la-check"></i
                                >{{ $t("Checkout") }}
                            </button>
                        </div>

                        <div v-if="this.accountingenabled" class="form-group" style="display: flex;">
                            <div v-if="this.accountingServiceIsUp">
                                <label class="switch switch-pill switch-success">
                                    <input v-model="sendInvoiceToAccounting" class="switch-input" type="checkbox" /><span class="switch-slider"></span>
                                </label>
                                <span v-if="sendInvoiceToAccounting">{{ $t( 'Send invoice to external accounting system' ) }}</span>
                                <span v-if="!sendInvoiceToAccounting">{{ $t("Mark this enrollment as paid but do not send to accounting system") }}</span>
                            </div>
                            <span v-else class="alert alert-danger">
                                {{ $t("Unable to communicate with Accounting Service. This invoice will NOT be sent automatically to the Accounting system") }}
                                <a href="#" @click="checkAccountingStatus()">{{ $t('Refresh status') }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <cart-invoice-type-component :invoicetypes="invoicetypes" :selectedInvoiceType="selectedInvoiceType"></cart-invoice-type-component>
            </div>
        </div>

        <div v-if="step === 4" class="row">
            <div class="col col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $t("The invoice has been generated") }}
                    </div>

                    <div class="card-body">
                        <p>
                            {{ $t("Enrollment number") }}
                            {{ this.enrollment.id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {EventBus} from "../eventBus";
import TotalPriceComponent from "./TotalPriceComponent";

export default {
    components: {TotalPriceComponent},
    props: [
        "enrollment",
        "availablebooks",
        "availablefees",
        "availablediscounts",
        "availablepaymentmethods",
        "accountingenabled",
        "currency",
        "currencyposition",
        "productslist",
        "clients",
        "invoicetypes",
    ],

    data() {
        return {
            totalPrice: 0,
            errors: [],
            discounts: [],
            step: 1,
            clientname: "",
            clientphone: "",
            clientaddress: "",
            clientemail: "",
            clientidnumber: "",
            payments: [],
            products: this.productslist,
            comment: "",
            sendInvoiceToAccounting: this.accountingenabled,
            accountingServiceIsUp: false,
            loading: false,
            selectedInvoiceType: this.invoicetypes[0].id,
        };
    },
    computed: {
        shoppingCartTotal() {
            let total = 0;

            if (this.products) {
                this.products.forEach(product => {
                    total += parseFloat(product.price);

                    if (product.type === 'enrollment') {
                        total -= this.discount(parseFloat(product.price));
                    }
                });
            }

            return total;
        },

        paidTotal() {
            let total = 0;
            if (this.payments) {
                this.payments.forEach(payment => {
                    total += parseFloat(payment.value);
                });
            }
            return Math.round(total * 100) / 100;
        },

        totalDiscount() {
            let total = 0;
            if (this.discounts) {
                this.discounts.forEach(discount => {
                    total += parseFloat(discount.value);
                });
            }

            return total;
        },
    },

    mounted() {
        this.checkAccountingStatus();
    },

    created() {
        EventBus.$on("updateStep", (value) => {
            this.step = value;
        });

        EventBus.$on("selectClientData", (data) => {
            this.selectInvoiceData(data);
        });

        EventBus.$on("removeFromCart", (index) => {
            this.removeFromCart(index);
        });

        EventBus.$on("paymentsUpated", (payments) => {
            this.payments = payments;
        });

        EventBus.$on("setInvoiceType", (id) => {
            this.selectedInvoiceType = id;
        });
    },

    methods: {
        checkAccountingStatus() {
            axios
                .get("/accountingservice/status")
                .then(
                    response => this.accountingServiceIsUp = response.data
                );
        },

        addBook(book) {
            this.products.push(book);
        },

        addFee(fee) {
            this.products.push(fee);
        },

        removeFromCart(index) {
            this.products.splice(index, 1);
        },

        addDiscount(discount) {
            this.discounts.push(discount);
        },

        removeDiscount(index) {
            this.discounts.splice(index, 1);
        },

        discount(price) {
            return price * (this.totalDiscount / 100);
        },


        selectInvoiceData(contact) {
            this.clientname = contact.name ?? "";
            this.clientphone =
                typeof contact.phone === "undefined"
                    ? ""
                    : contact.phone[0].phone_number;
            this.clientaddress = contact.address ?? "";
            this.clientidnumber = contact.idnumber ?? "";
            this.clientemail = contact.email ?? "";
        },

        checkForm: function (e) {
            if (
                this.clientname &&
                this.clientphone &&
                this.clientaddress &&
                this.clientidnumber &&
                this.clientemail
            ) {
                return true;
            }
        },

        confirmInvoiceData() {
            this.step = 3;
        },


        checkTotal() {
            if (this.paidTotal !== this.shoppingCartTotal) {
                swal({
                    title: this.$t('Warning'),
                    text: this.$t('Total paid amount does not match the invoice total price'),
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: this.$t('Cancel'),
                            value: null,
                            visible: true,
                            className: "bg-secondary",
                            closeModal: true,
                        },
                        delete: {
                            text: this.$t('Continue'),
                            value: true,
                            visible: true,
                            className: "bg-danger",
                        }
                    },
                }).then(value => {
                    if (value) {
                        this.finish();
                    }
                });
            }
            else {
                this.finish();
            }

        },

        finish() {
            this.loading = true;

            axios
                .post("/checkout", {
                    enrollment_id: this.enrollment? this.enrollment.id : null,
                    products: this.products,
                    payments: this.payments,
                    client_name: this.clientname,
                    client_idnumber: this.clientidnumber,
                    client_address: this.clientaddress,
                    client_phonenumber: this.clientaddress,
                    client_email: this.clientemail,
                    total_price: this.shoppingCartTotal,
                    comment: this.comment,
                    discounts: this.discounts,
                    sendinvoice: this.sendInvoiceToAccounting,
                    invoicetype: this.selectedInvoiceType,
                })
                .then(response => {
                    // handle success
                    this.step = 4;
                    window.location.href = this.enrollment ? `/enrollment/${this.enrollment.id}/show` : "/payment";
                    new Noty({
                        title: this.$t('Success'),
                        text: this.$t("Your changes were successful"),
                        type: "success",
                    }).show();
                })
                .catch(e => {
                    this.loading = false;
                    this.errors.push(e);
                    new Noty({
                        title: this.$t("Error"),
                        text: this.$t("Your changes could not be saved"),
                        type: "error",
                    }).show();
                });
        },
    },
};
</script>

<style scoped>
    .dropdown-menu {
        max-height: calc(80vh - 50px);
        overflow-y: auto;
    }
</style>
