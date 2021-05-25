<template>
    <div>
        <button v-if="this.enrollment.status_id === 2" class="btn btn-danger" @click="markAsUnpaid()">
            {{ this.$t('Mark as pending') }}
        </button>

        <button v-else class="btn btn-info" @click="markAsPaid()">
            {{ this.$t('Mark as paid') }}
        </button>

    </div>
</template>

<script>
export default {
    props: [
        "enrollment"
    ],

    data() {
        return {

        };
    },
    computed: {
        paidTotal() {
            let total = 0;
            if (this.payments) {
                this.payments.forEach(payment => {
                    total += parseFloat(payment.value);
                });
            }
            return total;
        },
    },

    mounted() {

    },

    methods: {
        markAsPaid() {
            if (this.paidTotal !== this.enrollment.price) {
                var message = this.$t('The total amount paid does not match the enrollment total price. Do you really want to mark as paid?')
            }
            else {
                var message = this.$t('Do you really want to mark this enrollment as paid?')
            }
            swal({
                title: this.$t('Warning'),
                text: message,
                icon: "warning",
                buttons: {
                    cancel: {
                        text: this.$t('Cancel'),
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    delete: {
                        text: this.$t('Mark as paid'),
                        value: true,
                        visible: true,
                        className: "bg-danger",
                    },
                },
            }).then(value => {
                if (value) {
                    axios.post(`/enrollment/${this.enrollment.id}/markaspaid`)
                        .then(response => window.location.reload())
                        .catch(error => console.log(error));
                }
            });
        },

        markAsUnpaid() {
            swal({
                title: this.$t('Warning'),
                text: this.$t('Do you really want to mark this enrollment as pending?'),
                icon: "warning",
                buttons: {
                    cancel: {
                        text: this.$t('Cancel'),
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    delete: {
                        text: this.$t('Mark as pending'),
                        value: true,
                        visible: true,
                        className: "bg-danger",
                    },
                },
            }).then(value => {
                if (value) {
                    axios.post(`/enrollment/${this.enrollment.id}/markasunpaid`)
                        .then(response => window.location.reload())
                        .catch(error => console.log(error));
                }
            });
        },
    },
};
</script>
