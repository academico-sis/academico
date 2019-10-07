<template>
<div>
    <b-field label="First Name">
        <b-input v-model="firstname" placeholder="Nombres" v-validate="{ required: true, is: password }" /></b-input>
    </b-field>

    <b-field label="Last Name">
        <b-input v-model="lastname" placeholder="Apellidos"></b-input>
    </b-field>

    <b-field label="Email">
        <b-input type="email" v-model="email" placeholder="Correo electronico" maxlength="40"></b-input>
    </b-field>

    <b-field label="Documento de identificacion">
        <div class="block">
            <b-radio v-model="idnumber_type" native-value="cédula">Cédula</b-radio>
            <b-radio v-model="idnumber_type" native-value="pasaporte">Pasaporte</b-radio>
        </div>
    </b-field>

    <b-field :label="'Numero de '+idnumber_type" type="is-danger" message="Cedula incorrecta">
        <b-input v-model="idnumber" maxlength="40" @input="checkCedula()"></b-input>
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
            idnumber_type: 'cédula',
            idnumber: null,
            address: null,
            phonenumber: null,
            tc_consent: false
        }
    },

    mounted() {
        this.checkCedula()
    },

    methods: {

        checkCedula() {
            const ced = this.idnumber;

            let [suma, mul, chars] = [0, 1, ced.length];
            for (let index = 0; index < chars; index += 1) {
            let num = ced[index] * mul;
            suma += num - (num > 9) * 9;
            mul = 1 << index % 2;
            }

            if ((suma % 10 === 0) && (suma > 0)) {
                console.log('Cedula valida');
            } else {
                console.error('Cedula invalida');
            }
        }

    }
}
</script>