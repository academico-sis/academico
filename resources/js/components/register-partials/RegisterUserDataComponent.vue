<template>
<div>
    <b-field label="First Name">
        <b-input v-model="firstname" placeholder="Nombres"></b-input>
    </b-field>

    <b-field label="Last Name">
        <b-input v-model="lastname" placeholder="Apellidos"></b-input>
    </b-field>

    <b-field label="Email">
        <b-input type="email" v-model="email" placeholder="Correo electronico" maxlength="40"></b-input>
    </b-field>

    <b-field label="Numero de identificacion">
        <div class="block">
            <b-radio v-model="idnumber_type" native-value="cédula">Cédula</b-radio>
            <b-radio v-model="idnumber_type" native-value="pasaporte">Pasaporte</b-radio>
        </div>
    </b-field>

    <b-field :label="'Numero de '+idnumber_type">
        <b-input v-model="idnumber" maxlength="40"></b-input>
    </b-field>

    <b-field label="Password">
        <b-input v-model="password" type="password" maxlength="30"></b-input>
    </b-field>

</div>
</template>



<script>

export default {

    props: [],
    
    data () {
        return {
            errors: [],
            firstname: null,
            lastname: null,
            email: null,
            password: null,
            idnumber_type: 'cédula',
            idnumber: null,
            address: null,
            phonenumber: null,
            tc_consent: false
        }
    },

    mounted() {

    },

    methods: {
        submitRegisterForm() {
            axios
                .post('/register', {
                    
                })
                .then(response => {
                    
                })
                .catch(e => {
                    this.errors.push(e)
                })
        },
    }
}
</script>