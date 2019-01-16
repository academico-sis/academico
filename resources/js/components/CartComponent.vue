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
                    <tr v-bind:key="product.id" v-for="product in products">
                        <td>{{ product.product.name }}</td>
                        <td>{{ product.product.price }}</td>
                        <td>
                            <button class="btn btn-xs btn-danger" v-on:click="removeFromCart(product.id)"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</template>

<script>

    export default {

props: ['products'],
        data () {
            return {

            }
        },

        mounted() {
        },

        methods: {

            removeFromCart(id)
            {
                axios
                    .delete('/carts/'+id)
                    .then(response => {
                        document.location.reload(true);
                        // TODO improve this: do not reload the whole page
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            }
 
        }
    }
</script>
