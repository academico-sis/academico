<template>
<div>

<ValidationObserver ref="observer" v-slot="{ valid }">
    <b-field :label="$t('firstname')">
    <ValidationProvider name="nombres" rules="required" v-slot="{ errors }">
    <b-input v-model="formdata.firstname" :placeholder="$t('firstname')" required></b-input>
    <p class="help is-danger">{{ errors[0] }}</p>
    </ValidationProvider>
    </b-field>

    <b-field :label="$t('lastname')">
        <ValidationProvider name="apellidos" rules="required" v-slot="{ errors }">
        <b-input v-model="formdata.lastname" :placeholder="$t('lastname')" required></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field :label="$t('email')">
        <ValidationProvider name="correo electrónico" rules="required|email" v-slot="{ errors }">
        <b-input type="email" v-model="formdata.email" :placeholder="$t('email')" required></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field :label="$t('iddocument')">
        <div class="block">
            <b-radio v-model="formdata.idnumber_type" native-value="cedula">{{ $t('cedula') }}</b-radio>
            <b-radio v-model="formdata.idnumber_type" native-value="passport">{{ $t('passport') }}</b-radio>
        </div>
    </b-field>

    <b-field v-if="formdata.idnumber_type == 'cedula'" :label="$t('cedula_number')">
        <ValidationProvider name="número de cédula" rules="required|cedula|length:10" v-slot="{ errors }">
        <b-input v-model="formdata.idnumber"></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field v-if="formdata.idnumber_type == 'passport'" rules="required" :label="$t('passport_number')">
        <ValidationProvider name="número de pasaporte" rules="required" v-slot="{ errors }">
        <b-input v-model="formdata.idnumber" maxlength="12"></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field :label="$t('password')">
        <ValidationProvider name="contraseña" rules="required|min:6" v-slot="{ errors }">
        <b-input v-model="formdata.password" type="password" password-reveal></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-button type="is-primary" @click="validateBeforeSubmit()">{{ $t('next') }}</b-button>

</ValidationObserver>
</div>
</template>



<script>
import { store } from './store.js';
import { EventBus } from './eventBus.js';
import { ValidationObserver } from 'vee-validate';

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
                idnumber: null,
                address: null,
                phonenumber: null,
                tc_consent: false,
            },
        }
    },

    mounted() {
        
    },
    
    components: {
        ValidationObserver
    },

    methods: {

        async validateBeforeSubmit() {
            const isValid = await this.$refs.observer.validate();

            if (isValid) {
                this.checkEmailUnicity()
                //this.updateData()
            } else {
                this.$buefy.toast.open({
                    message: 'El formulario no esta completo! Por favor verifique los campos en rojo.',
                    type: 'is-danger',
                    position: 'is-bottom'
                })
            }
        },

        async checkEmailUnicity() {

            const isValid = await axios.post('/api/checkemail', {
                email: this.formdata.email
            })
            .then(response => {
                if (response.status == 204) {
                    return true
                }
            })
            .catch((err) => {
                if(err.status == 409) {
                    return false
                };
            });

            if (isValid) {
                this.updateData()
            } else {
                this.$buefy.toast.open({
                    message: 'Ya existe una cuenta registrada con este correo electrónico',
                    type: 'is-danger',
                    position: 'is-bottom'
                })
            }

        },

        updateData() {
            store.updateUserData(this.formdata)
            EventBus.$emit("moveToNextStep");
        }

    },

}
</script>