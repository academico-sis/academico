<template>
<div>

<ValidationObserver ref="observer" v-slot="{ valid }">

<article class="message" v-for="(contact, index) in contacts" v-bind:key="index">
  <div class="message-header">
    Contacto addicional #{{ index + 1}}
    <button class="delete" @click="dropContact(index)"></button>
  </div>
  <div class="message-body">
      <b-field label="Nombres">
            <ValidationProvider name="nombres" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.firstname" placeholder="Nombres"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Apellidos">
            <ValidationProvider name="apellidos" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.lastname" placeholder="Apellidos"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Correo electrónico">
            <ValidationProvider name="correo electrónico" rules="required|email" v-slot="{ errors }">
            <b-input type="email" v-model="contact.email" placeholder="Correo electrónico"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Documento de identificación">
            <div class="block">
                <b-radio v-model="contact.idnumber_type" native-value="cédula">Cédula</b-radio>
                <b-radio v-model="contact.idnumber_type" native-value="pasaporte">Pasaporte</b-radio>
            </div>
        </b-field>

        <b-field v-if="contact.idnumber_type == 'cédula'" label="Número de cédula">
            <ValidationProvider name="número de cédula" rules="required|cedula|length:10" v-slot="{ errors }">
            <b-input v-model="contact.idnumber"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field v-if="contact.idnumber_type == 'pasaporte'" label="Número de pasaporte">
            <ValidationProvider name="número de pasaporte" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.idnumber"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>

        <b-field label="Dirección">
            <ValidationProvider name="dirección" rules="required" v-slot="{ errors }">
            <b-input v-model="contact.address" placeholder="Dirección"></b-input>
            <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>


        <p class="label">Número de teléfono</p>

            <b-field :label="'Teléfono #'+(numberindex + 1)" grouped label-position="on-border" v-for="(number, numberindex) in contact.phonenumbers" v-bind:key="numberindex">
                <ValidationProvider name="número de teléfono" rules="required" v-slot="{ errors }">
                <b-input v-model="number.number" placeholder="Número de teléfono"></b-input>
                <p class="control">
                    <b-button v-if="numberindex > 0" @click="dropPhoneNumber(index, numberindex)">Eliminar</b-button>
                </p>
                <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>
    
        <p>
            <b-button @click="addPhoneNumber(index)">Agregar otro</b-button>
            Puede agregar otros número, si tiene.
        </p>

  </div>
</article>

<div style="text-align:center; padding-top: 2em;">
    
    <p style="padding-bottom: 2em;">Los estudiantes menores de edad tienen que agregar el contacto de su representante. Puede agregar varios contactos aqui (por ejemplo, padre y madre).</p>

    <p style="padding-bottom: 2em;">Si desea, puede agregar los datos de una persona que podemos contactar en caso de emergencia.</p>

    <p style="padding-bottom: 2em;">Si desea la factura con otros datos, por favor agregar un contacto en este espacio también.</p>

    <b-button type="is-info" @click="addContact()">Agregar contacto</b-button>

    <b-button type="is-primary" @click="validateBeforeSubmit()">Siguiente</b-button>
</div>


</ValidationObserver>

</div>
</template>



<script>
import { EventBus } from './eventBus.js';
import { store } from './store.js';
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
                idnumber_type: 'cédula',
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
                    message: 'El formulario no esta completo! Por favor verifique los campos en rojo.',
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