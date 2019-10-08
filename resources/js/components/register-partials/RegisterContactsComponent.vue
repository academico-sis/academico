<template>
<div>


<article class="message" v-for="(contact, index) in contacts" v-bind:key="index">
  <div class="message-header">
    Contacto addicional #{{ index + 1}}
    <button class="delete" @click="dropContact(index)"></button>
  </div>
  <div class="message-body">
      <b-field label="First Name">
            <b-input v-model="contact.firstname" placeholder="Nombres"></b-input>
        </b-field>

        <b-field label="Last Name">
            <b-input v-model="contact.lastname" placeholder="Apellidos"></b-input>
        </b-field>

        <b-field label="Email">
            <b-input type="email" v-model="contact.email" placeholder="Correo electronico"></b-input>
        </b-field>

        <b-field label="Documento de identificacion">
            <div class="block">
                <b-radio v-model="contact.idnumber_type" native-value="cedula">Cédula</b-radio>
                <b-radio v-model="contact.idnumber_type" native-value="passport">Pasaporte</b-radio>
            </div>
        </b-field>

        <b-field v-if="contact.idnumber_type == 'cedula'" label="Numero de cédula" :type="{ 'is-success': contact.cedula_check == 1, 'is-danger': contact.cedula_check == 0}">
            <b-input v-model="contact.idnumber" minlength="10" maxlength="10" @input="checkCedula(contact)"></b-input>
        </b-field>

        <b-field v-if="contact.idnumber_type == 'passport'" label="Numero de pasaporte">
            <b-input v-model="contact.idnumber" maxlength="12"></b-input>
        </b-field>

        <b-field label="Address">
            <b-input v-model="contact.address" placeholder="Direccion"></b-input>
        </b-field>


        <p class="label">Phone Numbers</p>

            <b-field :label="'Phone Number #'+(numberindex + 1)" grouped label-position="on-border" v-for="(number, numberindex) in contact.phonenumbers" v-bind:key="numberindex">
                <b-input v-model="number.number" placeholder="Number"></b-input>
                <p class="control">
                    <b-button v-if="numberindex > 0" @click="dropPhoneNumber(index, numberindex)">Delete</b-button>
                </p>
            </b-field>
    
        <p>
            <b-button @click="addPhoneNumber(index)">Add</b-button>
            You may add several phone numbers to ensure we can reach you
        </p>

  </div>
</article>


    <b-button type="is-primary" @click="addContact()">Add contact</b-button>

    <b-button type="is-primary" @click="updateData()">Siguiente</b-button>

</div>
</template>



<script>
import { EventBus } from '../../eventBus.js';
import { store } from '../../store.js';

export default {

    props: [],
    
    data () {
        return {
            errors: [],
            contacts: [],
        }
    },

    mounted() {
        this.addPhoneNumber()
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
                phonenumbers: [],
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
        updateData() {
            store.updateContactsData(this.contacts)
            EventBus.$emit("moveToNextStep");
        },
        checkCedula(contact)
        {
            contact.cedula_check = store.checkCedula(contact.idnumber)
        }
    }
}
</script>