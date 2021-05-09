<template>
    <div>
    <div v-for="client in this.clients" :key="client.id" class="card">
        <div class="card-header">{{ $t('Contact') }}

            <div class="card-header-actions">
                <button class="btn btn-info" @click="selectInvoiceData(client)">
                    <i class="la la-check"></i>{{ $t("Select") }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <p>{{ client.name }}</p>
            <p>{{ client.idnumber }}</p>
            <p>{{ client.email }}</p>
        </div>
    </div>
    </div>
</template>

<script>
import {EventBus} from "../eventBus";

export default {
    props: ["clients"],

    methods: {
        selectInvoiceData(client) {
            EventBus.$emit("selectClientData", client);
        }
    }
}
</script>

<style scoped>

</style>
