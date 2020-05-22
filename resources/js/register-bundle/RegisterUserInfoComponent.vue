<template>
    <div>
        <ValidationObserver ref="observer" v-slot="{ valid }">
            <b-field :label="$t('birthdate')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="fecha de nacimiento"
                    rules="required"
                >
                    <b-datepicker
                        v-model="formdata.birthdate"
                        :show-week-number="false"
                        placeholder="Haz click para seleccionar"
                        icon="calendar-today"
                    >
                    </b-datepicker>
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
                        v-model="formdata.address"
                        :placeholder="$t('address')"
                    ></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <p class="label">{{ $t("phonenumber") }}</p>

            <b-field
                v-for="(number, index) in formdata.phonenumbers"
                :key="index"
                :label="$t('phonenumber') + ' #' + (index + 1)"
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
                            v-if="index > 0"
                            @click="dropPhoneNumber(index)"
                            >{{ $t("delete") }}</b-button
                        >
                    </p>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <p>
                <b-button @click="addPhoneNumber()">{{ $t("add") }}</b-button>
                {{ $t("phonenumber_explainer") }}
            </p>

            <b-field :label="$t('profesion')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="profesión"
                    rules="required"
                >
                    <b-input
                        v-model="formdata.profession"
                        :placeholder="$t('profesion_example')"
                    >
                    </b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field :label="$t('institution')">
                <ValidationProvider
                    v-slot="{ errors }"
                    name="institución"
                    rules="required"
                >
                    <b-input
                        v-model="formdata.institution"
                        :placeholder="$t('institution_example')"
                    >
                    </b-input>
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
                address: null,
                birthdate: null,
                profession: null,
                institution: null,
                phonenumbers: [],
            },
        };
    },

    mounted() {
        this.addPhoneNumber();
    },

    methods: {
        addPhoneNumber() {
            this.formdata.phonenumbers.push({
                number: null,
            });
        },
        dropPhoneNumber(index) {
            this.formdata.phonenumbers.splice(index, 1);
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
            store.updateInfoData(this.formdata);
            EventBus.$emit("moveToNextStep");
        },
    },
};
</script>
