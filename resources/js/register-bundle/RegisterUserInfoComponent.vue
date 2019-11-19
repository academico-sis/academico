<template>
<div>
<ValidationObserver ref="observer" v-slot="{ valid }">

    <b-field :label="$t('birthdate')">
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
 
    
    <b-field :label="$t('address')">
        <ValidationProvider name="dirección" rules="required" v-slot="{ errors }">
        <b-input v-model="formdata.address" :placeholder="$t('address')"></b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <p class="label">{{ $t('phonenumber') }}</p>

        <b-field :label="$t('phonenumber')+' #'+(index + 1)" grouped label-position="on-border" v-for="(number, index) in formdata.phonenumbers" v-bind:key="index">
            <ValidationProvider name="número de teléfono" rules="required" v-slot="{ errors }">
            <b-input v-model="number.number" :placeholder="$t('phonenumber')"></b-input>
            <p class="control">
                <b-button v-if="index > 0" @click="dropPhoneNumber(index)">{{ $t('delete') }}</b-button>
            </p>
             <p class="help is-danger">{{ errors[0] }}</p>
            </ValidationProvider>
        </b-field>
   
    <p>
        <b-button @click="addPhoneNumber()">{{ $t('add') }}</b-button>
        {{ $t('phonenumber_explainer') }}
    </p>


    <b-field :label="$t('profesion')">
        <ValidationProvider name="profesión" rules="required" v-slot="{ errors }">
        <b-input
            v-model="formdata.profession"
            :placeholder="$t('profesion_example')">
        </b-input>
        <p class="help is-danger">{{ errors[0] }}</p>
        </ValidationProvider>
    </b-field>

    <b-field :label="$t('institution')">
    <ValidationProvider name="institución" rules="required" v-slot="{ errors }">
        <b-input
            v-model="formdata.institution"
            :placeholder="$t('institution_example')">
        </b-input>
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