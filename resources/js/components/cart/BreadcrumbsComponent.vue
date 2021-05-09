<template>
    <ol class="breadcrumb bg-transparent">
        <li v-if="step >= 1" class="breadcrumb-item">
            <a @click="updateStep(1)">{{ $t("Products") }}</a>
        </li>
        <li v-if="step >= 2" class="breadcrumb-item">
            <a @click="updateStep(2)">{{ $t("Invoice Data") }}</a>
        </li>
        <li v-if="step >= 3" class="breadcrumb-item">
            <a @click="updateStep(3)">{{ $t("Payment") }}</a>
        </li>
    </ol>
</template>

<script>
import {EventBus} from "../eventBus";

export default {
    props: ['step'],
    methods: {
        updateStep(step) {
            EventBus.$emit("updateStep", step);
        }
    }
}
</script>

<style scoped>

</style>
