<template>
<div>
    <p v-for="phoneable in phoneables" v-bind:key="phoneable.id">
        <i class="fa fa-phone"></i>
        <span class="input-lg">{{ phoneable.phone_number }}</span>
        <button class="btn btn-danger" v-on:click="deletePhoneNumber(phoneable.id)"><i class="fa fa-trash"></i></button>
    </p>

    <div class="form-group">
        <input class="input-lg" id="new_number" name="new_number" type="numeric" v-model="number">
        <button class="btn" v-on:click="addPhoneNumber()"><i class="fa fa-plus"></i></button>
    </div>

</div>
</template>

<script>

    export default {
        
        props: ['student'],
        data () {
            return {
                phoneables: [ ],
                number: ''
            }
        },

        mounted() {
            this.getPhoneNumbers();
        },

        methods: {
            getPhoneNumbers()
            {
                axios.get('/student/' + this.student + '/phonenumbers')
                .then(response => {
                    this.phoneables = response.data
                    })
                {
                }
            },

            addPhoneNumber()
            {
                axios.post('/phonenumber',
                {
                    student: this.student,
                    number: this.number
                })
                .then(response => {
                        this.getPhoneNumbers();
                    })
                {
                }
            },

            deletePhoneNumber(phonenumber)
            {
                axios.delete('/phonenumber/' + phonenumber)
                    .then(response => {
                        this.getPhoneNumbers();
                    })
            }
        }
    }
</script>