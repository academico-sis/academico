<template>
  <div class="card">
    <div class="card-body">
    <ul class="nav nav-tabs" id="myTab1" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="student-tab" data-toggle="tab" href="#student-pane" role="tab" aria-controls="student-tab" aria-selected="true">{{ $t('Student') }}</a></li>
        <li class="nav-item" v-for="contact in contacts" v-bind:key="contact.id">
            <a class="nav-link" :id="`${contact.id}-tab`" data-toggle="tab" v-bind:href="`#contact-${contact.id}-pane`" role="tab" :aria-controls="`${contact.id}-tab`" aria-selected="false">
                <span v-if="contact.relationship">{{ contact.relationship.translated_name }}</span><span v-else>{{ $t('Additional Contact') }}</span>
            </a>
        </li>
            <li class="nav-item" v-if="writeaccess">
                <div class="nav-link">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#userDataModal">
                        <i class="la la-plus"></i>
                    </button>
                </div>
            </li>
    </ul>
    <div class="tab-content" id="myTab1Content">
        <div class="tab-pane fade show active" id="student-pane" role="tabpanel" aria-labelledby="student-tab">
            <div><strong>{{ $t('name') }}:</strong> {{ student.firstname }} {{ student.lastname }}</div>
            <div v-if="student.idnumber"><strong>{{ $t('ID number') }}:</strong> {{ student.idnumber }}</div>
            <div v-if="student.address"><strong>{{ $t('Address') }}:</strong> {{ student.address }}</div>
            <div v-if="student.city"><strong>{{ $t('City') }}:</strong> {{ student.city }}</div>
            <div v-if="student.state"><strong>{{ $t('State') }}:</strong> {{ student.state }}</div>
            <div v-if="student.country"><strong>{{ $t('Country') }}:</strong> {{ student.country }}</div>

                <div><strong>{{ $t('Phone Number') }}:</strong>
                    <button class="btn btn-sm btn-primary" @click="addingNumberToStudent = true" v-if="writeaccess">
                        <i class="la la-plus"></i>
                    </button>

                    <ul>
                        <li v-for="phone in student.phone" v-bind:key="phone.id">
                            {{ phone.phone_number }}
                            <button v-if="writeaccess" class="btn btn-sm btn-ghost-danger" @click="removePhoneNumber(student.phone, phone)">
                                <i class="la la-trash"></i>
                            </button>
                        </li>

                        <li class="controls" v-if="addingNumberToStudent && writeaccess">
                            <div class="input-group">
                            <input class="form-control" type="text" v-model="newNumber" />
                            <span class="input-group-append">
                                <button class="btn btn-sm btn-success" type="button" @click="saveStudentPhoneNumber(student)"><i class="la la-save"></i></button>
                            </span>
                            </div>
                        </li>
                    </ul>
                </div>
            <div><strong>{{ $t('email') }}:</strong> {{ student.email }}</div>
            <div v-if="student.birthdate"><strong>{{ $t('Birthdate') }}:</strong> {{ student.student_birthdate }} ({{ student.student_age }} {{ $t('years old') }})</div>
            <div v-if="student.institution"><strong>{{ $t('Institution') }}:</strong> <a :href="`/student?institutionId=${student.institution.id}`">{{ student.institution.name }}</a></div>
            <div v-if="student.profession"><strong>{{ $t('Profession') }}:</strong>{{ student.profession.name }}</div>
            <div v-if="writeaccess">
                <a class="btn btn-sm btn-warning" :href="`/student/${student.id}/edit`">
                    <i class="la la-edit"></i>
                </a>
            </div>
        </div>

        <div class="tab-pane fade" v-for="contact in this.contactsData" v-bind:key="contact.id" v-bind:id="`contact-${contact.id}-pane`" role="tabpanel" :aria-labelledby="`${contact.id}-tab`">
            <div><strong>{{ $t('name') }}:</strong> {{ contact.firstname }} {{ contact.lastname }}</div>
            <div><strong>{{ $t('ID nnumber') }}:</strong> {{ contact.idnumber }}</div>
            <div><strong>{{ $t('Address') }}:</strong> {{ contact.address }}</div>
                <div><strong>{{ $t('Phone Number') }}:</strong>
                    <button class="btn btn-sm btn-primary" @click="addingNumberToContact = true" v-if="writeaccess">
                        <i class="la la-plus"></i>
                    </button>

                    <ul>
                        <li v-for="phone in contact.phone" v-bind:key="phone.id">
                            {{ phone.phone_number }}
                            <button v-if="writeaccess" class="btn btn-sm btn-ghost-danger" @click="removePhoneNumber(contact.phone, phone)">
                                <i class="la la-trash"></i>
                            </button>
                        </li>

                        <li class="controls" v-if="addingNumberToContact && writeaccess">
                            <div class="input-group">
                            <input class="form-control" type="text" v-model="newNumber" />
                            <span class="input-group-append">
                                <button class="btn btn-sm btn-success" type="button" @click="saveContactPhoneNumber(contact)"><i class="la la-save"></i></button>
                            </span>
                            </div>
                        </li>
                    </ul>
                </div>
            <div><strong>{{ $t('Email') }}:</strong> {{ contact.email }}</div>
            <div v-if="contact.profession"><strong>{{ $t('profession') }}:</strong> {{ contact.profession.name }}</div>

            <div class="" v-if="writeaccess">
                <a class="btn btn-sm btn-warning" :href="`/contact/${contact.id}/edit`">
                    <i class="la la-edit"></i>
                </a>

                <button class="btn btn-sm btn-danger" @click="deleteContact(contact.id)">
                    <i class="la la-trash"></i>
                </button>
            </div>
        </div>

    </div>
    </div>


  </div>
</template>

<script>
export default {

    props: ['student', 'contacts', 'writeaccess'],

    data () {
        return {
            selectedTab: 0,
            studentData: this.student,
            contactsData: this.contacts,
            addingNumberToStudent: false,
            addingNumberToContact: false,
            newNumber: null
        }
    },

    methods: {
        removePhoneNumber(list, phone) {
            swal({
                title: 'Attention',
                text: "Voulez-vous vraiment supprimer ce numéro de téléphone ?",
                icon: "warning",
                buttons: {
                    cancel: {
                    text: "Annuler",
                    value: null,
                    visible: true,
                    className: "bg-secondary",
                    closeModal: true,
                    },
                    delete: {
                    text: "Supprimer",
                    value: true,
                    visible: true,
                    className: "bg-danger",
                    }
                },
                }).then(value => {
                    if (value) {
                        axios.delete(`/phonenumber/${phone.id}`)
                        .then(response => {
                            var index = list.indexOf(phone);
                            if (index !== -1) list.splice(index, 1);
                        })
                    }
                });
        },

        saveStudentPhoneNumber(student)
        {
            axios
                .post(`/phonenumber/student/${student.id}`, {
                    number: this.newNumber
                })
                .then(response => {
                    this.addingNumberToStudent = false
                    this.studentData.phone.push(response.data)
                    this.newNumber = null
                })
                .catch(errors => {
                    new Noty({
                        type: "error",
                        text: 'Unable to save your change',
                    }).show();
                })
        },

        saveContactPhoneNumber(contact)
        {
            axios
                .post(`/phonenumber/contact/${contact.id}`, {
                    number: this.newNumber
                })
                .then(response => {
                    this.addingNumberToContact = false
                    contact.phone.push(response.data)
                    this.newNumber = null
                })
                .catch(errors => {
                    new Noty({
                        type: "error",
                        text: 'Unable to save your change',
                    }).show();
                })
        },
        deleteContact(contact) {
            swal({
                title: "Attention",
                text: "Voulez-vous vraiment supprimer ce contact ?",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Annuler",
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    delete: {
                        text: "Supprimer",
                        value: true,
                        visible: true,
                        className: "bg-danger",
                    },
                },
            }).then(value => {
                if (value) {
                    axios
                        .delete(`/contact/${contact}/delete`)
                        .then(response => {
                            window.location.reload()
                        })
                        .catch(err => {
                            new Noty({
                                type: "error",
                                text: 'Unable to delete contact',
                            }).show();
                        });
                }
            });
        }
    }
}
</script>

<style>

</style>
