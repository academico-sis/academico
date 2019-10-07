<template>
<div>
    Please check your data one last time and click submit when everything is correct.

        <b-button class="is-large is-rounded is-success" @click="">Confirmar la creacion del estudiante</b-button>

</div>
</template>



<script>

export default {

    props: [],
    
    data () {
        return {
            errors: [],
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