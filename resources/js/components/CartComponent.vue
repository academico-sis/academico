<template>
<div>
    <ol class="breadcrumb bg-transparent">
        <li class="breadcrumb-item" v-if="step >= 1"><a @click="step = 1">{{ $t('Products') }}</a></li>
        <li class="breadcrumb-item" v-if="step >= 2"><a @click="step = 2">{{ $t('front.Invoice Data') }}</a></li>
        <li class="breadcrumb-item" v-if="step >= 3"><a @click="step = 3">{{ $t('front.Payment') }}</a></li>
      </ol>

    <div class="row" v-if="step == 1">
        <div class="col col-md-8">

            <div class="card">
                <div class="card-header">
                    {{ $t('Products') }}
                </div>
                
                <div class="card-body">

                    <table class="table">
                        <thead>
                            <th>{{ $t('Product') }}</th>
                            <th>{{ $t('Price') }}</th>
                            <th>{{ $t('Actions') }}</th>
                        </thead>
                        <tbody>
                            <tr v-bind:key="enrollment.id" v-for="enrollment in enrollments">
                                <td>{{ enrollment.course.name }} {{ $t('front.for') }} {{ enrollment.student.user.firstname }} {{ enrollment.student.user.lastname }}</td>
                                <td>$ {{ enrollment.course.price }} <span class="label label-info" v-if="discount(enrollment.course.price) > 0">- ${{ discount(enrollment.course.price) }}</span></td>
                                <td>
                                </td>
                            </tr>

                            <tr v-bind:key="book.id" v-for="(book, index) in books">
                                <td>{{ book.name }}</td>
                                <td>$ {{ book.price }}</td>
                                <td>
                                    <button class="btn btn-xs btn-danger" v-on:click="removeBookFromCart(index)"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>

                            <tr v-bind:key="fee.id" v-for="(fee, index) in fees">
                                <td>{{ fee.name }}</td>
                                <td>$ {{ fee.price }}</td>
                                <td>
                                    <button class="btn btn-xs btn-danger" v-on:click="removeFeeFromCart(index)"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                        <h4>{{ $t('front.Total price') }}: $ {{ shoppingCartTotal }}</h4>
                        <button class="btn btn-success" v-if="enrollments[0]" @click="step = 2"><i class="fa fa-check"></i>{{ $t('front.Confirm') }}</button>
                </div>
            </div>

        </div>

        <div class="col col-md-4">

            <div class="card">
                <div class="card-header">
                    {{ $t('front.Add products') }}
                </div>
                
                <div class="card-body">

                    <div class="form-group">
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> {{ $t('Books') }}
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" v-for="availableBook in this.availablebooks" v-bind:key="availableBook.id" @click="addBook(availableBook)">{{ availableBook.name }}</button>
                            </div>
                        </div>
                    
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> {{ $t('Fees') }}
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" v-for="availableFee in this.availablefees" v-bind:key="availableFee.id" @click="addFee(availableFee)">{{ availableFee.name }}</button>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    {{ $t('Discounts') }}
                </div>
                
                <div class="card-body">

                    <ul>
                        <li v-bind:key="discount.id" v-for="(discount, index) in discounts">
                            {{ discount.name }} ({{ discount.value }}%)
                            <button class="btn btn-xs btn-warning" v-on:click="removeDiscount(index)"><i class="fa fa-times"></i></button>
                        </li>
                    </ul>

                    <div class="form-group">
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span> {{ $t('front.Add discount') }}
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" v-for="availableDiscount in this.availablediscounts" v-bind:key="availableDiscount.id" @click="addDiscount(availableDiscount)">{{ availableDiscount.name }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



    <div class="row" v-if="step == 2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    {{ $t('Student') }}

                    <div class="card-header-actions">
                        <button class="btn btn-info" @click="selectStudentData()"><i class="fa fa-check"></i>{{ $t('front.Select') }}</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>{{enrollments[0].student.user.firstname}} {{enrollments[0].student.user.lastname}}</p>
                    <p>{{enrollments[0].student.idnumber}}</p>
                    <p>{{enrollments[0].student.user.email}}</p>
                </div>
            </div>

            <div class="card" v-for="contact in this.contactdata" v-bind:key="contact.id">
                <div class="card-header">
                    Contact

                    <div class="card-header-actions">
                        <button class="btn btn-info" @click="selectInvoiceData(contact)"><i class="fa fa-check"></i>{{ $t('front.Select') }}</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>{{contact.firstname}} {{contact.lastname}}</p>
                    <p>{{contact.idnumber}}</p>
                    <p>{{contact.email}}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $t('front.Invoice Data') }}
                    <div class="card-header-actions">
                        <button v-if="checkForm()" class="btn btn-success" @click="confirmInvoiceData()"><i class="fa fa-check"></i>{{ $t('front.Select') }}</button>
                    </div>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label for="clientname">{{ $t('Client name') }}</label>
                        <input required id="clientname" type="text" v-model="clientname" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="clientphone">{{ $t('Client Phone Number') }}</label>
                        <input required id="clientphone" type="text" v-model="clientphone" class="form-control">
                    </div>

                    
                    <div class="form-group">
                        <label for="clientaddress">{{ $t('Client address') }}</label>
                        <input required type="text" v-model="clientaddress" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="clientidnumber">{{ $t('Client ID Number') }}</label>
                        <input required type="text" v-model="clientidnumber" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="clientemail">{{ $t('Client email') }}</label>
                        <input required type="text" v-model="clientemail" class="form-control">
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="row" v-if="step == 3">

        <div class="col col-md-6">

            <div class="card">
                <div class="card-header">
                    {{ $t('Products') }}
                </div>
                
                <div class="card-body">

                    <table class="table">
                        <thead>
                            <th>{{ $t('Product') }}</th>
                            <th>{{ $t('Price') }}</th>
                        </thead>
                        <tbody>
                            <tr v-bind:key="enrollment.id + '-enrollment'" v-for="enrollment in enrollments">
                                <td>{{ enrollment.course.name }} {{ $t('front.for') }} {{ enrollment.student.user.firstname }} {{ enrollment.student.user.lastname }}</td>
                                <td>$ {{ enrollment.course.price }} <span class="label label-info" v-if="discount(enrollment.course.price) > 0">- ${{ discount(enrollment.course.price) }}</span></td>
                            </tr>

                            <tr v-bind:key="book.id + '-book'" v-for="book in books">
                                <td>{{ book.name }}</td>
                                <td>$ {{ book.price }}</td>
                            </tr>

                            <tr v-bind:key="fee.id + '-fee'" v-for="fee in fees">
                                <td>{{ fee.name }}</td>
                                <td>$ {{ fee.price }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="col-md-4">

            
            <div class="card card-solid card-primary">
                <div class="card-header">
                    {{ $t('front.Invoice Data') }}
                </div>
                <div class="card-body">
                    <ul>
                        <li>{{clientname}}</li>
                        <li>{{clientphone}}</li>
                        <li>{{clientaddress}}</li>
                        <li>{{clientemail}}</li>
                        <li>{{clientidnumber}}</li>
                    </ul>
                          
                </div>
            </div>
        </div>

        <div class="col-md-12">

            <div class="card">
                <div class="card-body text-center">
                        <h4>{{ $t('front.Total price') }}: $ {{ shoppingCartTotal }}</h4>
                </div>
            </div>

            <div class="card card-solid card-primary">
                <div class="card-header">
                        {{ $t('front.Payment method') }}
                </div>
                <div class="card-body">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ $t('front.Payment method') }}</th>
                                <th>{{ $t('front.Amount received') }}</th>
                                <th>{{ $t('Comment') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="payment in payments" v-bind:key="payment.id">
                                <td>
                                    <select class="form-control" name="method" v-model="payment.method">
                                        <option v-for="paymentmethod in availablepaymentmethods" v-bind:key="paymentmethod.id" v-bind:value="paymentmethod.code">{{paymentmethod.name}}</option>
                                    </select>
                                </td>

                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" step="0.01" v-model="payment.value" class="form-control">
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="input-group">
                                        <input type="text" v-model="payment.comment" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="btn btn-secondary" @click="addPayment()">{{ $t('front.Add') }}</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <h4>{{ $t('front.Total received amount') }}: $ {{ paidTotal }}</h4>
                            </div>

                            <div class="form-group">
                                <label for="comment">{{ $t('Comment') }}</label>
                                <textarea name="comment" id="comment" cols="50" rows="2" v-model="comment"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6" style="text-align: center;">
                            <div v-if="shoppingCartTotal == paidTotal">
                                <div class="form-group">
                                    <button class="btn btn-lg btn-success" @click="finish()"><i class="fa fa-check"></i>Facturar</button>
                                </div>
                                <div class="form-group" style="display:flex;" v-if="this.externalaccountingenabled">
                                    <label class="switch switch-pill switch-success">
                                        <input class="switch-input" type="checkbox" v-model="sendInvoiceToAccounting"><span class="switch-slider"></span>
                                    </label>
                                    <span v-if="sendInvoiceToAccounting">Mandar datos al sistema contable para generar factura</span>
                                    <span v-if="!sendInvoiceToAccounting">La matricula sera marcada como pagada sin generar la factura en el sistema contable</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>





    <div class="row" v-if="step == 4">

        <div class="col col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ $t('front.The invoice has been generated') }}
                </div>
                
                <div class="card-body">
                    <p>{{ $t('front.Enrollment number') }} {{ enrollments[0].id }}</p>
                </div>
            </div>

        </div>
    </div>

    </div>
</template>

<script>

    export default {

        props: ['enrollmentslist', 'feeslist', 'bookslist', 'availablebooks', 'availablefees', 'availablediscounts', 'contactdata', 'availablepaymentmethods', 'externalaccountingenabled'],

        data () {
            return {
                enrollments: this.enrollmentslist || [],
                books: this.bookslist || [],
                fees: this.feeslist || [],
                totalPrice: 0,
                errors: [],
                discounts: [],
                step: 1,
                clientname: '',
                clientphone: '',
                clientaddress: '',
                clientemail: '',
                clientidnumber: '',
                payments: [],
                products: [],
                comment: '',
                sendInvoiceToAccounting: this.externalaccountingenabled,
            }
        },

        mounted() {

        },

        methods: {

            addBook(book)
            {
                if(!this.books.some(el => el.id == book.id)) {
                    var addedbook = this.books.push(book) - 1;
                    this.books[addedbook].quantity = 1;
                }
            },

            addFee(fee)
            {
                if(!this.fees.some(el => el.id == fee.id)) {
                    var addedfee = this.fees.push(fee) - 1;
                    this.fees[addedfee].quantity = 1;
                }
            },
 
            removeBookFromCart(index)
            {
                this.books.splice(index, 1);

            },

            removeFeeFromCart(index)
            {
                this.fees.splice(index, 1);

            },


            addDiscount(discount)
            {
                this.discounts.push(discount);
            },

            removeDiscount(index)
            {
                this.discounts.splice(index, 1);

            },

            discount(price)
            {
                return price * (this.totalDiscount/100);
            },

            selectStudentData()
            {
                this.clientname = this.enrollments[0].student.user.firstname + ' ' + this.enrollments[0].student.user.lastname
                this.clientphone = (typeof this.enrollments[0].student.phone[0] === 'undefined') ? '' : this.enrollments[0].student.phone[0].phone_number;
                this.clientaddress = this.enrollments[0].student.address
                this.clientidnumber = this.enrollments[0].student.idnumber
                this.clientemail = this.enrollments[0].student.user.email
            },

            selectInvoiceData(contact)
            {
                this.clientname = contact.firstname + ' ' + contact.lastname
                this.clientphone = (typeof contact.phone[0] === 'undefined') ? '' : contact.phone[0].phone_number;
                this.clientaddress = contact.address
                this.clientidnumber = contact.idnumber
                this.clientemail = contact.email
            },

            checkForm: function (e) {
                if (this.clientname && this.clientphone && this.clientaddress && this.clientidnumber && this.clientemail) {
                    return true;
                }
            },

            confirmInvoiceData()
            {
                this.step = 3;
            },

            addPayment()
            {
                let payment = {
                    method: "",
                    value: this.shoppingCartTotal,
                    comment: ""
                };

                this.payments.push(payment);
            },

            finish()
            {

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


                axios.post('/checkout', {
                    enrollments: this.enrollments,
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
                    sendinvoice: this.sendInvoiceToAccounting,
                })
                .then(response => {
                    // handle success
                        this.step = 4;
                        window.location.href = '/enrollment/' + this.enrollments[0]. id + '/show';
                        new PNotify({
                            title: "Operation successful",
                            text: "The enrollment has been paid",
                            type: "success"
                            });
                    }
                )
                .catch(e => {
                        this.errors.push(e)
                        new PNotify({
                            title: "Error",
                            text: "The enrollment couldn't be paid",
                            type: "error"
                            });
                    }
                );
            }

        },
        computed: {
            shoppingCartTotal() {
                let total = 0;
                if(this.books) {
                    this.books.forEach(book => {
                        total += parseFloat(book.price);
                    });
                }
                
                if(this.fees) {
                    this.fees.forEach(fee => {
                        total += parseFloat(fee.price);
                    });
                }

                if(this.enrollments) {
                    this.enrollments.forEach(enrollment => {
                        total += parseFloat(enrollment.course.price) - this.discount(parseFloat(enrollment.course.price));
                    });
                }
                return total;

            },

            paidTotal()
            {
                let total = 0;
                if(this.payments) {
                    this.payments.forEach(payment => {
                        total += parseFloat(payment.value);
                    });
                }
                return total;
            },

            totalDiscount() {
                let total = 0;
                if(this.discounts) {
                    this.discounts.forEach(discount => {
                        total += parseFloat(discount.value);
                    });
                }
                
                return total;

            },

        },
    }
</script>
