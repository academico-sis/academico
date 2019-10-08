<template>
<div>
<ValidationObserver ref="observer" v-slot="{ valid }">

    <b-field label="Select a date">
        <ValidationProvider name="fecha de nacimiento" rules="required" v-slot="{ errors }">
        <b-datepicker
            :show-week-number=false
            placeholder="Click to select..."
            icon="calendar-today"
            v-model="formdata.birthdate">
        </b-datepicker>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>
 
    
    <b-field label="Address">
        <ValidationProvider name="direccion" rules="required" v-slot="{ errors }">
        <b-input v-model="formdata.address" placeholder="Direccion"></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <p class="label">Phone Numbers</p>

        <b-field :label="'Phone Number #'+(index + 1)" grouped label-position="on-border" v-for="(number, index) in formdata.phonenumbers" v-bind:key="index">
            <ValidationProvider name="telefono" rules="required" v-slot="{ errors }">
            <b-input v-model="number.number" placeholder="Number"></b-input>
            <p class="control">
                <b-button v-if="index > 0" @click="dropPhoneNumber(index)">Delete</b-button>
            </p>
             <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>
   
    <p>
        <b-button @click="addPhoneNumber()">Add</b-button>
        You may add several phone numbers to ensure we can reach you
    </p>


    <b-field label="Profesion">
        <ValidationProvider name="profesion" rules="required" v-slot="{ errors }">
        <b-autocomplete
            v-model="formdata.profession"
            :data="filteredDataArray"
            placeholder="e.g. jQuery">
        </b-autocomplete>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field label="Institucion">
    <ValidationProvider name="institucion" rules="required" v-slot="{ errors }">
        <b-autocomplete
            v-model="formdata.institution"
            :data="filteredDataArray"
            placeholder="e.g. jQuery">
        </b-autocomplete>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-button type="is-primary" @click="validateBeforeSubmit()">Siguiente</b-button>

</ValidationObserver>

</div>
</template>



<script>
import { store } from '../../store.js';
import { EventBus } from '../../eventBus.js';
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
                    message: 'Form is not valid! Please check the fields.',
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