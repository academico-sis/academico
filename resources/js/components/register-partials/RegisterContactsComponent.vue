<template>
<div>
    <b-field label="Address">
        <b-input v-model="address" placeholder="Direccion"></b-input>
    </b-field>

    <b-field label="Fecha de nacimiento">
        <b-input v-model="lastname" placeholder="Apellidos"></b-input>
    </b-field>

    <b-field label="Profesion">
        <b-taginput
            v-model="tags"
            :data="filteredTags"
            autocomplete
            maxtags="1"
            :allow-new=true
            :open-on-focus="openOnFocus"
            field="user.first_name"
            icon="label"
            placeholder="Add a tag"
            @typing="getFilteredTags">
        </b-taginput>
    </b-field>

    <b-field label="Institucion">
        <b-taginput
            v-model="tags"
            :data="filteredTags"
            autocomplete
            maxtags="1"
            :allow-new=true
            :open-on-focus="openOnFocus"
            field="user.first_name"
            icon="label"
            placeholder="Add a tag"
            @typing="getFilteredTags">
        </b-taginput>
    </b-field>


</div>
</template>



<script>

export default {

    props: [],
    
    data () {
        return {
            errors: [],
            firstname: null,
            lastname: null,
            email: null,
            password: null,
            idnumber_type: 'cédula',
            idnumber: null,
            address: null,
            phonenumber: null,
            tc_consent: false
        }
    },

    mounted() {

    },

    methods: {
        submitRegisterForm() {
            axios
                .post('/register', {
                    
                })
                .then(response => {
                    
                })
                .catch(e => {
                    this.errors.push(e)
                })
        },
    }
}
</script>