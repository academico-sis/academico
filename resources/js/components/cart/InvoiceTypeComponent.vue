<template>
    <div class="card">
        <div class="card-header">
            {{ $t("Invoice Type") }}
        </div>

        <div class="card-body">
            <div
                v-for="invoicetype in invoicetypes"
                :key="invoicetype.id"
                class="btn-group"
                role="group"
            >
                <button
                    class="btn btn-sm btn-secondary"
                    :class="{ 'btn-info': selectedInvoiceType && selectedInvoiceType === invoicetype.id }"
                    :disabled="isLoading"
                    @click="setInvoiceType(invoicetype.id)"
                >
                    {{ invoicetype.translated_name }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import {EventBus} from "../eventBus";

export default {
    name: "InvoiceTypeComponent",
    props: ["invoicetypes", "selectedInvoiceType"],
    data() {
        return {
            isLoading: false,
        };
    },
    methods: {
        setInvoiceType(id) {
            EventBus.$emit("setInvoiceType", id);
        }
    }
}
</script>

<style scoped>

</style>
