export const store = {
    state: {
        firstname: null,
        lastname: null,
        email: null,
        password: null,
        idnumber_type: 'cedula',
        cedula_check: null,
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
        this.state.cedula_check = data.cedula_check
        this.state.idnumber = data.idnumber
        this.state.address = data.address
        this.state.phonenumber = data.phonenumber
        this.state.tc_consentdata = data.tc_consentdata
    },

    updateInfoData(data) {
        this.state.address = data.address,
        this.state.birthdate = data.birthdate,
        this.state.profession = data.profession,
        this.state.institution = data.institution
    },

    updateContactsData(data) {
        this.state.contacts = data
    },

    checkCedula(ced) {
        let [suma, mul, index] = [0, 1, ced.length];
        while (index--) {
        let num = ced[index] * mul;
        suma += num - (num > 9) * 9;
        mul = 1 << index % 2;
        }

        if ((suma % 10 === 0) && (suma > 0) && (ced.length == 10)) {
            return 1
        } else {
            return 0
        }
    },

  };