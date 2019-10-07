<template>
<div>
    <b-field label="Address">
        <b-input v-model="address" placeholder="Direccion"></b-input>
    </b-field>

    <b-field label="Select a date">
        <b-datepicker
            :show-week-number=false
            placeholder="Click to select..."
            icon="calendar-today">
        </b-datepicker>
    </b-field>

    <b-field label="Profesion">
        <b-autocomplete
            v-model="profession"
            :data="filteredDataArray"
            placeholder="e.g. jQuery">
        </b-autocomplete>
    </b-field>

    <b-field label="Institucion">
        <b-autocomplete
            v-model="institution"
            :data="filteredDataArray"
            placeholder="e.g. jQuery">
        </b-autocomplete>
    </b-field>

</div>
</template>



<script>

export default {

    props: [],
    
    data () {
        return {
            errors: [],
            profession: null
        }
    },

    mounted() {

    },

    methods: {
        
    }
}
</script>