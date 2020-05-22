<template>
    <div>
        <p v-for="phoneable in phoneables" :key="phoneable.id">
            <i class="la la-phone"></i>
            <span class="input-lg">{{ phoneable.phone_number }}</span>
            <button
                class="btn btn-danger"
                @click="deletePhoneNumber(phoneable.id)"
            >
                <i class="la la-trash"></i>
            </button>
        </p>

        <div class="form-group">
            <i class="la la-phone"></i>
            <input
                id="new_number"
                v-model="number"
                class="input-lg"
                name="new_number"
                type="numeric"
            />
            <button class="btn" @click="addPhoneNumber()">
                <i class="la la-plus"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ["student"],
    data() {
        return {
            phoneables: [],
            number: "",
        };
    },

    mounted() {
        this.getPhoneNumbers();
    },

    methods: {
        getPhoneNumbers() {
            axios
                .get(`/phonenumber/student/${this.student}`)
                .then((response) => {
                    this.phoneables = response.data;
                });
        },

        addPhoneNumber() {
            axios
                .post(`/phonenumber/student/${this.student}`, {
                    number: this.number,
                })
                .then((response) => {
                    this.getPhoneNumbers();
                    this.number = "";
                });
        },

        deletePhoneNumber(phonenumber) {
            axios.delete(`/phonenumber/${phonenumber}`).then((response) => {
                this.getPhoneNumbers();
            });
        },
    },
};
</script>
