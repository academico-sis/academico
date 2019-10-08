<template>
<div>
    <b-field label="Address">
        <b-input v-model="formdata.address" placeholder="Direccion"></b-input>
    </b-field>

    <b-field label="Select a date">
        <b-datepicker
            :show-week-number=false
            placeholder="Click to select..."
            icon="calendar-today"
            v-model="formdata.birthdate">
        </b-datepicker>
    </b-field>

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
                institution: null
            }
        }
    },

    mounted() {

    },

    methods: {
        updateData() {
            store.updateInfoData(this.formdata)
            EventBus.$emit("moveToNextStep");
        }
    }
}
</script>