<template>
    <div class="card">
        <div class="card-body text-center">
            <h4>
                {{ $t("Total price") }}: <span v-if="currencyposition === 'before'">{{ currency }} </span>
                {{ value }}
                <span v-if="currencyposition === 'after'">{{ currency }} </span>
            </h4>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'currency', 'currencyposition'],
}
</script>

<style scoped>

</style>
