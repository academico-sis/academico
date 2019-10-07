<template>
<div>
<b-steps
    size="is-small"
    v-model="activeStep"
    :animated="isAnimated"
    :has-navigation="hasNavigation"
    :icon-prev="prevIcon"
    :icon-next="nextIcon">

    <b-step-item label="Datos del estudiante" :clickable="activeStep > 0">
        <register-user-data-component></register-user-data-component>
    </b-step-item>

    <b-step-item label="Informacion addicional" :clickable="activeStep > 1">
        <register-user-info-component></register-user-info-component>
    </b-step-item>

    <b-step-item label="Photo" :clickable="activeStep > 2">
        <h1 class="title has-text-centered">Profile picture</h1>
        Lorem ipsum dolor sit amet.
    </b-step-item>

    <b-step-item label="Contactos addicionales" :clickable="activeStep > 3">
        <register-contacts-component></register-contacts-component>
    </b-step-item>

    <b-step-item label="Finalizacion" :clickable="activeStep > 4">
        <register-user-finish-component></register-user-finish-component>
    </b-step-item>

</b-steps>

<b-button type="is-primary" v-if="activeStep < 4" @click="activeStep += 1">Siguiente</b-button>
</div>
</template>

<script>
    export default {
        data() {
            return {
                activeStep: 0,
                isAnimated: true,
                hasNavigation: false,
                isStepsClickable: false,
            }
        },
        
created() {
    window.addEventListener('beforeunload', (event) => {
        event.returnValue = `Are you sure you want to leave?`;
        });
  },
    methods: {
    handler: function handler(event) {
        alert('')
    }
  }
    }
    
</script>

