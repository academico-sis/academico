import moment from 'moment'

export const store = {
    state: {
        firstname: null,
        lastname: null,
        email: null,
        password: null,
        idnumber_type: 'passport',
        idnumber: null,
        address: null,
        phonenumber: null,
        tc_consent: false,
        address: null,
        birthdate: null,
        profession: null,
        institution: null,
        contacts: []
    },

    updateUserData(data) {
        this.state.firstname = data.firstname
        this.state.lastname = data.lastname
        this.state.email = data.email
        this.state.password = data.password
        this.state.idnumber_type = data.idnumber_type
        this.state.idnumber = data.idnumber
        this.state.address = data.address
        this.state.phonenumber = data.phonenumber
        this.state.tc_consentdata = data.tc_consentdata
    },

    updateInfoData(data) {
        this.state.address = data.address,
        this.state.birthdate = moment(data.birthdate).format(),
        this.state.profession = data.profession,
        this.state.institution = data.institution
        this.state.phonenumbers = data.phonenumbers
    },

    updateContactsData(data) {
        this.state.contacts = data
    },

  };