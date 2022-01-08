<template>
    <div>
        <cart-breadcrumbs-component :step="step"></cart-breadcrumbs-component>


        <div v-if="step === 1" class="row">

            <div class="col col-md-8">

                <cart-product-list-component
                    :products="products"
                    :currency="currency"
                    :currencyposition="currencyposition"
                    :availablediscounts="this.availablediscounts"
                    :availabletaxes="this.availabletaxes"
                    :key="componentKey"
                    :editsallowed="true"
                ></cart-product-list-component>

                <cart-total-price-component :value="shoppingCartTotal()" :currency="currency" :currencyposition="currencyposition"></cart-total-price-component>

                <!-- Button to move forward-->
                <div class="card">
                    <div class="card-body text-center">
                    <button :disabled="products.length === 0" class="btn btn-success" @click="confirmProductsStep()">
                        <i class="la la-check"></i>{{ $t("Confirm") }}
                    </button>
                    </div>
                </div>

            </div>

            <div class="col col-md-4">
                <!-- Add more products -->
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

                <cart-price-categories-component
                    v-if="this.invoicingMode === 'priceCategories'"
                    :pricecategories="pricecategories"
                    :studentpricecategory="studentpricecategory"
                    :currency="currency"
                    :currencyposition="currencyposition"
                >
                </cart-price-categories-component>
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
                <cart-product-list-component
                    :products="products"
                    :currency="currency"
                    :currencyposition="currencyposition"
                    :key="componentKey"
                    :editsallowed="false"
                ></cart-product-list-component>
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
                <total-price-component :value="shoppingCartTotal()" :currency="currency" :currencyposition="currencyposition"></total-price-component>

                <cart-payment-component :availablepaymentmethods="availablepaymentmethods" :currency="currency" :currencyposition="currencyposition" :totalPrice="shoppingCartTotal()"></cart-payment-component>
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


                        <div v-if="this.manualInvoiceNumbering" class="form-group">
                            <label for="receiptnumber" class="form-label">{{ $t("Receipt Number") }}</label>
                            <input
                                class="form-control"
                                id="receiptnumber"
                                v-model="receiptnumber"
                                name="receiptnumber"
                            ></input>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-lg btn-success" :disabled="!readyForInvoice" @click="checkTotal()">
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
        "invoicingMode",
        "enrollment",
        "availablebooks",
        "availablefees",
        "availablediscounts",
        "availabletaxes",
        "availablepaymentmethods",
        "accountingenabled",
        "currency",
        "currencyposition",
        "productslist",
        "clients",
        "invoicetypes",
        "allowemptypaymentmethods",
        "allowedblankfields",
        "pricecategories",
        "studentpricecategory",
        "skipDataStep",
        "invoicingMode",
        "manualInvoiceNumbering",
    ],

    data() {
        return {
            totalPrice: 0,
            errors: [],
            step: 1,
            clientname: "",
            clientphone: "",
            clientaddress: "",
            clientemail: "",
            clientidnumber: "",
            payments: [],
            products: this.productslist,
            comment: "",
            receiptnumber: "",
            sendInvoiceToAccounting: this.accountingenabled,
            accountingServiceIsUp: false,
            loading: false,
            selectedInvoiceType: this.invoicetypes[0].id,
            componentKey: 0,
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

        readyForInvoice() {
            if (this.allowemptypaymentmethods) {
                return ! (this.loading || this.payments.length === 0 || ! this.paidTotal > 0);
            } else {
                return ! ( this.loading || this.payments.length === 0 || ! this.paidTotal > 0 || this.payments.every(payment => payment.method === undefined));
            }
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

        EventBus.$on("productsUpated", (products) => {
            this.componentKey += 1;
        });

        EventBus.$on("setInvoiceType", (id) => {
            this.selectedInvoiceType = id;
        });

        EventBus.$on("setEnrollmentPrice", (price) => {
            this.enrollmentPrice = price;
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
            book.quantity = 1;
            this.products.push(book);
        },

        addFee(fee) {
            fee.quantity = 1;
            this.products.push(fee);
        },

        removeFromCart(index) {
            this.products.splice(index, 1);
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
                (this.clientphone || this.allowedblankfields.includes('phone')) &&
                (this.clientaddress || this.allowedblankfields.includes('address')) &&
                (this.clientidnumber || this.allowedblankfields.includes('idnumber')) &&
                (this.clientemail || this.allowedblankfields.includes('email'))
            ) {
                return true;
            }
        },

        confirmInvoiceData() {
            this.step = 3;
        },

        shoppingCartTotal() {
            let total = 0;

            if (this.products) {
                this.products.forEach(product => {
                    let quantity = typeof product.quantity === 'undefined' ? 1 : product.quantity;
                    let productTotal = parseFloat(product.price) * quantity;

                    if (product.discounts) {
                        product.discounts.forEach(discount => {
                            productTotal -= product.price * quantity * (discount.value / 100);
                        });
                    }

                    if (product.taxes) {
                        product.taxes.forEach(tax => {
                            productTotal += product.price * quantity * (tax.value / 100);
                        });
                    }

                    total += Math.max(0, productTotal)
                });
            }

            return Math.round(total * 100)/100;
        },

        checkTotal() {
            if (this.paidTotal !== this.shoppingCartTotal()) {
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

        confirmProductsStep() {
            if (this.skipDataStep && this.clients[0] !== undefined) {
                this.selectInvoiceData(this.clients[0]);
                this.step = 3;
            }
            else {
                this.step = 2;
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
                    total_price: this.shoppingCartTotal(),
                    comment: this.comment,
                    receiptnumber: this.receiptnumber,
                    sendinvoice: this.sendInvoiceToAccounting,
                    invoicetype: this.selectedInvoiceType,
                })
                .then(response => {
                    // handle success
                    this.step = 4;
                    window.location.href = this.enrollment ? `/enrollment/${this.enrollment.id}/show` : "/invoice";
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
