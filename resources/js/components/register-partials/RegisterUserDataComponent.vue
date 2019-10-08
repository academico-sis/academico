<template>
<div>
    <b-field label="First Name">
        <b-input v-model="formdata.firstname" placeholder="Nombres"></b-input>
    </b-field>

    <b-field label="Last Name">
        <b-input v-model="formdata.lastname" placeholder="Apellidos"></b-input>
    </b-field>

    <b-field label="Email">
        <b-input type="email" v-model="formdata.email" placeholder="Correo electronico"></b-input>
    </b-field>

    <b-field label="Documento de identificacion">
        <div class="block">
            <b-radio v-model="formdata.idnumber_type" native-value="cedula">Cédula</b-radio>
            <b-radio v-model="formdata.idnumber_type" native-value="passport">Pasaporte</b-radio>
        </div>
    </b-field>

    <b-field v-if="formdata.idnumber_type == 'cedula'" label="Numero de cédula" :type="{ 'is-success': formdata.cedula_check == 1, 'is-danger': formdata.cedula_check == 0}">
        <b-input v-model="formdata.idnumber" minlength="10" maxlength="10" @input="checkCedula()"></b-input>
    </b-field>

    <b-field v-if="formdata.idnumber_type == 'passport'" label="Numero de pasaporte">
        <b-input v-model="formdata.idnumber" maxlength="12"></b-input>
    </b-field>

    <b-field label="Password">
        <b-input v-model="formdata.password" type="password" password-reveal minlength="6"></b-input>
    </b-field>

    <div class="field">
        <b-checkbox v-model="formdata.tc_consent">
            I accept the TandCs
        </b-checkbox>
    </div>

    <b-button type="is-primary" @click="updateData()">Siguiente</b-button>

</div>
</template>



<script>
import { store } from '../../store.js';
import { EventBus } from '../../eventBus.js';

export default {

    props: [],
    
    data () {
        return {
            errors: [],
            formdata: {
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
        }
    },

    mounted() {
        
    },

    methods: {
        checkCedula()
        {
            this.formdata.cedula_check = store.checkCedula(this.formdata.idnumber)
        },

        updateData() {
            store.updateUserData(this.formdata)
            EventBus.$emit("moveToNextStep");
        }

    }
}
</script>