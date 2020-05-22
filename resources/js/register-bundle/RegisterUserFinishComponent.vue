<template>
    <div>
        <div style="padding-bottom: 3em;">
            <div class="container has-text-centered">
                <h2 class="subtitle">{{ $t("finish_subtitle") }}</h2>

                <b-button
                    class="is-large is-rounded is-success is-centered"
                    :class="{ 'is-loading': formSubmitted == true }"
                    @click="submitRegisterForm()"
                    >{{ $t("finish_action") }}</b-button
                >
            </div>
        </div>

        <nav class="panel">
            <p class="panel-heading">{{ $t("student_data") }}</p>
            <a class="panel-block is-active">
                <span class="panel-icon">
                    <i class="fas fa-user" aria-hidden="true"></i>
                </span>
                {{ $t("full_name") }}: {{ this.storeState.firstname }}
                {{ this.storeState.lastname }}
            </a>

            <a class="panel-block">
                <span class="panel-icon">
                    <i class="fas fa-at" aria-hidden="true"></i>
                </span>
                {{ $t("email") }}: {{ this.storeState.email }}
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
                {{ $t("idnumber") }}: {{ this.storeState.idnumber }}
            </a>

            <a class="panel-block">
                <span class="panel-icon">
                    <i class="fas fa-home" aria-hidden="true"></i>
                </span>
                {{ $t("address") }}: {{ this.storeState.address }}
            </a>
            <label
                v-for="(number, index) in this.storeState.phonenumbers"
                :key="index"
                class="panel-block"
            >
                <span class="panel-icon">
                    <i class="fas fa-phone" aria-hidden="true"></i>
                </span>
                {{ $t("phonenumber") }} #{{ index + 1 }}: {{ number.number }}
            </label>
            <div class="panel-block">
                <button
                    class="button is-link is-outlined is-fullwidth is-warning"
                    @click="goBackToStep(0)"
                >
                    {{ $t("edit") }}
                </button>
            </div>
        </nav>

        <nav
            v-for="(contact, contactindex) in this.storeState.contacts"
            :key="contactindex"
            class="panel"
        >
            <p class="panel-heading">Contacto #{{ contactindex + 1 }}</p>
            <a class="panel-block is-active">
                <span class="panel-icon">
                    <i class="fas fa-user" aria-hidden="true"></i>
                </span>
                {{ $t("full_name") }}: {{ contact.firstname }}
                {{ contact.lastname }}
            </a>

            <a class="panel-block">
                <span class="panel-icon">
                    <i class="fas fa-at" aria-hidden="true"></i>
                </span>
                {{ $t("email") }}: {{ contact.email }}
            </a>

            <a class="panel-block">
                <span class="panel-icon">
                    <i class="fas fa-passport" aria-hidden="true"></i>
                </span>
                {{ $t("idnumber") }}: {{ contact.idnumber }}
            </a>

            <a class="panel-block">
                <span class="panel-icon">
                    <i class="fas fa-home" aria-hidden="true"></i>
                </span>
                {{ $t("address") }}: {{ contact.address }}
            </a>
            <label
                v-for="(number, index) in contact.phonenumbers"
                :key="index"
                class="panel-block"
            >
                <span class="panel-icon">
                    <i class="fas fa-phone" aria-hidden="true"></i>
                </span>
                {{ $t("phonenumber") }} #{{ index + 1 }}: {{ number.number }}
            </label>
            <div class="panel-block">
                <button
                    class="button is-link is-outlined is-fullwidth is-warning"
                    @click="goBackToStep(2)"
                >
                    {{ $t("edit") }}
                </button>
            </div>
        </nav>

        <div class="container has-text-centered">
            <b-button
                class="is-large is-rounded is-success is-centered"
                :class="{ 'is-loading': formSubmitted == true }"
                @click="submitRegisterForm()"
                >{{ $t("finish_action") }}</b-button
            >
        </div>
    </div>
</template>

<script>
import { store } from "./store.js";
import { EventBus } from "./eventBus.js";

export default {
    props: [],

    data() {
        return {
            errors: [],
            formSubmitted: false,
            storeState: store.state,
        };
    },

    mounted() {},

    methods: {
        goBackToStep(step) {
            EventBus.$emit("goBackToStep", step);
        },

        submitRegisterForm() {
            this.formSubmitted = true;

            const sleep = (milliseconds) => {
                return new Promise((resolve) =>
                    setTimeout(resolve, milliseconds)
                );
            };

            axios
                .post("/register", {
                    data: this.storeState,
                })
                .then((response) => {
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: "La cuenta ha sido creada con éxito",
                        type: "is-success",
                        position: "is-bottom",
                    });

                    sleep(2500).then(() => {
                        window.location.href = "/";
                    });
                })
                .catch((e) => {
                    this.errors.push(e);
                    this.$buefy.toast.open({
                        message: "Error al crear la cuenta",
                        type: "is-danger",
                        position: "is-bottom",
                    });

                    this.formSubmitted = false;
                });
        },
    },
};
</script>
