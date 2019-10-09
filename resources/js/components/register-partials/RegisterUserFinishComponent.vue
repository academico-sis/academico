<template>
<div>
    <div style="padding-bottom: 3em;">
        <div class="container has-text-centered">

        <h2 class="subtitle">Comprobe sus datos una última vez. Cuando todo esta correcto, haz click en "confirmar la creación de la cuenta"</h2>
        
        <b-button class="is-large is-rounded is-success is-centered" @click="submitRegisterForm()">Confirmar la creación de la cuenta</b-button>
        
        </div>
    </div>

    <nav class="panel">
        <p class="panel-heading">Datos del estudiante</p>
        <a class="panel-block is-active">
            <span class="panel-icon">
            <i class="fas fa-user" aria-hidden="true"></i>
            </span>
            Nombre completo: {{ this.storeState.firstname }} {{ this.storeState.lastname }}
        </a>
        
        <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-at" aria-hidden="true"></i>
            </span>
            Correo electrónico: {{ this.storeState.email }}
        </a>

<!--         <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-calendar" aria-hidden="true"></i>
            </span>
            Fecha de nacimiento: {{ this.storeState.birthdate.getDate() }}
        </a> -->

        <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-passport" aria-hidden="true"></i>
            </span>
            Número de {{ this.storeState.idnumber_type }}: {{ this.storeState.idnumber }}
        </a>

        <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-home" aria-hidden="true"></i>
            </span>
            Dirección: {{ this.storeState.address }}
        </a>
        <label class="panel-block" v-for="(number, index) in this.storeState.phonenumbers" v-bind:key="index">
            <span class="panel-icon">
            <i class="fas fa-phone" aria-hidden="true"></i>
            </span>
            Teléfono #{{ index + 1 }}: {{ number.number }}
        </label>
        <div class="panel-block">
            <button class="button is-link is-outlined is-fullwidth is-warning" @click="goBackToStep(0)">
            Corregir la información
            </button>
        </div>
    </nav>

    <nav class="panel" v-for="(contact, contactindex) in this.storeState.contacts" v-bind:key="contactindex">
        <p class="panel-heading">Contacto #{{ contactindex+1}}</p>
        <a class="panel-block is-active">
            <span class="panel-icon">
            <i class="fas fa-user" aria-hidden="true"></i>
            </span>
            Nombre completo: {{ contact.firstname }} {{ contact.lastname }}
        </a>
        
        <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-at" aria-hidden="true"></i>
            </span>
            Correo electrónico: {{ contact.email }}
        </a>

        <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-passport" aria-hidden="true"></i>
            </span>
            Número de {{ contact.idnumber_type }}: {{ contact.idnumber }}
        </a>

        <a class="panel-block">
            <span class="panel-icon">
            <i class="fas fa-home" aria-hidden="true"></i>
            </span>
            Dirección: {{ contact.address }}
        </a>
        <label class="panel-block" v-for="(number, index) in contact.phonenumbers" v-bind:key="index">
            <span class="panel-icon">
            <i class="fas fa-phone" aria-hidden="true"></i>
            </span>
            Teléfono #{{ index + 1 }}: {{ number.number }}
        </label>
        <div class="panel-block">
            <button class="button is-link is-outlined is-fullwidth is-warning" @click="goBackToStep(2)">
            Corregir la información
            </button>
        </div>
    </nav>

<div class="container has-text-centered">
    <b-button class="is-large is-rounded is-success is-centered" @click="submitRegisterForm()">Confirmar la creación de la cuenta</b-button>
</div>
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
            storeState: store.state
        }
    },

    mounted() {

    },

    methods: {

        goBackToStep(step)
        {
            EventBus.$emit("goBackToStep", step)
        },

        submitRegisterForm() {
            axios
                .post('/register', {
                    data: this.storeState
                })
                .then(response => {
                    this.$buefy.toast.open({
                        message: 'La cuenta ha sido creada con éxito',
                        type: 'is-success',
                        position: 'is-bottom'
                    })
                    windows.location.href="/"
                })
                .catch(e => {
                    this.errors.push(e)
                    this.$buefy.toast.open({
                        message: 'Error al crear la cuenta',
                        type: 'is-danger',
                        position: 'is-bottom'
                    })
                })
        },
    }
}
</script>