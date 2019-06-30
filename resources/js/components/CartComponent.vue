<template>
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
                    <tr v-bind:key="enrollment.id" v-for="enrollment in enrollments">
                        <td>{{ enrollment.course.name }}</td>
                        <td>{{ enrollment.course.price }}</td>
                        <td>
                            <button class="btn btn-xs btn-danger" v-on:click="removeFromCart(product.id)"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>

                    <tr v-bind:key="book.id" v-for="book in books">
                        <td>{{ book.name }}</td>
                        <td>{{ book.price }}</td>
                        <td>
                            <button class="btn btn-xs btn-danger" v-on:click="removeFromCart(product.id)"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>

                    <tr v-bind:key="fee.id" v-for="fee in fees">
                        <td>{{ fee.name }}</td>
                        <td>{{ fee.price }}</td>
                        <td>
                            <button class="btn btn-xs btn-danger" v-on:click="removeFromCart(product.id)"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </tbody>

            </table>
            <p>Total: {{ shoppingCartTotal }}</p>
        </div>
    </div>
</template>

<script>

    export default {

        props: ['enrollmentslist', 'feeslist', 'bookslist'],

        data () {
            return {
                enrollments: this.enrollmentslist,
                books: this.bookslist,
                fees: this.feeslist,
                totalPrice: 0,
            }
        },

        mounted() {
        },

        methods: {

 
        },

        computed: {
            shoppingCartTotal() {
                return this.books.map(item => parseFloat(item.price)).reduce((total, amount) => total + amount)
                +this.fees.map(item => parseFloat(item.price)).reduce((total, amount) => total + amount)
                +this.enrollments.map(item => parseFloat(item.course.price)).reduce((total, amount) => total + amount);
            }
        },
    }
</script>
