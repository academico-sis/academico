<template>
<div>
<b-steps
    size="is-small"
    v-model="activeStep"
    :animated="isAnimated"
    :has-navigation="hasNavigation">

    <b-step-item label="Datos del estudiante" :clickable="activeStep > 0">
        <register-user-data-component></register-user-data-component>
    </b-step-item>

    <b-step-item label="Informacion addicional" :clickable="activeStep > 1">
        <register-user-info-component></register-user-info-component>
    </b-step-item>

<!--     <b-step-item label="Photo" :clickable="activeStep > 2">
        <h1 class="title has-text-centered">Profile picture</h1>
        Lorem ipsum dolor sit amet.
    </b-step-item> -->

    <b-step-item label="Contactos addicionales" :clickable="activeStep > 2">
        <register-contacts-component></register-contacts-component>
    </b-step-item>

    <b-step-item label="Finalizacion" :clickable="activeStep > 3">
        <register-user-finish-component></register-user-finish-component>
    </b-step-item>

</b-steps>

</div>
</template>

<script>
import { store } from '../store.js';
import { EventBus } from '../eventBus.js';

export default {
    data() {
        return {
            storeState: store.state,
            activeStep: 0,
            isAnimated: true,
            hasNavigation: false,
            isStepsClickable: false,
        }
    },
        
    created() {
        EventBus.$on("moveToNextStep", () => {
            this.activeStep += 1
        });

        EventBus.$on("goBackToStep", (step) => {
            this.activeStep = step
        });
    },
    
    methods: {
        
    }
}
    
</script>

