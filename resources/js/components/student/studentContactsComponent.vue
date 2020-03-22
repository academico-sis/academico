<template>
  <div class="card">
    <div class="card-body">
    <ul class="nav nav-tabs" id="myTab1" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="student-tab" data-toggle="tab" href="#student-pane" role="tab" aria-controls="student-tab" aria-selected="true">{{ $t('Student') }}</a></li>
        <li class="nav-item" v-for="contact in contacts" v-bind:key="contact.id">
            <a class="nav-link" :id="contact.id+'-tab'" data-toggle="tab" v-bind:href="`#contact-${contact.id}-pane`" role="tab" :aria-controls="contact.id+'-tab'" aria-selected="false">{{ $t('Additional Contact') }}</a>
        </li>
    </ul>
    <div class="tab-content" id="myTab1Content">
        <div class="tab-pane fade show active" id="student-pane" role="tabpanel" aria-labelledby="student-tab">
            <div><strong>{{ $t('name') }}:</strong> {{ student.firstname }} {{ student.lastname }}</div>
            <div><strong>{{ $t('idnumber') }}:</strong> {{ student.idnumber }}</div>
            <div><strong>{{ $t('address') }}:</strong> {{ student.address }}</div>

                <div v-if="student.phone.length > 0"><strong>{{ $t('Phone Number') }}:</strong>
                    <ul>
                        <li v-for="phone in student.phone" v-bind:key="phone.id">{{ phone.phone_number }}</li>
                    </ul>
                </div>
            <div><strong>{{ $t('email') }}:</strong> {{ student.email }}</div>
            <div><strong>{{ $t('birthdate') }}:</strong> {{ student.student_birthdate }}</div>
            <div><strong>{{ $t('age') }}:</strong> {{ student.student_age }} {{ $t('years old') }}</div>

            <div class="" v-if="writeaccess">
                <a class="btn btn-sm btn-warning" :href="`/student/${student.id}/edit`">
                    <i class="fa fa-edit"></i>
                </a>

                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#userDataModal">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="tab-pane fade" v-for="contact in contacts" v-bind:key="contact.id" v-bind:id="`contact-${contact.id}-pane`" role="tabpanel" :aria-labelledby="contact.id+'-tab'">
            <h4 v-if="contact.relationship">{{ contact.relationship.name }}</h4>

            <div><strong>{{ $t('name') }}:</strong> {{ contact.firstname }} {{ contact.lastname }}</div>
            <div><strong>{{ $t('idnumber') }}:</strong> {{ contact.idnumber }}</div>
            <div><strong>{{ $t('address') }}:</strong> {{ contact.address }}</div>
                <div v-if="contact.phone.length > 0"><strong>{{ $t('Phone Number') }}:</strong>
                    <ul>
                        <li v-for="phone in contact.phone" v-bind:key="phone.id">{{ phone.phone_number }}</li>
                    </ul>
                </div>
            <div><strong>{{ $t('email') }}:</strong> {{ contact.email }}</div>

            <div class="" v-if="writeaccess">
                <a class="btn btn-sm btn-warning" :href="`/contact/${contact.id}/edit`">
                    <i class="fa fa-edit"></i>
                </a>

                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#userDataModal">
                    <i class="fa fa-plus"></i>
                </button>

                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#userDataModal">
                    <i class="fa fa-trash"></i>
                </button><!-- TODO -->
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
        }
    },
}
</script>

<style>

</style>