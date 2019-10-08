<template>
<div>
    Please check your data one last time and click submit when everything is correct.
    If you want to update a field, you may go back to the previous step using the navigation (arrow)
    
    <div class="box">
        Estudiante
        {{ this.storeState.firstname }}
        {{ this.storeState.lastname }}
        {{ this.storeState.birthdate }}
    </div>

    <b-button class="is-large is-rounded is-success" @click="submitRegisterForm()">Confirmar la creacion del estudiante</b-button>

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
        submitRegisterForm() {
            axios
                .post('/register', {
                    data: this.storeState
                })
                .then(response => {
                    windows.location.href="/"
                })
                .catch(e => {
                    this.errors.push(e)
                })
        },
    }
}
</script>