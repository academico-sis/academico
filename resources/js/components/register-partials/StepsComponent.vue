<template>
<b-steps
    v-model="activeStep"
    :animated="isAnimated"
    :has-navigation="hasNavigation"
    :icon-prev="prevIcon"
    :icon-next="nextIcon">
    <b-step-item label="Datos del estudiante" :clickable="isStepsClickable">
        <register-user-data-component></register-user-data-component>
    </b-step-item>

    <b-step-item label="Profile" :clickable="isStepsClickable" :type="{'is-success': isProfileSuccess}">
        <h1 class="title has-text-centered">Profile</h1>
        Lorem ipsum dolor sit amet.
    </b-step-item>

    <b-step-item :visible="showSocial" label="Social" :clickable="isStepsClickable">
        <h1 class="title has-text-centered">Social</h1>
        Lorem ipsum dolor sit amet.
    </b-step-item>

    <b-step-item label="Finish" :clickable="isStepsClickable" disabled>
        <h1 class="title has-text-centered">Finish</h1>
        Lorem ipsum dolor sit amet.
    </b-step-item>
</b-steps>
</template>

<script>
    export default {
        data() {
            return {
                activeStep: 0,
                showSocial: false,
                isAnimated: true,
                hasNavigation: true,
                prevIcon: 'chevron-left',
                nextIcon: 'chevron-right',
                isStepsClickable: false,
                isProfileSuccess: false
            }
        }
    }
</script>

