<template>
<div>
    <steps-component></steps-component>
</div>
</template>



<script>
import { StepsComponent } from "./register-partials/StepsComponent";
import { RegisterUserDataComponent } from "./register-partials/RegisterUserDataComponent";

export default {

    props: [],
    components: {
        
    },
    data () {
        return {
            errors: [],
        }
    },

    mounted() {

    },

    methods: {
    }
}
</script>