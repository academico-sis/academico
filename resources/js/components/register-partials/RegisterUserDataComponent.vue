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

    <b-field label="Documento de identificacion">
        <div class="block">
            <b-radio v-model="idnumber_type" native-value="cedula">Cédula</b-radio>
            <b-radio v-model="idnumber_type" native-value="passport">Pasaporte</b-radio>
        </div>
    </b-field>

    <b-field v-if="idnumber_type == 'cedula'" label="Numero de cédula" :type="{ 'is-success': cedula_check == 1, 'is-danger': cedula_check == 0}">
        <b-input v-model="idnumber" minlength="10" maxlength="10" @input="checkCedula()"></b-input>
    </b-field>

    <b-field v-if="idnumber_type == 'passport'" label="Numero de pasaporte">
        <b-input v-model="idnumber" maxlength="40"></b-input>
    </b-field>

    <b-field label="Password">
        <b-input v-model="password" type="password" maxlength="30"></b-input>
    </b-field>

    <b-field label="Password">
        <b-input v-model="password" type="password" maxlength="30"></b-input>
    </b-field>

    <div class="field">
        <b-checkbox v-model="tc_consent">
            I accept the TandCs
        </b-checkbox>
    </div>

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
            idnumber_type: 'cedula',
            cedula_check: null,
            idnumber: null,
            address: null,
            phonenumber: null,
            tc_consent: false
        }
    },

    mounted() {
        
    },

    methods: {

        checkCedula() {
            const ced = this.idnumber;
            let [suma, mul, index] = [0, 1, ced.length];
            while (index--) {
            let num = ced[index] * mul;
            suma += num - (num > 9) * 9;
            mul = 1 << index % 2;
            }

            if ((suma % 10 === 0) && (suma > 0) && (ced.length == 10)) {
                this.cedula_check = 1
            } else {
                this.cedula_check = 0
            }
        }

    }
}
</script>