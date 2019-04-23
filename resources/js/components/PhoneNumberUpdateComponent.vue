<template>
<div>
                <p v-for="phoneable in phoneables">
                    <i class="fa fa-phone"></i>
                    <span class="input-lg">{{ phoneable.phone_number }}</span>
                    <button class="btn" onclick="delete({{ phoneable.id }})"><i class="fa fa-trash"></i></button>
                </p>

            <button class="btn"><i class="fa fa-plus"></i></button>
</div>
</template>