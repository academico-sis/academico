<template>
    <div>
        <ValidationObserver ref="observer" v-slot="{ valid }">
            <article
                v-for="(contact, index) in contacts"
                :key="index"
                class="message"
            >
                <div class="message-header">
                    {{ $t("contact") }} #{{ index + 1 }}
                    <button class="delete" @click="dropContact(index)"></button>
                </div>
                <div class="message-body">
                    <b-field :label="$t('firstname')">
                        <ValidationProvider
                            v-slot="{ errors }"
                            name="nombres"
                            rules="required"
                        >
                            <b-input
                                v-model="contact.firstname"
                                :placeholder="$t('firstname')"
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
                                v-model="contact.lastname"
                                :placeholder="$t('lastname')"
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
                                v-model="contact.email"
                                type="email"
                                :placeholder="$t('email')"
                            ></b-input>
                            <p class="help is-danger">{{ errors[0] }}</p>
                        </ValidationProvider>
                    </b-field>

                    <b-field
                        label="Número de pasaporte"
                    >
                        <ValidationProvider
                            v-slot="{ errors }"
                            name="número de pasaporte"
                            rules="required"
                        >
                            <b-input v-model="contact.idnumber"></b-input>
                            <p class="help is-danger">{{ errors[0] }}</p>
                        </ValidationProvider>
                    </b-field>

                    <b-field :label="$t('address')">
                        <ValidationProvider
                            v-slot="{ errors }"
                            name="dirección"
                            rules="required"
                        >
                            <b-input
                                v-model="contact.address"
                                :placeholder="$t('address')"
                            ></b-input>
                            <p class="help is-danger">{{ errors[0] }}</p>
                        </ValidationProvider>
                    </b-field>

                    <p class="label">{{ $t("phonenumber") }}</p>

                    <b-field
                        v-for="(number, numberindex) in contact.phonenumbers"
                        :key="numberindex"
                        :label="'Teléfono #' + (numberindex + 1)"
                        grouped
                        label-position="on-border"
                    >
                        <ValidationProvider
                            v-slot="{ errors }"
                            name="número de teléfono"
                            rules="required"
                        >
                            <b-input
                                v-model="number.number"
                                :placeholder="$t('phonenumber')"
                            ></b-input>
                            <p class="control">
                                <b-button
                                    v-if="numberindex > 0"
                                    @click="dropPhoneNumber(index, numberindex)"
                                    >{{ $t("delete") }}</b-button
                                >
                            </p>
                            <p class="help is-danger">{{ errors[0] }}</p>
                        </ValidationProvider>
                    </b-field>

                    <p>
                        <b-button @click="addPhoneNumber(index)">{{
                            $t("add")
                        }}</b-button>
                        {{ $t("phonenumber_explainer") }}
                    </p>
                </div>
            </article>

            <div style="text-align: center; padding-top: 2em;">
                <p style="padding-bottom: 2em;">
                    {{ $t("contact_explainer1") }}
                </p>

                <p style="padding-bottom: 2em;">
                    {{ $t("contact_explainer2") }}
                </p>

                <p style="padding-bottom: 2em;">
                    {{ $t("contact_explainer3") }}
                </p>

                <b-button type="is-info" @click="addContact()">{{
                    $t("add")
                }}</b-button>

                <b-button type="is-primary" @click="validateBeforeSubmit()">{{
                    $t("next")
                }}</b-button>
            </div>
        </ValidationObserver>
    </div>
</template>

<script>
import { EventBus } from "./eventBus.js";
import { store } from "./store.js";
import { ValidationObserver } from "vee-validate";

export default {
    components: {
        ValidationObserver,
    },

    props: [],

    data() {
        return {
            errors: [],
            contacts: [],
        };
    },

    mounted() {},

    methods: {
        addContact() {
            this.contacts.push({
                firstname: null,
                lastname: null,
                email: null,
                idnumber: null,
                address: null,
                phonenumbers: [{ number: null }],
            });
        },
        dropContact(index) {
            this.contacts.splice(index, 1);
        },
        addPhoneNumber(index) {
            this.contacts[index].phonenumbers.push({
                number: null,
            });
        },
        dropPhoneNumber(contact, index) {
            this.contacts[contact].phonenumbers.splice(index, 1);
        },

        async validateBeforeSubmit() {
            const isValid = await this.$refs.observer.validate();

            if (isValid) {
                this.updateData();
            } else {
                this.$buefy.toast.open({
                    message:
                        "El formulario no esta completo! Por favor verifique los campos en rojo.",
                    type: "is-danger",
                    position: "is-bottom",
                });
            }
        },

        updateData() {
            store.updateContactsData(this.contacts);
            EventBus.$emit("moveToNextStep");
        },
    },
};
</script>
