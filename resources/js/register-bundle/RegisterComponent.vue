<template>
    <div>
        <div class="is-pulled-right">
            <b-dropdown v-model="$i18n.locale" hoverable aria-role="list">
                <button slot="trigger" class="button is-info">
                    <span>{{ $t("language") }}</span>
                    <b-icon icon="menu-down"></b-icon>
                </button>

                <b-dropdown-item
                    v-for="(lang, i) in this.langs"
                    :key="`Lang${i}`"
                    :value="lang"
                    aria-role="listitem"
                    >{{ lang }}</b-dropdown-item
                >
            </b-dropdown>
        </div>

        <b-steps
            v-model="activeStep"
            size="is-small"
            :animated="isAnimated"
            :has-navigation="hasNavigation"
        >
            <b-step-item :label="$t('step1')" :clickable="activeStep > 0">
                <register-user-data-component></register-user-data-component>
            </b-step-item>

            <b-step-item :label="$t('step2')" :clickable="activeStep > 1">
                <register-user-info-component :institutions="institutions"></register-user-info-component>
            </b-step-item>

            <b-step-item v-if="pictureallowed" :label="$t('step3')" :clickable="activeStep > 2">
                <register-user-picture-component :picturemandatory="picturemandatory"></register-user-picture-component>
            </b-step-item>

            <b-step-item :label="$t('step4')" :clickable="activeStep > 3">
                <register-contacts-component></register-contacts-component>
            </b-step-item>

            <b-step-item :label="$t('step5')" :clickable="activeStep > 4">
                <register-user-finish-component></register-user-finish-component>
            </b-step-item>
        </b-steps>
    </div>
</template>

<script>
import Vue from "vue";
import Buefy from "buefy";
import "buefy/dist/buefy.css";

Vue.use(Buefy);

import { store } from "./store.js";
import { EventBus } from "./eventBus.js";

export default {
    props: ['institutions', 'langs', 'pictureallowed', 'picturemandatory'],
    data() {
        return {
            storeState: store.state,
            activeStep: 2,
            isAnimated: true,
            hasNavigation: false,
            isStepsClickable: false,
        };
    },

    created() {
        EventBus.$on("moveToNextStep", () => {
            this.activeStep += 1;
        });

        EventBus.$on("goBackToStep", step => {
            this.activeStep = step;
        });
    },

    methods: {},
};
</script>
