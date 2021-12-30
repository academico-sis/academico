<template>
    <div>
        <ValidationObserver ref="observer" v-slot="{ valid }">
            <b-field :label="$t('birthdate')">
                <ValidationProvider v-slot="{ errors }" name="birthdate" rules="required">
                    <b-datepicker v-model="formdata.birthdate" :show-week-number="false" :placeholder="$t('Click to pick a date')" icon="calendar-today"></b-datepicker>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field :label="$t('address')">
                <ValidationProvider v-slot="{ errors }" name="addresse" rules="required">
                    <b-input v-model="formdata.address" :placeholder="$t('address')"></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <p class="label">{{ $t("phonenumber") }}</p>

            <b-field
                v-for="(number, index) in formdata.phonenumbers"
                :key="index"
                :label="`${$t('phonenumber')} #${index + 1}`"
                grouped
                label-position="on-border">
                <ValidationProvider v-slot="{ errors }" name="phone number" rules="required">
                    <b-input v-model="number.number" :placeholder="$t('phonenumber')"></b-input>
                    <p class="control">
                        <b-button v-if="index > 0" @click="dropPhoneNumber(index)">{{ $t("delete") }}</b-button>
                    </p>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <p>
                <b-button @click="addPhoneNumber()">{{ $t("add") }}</b-button>
                {{ $t("phonenumber_explainer") }}
            </p>

            <b-field :label="$t('profesion')">
                <ValidationProvider v-slot="{ errors }" name="profesion">
                    <b-input v-model="formdata.profession" :placeholder="$t('profesion_example')"></b-input>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-field :label="$t('institution')">
                <ValidationProvider v-slot="{ errors }" name="institution">
                    <b-autocomplete
                        v-model="formdata.institution"
                        :data="filteredinstitutions"
                        ref="autocomplete"
                        :allow-new=true
                        :open-on-focus=true
                        maxtags="1"
                        :placeholder="$t('institution_example')"
                        @select="option => selected = option">
                        <template #header>
                            <a @click="showAddInstitution">
                                <span> Add new... </span>
                            </a>
                        </template>
                        <template #empty>No results </template>
                    </b-autocomplete>
                    <p class="help is-danger">{{ errors[0] }}</p>
                </ValidationProvider>
            </b-field>

            <b-button type="is-primary" @click="validateBeforeSubmit()">{{ $t("next") }}</b-button>
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

    props: ['institutions'],

    data() {
        return {
            errors: [],
            institutionslist: this.institutions,
            filteredinstitutions: this.institutions,
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
        showAddInstitution() {
            this.$buefy.dialog.prompt({
                message: `Institution`,
                inputAttrs: {
                    placeholder: 'e.g. Université Lyon 3',
                    maxlength: 20,
                    value: this.name
                },
                confirmText: 'Add',
                onConfirm: (value) => {
                    this.institutionslist.push(value)
                    this.$refs.autocomplete.setSelected(value)
                }
            })
        },
        async validateBeforeSubmit() {
            const isValid = await this.$refs.observer.validate();

            if (isValid) {
                this.updateData();
            } else {
                this.$buefy.toast.open({
                    message: this.$t('The form is invalid, please check the fields marked in red and try again'),
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
