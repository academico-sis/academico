<template>
    <div class="row">
        <div class="col col-md-8">

            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        Cart details
                    </div>
                    <div class="box-tools pull-right">
                        <!-- <button class="btn btn-primary"><i class="fa fa-plus"></i></button> todo -->
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
                                <td>$ {{ enrollment.course.price }}</td>
                                <td>
                                    <button class="btn btn-xs btn-danger" v-on:click="removeEnrollmentFromCart(index)"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>

                            <tr v-bind:key="book.id" v-for="(book, index) in books">
                                <td>{{ book.name }}</td>
                                <td>$ {{ book.price }}</td>
                                <td>
                                    <button class="btn btn-xs btn-danger" v-on:click="removeBookFromCart(index)"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>

                            <tr v-bind:key="fee.id" v-for="(fee, index) in fees">
                                <td>{{ fee.name }}</td>
                                <td>$ {{ fee.price }}</td>
                                <td>
                                    <button class="btn btn-xs btn-danger" v-on:click="removeFeeFromCart(index)"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bold">
                                <td>TOTAL</td>
                                <td>$ {{ shoppingCartTotal }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
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
                        <!-- <button class="btn btn-primary"><i class="fa fa-plus"></i></button> todo -->
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
                    </div>

                    <div class="form-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span> Fees
                            </button>
                            <ul class="dropdown-menu">
                            <li v-for="availableFee in this.availablefees" v-bind:key="availableFee.id"><a href="#" @click="addFee(availableFee)">{{ availableFee.name }}</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
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

        </div>
    </div>
</template>

<script>

    export default {

        props: ['enrollmentslist', 'feeslist', 'bookslist', 'availablebooks', 'availablefees', 'availableenrollments'],

        data () {
            return {
                enrollments: this.enrollmentslist || [],
                books: this.bookslist || [],
                fees: this.feeslist || [],
                totalPrice: 0,
                errors: [],
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
                        total += parseFloat(enrollment.course.price);
                    });
                }
                return total;

                /* this.books.map(item => parseFloat(item.price)).reduce((total, amount) => total + amount)
                +this.fees.map(item => parseFloat(item.price)).reduce((total, amount) => total + amount)
                +this.enrollments.map(item => parseFloat(item.course.price)).reduce((total, amount) => total + amount); */
            }
        },
    }
</script>
