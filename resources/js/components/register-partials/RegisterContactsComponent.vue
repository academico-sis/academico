<template>
<div>

<ValidationObserver ref="observer" v-slot="{ valid }">

<article class="message" v-for="(contact, index) in contacts" v-bind:key="index">
  <div class="message-header">
    Contacto addicional #{{ index + 1}}
    <button class="delete" @click="dropContact(index)"></button>
  </div>
  <div class="message-body">
      <b-field label="First Name">
            <ValidationProvider name="nombres" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.firstname" placeholder="Nombres"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Last Name">
            <ValidationProvider name="appellidos" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.lastname" placeholder="Apellidos"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Email">
            <ValidationProvider name="correo electronico" rules="required|email" v-slot="{ errors }">
            <b-input type="email" v-model="contact.email" placeholder="Correo electronico"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Documento de identificacion">
            <div class="block">
                <b-radio v-model="contact.idnumber_type" native-value="cedula">Cédula</b-radio>
                <b-radio v-model="contact.idnumber_type" native-value="passport">Pasaporte</b-radio>
            </div>
        </b-field>

        <b-field v-if="contact.idnumber_type == 'cedula'" label="Numero de cédula">
            <ValidationProvider name="cédula" rules="required|cedula|length:10" v-slot="{ errors }">
            <b-input v-model="contact.idnumber"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field v-if="contact.idnumber_type == 'passport'" label="Numero de pasaporte">
            <ValidationProvider name="pasaporte" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.idnumber"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Address">
            <ValidationProvider name="direccion" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.address" placeholder="Direccion"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>


        <p class="label">Phone Numbers</p>

            <b-field :label="'Phone Number #'+(numberindex + 1)" grouped label-position="on-border" v-for="(number, numberindex) in contact.phonenumbers" v-bind:key="numberindex">
                <ValidationProvider name="telefono" rules="required" v-slot="{ errors }">
                <b-input v-model="number.number" placeholder="Number"></b-input>
                <p class="control">
                    <b-button v-if="numberindex > 0" @click="dropPhoneNumber(index, numberindex)">Delete</b-button>
                </p>
                <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>
    
        <p>
            <b-button @click="addPhoneNumber(index)">Add</b-button>
            You may add several phone numbers to ensure we can reach you
        </p>

  </div>
</article>


    <b-button type="is-info" @click="addContact()">Add contact</b-button>

    <b-button type="is-primary" @click="validateBeforeSubmit()">Siguiente</b-button>
</ValidationObserver>

</div>
</template>



<script>
import { EventBus } from '../../eventBus.js';
import { store } from '../../store.js';
import { ValidationObserver } from 'vee-validate';

export default {

    props: [],
    
    data () {
        return {
            errors: [],
            contacts: [],
        }
    },

    mounted() {
        
    },
        
    components: {
        ValidationObserver
    },

    methods: {
        addContact() {
            this.contacts.push({
                firstname: null,
                lastname: null,
                email: null,
                idnumber_type: 'cedula',
                cedula_check: null,
                idnumber: null,
                address: null,
                phonenumbers: [{number:null}],
            })
        },
        dropContact(index) {
            this.contacts.splice(index, 1); 
        },
        addPhoneNumber(index) {
            this.contacts[index].phonenumbers.push({
                number: null,
            })
        },
        dropPhoneNumber(contact, index) {
            this.contacts[contact].phonenumbers.splice(index, 1); 
        },
        
        async validateBeforeSubmit() {
            const isValid = await this.$refs.observer.validate();

            if (isValid) {
                this.updateData()
            } else {
                this.$buefy.toast.open({
                    message: 'Form is not valid! Please check the fields.',
                    type: 'is-danger',
                    position: 'is-bottom'
                })
            }
        },

        updateData() {
            store.updateContactsData(this.contacts)
            EventBus.$emit("moveToNextStep");
        },

    }
}
</script>