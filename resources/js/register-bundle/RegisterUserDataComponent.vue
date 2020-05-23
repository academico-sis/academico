<template>
    <div>
        <ValidationObserver ref="observer" v-slot="{ valid }">
            <b-field :label="$t('firstname')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="nombres"
                    rules="required"
                >
                    <b-input
                        v-model="formdata.firstname"
                        :placeholder="$t('firstname')"
                        required
                    ></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field :label="$t('lastname')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="apellidos"
                    rules="required"
                >
                    <b-input
                        v-model="formdata.lastname"
                        :placeholder="$t('lastname')"
                        required
                    ></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field :label="$t('email')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="correo electrónico"
                    rules="required|email"
                >
                    <b-input
                        v-model="formdata.email"
                        type="email"
                        :placeholder="$t('email')"
                        required
                    ></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field
                v-if="formdata.idnumber_type == 'passport'"
                rules="required"
                :label="$t('passport_number')"
            >
                <ValidationProvider
                    v-slot="{ errors }"
                    name="número de pasaporte"
                    rules="required"
                >
                    <b-input
                        v-model="formdata.idnumber"
                        maxlength="12"
                    ></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field :label="$t('password')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="contraseña"
                    rules="required|min:6"
                >
                    <b-input
                        v-model="formdata.password"
                        type="password"
                        password-reveal
                    ></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-button type="is-primary" @click="validateBeforeSubmit()">{{
                $t("next")
            }}</b-button>
        </ValidationObserver>
    </div>
</template>

<script>
import { store } from "./store.js";
import { EventBus } from "./eventBus.js";
import { ValidationObserver } from "vee-validate";

export default {
    components: {
        ValidationObserver,
    },
    props: [],

    data() {
        return {
            errors: [],
            formdata: {
                firstname: null,
                lastname: null,
                email: null,
                password: null,
                idnumber_type: "passport",
                idnumber: null,
                address: null,
                phonenumber: null,
                tc_consent: false,
            },
        };
    },

    mounted() {},

    methods: {
        async validateBeforeSubmit() {
            const isValid = await this.$refs.observer.validate();

            if (isValid) {
                this.checkEmailUnicity();
                //this.updateData()
            } else {
                this.$buefy.toast.open({
                    message:
                        "El formulario no esta completo! Por favor verifique los campos en rojo.",
                    type: "is-danger",
                    position: "is-bottom",
                });
            }
        },

        async checkEmailUnicity() {
            const isValid = await axios
                .post("/api/checkemail", {
                    email: this.formdata.email,
                })
                .then((response) => {
                    if (response.status == 204) {
                        return true;
                    }
                })
                .catch((err) => {
                    if (err.status == 409) {
                        return false;
                    }
                });

            if (isValid) {
                this.updateData();
            } else {
                this.$buefy.toast.open({
                    message:
                        "Ya existe una cuenta registrada con este correo electrónico",
                    type: "is-danger",
                    position: "is-bottom",
                });
            }
        },

        updateData() {
            store.updateUserData(this.formdata);
            EventBus.$emit("moveToNextStep");
        },
    },
};
</script>
