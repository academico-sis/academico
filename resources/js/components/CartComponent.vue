<template>
<div>
    <ol class="breadcrumb">
        <li v-if="step >= 1"><a @click="step = 1">Products</a></li>
        <li v-if="step >= 2" class="active"><a @click="step = 2">Invoice Data</a></li>
        <li v-if="step >= 3" class="active"><a @click="step = 3">Payment</a></li>
      </ol>

    <div class="row" v-if="step == 1">
        <div class="col col-md-8">

            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Cart details
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">

                    <table class="table">
                        <thead>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <tr v-bind:key="enrollment.id" v-for="(enrollment, index) in enrollments">
                                <td>{{ enrollment.course.name }} para {{ enrollment.student.user.firstname }} {{ enrollment.student.user.lastname }}</td>
                                <td>$ {{ enrollment.course.price }} <span class="label label-info" v-if="discount(enrollment.course.price) > 0">- ${{ discount(enrollment.course.price) }}</span></td>
                                <td>
                                    <button class="btn btn-xs btn-danger" v-on:click="removeEnrollmentFromCart(index)"><i class="fa fa-trash"></i></button>
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

            <div class="box">
                <div class="box-body text-center">
                        <h4> PRECIO TOTAL: $ {{ shoppingCartTotal }} <button class="btn btn-success" @click="step = 2"><i class="fa fa-check"></i>Confirmar</button></h4>
                </div>
            </div>

        </div>

        <div class="col col-md-4">

            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Add products
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">

                    <div class="form-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> Books
                            </button>
                            <ul class="dropdown-menu">
                            <li v-for="availableBook in this.availablebooks" v-bind:key="availableBook.id"><a href="#" @click="addBook(availableBook)">{{ availableBook.name }}</a></li>
                            </ul>
                        </div>
                    
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> Fees
                            </button>
                            <ul class="dropdown-menu">
                            <li v-for="availableFee in this.availablefees" v-bind:key="availableFee.id"><a href="#" @click="addFee(availableFee)">{{ availableFee.name }}</a></li>
                            </ul>
                        </div>
                    
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> Enrollment
                            </button>
                            <ul class="dropdown-menu">
                            <li v-for="availableEnrollment in this.availableenrollments" v-bind:key="availableEnrollment.id"><a href="#" @click="addEnrollment(availableEnrollment)">{{ availableEnrollment.student.user.lastname }} {{ availableEnrollment.student.user.firstname }} ({{ availableEnrollment.course.name }})</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Discounts
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">

                    <ul>
                        <li v-bind:key="discount.id" v-for="(discount, index) in discounts">
                            {{ discount.name }} ({{ discount.value }}%)
                            <button class="btn btn-xs btn-warning" v-on:click="removeDiscount(index)"><i class="fa fa-times"></i></button>
                        </li>
                    </ul>

                    <div class="form-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> Add discount
                            </button>
                            <ul class="dropdown-menu">
                            <li v-for="availableDiscount in this.availablediscounts" v-bind:key="availableDiscount.id"><a href="#" @click="addDiscount(availableDiscount)">{{ availableDiscount.name }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



    <div class="row" v-if="step == 2">
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Estudiante
                    </div>
                    <div class="box-tools pull-right">
                        <button class="btn btn-success" @click="selectStudentData()"><i class="fa fa-check"></i>Selectionar</button>
                    </div>
                </div>
                <div class="box-body">
                    <p>{{enrollments[0].student.user.firstname}} {{enrollments[0].student.user.lastname}}</p>
                    <p>{{enrollments[0].student.idnumber}}</p>
                    <p>{{enrollments[0].student.address}}</p>
                    <p>{{enrollments[0].student.user.email}}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4" v-for="contact in this.contactdata" v-bind:key="contact.id">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Contact
                    </div>
                    <div class="box-tools pull-right">
                        <button class="btn btn-success" @click="selectInvoiceData(contact)"><i class="fa fa-check"></i>Selectionar</button>
                    </div>
                </div>
                <div class="box-body">
                    <p>{{contact.firstname}} {{contact.lastname}}</p>
                    <p>{{contact.idnumber}}</p>
                    <p>{{contact.address}}</p>
                    <p>{{contact.email}}</p>
                </div>
            </div>
        </div>

    </div>

    <div class="row" v-if="step == 3">

        <div class="col col-md-6">

            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Cart details
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">

                    <table class="table">
                        <thead>
                            <th>Nom</th>
                            <th>Prix</th>
                        </thead>
                        <tbody>
                            <tr v-bind:key="enrollment.id" v-for="enrollment in enrollments">
                                <td>{{ enrollment.course.name }} para {{ enrollment.student.user.firstname }} {{ enrollment.student.user.lastname }}</td>
                                <td>$ {{ enrollment.course.price }} <span class="label label-info" v-if="discount(enrollment.course.price) > 0">- ${{ discount(enrollment.course.price) }}</span></td>
                            </tr>

                            <tr v-bind:key="book.id" v-for="book in books">
                                <td>{{ book.name }}</td>
                                <td>$ {{ book.price }}</td>
                            </tr>

                            <tr v-bind:key="fee.id" v-for="fee in fees">
                                <td>{{ fee.name }}</td>
                                <td>$ {{ fee.price }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="col-md-4">

            
            <div class="box box-solid box-primary">
                <div class="box-header with-border">
                    <div class="box-title">
                        Datos de factura
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
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

            <div class="box">
                <div class="box-body text-center">
                        <h4> PRECIO TOTAL: $ {{ shoppingCartTotal }}</h4>
                </div>
            </div>

            <div class="box box-solid box-primary">
                <div class="box-header with-border">
                    <div class="box-title">
                        Forma de pago
                    </div>
                    <div class="box-tools pull-right">
                        <button v-if="shoppingCartTotal == paidTotal" class="btn btn-success" @click="finish()"><i class="fa fa-check"></i>Facturar</button>

                    </div>
                </div>
                <div class="box-body">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Forma de pago</th>
                                <th>Valor recibida</th>
                                <th>Observación</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="payment in payments" v-bind:key="payment.id">
                                <td>
                                    <select class="form-control" name="method" v-model="payment.method">
                                        <option v-for="paymentmethod in availablepaymentmethods" v-bind:key="paymentmethod.id" value="paymentmethod.code">{{paymentmethod.name}}</option>
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
                                    <button class="btn btn-secondary" @click="addPayment()">Agregar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="form-group">
                        <h4>Valor total recibida: $ {{ paidTotal }}</h4>
                    </div>

                    <div class="form-group">
                        <label for="comment">Comentario:</label>
                        <textarea name="comment" id="comment" cols="50" rows="2"></textarea>
                    </div>

                </div>
            </div>


        </div>

    </div>



    </div>
</template>

<script>

    export default {

        props: ['enrollmentslist', 'feeslist', 'bookslist', 'availablebooks', 'availablefees', 'availableenrollments', 'availablediscounts', 'contactdata', 'availablepaymentmethods'],

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
            }
        },

        mounted() {
        },

        methods: {

            addBook(book)
            {
                this.books.push(book);
            },

            addFee(fee)
            {
                this.fees.push(fee);
            },
            
            addEnrollment(enrollment)
            {
                this.enrollments.push(enrollment);
            },

            removeEnrollmentFromCart(index)
            {
                this.enrollments.splice(index, 1);
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
                this.clientphone = this.enrollments[0].student.user.email //todo
                this.clientaddress = this.enrollments[0].student.address
                this.clientidnumber = this.enrollments[0].student.idnumber
                this.clientemail = this.enrollments[0].student.user.email
                
                this.step = 3;

            },

            selectInvoiceData(contact)
            {
                this.clientname = contact.firstname + ' ' + contact.lastname
                this.clientphone = contact.email //todo
                this.clientaddress = contact.address
                this.clientidnumber = contact.idnumber
                this.clientemail = contact.email
                
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

                this.enrollments.forEach(element => {
                    let enrollment = {
                        codinventario: element.course.rhythm.product_code,
                        codbodega: "MAT",
                        cantidad: 1,
                        descuento: this.totalDiscount,
                        iva: 0.12,
                        preciototal: element.course.price / 1.12, // sin descuento (precio * cantidad) Y SIN IVA
                        valoriva: -((element.course.price / 1.12) - element.course.price)
                    };

                    this.products.push(enrollment);
                });


                this.books.forEach(element => {
                    let book = {
                        codinventario: element.product_code,
                        codbodega: "MAT",
                        cantidad: 1,
                        descuento: 0,
                        iva: 0.12,
                        preciototal: element.price / 1.12, // sin descuento (precio * cantidad) Y SIN IVA
                        valoriva: -((element.price / 1.12) - element.price)
                    };

                    this.products.push(book);
                });


                this.fees.forEach(element => {
                    let fee = {
                        codinventario: element.product_code,
                        codbodega: "MAT",
                        cantidad: 1,
                        descuento: 0,
                        iva: 0.12,
                        preciototal: element.price / 1.12, // sin descuento (precio * cantidad) Y SIN IVA
                        valoriva: -((element.price / 1.12) - element.price)
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
                    client_email: this.clientemail,
                    total_price: this.shoppingCartTotal,
                });
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
