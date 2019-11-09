<template>
<div>
<ValidationObserver ref="observer" v-slot="{ valid }">

    <b-field label="Fecha de nacimiento">
        <ValidationProvider name="fecha de nacimiento" rules="required" v-slot="{ errors }">
        <b-datepicker
            :show-week-number=false
            placeholder="Haz click para seleccionar"
            icon="calendar-today"
            v-model="formdata.birthdate">
        </b-datepicker>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>
 
    
    <b-field label="Dirección">
        <ValidationProvider name="dirección" rules="required" v-slot="{ errors }">
        <b-input v-model="formdata.address" placeholder="Dirección"></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <p class="label">Número de teléfono</p>

        <b-field :label="'Teléfono #'+(index + 1)" grouped label-position="on-border" v-for="(number, index) in formdata.phonenumbers" v-bind:key="index">
            <ValidationProvider name="número de teléfono" rules="required" v-slot="{ errors }">
            <b-input v-model="number.number" placeholder="Number"></b-input>
            <p class="control">
                <b-button v-if="index > 0" @click="dropPhoneNumber(index)">Eliminar</b-button>
            </p>
             <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>
   
    <p>
        <b-button @click="addPhoneNumber()">Agregar otro</b-button>
        Si tiene otros números, los puede agregar también.
    </p>


    <b-field label="Profesión">
        <ValidationProvider name="profesión" rules="required" v-slot="{ errors }">
        <b-input
            v-model="formdata.profession"
            placeholder="e.g. estudiante, médico...">
        </b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field label="Institución">
    <ValidationProvider name="institución" rules="required" v-slot="{ errors }">
        <b-input
            v-model="formdata.institution"
            placeholder="e.g. Universidad de Cuenca">
        </b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-button type="is-primary" @click="validateBeforeSubmit()">Siguiente</b-button>

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
                address: null,
                birthdate: null,
                profession: null,
                institution: null,
                phonenumbers: []
            }
        }
    },

    mounted() {
        this.addPhoneNumber()
    },

    components: {
        ValidationObserver
    },

    methods: {
        addPhoneNumber() {
            this.formdata.phonenumbers.push({
                number: null,
            })
        },
        dropPhoneNumber(index) {
            this.formdata.phonenumbers.splice(index, 1); 
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
            store.updateInfoData(this.formdata)
            EventBus.$emit("moveToNextStep");
        }
    }
}
</script>