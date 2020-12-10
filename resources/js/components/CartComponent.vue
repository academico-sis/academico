<template>
    <div>

            <div class="row">
                <div class="col col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <th>{{ $t("Product") }}</th>
                                    <th>{{ $t("Price") }}</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ enrollment.course.name }}
                                            {{ $t("for") }}
                                            {{ enrollment.student.name }}
                                        </td>
                                        <td>
                                            $ {{ this.enrollmentprice }}
                                        </td>
                                    </tr>

                                    <tr v-for="previouspayment in previouspayments" v-bind:key="previouspayment.id">
                                        <td>
                                            {{ $t('Payment') }} ({{ previouspayment.date }})
                                        </td>
                                        <td>
                                            - ${{ previouspayment.value }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <h4>
                            {{ $t("Total price") }}: $
                            {{ shoppingCartTotal }}
                        </h4>
                        <button
                            v-if="enrollments[0]"
                            class="btn btn-success"
                            @click="step = 2"
                        >
                            <i class="la la-check"></i>{{ $t("Confirm") }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="col col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{ $t("Add products") }}
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <div class="dropdown">
                                <button
                                    type="button"
                                    class="btn btn-secondary dropdown-toggle"
                                    data-toggle="dropdown"
                                >
                                    <span class="caret"></span>
                                    {{ $t("Books") }}
                                </button>
                                <div class="dropdown-menu">
                                    <button
                                        v-for="availableBook in this
                                            .availablebooks"
                                        :key="availableBook.id"
                                        class="dropdown-item"
                                        @click="addBook(availableBook)"
                                    >
                                        {{ availableBook.name }}
                                    </button>
                                </div>
                            </div>

                            <div class="dropdown">
                                <button
                                    type="button"
                                    class="btn btn-secondary dropdown-toggle"
                                    data-toggle="dropdown"
                                >
                                    <span class="caret"></span> {{ $t("Fees") }}
                                </button>
                                <div class="dropdown-menu">
                                    <button
                                        v-for="availableFee in this
                                            .availablefees"
                                        :key="availableFee.id"
                                        class="dropdown-item"
                                        @click="addFee(availableFee)"
                                    >
                                        {{ availableFee.name }}
                                    </button>
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="enrollmentprice">{{ $t('Total price') }}</label>
                                <input id="price" type="text" v-model="enrollmentprice">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h4>
                            {{ $t("Total price") }}: $
                            {{ shoppingCartTotal }}
                        </h4>
                    </div>
                </div>

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
                                    <th>{{ $t("Comment") }}</th>
                                    <th>{{ $t("Invoice ID") }}</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="payment in payments"
                                    :key="payment.id"
                                >
                                    <td>
                                        <select
                                            v-model="payment.method"
                                            class="form-control"
                                            name="method"
                                        >
                                            <option
                                                v-for="paymentmethod in availablepaymentmethods"
                                                :key="paymentmethod.id"
                                                :value="paymentmethod.code"
                                                >{{
                                                    paymentmethod.name
                                                }}</option
                                            >
                                        </select>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon"
                                                >$</span
                                            >
                                            <input
                                                v-model="payment.value"
                                                type="number"
                                                step="0.01"
                                                class="form-control"
                                            />
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <input
                                                v-model="payment.comment"
                                                type="text"
                                                class="form-control"
                                            />
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <input
                                                v-model="payment.invoice_id"
                                                type="text"
                                                class="form-control"
                                            />
                                        </div>
                                    </td>

                                    <td>
                                        <button
                                            class="btn btn-sm btn-ghost-danger"
                                            @click="removePayment(payment)"
                                        >
                                            <i class="la la-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button
                                                    id="dropdownMenuButton"
                                                    class="btn btn-secondary dropdown-toggle"
                                                    type="button"
                                                    data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false"
                                                >
                                                    {{ $t("Add") }}
                                                </button>
                                                <div
                                                    class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton"
                                                >
                                                    <a
                                                        v-for="paymentmethod in availablepaymentmethods"
                                                        :key="paymentmethod.id"
                                                        class="dropdown-item"
                                                        href="#"
                                                        @click="
                                                            addPayment(
                                                                paymentmethod.code
                                                            )
                                                        "
                                                        >{{
                                                            paymentmethod.name
                                                        }}</a
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h4>
                                        {{ $t("Total received amount") }}:
                                        $ {{ paidTotal }}
                                    </h4>
                                </div>

                                <div class="form-group">
                                    <label for="comment">{{
                                        $t("Comment")
                                    }}</label>
                                    <textarea
                                        id="comment"
                                        v-model="comment"
                                        name="comment"
                                        cols="50"
                                        rows="2"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="col-md-6" style="text-align: center;">
                                <div v-if="shoppingCartTotal == paidTotal">
                                    <div class="form-group">
                                        <button
                                            class="btn btn-lg btn-success"
                                            :disabled="loading"
                                            @click="finish()"
                                        >
                                            <span
                                                v-if="loading"
                                                class="spinner-border spinner-border-sm"
                                                role="status"
                                                aria-hidden="true"
                                            ></span>
                                            <i class="la la-check"></i
                                            >{{ $t("Checkout") }}
                                        </button>
                                    </div>
                                    <div
                                        v-if="this.externalaccountingenabled"
                                        class="form-group"
                                        style="display: flex;"
                                    >
                                        <div v-if="this.accountingServiceIsUp">
                                            <label
                                                class="switch switch-pill switch-success"
                                            >
                                                <input
                                                    v-model="
                                                        sendInvoiceToAccounting
                                                    "
                                                    class="switch-input"
                                                    type="checkbox"
                                                /><span
                                                    class="switch-slider"
                                                ></span>
                                            </label>
                                            <span
                                                v-if="sendInvoiceToAccounting"
                                                >{{
                                                    $t(
                                                        "Mandar datos al sistema contable para generar factura"
                                                    )
                                                }}</span
                                            >
                                            <span
                                                v-if="!sendInvoiceToAccounting"
                                                >{{
                                                    $t(
                                                        "Mark this enrollment as paid but do not send to accounting system"
                                                    )
                                                }}</span
                                            >
                                        </div>
                                        <span v-else class="alert alert-danger"
                                            >{{
                                                $t(
                                                    "Unable to communicate with Accounting Service. This invoice will NOT be sent automatically to the Accounting system"
                                                )
                                            }}
                                            <a
                                                href="#"
                                                @click="checkAccountingStatus()"
                                                >{{ $t('Refresh status') }}</a
                                            ></span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="step == 4" class="row">
            <div class="col col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $t("The invoice has been generated") }}
                    </div>

                    <div class="card-body">
                        <p>
                            {{ $t("Enrollment number") }}
                            {{ enrollments[0].id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "enrollmentslist",
        "feeslist",
        "bookslist",
        "availablebooks",
        "availablefees",
        "availablediscounts",
        "contactdata",
        "availablepaymentmethods",
        "externalaccountingenabled",
    ],

    data() {
        return {
            enrollments: this.enrollmentslist || [],
            books: this.bookslist || [],
            fees: this.feeslist || [],
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
            products: [],
            comment: "",
            sendInvoiceToAccounting: this.externalaccountingenabled,
            accountingServiceIsUp: false,
            loading: false,
        };
    },
    computed: {
        shoppingCartTotal() {
            let total = 0;
            if (this.books) {
                this.books.forEach(book => {
                    total += parseFloat(book.price);
                });
            }

            if (this.fees) {
                this.fees.forEach(fee => {
                    total += parseFloat(fee.price);
                });
            }

            if (this.enrollments) {
                this.enrollments.forEach(enrollment => {
                    total +=
                        parseFloat(enrollment.course.price) -
                        this.discount(parseFloat(enrollment.course.price));
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
            return total;
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
            if (!this.books.some(el => el.id == book.id)) {
                var addedbook = this.books.push(book) - 1;
                this.books[addedbook].quantity = 1;
            }
        },

        addFee(fee) {
            if (!this.fees.some(el => el.id == fee.id)) {
                var addedfee = this.fees.push(fee) - 1;
                this.fees[addedfee].quantity = 1;
            }
        },

        removeBookFromCart(index) {
            this.books.splice(index, 1);
        },

        removeFeeFromCart(index) {
            this.fees.splice(index, 1);
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

        addPayment(method) {
            let payment = {
                method: method,
                value: this.shoppingCartTotal,
                comment: "",
            };

            this.payments.push(payment);
        },

        removePayment(payment) {
            var index = this.payments.indexOf(payment);
            if (index !== -1) this.payments.splice(index, 1);
        },

        finish() {
            this.loading = true;
            this.products = [];

            this.enrollments.forEach(element => {
                let enrollment = {
                    codinventario: element.course.rhythm.product_code,
                    codbodega: "MAT",
                    cantidad: 1,
                    descuento: this.totalDiscount,
                    preciototal: element.course.price,
                };

                this.products.push(enrollment);
            });

            this.books.forEach(element => {
                let book = {
                    codinventario: element.product_code,
                    codbodega: "MAT",
                    cantidad: 1,
                    descuento: 0,
                    preciototal: element.price, // sin descuento (precio * cantidad)
                };

                this.products.push(book);
            });

            this.fees.forEach(element => {
                let fee = {
                    codinventario: element.product_code,
                    codbodega: "MAT",
                    cantidad: 1,
                    descuento: 0,
                    preciototal: element.price, // sin descuento (precio * cantidad)
                };

                this.products.push(fee);
            });

            axios
                .post("/checkout", {
                    enrollments: this.enrollments,
                    fees: this.fees,
                    books: this.books,
                    products: this.products,
                    payments: this.payments,
                    total_price: this.enrollmentprice,
                    comment: this.comment,
                    sendinvoice: this.sendInvoiceToAccounting,
                })
                .then(response => {
                    // handle success
                    this.step = 4;
                    window.location.href =
                        `/enrollment/${this.enrollments[0].id}/show`;
                    new Noty({
                        title: "Operation successful",
                        text: "The enrollment has been paid",
                        type: "success",
                    }).show();
                })
                .catch(e => {
                    this.loading = false;
                    this.errors.push(e);
                    new Noty({
                        title: "Error",
                        text: "The enrollment couldn't be paid",
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
