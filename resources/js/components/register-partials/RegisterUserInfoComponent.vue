<template>
<div>
    <b-field label="Select a date">
        <b-datepicker
            :show-week-number=false
            placeholder="Click to select..."
            icon="calendar-today"
            v-model="formdata.birthdate">
        </b-datepicker>
    </b-field>
 
    
    <b-field label="Address">
        <b-input v-model="formdata.address" placeholder="Direccion"></b-input>
    </b-field>

    <p class="label">Phone Numbers</p>

        <b-field :label="'Phone Number #'+(index + 1)" grouped label-position="on-border" v-for="(number, index) in formdata.phonenumbers" v-bind:key="index">
            <b-input v-model="number.number" placeholder="Number"></b-input>
            <p class="control">
                <b-button v-if="index > 0" @click="dropPhoneNumber(index)">Delete</b-button>
            </p>
        </b-field>
   
    <p>
        <b-button @click="addPhoneNumber()">Add</b-button>
        You may add several phone numbers to ensure we can reach you
    </p>


    <b-field label="Profesion">
        <b-autocomplete
            v-model="formdata.profession"
            :data="filteredDataArray"
            placeholder="e.g. jQuery">
        </b-autocomplete>
    </b-field>

    <b-field label="Institucion">
        <b-autocomplete
            v-model="formdata.institution"
            :data="filteredDataArray"
            placeholder="e.g. jQuery">
        </b-autocomplete>
    </b-field>

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

    methods: {
        addPhoneNumber() {
            this.formdata.phonenumbers.push({
                number: null,
            })
        },
        dropPhoneNumber(index) {
            this.formdata.phonenumbers.splice(index, 1); 
        },
        updateData() {
            store.updateInfoData(this.formdata)
            EventBus.$emit("moveToNextStep");
        }
    }
}
</script>