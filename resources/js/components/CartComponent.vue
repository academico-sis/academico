<template>
    <div>
        <ol class="breadcrumb bg-transparent">
            <li v-if="step >= 1" class="breadcrumb-item">
                <a @click="step = 1">{{ $t("Products") }}</a>
            </li>
            <li v-if="step >= 2" class="breadcrumb-item">
                <a @click="step = 2">{{ $t("Invoice Data") }}</a>
            </li>
            <li v-if="step >= 3" class="breadcrumb-item">
                <a @click="step = 3">{{ $t("Payment") }}</a>
            </li>
        </ol>

        <div v-if="step === 1" class="row">
            <div class="col col-md-8">
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
                                <tr>
                                    <td>
                                        {{ this.enrollment.course.name }}
                                        {{ $t("for") }}
                                        {{ this.enrollment.student.name }}
                                    </td>
                                    <td>
                                        {{ this.enrollment.price_with_currency }}
                                        <span v-if="discount(this.enrollment.price) > 0" class="label label-info"> - ${{discount(this.enrollment.price)}}</span>
                                    </td>
                                    <td></td>
                                </tr>

                                <tr v-for="(book, index) in books" :key="book.id">
                                    <td>{{ book.name }}</td>
                                    <td>{{ book.price_with_currency }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-danger" @click="removeBookFromCart(index)">
                                            <i class="la la-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr v-for="(fee, index) in fees" :key="fee.id">
                                    <td>{{ fee.name }}</td>
                                    <td>{{ fee.price_with_currency }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-danger" @click="removeFeeFromCart(index)">
                                            <i class="la la-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <h4>
                            {{ $t("Total price") }}: <span v-if="currencyposition === 'before'">{{ currency }} </span>
                            {{ shoppingCartTotal }}
                            <span v-if="currencyposition === 'after'">{{ currency }} </span>
                        </h4>
                        <button class="btn btn-success" @click="step = 2">
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
                <div class="card">
                    <div class="card-header">
                        {{ $t("Student") }}

                        <div class="card-header-actions">
                            <button class="btn btn-info" @click="selectStudentData()">
                                <i class="la la-check"></i>{{ $t("Select") }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            {{ this.enrollment.student.name }}
                        </p>
                        <p>{{ this.enrollment.student.idnumber }}</p>
                        <p>{{ this.enrollment.student.user.email }}</p>
                    </div>
                </div>

                <div v-for="contact in this.contactdata" :key="contact.id" class="card">
                    <div class="card-header">{{ $t('Contact') }}

                        <div class="card-header-actions">
                            <button class="btn btn-info" @click="selectInvoiceData(contact)">
                                <i class="la la-check"></i>{{ $t("Select") }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>{{ contact.firstname }} {{ contact.lastname }}</p>
                        <p>{{ contact.idnumber }}</p>
                        <p>{{ contact.email }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ $t("Invoice Data") }}
                        <div class="card-header-actions">
                            <button v-if="checkForm()" class="btn btn-success" @click="confirmInvoiceData()">
                                <i class="la la-check"></i>{{ $t("Select") }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="clientname">{{ $t("Client name") }}</label>
                            <input id="clientname" v-model="clientname" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientphone">{{ $t("Client Phone Number") }}</label>
                            <input id="clientphone" v-model="clientphone" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientaddress">{{ $t("Client address") }}</label>
                            <input id="clientaddress" v-model="clientaddress" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientidnumber">{{ $t("Client ID Number") }}</label>
                            <input v-model="clientidnumber" id="clientidnumber" required type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="clientemail">{{ $t("Client email") }}</label>
                            <input v-model="clientemail" id="clientemail" required type="text" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="step === 3" class="row">
            <div class="col col-md-6">
                <div class="card">
                    <div class="card-header">
                        {{ $t("Products") }}
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th>{{ $t("Product") }}</th>
                                <th>{{ $t("Price") }}</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ this.enrollment.course.name }} {{ $t("for") }} {{ this.enrollment.student.name }}
                                    </td>
                                    <td>
                                        {{ this.enrollment.price_with_currency }}
                                        <span v-if="discount(this.enrollment.price) > 0" class="label label-info"> - ${{ discount(this.enrollment.price)}}</span>
                                    </td>
                                </tr>

                                <tr v-for="book in books" :key="book.id + '-book'">
                                    <td>{{ book.name }}</td>
                                    <td>{{ book.price_with_currency }}</td>
                                </tr>

                                <tr v-for="fee in fees" :key="fee.id + '-fee'">
                                    <td>{{ fee.name }}</td>
                                    <td>{{ fee.price_with_currency }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-solid card-primary">
                    <div class="card-header">
                        {{ $t("Invoice Data") }}
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>{{ clientname }}</li>
                            <li>{{ clientphone }}</li>
                            <li>{{ clientaddress }}</li>
                            <li>{{ clientemail }}</li>
                            <li>{{ clientidnumber }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h4>
                            {{ $t("Total price") }}: <span v-if="currencyposition === 'before'">{{ currency }} </span>
                            {{ shoppingCartTotal }}
                            <span v-if="currencyposition === 'after'">{{ currency }} </span>
                        </h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-solid card-primary">
                            <div class="card-header">
                                {{ $t("Scheduled Payments") }}
                            </div>
                            <div class="card-body">

                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label" for="paymentsCount">{{ $t('Number of payments') }}</label>
                                    <div class="col-sm-8">
                                        <input id="paymentsCount" v-model="paymentsCount" class="form-control" type="number" step="1" min="1" decimal="0">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label" for="firstPaymentDate">{{ $t('First payment date') }}</label>
                                    <div class="col-sm-8">
                                        <input id="firstPaymentDate" v-model="firstPaymentDate" class="form-control" type="date">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                <div class="card card-solid card-primary">
                    <div class="card-header">
                        {{ $t("Scheduled Payments") }}
                    </div>
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
                </div>
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
                            <button class="btn btn-lg btn-success" :disabled="loading || payments.length === 0" @click="checkTotal()">
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
                                <span v-if="sendInvoiceToAccounting">
                                                {{ $t( 'Send invoice to external accounting system' ) }}</span>
                                <span v-if="!sendInvoiceToAccounting">{{ $t("Mark this enrollment as paid but do not send to accounting system") }}</span>
                            </div>
                            <span v-else class="alert alert-danger">
                                            {{ $t("Unable to communicate with Accounting Service. This invoice will NOT be sent automatically to the Accounting system") }}
                                            <a href="#" @click="checkAccountingStatus()">{{ $t('Refresh status') }}</a></span>
                        </div>
                    </div>
                </div>
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
export default {
    props: [
        "enrollment",
        "feeslist",
        "bookslist",
        "availablebooks",
        "availablefees",
        "availablediscounts",
        "contactdata",
        "availablepaymentmethods",
        "accountingenabled",
        "currency",
        "currencyposition",
    ],

    data() {
        return {
            books: this.bookslist || [],
            fees: this.feeslist || [],
            totalPrice: 0,
            errors: [],
            discounts: [],
            step: 3,
            clientname: "",
            clientphone: "",
            clientaddress: "",
            clientemail: "",
            clientidnumber: "",
            payments: [],
            products: [],
            comment: "",
            sendInvoiceToAccounting: this.accountingenabled,
            accountingServiceIsUp: false,
            loading: false,
            paymentsCount: 1,
            firstPaymentDate: new Date().toISOString().substr(0, 10),
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

            total +=
                parseFloat(this.enrollment.price) -
                this.discount(parseFloat(this.enrollment.price));

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

        this.addPayment(this.shoppingCartTotal)
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
            if (!this.books.some(el => el.id === book.id)) {
                var addedbook = this.books.push(book) - 1;
                this.books[addedbook].quantity = 1;
            }
        },

        addFee(fee) {
            if (!this.fees.some(el => el.id === fee.id)) {
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

        selectStudentData() {
            this.clientname =
                this.enrollment.student.user.firstname +
                " " +
                this.enrollment.student.user.lastname;
            this.clientphone =
                typeof this.enrollment.student.phone[0] === "undefined"
                    ? ""
                    : this.enrollment.student.phone[0].phone_number;
            this.clientaddress = this.enrollment.student.address;
            this.clientidnumber = this.enrollment.student.idnumber;
            this.clientemail = this.enrollment.student.user.email;
        },

        selectInvoiceData(contact) {
            this.clientname = contact.firstname + " " + contact.lastname;
            this.clientphone =
                typeof contact.phone[0] === "undefined"
                    ? ""
                    : contact.phone[0].phone_number;
            this.clientaddress = contact.address;
            this.clientidnumber = contact.idnumber;
            this.clientemail = contact.email;
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
            this.products = [];

            let enrollment = {
                codinventario: this.enrollment.course?.rhythm?.product_code ?? '',
                codbodega: "MAT",
                cantidad: 1,
                descuento: this.totalDiscount,
                preciototal: this.enrollment.price,
            };

            this.products.push(enrollment);

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
                    enrollment_id: this.enrollment.id,
                    fees: this.fees,
                    books: this.books,
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
                })
                .then(response => {
                    // handle success
                    this.step = 4;
                    window.location.href =
                        `/enrollment/${this.enrollment.id}/show`;
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
    watch: {
        paymentsCount: function () {
            // empty payments array
            this.payments.length = 0;

            var i = 0;
            while (i < this.paymentsCount) {
                this.addPayment(this.shoppingCartTotal / this.paymentsCount);
                i++
            }
        },
        payments: function () {
            this.paymentsCount = this.payments.length;
        }
    },
};
</script>

<style scoped>
    .dropdown-menu {
        max-height: calc(80vh - 50px);
        overflow-y: auto;
    }
</style>
