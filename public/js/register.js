(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/register"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterComponent.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterComponent.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var buefy__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! buefy */ "./node_modules/buefy/dist/esm/index.js");
/* harmony import */ var buefy_dist_buefy_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! buefy/dist/buefy.css */ "./node_modules/buefy/dist/buefy.css");
/* harmony import */ var buefy_dist_buefy_css__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(buefy_dist_buefy_css__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./store.js */ "./resources/js/register-bundle/store.js");
/* harmony import */ var _eventBus_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./eventBus.js */ "./resources/js/register-bundle/eventBus.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(buefy__WEBPACK_IMPORTED_MODULE_1__["default"]);


/* harmony default export */ __webpack_exports__["default"] = ({
  props: ['institutions', 'langs', 'pictureallowed', 'picturemandatory'],
  data: function data() {
    return {
      storeState: _store_js__WEBPACK_IMPORTED_MODULE_3__["store"].state,
      activeStep: 0,
      isAnimated: true,
      hasNavigation: false,
      isStepsClickable: false
    };
  },
  created: function created() {
    var _this = this;

    _eventBus_js__WEBPACK_IMPORTED_MODULE_4__["EventBus"].$on("moveToNextStep", function () {
      _this.activeStep += 1;
    });
    _eventBus_js__WEBPACK_IMPORTED_MODULE_4__["EventBus"].$on("goBackToStep", function (step) {
      _this.activeStep = step;
    });
  },
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _eventBus_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./eventBus.js */ "./resources/js/register-bundle/eventBus.js");
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store.js */ "./resources/js/register-bundle/store.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_3__["ValidationObserver"]
  },
  props: [],
  data: function data() {
    return {
      errors: [],
      contacts: []
    };
  },
  mounted: function mounted() {},
  methods: {
    addContact: function addContact() {
      this.contacts.push({
        firstname: null,
        lastname: null,
        email: null,
        idnumber: null,
        address: null,
        phonenumbers: [{
          number: null
        }],
        invoiceable: 0
      });
    },
    dropContact: function dropContact(index) {
      this.contacts.splice(index, 1);
    },
    addPhoneNumber: function addPhoneNumber(index) {
      this.contacts[index].phonenumbers.push({
        number: null
      });
    },
    dropPhoneNumber: function dropPhoneNumber(contact, index) {
      this.contacts[contact].phonenumbers.splice(index, 1);
    },
    validateBeforeSubmit: function validateBeforeSubmit() {
      var _this = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var isValid;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return _this.$refs.observer.validate();

              case 2:
                isValid = _context.sent;

                if (isValid) {
                  _this.updateData();
                } else {
                  _this.$buefy.toast.open({
                    message: _this.$t('The form is invalid, please check the fields marked in red and try again'),
                    type: "is-danger",
                    position: "is-bottom"
                  });
                }

              case 4:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    },
    updateData: function updateData() {
      _store_js__WEBPACK_IMPORTED_MODULE_2__["store"].updateContactsData(this.contacts);
      _eventBus_js__WEBPACK_IMPORTED_MODULE_1__["EventBus"].$emit("moveToNextStep");
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store.js */ "./resources/js/register-bundle/store.js");
/* harmony import */ var _eventBus_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./eventBus.js */ "./resources/js/register-bundle/eventBus.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_3__["ValidationObserver"]
  },
  props: [],
  data: function data() {
    return {
      errors: [],
      formdata: {
        firstname: null,
        lastname: null,
        email: null,
        password: null,
        idnumber_type: "passport",
        idnumber: null,
        address: null,
        phonenumber: null,
        tc_consent: false
      }
    };
  },
  mounted: function mounted() {},
  methods: {
    validateBeforeSubmit: function validateBeforeSubmit() {
      var _this = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var isValid;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return _this.$refs.observer.validate();

              case 2:
                isValid = _context.sent;

                if (!isValid) {
                  _context.next = 8;
                  break;
                }

                _context.next = 6;
                return _this.checkEmailUnicity();

              case 6:
                _context.next = 9;
                break;

              case 8:
                _this.$buefy.toast.open({
                  message: _this.$t('The form is invalid, please check the fields marked in red and try again'),
                  type: "is-danger",
                  position: "is-bottom"
                });

              case 9:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    },
    checkEmailUnicity: function checkEmailUnicity() {
      var _this2 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee2() {
        var isValid;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _context2.next = 2;
                return axios.post("/api/checkemail", {
                  email: _this2.formdata.email
                }).then(function (response) {
                  if (response.status === 204) {
                    return true;
                  }
                })["catch"](function (err) {
                  if (err.status === 409) {
                    return false;
                  }
                });

              case 2:
                isValid = _context2.sent;

                if (isValid) {
                  _this2.updateData();
                } else {
                  _this2.$buefy.toast.open({
                    message: _this2.$t('An account with this email already exists.'),
                    type: "is-danger",
                    position: "is-bottom"
                  });
                }

              case 4:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2);
      }))();
    },
    updateData: function updateData() {
      _store_js__WEBPACK_IMPORTED_MODULE_1__["store"].updateUserData(this.formdata);
      _eventBus_js__WEBPACK_IMPORTED_MODULE_2__["EventBus"].$emit("moveToNextStep");
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store.js */ "./resources/js/register-bundle/store.js");
/* harmony import */ var _eventBus_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./eventBus.js */ "./resources/js/register-bundle/eventBus.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  props: [],
  data: function data() {
    return {
      errors: [],
      formSubmitted: false,
      storeState: _store_js__WEBPACK_IMPORTED_MODULE_0__["store"].state
    };
  },
  mounted: function mounted() {},
  methods: {
    goBackToStep: function goBackToStep(step) {
      _eventBus_js__WEBPACK_IMPORTED_MODULE_1__["EventBus"].$emit("goBackToStep", step);
    },
    submitRegisterForm: function submitRegisterForm() {
      var _this = this;

      this.formSubmitted = true;

      var sleep = function sleep(milliseconds) {
        return new Promise(function (resolve) {
          return setTimeout(resolve, milliseconds);
        });
      };

      axios.post("/register", {
        data: this.storeState
      }).then(function (response) {
        _this.$buefy.toast.open({
          duration: 5000,
          message: _this.$t('The account was created successfully'),
          type: "is-success",
          position: "is-bottom"
        });

        sleep(2500).then(function () {
          window.location.href = "/";
        });
      })["catch"](function (e) {
        _this.errors.push(e);

        _this.$buefy.toast.open({
          message: _this.$t('The user could not be created. Please get in touch with an administrator.'),
          type: "is-danger",
          position: "is-bottom"
        });

        _this.formSubmitted = false;
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store.js */ "./resources/js/register-bundle/store.js");
/* harmony import */ var _eventBus_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./eventBus.js */ "./resources/js/register-bundle/eventBus.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_3__["ValidationObserver"]
  },
  props: ['institutions'],
  data: function data() {
    return {
      errors: [],
      institutionslist: this.institutions,
      filteredInstitutions: this.institutionslist,
      formdata: {
        address: null,
        birthdate: null,
        profession: null,
        institution: null,
        phonenumbers: []
      }
    };
  },
  mounted: function mounted() {
    this.addPhoneNumber();
  },
  methods: {
    addPhoneNumber: function addPhoneNumber() {
      this.formdata.phonenumbers.push({
        number: null
      });
    },
    dropPhoneNumber: function dropPhoneNumber(index) {
      this.formdata.phonenumbers.splice(index, 1);
    },
    showAddInstitution: function showAddInstitution() {
      var _this = this;

      this.$buefy.dialog.prompt({
        message: "Fruit",
        inputAttrs: {
          placeholder: 'e.g. Watermelon',
          maxlength: 20,
          value: this.name
        },
        confirmText: 'Add',
        onConfirm: function onConfirm(value) {
          _this.institutionslist.push(value);

          _this.$refs.autocomplete.setSelected(value);
        }
      });
    },
    validateBeforeSubmit: function validateBeforeSubmit() {
      var _this2 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var isValid;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return _this2.$refs.observer.validate();

              case 2:
                isValid = _context.sent;

                if (isValid) {
                  _this2.updateData();
                } else {
                  _this2.$buefy.toast.open({
                    message: _this2.$t('The form is invalid, please check the fields marked in red and try again'),
                    type: "is-danger",
                    position: "is-bottom"
                  });
                }

              case 4:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    },
    updateData: function updateData() {
      _store_js__WEBPACK_IMPORTED_MODULE_1__["store"].updateInfoData(this.formdata);
      _eventBus_js__WEBPACK_IMPORTED_MODULE_2__["EventBus"].$emit("moveToNextStep");
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store.js */ "./resources/js/register-bundle/store.js");
/* harmony import */ var _eventBus_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./eventBus.js */ "./resources/js/register-bundle/eventBus.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  props: ['picturemandatory'],
  components: {
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_3__["ValidationObserver"]
  },
  data: function data() {
    return {
      errors: [],
      player: null,
      canvas: null,
      context: null,
      userPicture: null
    };
  },
  // TODO: Add deletion of visual representation of uploaded picture on the site.
  mounted: function mounted() {
    this.player = document.getElementById('player');
    this.canvas = document.getElementById('canvas1');
    this.context = this.canvas.getContext('2d');
  },
  methods: {
    // Checks the image size then sets the userPicture variable to the uploaded picture
    onFileChange: function onFileChange(e) {
      var _this = this;

      var image = e.target.files[0];

      if (image.size > 3145728) {
        this.$buefy.toast.open({
          message: "The image is too large. Maximum size is 2mb.",
          type: "is-danger",
          position: "is-bottom"
        });
      } else {
        var reader = new FileReader();
        reader.readAsDataURL(image);

        reader.onload = function (e) {
          _this.userPicture = e.target.result;
          console.log(_this.userPicture);
        };
      }
    },
    validateBeforeSubmit: function validateBeforeSubmit() {
      var _this2 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _this2.updateData();

              case 1:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    },
    // Asks the user for permission to use their attached media and
    // activates the video stream.
    enableUserToTakePicture: function enableUserToTakePicture() {
      var _this3 = this;

      // Specifying the media to request, along with the requirements.
      var constraints = {
        audio: false,
        video: {
          width: 720,
          height: 480
        }
      }; // Asks the user for permission and starts the stream.

      navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
        _this3.player.srcObject = stream;
      }); // Shows the capture button

      document.getElementById('captureButton').style.display = "block";
    },
    // Takes a snapshot of the users video stream and displays it in the
    // canvas element and saves the image in the userPicture data // variable
    takeUserPicture: function takeUserPicture() {
      var constraints = {
        audio: false,
        video: {
          width: 720,
          height: 480
        }
      };
      this.context = this.canvas.getContext('2d'); // Draws the image to the canvas

      this.context.drawImage(this.player, 0, 0, this.canvas.width, this.canvas.height);
      document.getElementById('captureButton').style.display = "none";
      var userPictureCapture = this.canvas.toDataURL('image/jpeg', 1.0);
      var that = this; // Saves the image

      that.userPicture = userPictureCapture; // Cuts the stream

      this.player.srcObject.getVideoTracks().forEach(function (track) {
        return track.stop();
      });
    },
    updateData: function updateData() {
      if (this.userPicture != null) {
        _store_js__WEBPACK_IMPORTED_MODULE_1__["store"].updatePictureData(this.userPicture);
      }

      _eventBus_js__WEBPACK_IMPORTED_MODULE_2__["EventBus"].$emit("moveToNextStep");
    }
  }
});

/***/ }),

/***/ "./node_modules/moment/locale sync recursive ^\\.\\/.*$":
/*!**************************************************!*\
  !*** ./node_modules/moment/locale sync ^\.\/.*$ ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var map = {
	"./af": "./node_modules/moment/locale/af.js",
	"./af.js": "./node_modules/moment/locale/af.js",
	"./ar": "./node_modules/moment/locale/ar.js",
	"./ar-dz": "./node_modules/moment/locale/ar-dz.js",
	"./ar-dz.js": "./node_modules/moment/locale/ar-dz.js",
	"./ar-kw": "./node_modules/moment/locale/ar-kw.js",
	"./ar-kw.js": "./node_modules/moment/locale/ar-kw.js",
	"./ar-ly": "./node_modules/moment/locale/ar-ly.js",
	"./ar-ly.js": "./node_modules/moment/locale/ar-ly.js",
	"./ar-ma": "./node_modules/moment/locale/ar-ma.js",
	"./ar-ma.js": "./node_modules/moment/locale/ar-ma.js",
	"./ar-sa": "./node_modules/moment/locale/ar-sa.js",
	"./ar-sa.js": "./node_modules/moment/locale/ar-sa.js",
	"./ar-tn": "./node_modules/moment/locale/ar-tn.js",
	"./ar-tn.js": "./node_modules/moment/locale/ar-tn.js",
	"./ar.js": "./node_modules/moment/locale/ar.js",
	"./az": "./node_modules/moment/locale/az.js",
	"./az.js": "./node_modules/moment/locale/az.js",
	"./be": "./node_modules/moment/locale/be.js",
	"./be.js": "./node_modules/moment/locale/be.js",
	"./bg": "./node_modules/moment/locale/bg.js",
	"./bg.js": "./node_modules/moment/locale/bg.js",
	"./bm": "./node_modules/moment/locale/bm.js",
	"./bm.js": "./node_modules/moment/locale/bm.js",
	"./bn": "./node_modules/moment/locale/bn.js",
	"./bn-bd": "./node_modules/moment/locale/bn-bd.js",
	"./bn-bd.js": "./node_modules/moment/locale/bn-bd.js",
	"./bn.js": "./node_modules/moment/locale/bn.js",
	"./bo": "./node_modules/moment/locale/bo.js",
	"./bo.js": "./node_modules/moment/locale/bo.js",
	"./br": "./node_modules/moment/locale/br.js",
	"./br.js": "./node_modules/moment/locale/br.js",
	"./bs": "./node_modules/moment/locale/bs.js",
	"./bs.js": "./node_modules/moment/locale/bs.js",
	"./ca": "./node_modules/moment/locale/ca.js",
	"./ca.js": "./node_modules/moment/locale/ca.js",
	"./cs": "./node_modules/moment/locale/cs.js",
	"./cs.js": "./node_modules/moment/locale/cs.js",
	"./cv": "./node_modules/moment/locale/cv.js",
	"./cv.js": "./node_modules/moment/locale/cv.js",
	"./cy": "./node_modules/moment/locale/cy.js",
	"./cy.js": "./node_modules/moment/locale/cy.js",
	"./da": "./node_modules/moment/locale/da.js",
	"./da.js": "./node_modules/moment/locale/da.js",
	"./de": "./node_modules/moment/locale/de.js",
	"./de-at": "./node_modules/moment/locale/de-at.js",
	"./de-at.js": "./node_modules/moment/locale/de-at.js",
	"./de-ch": "./node_modules/moment/locale/de-ch.js",
	"./de-ch.js": "./node_modules/moment/locale/de-ch.js",
	"./de.js": "./node_modules/moment/locale/de.js",
	"./dv": "./node_modules/moment/locale/dv.js",
	"./dv.js": "./node_modules/moment/locale/dv.js",
	"./el": "./node_modules/moment/locale/el.js",
	"./el.js": "./node_modules/moment/locale/el.js",
	"./en-au": "./node_modules/moment/locale/en-au.js",
	"./en-au.js": "./node_modules/moment/locale/en-au.js",
	"./en-ca": "./node_modules/moment/locale/en-ca.js",
	"./en-ca.js": "./node_modules/moment/locale/en-ca.js",
	"./en-gb": "./node_modules/moment/locale/en-gb.js",
	"./en-gb.js": "./node_modules/moment/locale/en-gb.js",
	"./en-ie": "./node_modules/moment/locale/en-ie.js",
	"./en-ie.js": "./node_modules/moment/locale/en-ie.js",
	"./en-il": "./node_modules/moment/locale/en-il.js",
	"./en-il.js": "./node_modules/moment/locale/en-il.js",
	"./en-in": "./node_modules/moment/locale/en-in.js",
	"./en-in.js": "./node_modules/moment/locale/en-in.js",
	"./en-nz": "./node_modules/moment/locale/en-nz.js",
	"./en-nz.js": "./node_modules/moment/locale/en-nz.js",
	"./en-sg": "./node_modules/moment/locale/en-sg.js",
	"./en-sg.js": "./node_modules/moment/locale/en-sg.js",
	"./eo": "./node_modules/moment/locale/eo.js",
	"./eo.js": "./node_modules/moment/locale/eo.js",
	"./es": "./node_modules/moment/locale/es.js",
	"./es-do": "./node_modules/moment/locale/es-do.js",
	"./es-do.js": "./node_modules/moment/locale/es-do.js",
	"./es-mx": "./node_modules/moment/locale/es-mx.js",
	"./es-mx.js": "./node_modules/moment/locale/es-mx.js",
	"./es-us": "./node_modules/moment/locale/es-us.js",
	"./es-us.js": "./node_modules/moment/locale/es-us.js",
	"./es.js": "./node_modules/moment/locale/es.js",
	"./et": "./node_modules/moment/locale/et.js",
	"./et.js": "./node_modules/moment/locale/et.js",
	"./eu": "./node_modules/moment/locale/eu.js",
	"./eu.js": "./node_modules/moment/locale/eu.js",
	"./fa": "./node_modules/moment/locale/fa.js",
	"./fa.js": "./node_modules/moment/locale/fa.js",
	"./fi": "./node_modules/moment/locale/fi.js",
	"./fi.js": "./node_modules/moment/locale/fi.js",
	"./fil": "./node_modules/moment/locale/fil.js",
	"./fil.js": "./node_modules/moment/locale/fil.js",
	"./fo": "./node_modules/moment/locale/fo.js",
	"./fo.js": "./node_modules/moment/locale/fo.js",
	"./fr": "./node_modules/moment/locale/fr.js",
	"./fr-ca": "./node_modules/moment/locale/fr-ca.js",
	"./fr-ca.js": "./node_modules/moment/locale/fr-ca.js",
	"./fr-ch": "./node_modules/moment/locale/fr-ch.js",
	"./fr-ch.js": "./node_modules/moment/locale/fr-ch.js",
	"./fr.js": "./node_modules/moment/locale/fr.js",
	"./fy": "./node_modules/moment/locale/fy.js",
	"./fy.js": "./node_modules/moment/locale/fy.js",
	"./ga": "./node_modules/moment/locale/ga.js",
	"./ga.js": "./node_modules/moment/locale/ga.js",
	"./gd": "./node_modules/moment/locale/gd.js",
	"./gd.js": "./node_modules/moment/locale/gd.js",
	"./gl": "./node_modules/moment/locale/gl.js",
	"./gl.js": "./node_modules/moment/locale/gl.js",
	"./gom-deva": "./node_modules/moment/locale/gom-deva.js",
	"./gom-deva.js": "./node_modules/moment/locale/gom-deva.js",
	"./gom-latn": "./node_modules/moment/locale/gom-latn.js",
	"./gom-latn.js": "./node_modules/moment/locale/gom-latn.js",
	"./gu": "./node_modules/moment/locale/gu.js",
	"./gu.js": "./node_modules/moment/locale/gu.js",
	"./he": "./node_modules/moment/locale/he.js",
	"./he.js": "./node_modules/moment/locale/he.js",
	"./hi": "./node_modules/moment/locale/hi.js",
	"./hi.js": "./node_modules/moment/locale/hi.js",
	"./hr": "./node_modules/moment/locale/hr.js",
	"./hr.js": "./node_modules/moment/locale/hr.js",
	"./hu": "./node_modules/moment/locale/hu.js",
	"./hu.js": "./node_modules/moment/locale/hu.js",
	"./hy-am": "./node_modules/moment/locale/hy-am.js",
	"./hy-am.js": "./node_modules/moment/locale/hy-am.js",
	"./id": "./node_modules/moment/locale/id.js",
	"./id.js": "./node_modules/moment/locale/id.js",
	"./is": "./node_modules/moment/locale/is.js",
	"./is.js": "./node_modules/moment/locale/is.js",
	"./it": "./node_modules/moment/locale/it.js",
	"./it-ch": "./node_modules/moment/locale/it-ch.js",
	"./it-ch.js": "./node_modules/moment/locale/it-ch.js",
	"./it.js": "./node_modules/moment/locale/it.js",
	"./ja": "./node_modules/moment/locale/ja.js",
	"./ja.js": "./node_modules/moment/locale/ja.js",
	"./jv": "./node_modules/moment/locale/jv.js",
	"./jv.js": "./node_modules/moment/locale/jv.js",
	"./ka": "./node_modules/moment/locale/ka.js",
	"./ka.js": "./node_modules/moment/locale/ka.js",
	"./kk": "./node_modules/moment/locale/kk.js",
	"./kk.js": "./node_modules/moment/locale/kk.js",
	"./km": "./node_modules/moment/locale/km.js",
	"./km.js": "./node_modules/moment/locale/km.js",
	"./kn": "./node_modules/moment/locale/kn.js",
	"./kn.js": "./node_modules/moment/locale/kn.js",
	"./ko": "./node_modules/moment/locale/ko.js",
	"./ko.js": "./node_modules/moment/locale/ko.js",
	"./ku": "./node_modules/moment/locale/ku.js",
	"./ku.js": "./node_modules/moment/locale/ku.js",
	"./ky": "./node_modules/moment/locale/ky.js",
	"./ky.js": "./node_modules/moment/locale/ky.js",
	"./lb": "./node_modules/moment/locale/lb.js",
	"./lb.js": "./node_modules/moment/locale/lb.js",
	"./lo": "./node_modules/moment/locale/lo.js",
	"./lo.js": "./node_modules/moment/locale/lo.js",
	"./lt": "./node_modules/moment/locale/lt.js",
	"./lt.js": "./node_modules/moment/locale/lt.js",
	"./lv": "./node_modules/moment/locale/lv.js",
	"./lv.js": "./node_modules/moment/locale/lv.js",
	"./me": "./node_modules/moment/locale/me.js",
	"./me.js": "./node_modules/moment/locale/me.js",
	"./mi": "./node_modules/moment/locale/mi.js",
	"./mi.js": "./node_modules/moment/locale/mi.js",
	"./mk": "./node_modules/moment/locale/mk.js",
	"./mk.js": "./node_modules/moment/locale/mk.js",
	"./ml": "./node_modules/moment/locale/ml.js",
	"./ml.js": "./node_modules/moment/locale/ml.js",
	"./mn": "./node_modules/moment/locale/mn.js",
	"./mn.js": "./node_modules/moment/locale/mn.js",
	"./mr": "./node_modules/moment/locale/mr.js",
	"./mr.js": "./node_modules/moment/locale/mr.js",
	"./ms": "./node_modules/moment/locale/ms.js",
	"./ms-my": "./node_modules/moment/locale/ms-my.js",
	"./ms-my.js": "./node_modules/moment/locale/ms-my.js",
	"./ms.js": "./node_modules/moment/locale/ms.js",
	"./mt": "./node_modules/moment/locale/mt.js",
	"./mt.js": "./node_modules/moment/locale/mt.js",
	"./my": "./node_modules/moment/locale/my.js",
	"./my.js": "./node_modules/moment/locale/my.js",
	"./nb": "./node_modules/moment/locale/nb.js",
	"./nb.js": "./node_modules/moment/locale/nb.js",
	"./ne": "./node_modules/moment/locale/ne.js",
	"./ne.js": "./node_modules/moment/locale/ne.js",
	"./nl": "./node_modules/moment/locale/nl.js",
	"./nl-be": "./node_modules/moment/locale/nl-be.js",
	"./nl-be.js": "./node_modules/moment/locale/nl-be.js",
	"./nl.js": "./node_modules/moment/locale/nl.js",
	"./nn": "./node_modules/moment/locale/nn.js",
	"./nn.js": "./node_modules/moment/locale/nn.js",
	"./oc-lnc": "./node_modules/moment/locale/oc-lnc.js",
	"./oc-lnc.js": "./node_modules/moment/locale/oc-lnc.js",
	"./pa-in": "./node_modules/moment/locale/pa-in.js",
	"./pa-in.js": "./node_modules/moment/locale/pa-in.js",
	"./pl": "./node_modules/moment/locale/pl.js",
	"./pl.js": "./node_modules/moment/locale/pl.js",
	"./pt": "./node_modules/moment/locale/pt.js",
	"./pt-br": "./node_modules/moment/locale/pt-br.js",
	"./pt-br.js": "./node_modules/moment/locale/pt-br.js",
	"./pt.js": "./node_modules/moment/locale/pt.js",
	"./ro": "./node_modules/moment/locale/ro.js",
	"./ro.js": "./node_modules/moment/locale/ro.js",
	"./ru": "./node_modules/moment/locale/ru.js",
	"./ru.js": "./node_modules/moment/locale/ru.js",
	"./sd": "./node_modules/moment/locale/sd.js",
	"./sd.js": "./node_modules/moment/locale/sd.js",
	"./se": "./node_modules/moment/locale/se.js",
	"./se.js": "./node_modules/moment/locale/se.js",
	"./si": "./node_modules/moment/locale/si.js",
	"./si.js": "./node_modules/moment/locale/si.js",
	"./sk": "./node_modules/moment/locale/sk.js",
	"./sk.js": "./node_modules/moment/locale/sk.js",
	"./sl": "./node_modules/moment/locale/sl.js",
	"./sl.js": "./node_modules/moment/locale/sl.js",
	"./sq": "./node_modules/moment/locale/sq.js",
	"./sq.js": "./node_modules/moment/locale/sq.js",
	"./sr": "./node_modules/moment/locale/sr.js",
	"./sr-cyrl": "./node_modules/moment/locale/sr-cyrl.js",
	"./sr-cyrl.js": "./node_modules/moment/locale/sr-cyrl.js",
	"./sr.js": "./node_modules/moment/locale/sr.js",
	"./ss": "./node_modules/moment/locale/ss.js",
	"./ss.js": "./node_modules/moment/locale/ss.js",
	"./sv": "./node_modules/moment/locale/sv.js",
	"./sv.js": "./node_modules/moment/locale/sv.js",
	"./sw": "./node_modules/moment/locale/sw.js",
	"./sw.js": "./node_modules/moment/locale/sw.js",
	"./ta": "./node_modules/moment/locale/ta.js",
	"./ta.js": "./node_modules/moment/locale/ta.js",
	"./te": "./node_modules/moment/locale/te.js",
	"./te.js": "./node_modules/moment/locale/te.js",
	"./tet": "./node_modules/moment/locale/tet.js",
	"./tet.js": "./node_modules/moment/locale/tet.js",
	"./tg": "./node_modules/moment/locale/tg.js",
	"./tg.js": "./node_modules/moment/locale/tg.js",
	"./th": "./node_modules/moment/locale/th.js",
	"./th.js": "./node_modules/moment/locale/th.js",
	"./tk": "./node_modules/moment/locale/tk.js",
	"./tk.js": "./node_modules/moment/locale/tk.js",
	"./tl-ph": "./node_modules/moment/locale/tl-ph.js",
	"./tl-ph.js": "./node_modules/moment/locale/tl-ph.js",
	"./tlh": "./node_modules/moment/locale/tlh.js",
	"./tlh.js": "./node_modules/moment/locale/tlh.js",
	"./tr": "./node_modules/moment/locale/tr.js",
	"./tr.js": "./node_modules/moment/locale/tr.js",
	"./tzl": "./node_modules/moment/locale/tzl.js",
	"./tzl.js": "./node_modules/moment/locale/tzl.js",
	"./tzm": "./node_modules/moment/locale/tzm.js",
	"./tzm-latn": "./node_modules/moment/locale/tzm-latn.js",
	"./tzm-latn.js": "./node_modules/moment/locale/tzm-latn.js",
	"./tzm.js": "./node_modules/moment/locale/tzm.js",
	"./ug-cn": "./node_modules/moment/locale/ug-cn.js",
	"./ug-cn.js": "./node_modules/moment/locale/ug-cn.js",
	"./uk": "./node_modules/moment/locale/uk.js",
	"./uk.js": "./node_modules/moment/locale/uk.js",
	"./ur": "./node_modules/moment/locale/ur.js",
	"./ur.js": "./node_modules/moment/locale/ur.js",
	"./uz": "./node_modules/moment/locale/uz.js",
	"./uz-latn": "./node_modules/moment/locale/uz-latn.js",
	"./uz-latn.js": "./node_modules/moment/locale/uz-latn.js",
	"./uz.js": "./node_modules/moment/locale/uz.js",
	"./vi": "./node_modules/moment/locale/vi.js",
	"./vi.js": "./node_modules/moment/locale/vi.js",
	"./x-pseudo": "./node_modules/moment/locale/x-pseudo.js",
	"./x-pseudo.js": "./node_modules/moment/locale/x-pseudo.js",
	"./yo": "./node_modules/moment/locale/yo.js",
	"./yo.js": "./node_modules/moment/locale/yo.js",
	"./zh-cn": "./node_modules/moment/locale/zh-cn.js",
	"./zh-cn.js": "./node_modules/moment/locale/zh-cn.js",
	"./zh-hk": "./node_modules/moment/locale/zh-hk.js",
	"./zh-hk.js": "./node_modules/moment/locale/zh-hk.js",
	"./zh-mo": "./node_modules/moment/locale/zh-mo.js",
	"./zh-mo.js": "./node_modules/moment/locale/zh-mo.js",
	"./zh-tw": "./node_modules/moment/locale/zh-tw.js",
	"./zh-tw.js": "./node_modules/moment/locale/zh-tw.js"
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = "./node_modules/moment/locale sync recursive ^\\.\\/.*$";

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterComponent.vue?vue&type=template&id=1c1f69ee&":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterComponent.vue?vue&type=template&id=1c1f69ee& ***!
  \*************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "div",
        { staticClass: "is-pulled-right" },
        [
          _c(
            "b-dropdown",
            {
              attrs: { hoverable: "", "aria-role": "list" },
              model: {
                value: _vm.$i18n.locale,
                callback: function($$v) {
                  _vm.$set(_vm.$i18n, "locale", $$v)
                },
                expression: "$i18n.locale"
              }
            },
            [
              _c(
                "button",
                {
                  staticClass: "button is-info",
                  attrs: { slot: "trigger" },
                  slot: "trigger"
                },
                [
                  _c("span", [_vm._v(_vm._s(_vm.$t("language")))]),
                  _vm._v(" "),
                  _c("b-icon", { attrs: { icon: "menu-down" } })
                ],
                1
              ),
              _vm._v(" "),
              _vm._l(this.langs, function(lang, i) {
                return _c(
                  "b-dropdown-item",
                  {
                    key: "Lang" + i,
                    attrs: { value: lang, "aria-role": "listitem" }
                  },
                  [_vm._v(_vm._s(lang))]
                )
              })
            ],
            2
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-steps",
        {
          attrs: {
            size: "is-small",
            animated: _vm.isAnimated,
            "has-navigation": _vm.hasNavigation
          },
          model: {
            value: _vm.activeStep,
            callback: function($$v) {
              _vm.activeStep = $$v
            },
            expression: "activeStep"
          }
        },
        [
          _c(
            "b-step-item",
            {
              attrs: { label: _vm.$t("step1"), clickable: _vm.activeStep > 0 }
            },
            [_c("register-user-data-component")],
            1
          ),
          _vm._v(" "),
          _c(
            "b-step-item",
            {
              attrs: { label: _vm.$t("step2"), clickable: _vm.activeStep > 1 }
            },
            [
              _c("register-user-info-component", {
                attrs: { institutions: _vm.institutions }
              })
            ],
            1
          ),
          _vm._v(" "),
          _vm.pictureallowed
            ? _c(
                "b-step-item",
                {
                  attrs: {
                    label: _vm.$t("step3"),
                    clickable: _vm.activeStep > 2
                  }
                },
                [
                  _c("register-user-picture-component", {
                    attrs: { picturemandatory: _vm.picturemandatory }
                  })
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _c(
            "b-step-item",
            {
              attrs: { label: _vm.$t("step4"), clickable: _vm.activeStep > 3 }
            },
            [_c("register-contacts-component")],
            1
          ),
          _vm._v(" "),
          _c(
            "b-step-item",
            {
              attrs: { label: _vm.$t("step5"), clickable: _vm.activeStep > 4 }
            },
            [_c("register-user-finish-component")],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=template&id=116f4576&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=template&id=116f4576& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("ValidationObserver", {
        ref: "observer",
        scopedSlots: _vm._u([
          {
            key: "default",
            fn: function(ref) {
              var valid = ref.valid
              return [
                _vm._l(_vm.contacts, function(contact, index) {
                  return _c("article", { key: index, staticClass: "message" }, [
                    _c("div", { staticClass: "message-header" }, [
                      _vm._v(
                        "\n                " +
                          _vm._s(_vm.$t("contact")) +
                          " #" +
                          _vm._s(index + 1) +
                          "\n                "
                      ),
                      _c("button", {
                        staticClass: "delete",
                        on: {
                          click: function($event) {
                            return _vm.dropContact(index)
                          }
                        }
                      })
                    ]),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "message-body" },
                      [
                        _c(
                          "b-checkbox",
                          {
                            attrs: { "native-value": "0" },
                            model: {
                              value: contact.invoiceable,
                              callback: function($$v) {
                                _vm.$set(contact, "invoiceable", $$v)
                              },
                              expression: "contact.invoiceable"
                            }
                          },
                          [_vm._v(_vm._s(_vm.$t("Use this data for invoices")))]
                        ),
                        _vm._v(" "),
                        _c(
                          "b-field",
                          { attrs: { label: _vm.$t("firstname") } },
                          [
                            _c("ValidationProvider", {
                              attrs: { name: "firstname", rules: "required" },
                              scopedSlots: _vm._u(
                                [
                                  {
                                    key: "default",
                                    fn: function(ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-input", {
                                          attrs: {
                                            placeholder: _vm.$t("firstname")
                                          },
                                          model: {
                                            value: contact.firstname,
                                            callback: function($$v) {
                                              _vm.$set(
                                                contact,
                                                "firstname",
                                                $$v
                                              )
                                            },
                                            expression: "contact.firstname"
                                          }
                                        }),
                                        _c(
                                          "p",
                                          { staticClass: "help is-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        )
                                      ]
                                    }
                                  }
                                ],
                                null,
                                true
                              )
                            })
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "b-field",
                          { attrs: { label: _vm.$t("lastname") } },
                          [
                            _c("ValidationProvider", {
                              attrs: { name: "lastname", rules: "required" },
                              scopedSlots: _vm._u(
                                [
                                  {
                                    key: "default",
                                    fn: function(ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-input", {
                                          attrs: {
                                            placeholder: _vm.$t("lastname")
                                          },
                                          model: {
                                            value: contact.lastname,
                                            callback: function($$v) {
                                              _vm.$set(contact, "lastname", $$v)
                                            },
                                            expression: "contact.lastname"
                                          }
                                        }),
                                        _c(
                                          "p",
                                          { staticClass: "help is-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        )
                                      ]
                                    }
                                  }
                                ],
                                null,
                                true
                              )
                            })
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "b-field",
                          { attrs: { label: _vm.$t("email") } },
                          [
                            _c("ValidationProvider", {
                              attrs: { name: "email", rules: "required|email" },
                              scopedSlots: _vm._u(
                                [
                                  {
                                    key: "default",
                                    fn: function(ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-input", {
                                          attrs: {
                                            type: "email",
                                            placeholder: _vm.$t("email"),
                                            required: ""
                                          },
                                          model: {
                                            value: contact.email,
                                            callback: function($$v) {
                                              _vm.$set(contact, "email", $$v)
                                            },
                                            expression: "contact.email"
                                          }
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "p",
                                          { staticClass: "help is-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        )
                                      ]
                                    }
                                  }
                                ],
                                null,
                                true
                              )
                            })
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "b-field",
                          { attrs: { label: "Número de pasaporte" } },
                          [
                            _c("ValidationProvider", {
                              attrs: { name: "ID Number", rules: "required" },
                              scopedSlots: _vm._u(
                                [
                                  {
                                    key: "default",
                                    fn: function(ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-input", {
                                          model: {
                                            value: contact.idnumber,
                                            callback: function($$v) {
                                              _vm.$set(contact, "idnumber", $$v)
                                            },
                                            expression: "contact.idnumber"
                                          }
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "p",
                                          { staticClass: "help is-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        )
                                      ]
                                    }
                                  }
                                ],
                                null,
                                true
                              )
                            })
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "b-field",
                          { attrs: { label: _vm.$t("address") } },
                          [
                            _c("ValidationProvider", {
                              attrs: { name: "address", rules: "required" },
                              scopedSlots: _vm._u(
                                [
                                  {
                                    key: "default",
                                    fn: function(ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-input", {
                                          attrs: {
                                            placeholder: _vm.$t("address")
                                          },
                                          model: {
                                            value: contact.address,
                                            callback: function($$v) {
                                              _vm.$set(contact, "address", $$v)
                                            },
                                            expression: "contact.address"
                                          }
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "p",
                                          { staticClass: "help is-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        )
                                      ]
                                    }
                                  }
                                ],
                                null,
                                true
                              )
                            })
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c("p", { staticClass: "label" }, [
                          _vm._v(_vm._s(_vm.$t("phonenumber")))
                        ]),
                        _vm._v(" "),
                        _vm._l(contact.phonenumbers, function(
                          number,
                          numberindex
                        ) {
                          return _c(
                            "b-field",
                            {
                              key: numberindex,
                              attrs: {
                                label: "Phone #" + (numberindex + 1),
                                grouped: "",
                                "label-position": "on-border"
                              }
                            },
                            [
                              _c("ValidationProvider", {
                                attrs: {
                                  name: "número de teléfono",
                                  rules: "required"
                                },
                                scopedSlots: _vm._u(
                                  [
                                    {
                                      key: "default",
                                      fn: function(ref) {
                                        var errors = ref.errors
                                        return [
                                          _c("b-input", {
                                            attrs: {
                                              placeholder: _vm.$t("phonenumber")
                                            },
                                            model: {
                                              value: number.number,
                                              callback: function($$v) {
                                                _vm.$set(number, "number", $$v)
                                              },
                                              expression: "number.number"
                                            }
                                          }),
                                          _vm._v(" "),
                                          _c(
                                            "p",
                                            { staticClass: "control" },
                                            [
                                              numberindex > 0
                                                ? _c(
                                                    "b-button",
                                                    {
                                                      on: {
                                                        click: function(
                                                          $event
                                                        ) {
                                                          return _vm.dropPhoneNumber(
                                                            index,
                                                            numberindex
                                                          )
                                                        }
                                                      }
                                                    },
                                                    [
                                                      _vm._v(
                                                        _vm._s(_vm.$t("delete"))
                                                      )
                                                    ]
                                                  )
                                                : _vm._e()
                                            ],
                                            1
                                          ),
                                          _vm._v(" "),
                                          _c(
                                            "p",
                                            { staticClass: "help is-danger" },
                                            [_vm._v(_vm._s(errors[0]))]
                                          )
                                        ]
                                      }
                                    }
                                  ],
                                  null,
                                  true
                                )
                              })
                            ],
                            1
                          )
                        }),
                        _vm._v(" "),
                        _c(
                          "p",
                          [
                            _c(
                              "b-button",
                              {
                                on: {
                                  click: function($event) {
                                    return _vm.addPhoneNumber(index)
                                  }
                                }
                              },
                              [_vm._v(_vm._s(_vm.$t("add")))]
                            ),
                            _vm._v(
                              "\n                    " +
                                _vm._s(_vm.$t("phonenumber_explainer")) +
                                "\n                "
                            )
                          ],
                          1
                        )
                      ],
                      2
                    )
                  ])
                }),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticStyle: {
                      "text-align": "center",
                      "padding-top": "2em"
                    }
                  },
                  [
                    _c("p", { staticStyle: { "padding-bottom": "2em" } }, [
                      _vm._v(
                        "\n                " +
                          _vm._s(_vm.$t("contact_explainer1")) +
                          "\n            "
                      )
                    ]),
                    _vm._v(" "),
                    _c("p", { staticStyle: { "padding-bottom": "2em" } }, [
                      _vm._v(
                        "\n                " +
                          _vm._s(_vm.$t("contact_explainer2")) +
                          "\n            "
                      )
                    ]),
                    _vm._v(" "),
                    _c("p", { staticStyle: { "padding-bottom": "2em" } }, [
                      _vm._v(
                        "\n                " +
                          _vm._s(_vm.$t("contact_explainer3")) +
                          "\n            "
                      )
                    ]),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        attrs: { type: "is-info" },
                        on: {
                          click: function($event) {
                            return _vm.addContact()
                          }
                        }
                      },
                      [_vm._v(_vm._s(_vm.$t("add")))]
                    ),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        attrs: { type: "is-primary" },
                        on: {
                          click: function($event) {
                            return _vm.validateBeforeSubmit()
                          }
                        }
                      },
                      [_vm._v(_vm._s(_vm.$t("next")))]
                    )
                  ],
                  1
                )
              ]
            }
          }
        ])
      })
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("ValidationObserver", {
        ref: "observer",
        scopedSlots: _vm._u([
          {
            key: "default",
            fn: function(ref) {
              var valid = ref.valid
              return [
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("firstname") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "firstname", rules: "required" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: {
                                    placeholder: _vm.$t("firstname"),
                                    required: ""
                                  },
                                  model: {
                                    value: _vm.formdata.firstname,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "firstname", $$v)
                                    },
                                    expression: "formdata.firstname"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("lastname") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "lastname", rules: "required" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: {
                                    placeholder: _vm.$t("lastname"),
                                    required: ""
                                  },
                                  model: {
                                    value: _vm.formdata.lastname,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "lastname", $$v)
                                    },
                                    expression: "formdata.lastname"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("email") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "email", rules: "required|email" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: {
                                    type: "email",
                                    placeholder: _vm.$t("email"),
                                    required: ""
                                  },
                                  model: {
                                    value: _vm.formdata.email,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "email", $$v)
                                    },
                                    expression: "formdata.email"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("iddocument") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "ID Number", rules: "required" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: { maxlength: "12", required: "" },
                                  model: {
                                    value: _vm.formdata.idnumber,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "idnumber", $$v)
                                    },
                                    expression: "formdata.idnumber"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("password") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "password", rules: "required|min:6" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: {
                                    type: "password",
                                    "password-reveal": ""
                                  },
                                  model: {
                                    value: _vm.formdata.password,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "password", $$v)
                                    },
                                    expression: "formdata.password"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-button",
                  {
                    attrs: { type: "is-primary" },
                    on: {
                      click: function($event) {
                        return _vm.validateBeforeSubmit()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.$t("next")))]
                )
              ]
            }
          }
        ])
      })
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b&":
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b& ***!
  \***********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("div", { staticStyle: { "padding-bottom": "3em" } }, [
        _c(
          "div",
          { staticClass: "container has-text-centered" },
          [
            _c("h2", { staticClass: "subtitle" }, [
              _vm._v(_vm._s(_vm.$t("finish_subtitle")))
            ]),
            _vm._v(" "),
            _c(
              "b-button",
              {
                staticClass: "is-large is-rounded is-success is-centered",
                class: { "is-loading": _vm.formSubmitted === true },
                on: {
                  click: function($event) {
                    return _vm.submitRegisterForm()
                  }
                }
              },
              [_vm._v(_vm._s(_vm.$t("finish_action")))]
            )
          ],
          1
        )
      ]),
      _vm._v(" "),
      _c(
        "nav",
        { staticClass: "panel" },
        [
          _c("p", { staticClass: "panel-heading" }, [
            _vm._v(_vm._s(_vm.$t("student_data")))
          ]),
          _vm._v(" "),
          _c("a", { staticClass: "panel-block is-active" }, [
            _vm._m(0),
            _vm._v(
              "\n            " +
                _vm._s(_vm.$t("full_name")) +
                ": " +
                _vm._s(this.storeState.firstname) +
                "\n            " +
                _vm._s(this.storeState.lastname) +
                "\n        "
            )
          ]),
          _vm._v(" "),
          _c("a", { staticClass: "panel-block" }, [
            _vm._m(1),
            _vm._v(
              "\n            " +
                _vm._s(_vm.$t("email")) +
                ": " +
                _vm._s(this.storeState.email) +
                "\n        "
            )
          ]),
          _vm._v(" "),
          _c("a", { staticClass: "panel-block" }, [
            _vm._m(2),
            _vm._v(
              "\n            " +
                _vm._s(_vm.$t("idnumber")) +
                ": " +
                _vm._s(this.storeState.idnumber) +
                "\n        "
            )
          ]),
          _vm._v(" "),
          _c("a", { staticClass: "panel-block" }, [
            _vm._m(3),
            _vm._v(
              "\n            " +
                _vm._s(_vm.$t("address")) +
                ": " +
                _vm._s(this.storeState.address) +
                "\n        "
            )
          ]),
          _vm._v(" "),
          _vm._l(this.storeState.phonenumbers, function(number, index) {
            return _c("label", { key: index, staticClass: "panel-block" }, [
              _vm._m(4, true),
              _vm._v(
                "\n            " +
                  _vm._s(_vm.$t("phonenumber")) +
                  " #" +
                  _vm._s(index + 1) +
                  ": " +
                  _vm._s(number.number) +
                  "\n        "
              )
            ])
          }),
          _vm._v(" "),
          _c("div", { staticClass: "panel-block" }, [
            _c(
              "button",
              {
                staticClass:
                  "button is-link is-outlined is-fullwidth is-warning",
                on: {
                  click: function($event) {
                    return _vm.goBackToStep(0)
                  }
                }
              },
              [
                _vm._v(
                  "\n                " +
                    _vm._s(_vm.$t("edit")) +
                    "\n            "
                )
              ]
            )
          ])
        ],
        2
      ),
      _vm._v(" "),
      _vm._l(this.storeState.contacts, function(contact, contactindex) {
        return _c(
          "nav",
          { key: contactindex, staticClass: "panel" },
          [
            _c("p", { staticClass: "panel-heading" }, [
              _vm._v("Contacto #" + _vm._s(contactindex + 1))
            ]),
            _vm._v(" "),
            _c("a", { staticClass: "panel-block is-active" }, [
              _vm._m(5, true),
              _vm._v(
                "\n            " +
                  _vm._s(_vm.$t("full_name")) +
                  ": " +
                  _vm._s(contact.firstname) +
                  "\n            " +
                  _vm._s(contact.lastname) +
                  "\n        "
              )
            ]),
            _vm._v(" "),
            _c("a", { staticClass: "panel-block" }, [
              _vm._m(6, true),
              _vm._v(
                "\n            " +
                  _vm._s(_vm.$t("email")) +
                  ": " +
                  _vm._s(contact.email) +
                  "\n        "
              )
            ]),
            _vm._v(" "),
            _c("a", { staticClass: "panel-block" }, [
              _vm._m(7, true),
              _vm._v(
                "\n            " +
                  _vm._s(_vm.$t("idnumber")) +
                  ": " +
                  _vm._s(contact.idnumber) +
                  "\n        "
              )
            ]),
            _vm._v(" "),
            _c("a", { staticClass: "panel-block" }, [
              _vm._m(8, true),
              _vm._v(
                "\n            " +
                  _vm._s(_vm.$t("address")) +
                  ": " +
                  _vm._s(contact.address) +
                  "\n        "
              )
            ]),
            _vm._v(" "),
            _vm._l(contact.phonenumbers, function(number, index) {
              return _c("label", { key: index, staticClass: "panel-block" }, [
                _vm._m(9, true),
                _vm._v(
                  "\n            " +
                    _vm._s(_vm.$t("phonenumber")) +
                    " #" +
                    _vm._s(index + 1) +
                    ": " +
                    _vm._s(number.number) +
                    "\n        "
                )
              ])
            }),
            _vm._v(" "),
            _c("div", { staticClass: "panel-block" }, [
              _c(
                "button",
                {
                  staticClass:
                    "button is-link is-outlined is-fullwidth is-warning",
                  on: {
                    click: function($event) {
                      return _vm.goBackToStep(2)
                    }
                  }
                },
                [
                  _vm._v(
                    "\n                " +
                      _vm._s(_vm.$t("edit")) +
                      "\n            "
                  )
                ]
              )
            ])
          ],
          2
        )
      }),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "container has-text-centered" },
        [
          _c(
            "b-button",
            {
              staticClass: "is-large is-rounded is-success is-centered",
              class: { "is-loading": _vm.formSubmitted === true },
              on: {
                click: function($event) {
                  return _vm.submitRegisterForm()
                }
              }
            },
            [_vm._v(_vm._s(_vm.$t("finish_action")))]
          )
        ],
        1
      )
    ],
    2
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-user", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-at", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", {
        staticClass: "fas fa-passport",
        attrs: { "aria-hidden": "true" }
      })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-home", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-phone", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-user", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-at", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", {
        staticClass: "fas fa-passport",
        attrs: { "aria-hidden": "true" }
      })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-home", attrs: { "aria-hidden": "true" } })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "panel-icon" }, [
      _c("i", { staticClass: "fas fa-phone", attrs: { "aria-hidden": "true" } })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=template&id=7e777070&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=template&id=7e777070& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("ValidationObserver", {
        ref: "observer",
        scopedSlots: _vm._u([
          {
            key: "default",
            fn: function(ref) {
              var valid = ref.valid
              return [
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("birthdate") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "birthdate", rules: "required" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-datepicker", {
                                  attrs: {
                                    "show-week-number": false,
                                    placeholder: _vm.$t("Click to pick a date"),
                                    icon: "calendar-today"
                                  },
                                  model: {
                                    value: _vm.formdata.birthdate,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "birthdate", $$v)
                                    },
                                    expression: "formdata.birthdate"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("address") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "addresse", rules: "required" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: { placeholder: _vm.$t("address") },
                                  model: {
                                    value: _vm.formdata.address,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "address", $$v)
                                    },
                                    expression: "formdata.address"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c("p", { staticClass: "label" }, [
                  _vm._v(_vm._s(_vm.$t("phonenumber")))
                ]),
                _vm._v(" "),
                _vm._l(_vm.formdata.phonenumbers, function(number, index) {
                  return _c(
                    "b-field",
                    {
                      key: index,
                      attrs: {
                        label: _vm.$t("phonenumber") + " #" + (index + 1),
                        grouped: "",
                        "label-position": "on-border"
                      }
                    },
                    [
                      _c("ValidationProvider", {
                        attrs: { name: "phone number", rules: "required" },
                        scopedSlots: _vm._u(
                          [
                            {
                              key: "default",
                              fn: function(ref) {
                                var errors = ref.errors
                                return [
                                  _c("b-input", {
                                    attrs: {
                                      placeholder: _vm.$t("phonenumber")
                                    },
                                    model: {
                                      value: number.number,
                                      callback: function($$v) {
                                        _vm.$set(number, "number", $$v)
                                      },
                                      expression: "number.number"
                                    }
                                  }),
                                  _vm._v(" "),
                                  _c(
                                    "p",
                                    { staticClass: "control" },
                                    [
                                      index > 0
                                        ? _c(
                                            "b-button",
                                            {
                                              on: {
                                                click: function($event) {
                                                  return _vm.dropPhoneNumber(
                                                    index
                                                  )
                                                }
                                              }
                                            },
                                            [_vm._v(_vm._s(_vm.$t("delete")))]
                                          )
                                        : _vm._e()
                                    ],
                                    1
                                  ),
                                  _vm._v(" "),
                                  _c("p", { staticClass: "help is-danger" }, [
                                    _vm._v(_vm._s(errors[0]))
                                  ])
                                ]
                              }
                            }
                          ],
                          null,
                          true
                        )
                      })
                    ],
                    1
                  )
                }),
                _vm._v(" "),
                _c(
                  "p",
                  [
                    _c(
                      "b-button",
                      {
                        on: {
                          click: function($event) {
                            return _vm.addPhoneNumber()
                          }
                        }
                      },
                      [_vm._v(_vm._s(_vm.$t("add")))]
                    ),
                    _vm._v(
                      "\n            " +
                        _vm._s(_vm.$t("phonenumber_explainer")) +
                        "\n        "
                    )
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("profesion") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "profesion" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-input", {
                                  attrs: {
                                    placeholder: _vm.$t("profesion_example")
                                  },
                                  model: {
                                    value: _vm.formdata.profession,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "profession", $$v)
                                    },
                                    expression: "formdata.profession"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-field",
                  { attrs: { label: _vm.$t("institution") } },
                  [
                    _c("ValidationProvider", {
                      attrs: { name: "institution" },
                      scopedSlots: _vm._u(
                        [
                          {
                            key: "default",
                            fn: function(ref) {
                              var errors = ref.errors
                              return [
                                _c("b-autocomplete", {
                                  ref: "autocomplete",
                                  attrs: {
                                    data: _vm.filteredInstitutions,
                                    "allow-new": true,
                                    "open-on-focus": true,
                                    maxtags: "1",
                                    placeholder: _vm.$t("institution_example")
                                  },
                                  on: {
                                    select: function(option) {
                                      return (_vm.selected = option)
                                    }
                                  },
                                  scopedSlots: _vm._u(
                                    [
                                      {
                                        key: "header",
                                        fn: function() {
                                          return [
                                            _c(
                                              "a",
                                              {
                                                on: {
                                                  click: _vm.showAddInstitution
                                                }
                                              },
                                              [
                                                _c("span", [
                                                  _vm._v(" Add new... ")
                                                ])
                                              ]
                                            )
                                          ]
                                        },
                                        proxy: true
                                      },
                                      {
                                        key: "empty",
                                        fn: function() {
                                          return [_vm._v("No results ")]
                                        },
                                        proxy: true
                                      }
                                    ],
                                    null,
                                    true
                                  ),
                                  model: {
                                    value: _vm.formdata.institution,
                                    callback: function($$v) {
                                      _vm.$set(_vm.formdata, "institution", $$v)
                                    },
                                    expression: "formdata.institution"
                                  }
                                }),
                                _vm._v(" "),
                                _c("p", { staticClass: "help is-danger" }, [
                                  _vm._v(_vm._s(errors[0]))
                                ])
                              ]
                            }
                          }
                        ],
                        null,
                        true
                      )
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-button",
                  {
                    attrs: { type: "is-primary" },
                    on: {
                      click: function($event) {
                        return _vm.validateBeforeSubmit()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.$t("next")))]
                )
              ]
            }
          }
        ])
      })
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae&":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae& ***!
  \************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "app" } },
    [
      _c("ValidationObserver", {
        ref: "observer",
        scopedSlots: _vm._u([
          {
            key: "default",
            fn: function(ref) {
              var valid = ref.valid
              return [
                _c("section", { staticClass: "section" }, [
                  _c(
                    "div",
                    {
                      staticClass:
                        "container has-text-centered has-text-link is-size-4"
                    },
                    [
                      _c("p", [_vm._v(_vm._s(_vm.$t("profile_picture")))]),
                      _vm._v(" "),
                      !_vm.picturemandatory
                        ? _c(
                            "b-button",
                            {
                              attrs: { type: "is-primary" },
                              on: {
                                click: function($event) {
                                  return _vm.validateBeforeSubmit()
                                }
                              }
                            },
                            [
                              _vm._v(
                                "\n                " +
                                  _vm._s(
                                    _vm.$t("Skip without adding a picture")
                                  ) +
                                  "\n            "
                              )
                            ]
                          )
                        : _vm._e()
                    ],
                    1
                  )
                ]),
                _vm._v(" "),
                _c("section", { staticClass: "section" }, [
                  _c("div", { staticClass: "tile is-ancestor" }, [
                    _c(
                      "div",
                      { staticClass: "tile is-6 is-vertical is-parent" },
                      [
                        _c("div", { staticClass: "tile is-child" }, [
                          _c("div", { staticClass: "file" }, [
                            _c(
                              "label",
                              { staticClass: "file-label" },
                              [
                                _c("ValidationProvider", {
                                  attrs: {
                                    rules: "image",
                                    name: "Picture upload"
                                  },
                                  scopedSlots: _vm._u(
                                    [
                                      {
                                        key: "default",
                                        fn: function(ref) {
                                          var errors = ref.errors
                                          return [
                                            _c("input", {
                                              staticClass: "file-input",
                                              attrs: {
                                                type: "file",
                                                accept: "image/png, image/jpeg"
                                              },
                                              on: { change: _vm.onFileChange }
                                            })
                                          ]
                                        }
                                      }
                                    ],
                                    null,
                                    true
                                  )
                                }),
                                _vm._v(" "),
                                _c(
                                  "span",
                                  {
                                    staticClass: "file-cta has-text-white",
                                    staticStyle: {
                                      "background-color": "#7957d5"
                                    }
                                  },
                                  [
                                    _c("span", { staticClass: "file-icon" }, [
                                      _c("i", { staticClass: "fas fa-upload" })
                                    ]),
                                    _vm._v(" "),
                                    _c("span", { staticClass: "file-label" }, [
                                      _vm._v(
                                        "\n\t\t\t\t\t        " +
                                          _vm._s(_vm.$t("click_upload")) +
                                          "\n\t\t\t\t\t      "
                                      )
                                    ])
                                  ]
                                )
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c("p", { staticClass: "help is-danger" }, [
                              _vm._v(_vm._s(_vm.errors[0]))
                            ])
                          ])
                        ]),
                        _vm._v(" "),
                        _c(
                          "div",
                          { staticClass: "tile is-child" },
                          [
                            _c(
                              "b-button",
                              {
                                attrs: {
                                  type: "is-primary",
                                  id: "enableCapture"
                                },
                                on: { click: _vm.enableUserToTakePicture }
                              },
                              [
                                _vm._v(
                                  " " + _vm._s(_vm.$t("take_picture")) + " "
                                )
                              ]
                            )
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          { staticClass: "tile is-child" },
                          [
                            _c(
                              "b-button",
                              {
                                attrs: { type: "is-primary" },
                                on: {
                                  click: function($event) {
                                    return _vm.validateBeforeSubmit()
                                  }
                                }
                              },
                              [
                                _vm._v(
                                  "\n\t\t            \t" +
                                    _vm._s(_vm.$t("next")) +
                                    "\n\t\t            "
                                )
                              ]
                            )
                          ],
                          1
                        )
                      ]
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "tile is-vertical is-parent" }, [
                      _c("div", { staticClass: "tile is-child" }, [
                        _c("figure", { staticClass: "image" }, [
                          _c("canvas", {
                            staticStyle: {
                              position: "relative",
                              border: "1px solid #000000"
                            },
                            attrs: {
                              id: "canvas1",
                              width: "315",
                              height: "180"
                            }
                          })
                        ])
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "tile is-child" }, [
                        _c("video", {
                          staticStyle: {
                            position: "relative",
                            border: "1px solid #000000"
                          },
                          attrs: {
                            id: "player",
                            autoplay: "",
                            width: "320",
                            height: "250"
                          }
                        })
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "tile is-child mt-20" }, [
                        _c(
                          "div",
                          {
                            staticStyle: { display: "none" },
                            attrs: { id: "captureButton" }
                          },
                          [
                            _c(
                              "b-button",
                              {
                                attrs: { type: "is-primary" },
                                on: { click: _vm.takeUserPicture }
                              },
                              [_vm._v(_vm._s(_vm.$t("snap_picture")))]
                            )
                          ],
                          1
                        )
                      ])
                    ])
                  ])
                ])
              ]
            }
          }
        ])
      })
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/bootstrap.js":
/*!***********************************!*\
  !*** ./resources/js/bootstrap.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var chart_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! chart.js */ "./node_modules/chart.js/dist/Chart.js");
/* harmony import */ var chart_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(chart_js__WEBPACK_IMPORTED_MODULE_0__);
window.axios = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
var token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}



/***/ }),

/***/ "./resources/js/register-bundle sync recursive \\.vue$/":
/*!***************************************************!*\
  !*** ./resources/js/register-bundle sync \.vue$/ ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var map = {
	"./RegisterComponent.vue": "./resources/js/register-bundle/RegisterComponent.vue",
	"./RegisterContactsComponent.vue": "./resources/js/register-bundle/RegisterContactsComponent.vue",
	"./RegisterUserDataComponent.vue": "./resources/js/register-bundle/RegisterUserDataComponent.vue",
	"./RegisterUserFinishComponent.vue": "./resources/js/register-bundle/RegisterUserFinishComponent.vue",
	"./RegisterUserInfoComponent.vue": "./resources/js/register-bundle/RegisterUserInfoComponent.vue",
	"./RegisterUserPictureComponent.vue": "./resources/js/register-bundle/RegisterUserPictureComponent.vue"
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = "./resources/js/register-bundle sync recursive \\.vue$/";

/***/ }),

/***/ "./resources/js/register-bundle/RegisterComponent.vue":
/*!************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterComponent.vue ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterComponent_vue_vue_type_template_id_1c1f69ee___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterComponent.vue?vue&type=template&id=1c1f69ee& */ "./resources/js/register-bundle/RegisterComponent.vue?vue&type=template&id=1c1f69ee&");
/* harmony import */ var _RegisterComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterComponent.vue?vue&type=script&lang=js& */ "./resources/js/register-bundle/RegisterComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterComponent_vue_vue_type_template_id_1c1f69ee___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterComponent_vue_vue_type_template_id_1c1f69ee___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/register-bundle/RegisterComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/register-bundle/RegisterComponent.vue?vue&type=script&lang=js&":
/*!*************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterComponent.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/register-bundle/RegisterComponent.vue?vue&type=template&id=1c1f69ee&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterComponent.vue?vue&type=template&id=1c1f69ee& ***!
  \*******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterComponent_vue_vue_type_template_id_1c1f69ee___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterComponent.vue?vue&type=template&id=1c1f69ee& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterComponent.vue?vue&type=template&id=1c1f69ee&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterComponent_vue_vue_type_template_id_1c1f69ee___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterComponent_vue_vue_type_template_id_1c1f69ee___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/register-bundle/RegisterContactsComponent.vue":
/*!********************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterContactsComponent.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterContactsComponent_vue_vue_type_template_id_116f4576___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterContactsComponent.vue?vue&type=template&id=116f4576& */ "./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=template&id=116f4576&");
/* harmony import */ var _RegisterContactsComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterContactsComponent.vue?vue&type=script&lang=js& */ "./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterContactsComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterContactsComponent_vue_vue_type_template_id_116f4576___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterContactsComponent_vue_vue_type_template_id_116f4576___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/register-bundle/RegisterContactsComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterContactsComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterContactsComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterContactsComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=template&id=116f4576&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=template&id=116f4576& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterContactsComponent_vue_vue_type_template_id_116f4576___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterContactsComponent.vue?vue&type=template&id=116f4576& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterContactsComponent.vue?vue&type=template&id=116f4576&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterContactsComponent_vue_vue_type_template_id_116f4576___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterContactsComponent_vue_vue_type_template_id_116f4576___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserDataComponent.vue":
/*!********************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserDataComponent.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterUserDataComponent_vue_vue_type_template_id_069c9ef4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4& */ "./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4&");
/* harmony import */ var _RegisterUserDataComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterUserDataComponent.vue?vue&type=script&lang=js& */ "./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterUserDataComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterUserDataComponent_vue_vue_type_template_id_069c9ef4___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterUserDataComponent_vue_vue_type_template_id_069c9ef4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/register-bundle/RegisterUserDataComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserDataComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserDataComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserDataComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserDataComponent_vue_vue_type_template_id_069c9ef4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserDataComponent.vue?vue&type=template&id=069c9ef4&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserDataComponent_vue_vue_type_template_id_069c9ef4___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserDataComponent_vue_vue_type_template_id_069c9ef4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserFinishComponent.vue":
/*!**********************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserFinishComponent.vue ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterUserFinishComponent_vue_vue_type_template_id_1a3d4e6b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b& */ "./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b&");
/* harmony import */ var _RegisterUserFinishComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterUserFinishComponent.vue?vue&type=script&lang=js& */ "./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterUserFinishComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterUserFinishComponent_vue_vue_type_template_id_1a3d4e6b___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterUserFinishComponent_vue_vue_type_template_id_1a3d4e6b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/register-bundle/RegisterUserFinishComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserFinishComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserFinishComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserFinishComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b&":
/*!*****************************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b& ***!
  \*****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserFinishComponent_vue_vue_type_template_id_1a3d4e6b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserFinishComponent.vue?vue&type=template&id=1a3d4e6b&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserFinishComponent_vue_vue_type_template_id_1a3d4e6b___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserFinishComponent_vue_vue_type_template_id_1a3d4e6b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserInfoComponent.vue":
/*!********************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserInfoComponent.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterUserInfoComponent_vue_vue_type_template_id_7e777070___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterUserInfoComponent.vue?vue&type=template&id=7e777070& */ "./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=template&id=7e777070&");
/* harmony import */ var _RegisterUserInfoComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterUserInfoComponent.vue?vue&type=script&lang=js& */ "./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterUserInfoComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterUserInfoComponent_vue_vue_type_template_id_7e777070___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterUserInfoComponent_vue_vue_type_template_id_7e777070___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/register-bundle/RegisterUserInfoComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserInfoComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserInfoComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserInfoComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=template&id=7e777070&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=template&id=7e777070& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserInfoComponent_vue_vue_type_template_id_7e777070___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserInfoComponent.vue?vue&type=template&id=7e777070& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserInfoComponent.vue?vue&type=template&id=7e777070&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserInfoComponent_vue_vue_type_template_id_7e777070___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserInfoComponent_vue_vue_type_template_id_7e777070___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserPictureComponent.vue":
/*!***********************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserPictureComponent.vue ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterUserPictureComponent_vue_vue_type_template_id_22fe72ae___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae& */ "./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae&");
/* harmony import */ var _RegisterUserPictureComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterUserPictureComponent.vue?vue&type=script&lang=js& */ "./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterUserPictureComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterUserPictureComponent_vue_vue_type_template_id_22fe72ae___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterUserPictureComponent_vue_vue_type_template_id_22fe72ae___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/register-bundle/RegisterUserPictureComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=script&lang=js&":
/*!************************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserPictureComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserPictureComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserPictureComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae&":
/*!******************************************************************************************************!*\
  !*** ./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae& ***!
  \******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserPictureComponent_vue_vue_type_template_id_22fe72ae___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/register-bundle/RegisterUserPictureComponent.vue?vue&type=template&id=22fe72ae&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserPictureComponent_vue_vue_type_template_id_22fe72ae___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterUserPictureComponent_vue_vue_type_template_id_22fe72ae___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/register-bundle/eventBus.js":
/*!**************************************************!*\
  !*** ./resources/js/register-bundle/eventBus.js ***!
  \**************************************************/
/*! exports provided: EventBus */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EventBus", function() { return EventBus; });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);

var EventBus = new vue__WEBPACK_IMPORTED_MODULE_0___default.a();

/***/ }),

/***/ "./resources/js/register-bundle/register.js":
/*!**************************************************!*\
  !*** ./resources/js/register-bundle/register.js ***!
  \**************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-i18n */ "./node_modules/vue-i18n/dist/vue-i18n.esm.js");
/* harmony import */ var _vue_i18n_locales__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./vue-i18n-locales */ "./resources/js/register-bundle/vue-i18n-locales.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");
/* harmony import */ var vee_validate_dist_rules__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vee-validate/dist/rules */ "./node_modules/vee-validate/dist/rules.js");
__webpack_require__(/*! ../bootstrap */ "./resources/js/bootstrap.js");

window.Vue = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
Vue.use(__webpack_require__(/*! vue-moment */ "./node_modules/vue-moment/dist/vue-moment.js"));


Vue.use(vue_i18n__WEBPACK_IMPORTED_MODULE_0__["default"]);
var lang = document.documentElement.lang.substr(0, 2); // or however you determine your current app locale

var i18n = new vue_i18n__WEBPACK_IMPORTED_MODULE_0__["default"]({
  locale: lang,
  messages: _vue_i18n_locales__WEBPACK_IMPORTED_MODULE_1__["default"]
});




Object(vee_validate__WEBPACK_IMPORTED_MODULE_2__["configure"])({
  classes: {
    valid: 'is-success',
    // one class
    invalid: 'is-danger' // multiple classes

  }
}); // Add the required rule

Object(vee_validate__WEBPACK_IMPORTED_MODULE_2__["extend"])('required', vee_validate_dist_rules__WEBPACK_IMPORTED_MODULE_3__["required"]); // Add the email rule

Object(vee_validate__WEBPACK_IMPORTED_MODULE_2__["extend"])('email', vee_validate_dist_rules__WEBPACK_IMPORTED_MODULE_3__["email"]);
Object(vee_validate__WEBPACK_IMPORTED_MODULE_2__["extend"])('min', vee_validate_dist_rules__WEBPACK_IMPORTED_MODULE_3__["min"]);
Object(vee_validate__WEBPACK_IMPORTED_MODULE_2__["extend"])('length', vee_validate_dist_rules__WEBPACK_IMPORTED_MODULE_3__["length"]); // Add the image rule

Object(vee_validate__WEBPACK_IMPORTED_MODULE_2__["extend"])('image', vee_validate_dist_rules__WEBPACK_IMPORTED_MODULE_3__["image"]); // Register vee-validate globally

Vue.component('ValidationProvider', vee_validate__WEBPACK_IMPORTED_MODULE_2__["ValidationProvider"]);
/**
 * Automatically register Vue components
 */

var files = __webpack_require__("./resources/js/register-bundle sync recursive \\.vue$/");

files.keys().map(function (key) {
  return Vue.component(key.split('/').pop().split('.')[0], files(key)["default"]);
});
var app = new Vue({
  el: '#app',
  i18n: i18n
});

/***/ }),

/***/ "./resources/js/register-bundle/store.js":
/*!***********************************************!*\
  !*** ./resources/js/register-bundle/store.js ***!
  \***********************************************/
/*! exports provided: store */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "store", function() { return store; });
/* harmony import */ var moment__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! moment */ "./node_modules/moment/moment.js");
/* harmony import */ var moment__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(moment__WEBPACK_IMPORTED_MODULE_0__);

var store = {
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
    birthdate: null,
    profession: null,
    institution: null,
    userPicture: null,
    contacts: []
  },
  updateUserData: function updateUserData(data) {
    this.state.firstname = data.firstname;
    this.state.lastname = data.lastname;
    this.state.email = data.email;
    this.state.password = data.password;
    this.state.idnumber_type = data.idnumber_type;
    this.state.idnumber = data.idnumber;
    this.state.address = data.address;
    this.state.phonenumber = data.phonenumber;
    this.state.tc_consentdata = data.tc_consentdata;
  },
  updatePictureData: function updatePictureData(data) {
    this.state.userPicture = data;
  },
  updateInfoData: function updateInfoData(data) {
    this.state.address = data.address, this.state.birthdate = moment__WEBPACK_IMPORTED_MODULE_0___default()(data.birthdate).format(), this.state.profession = data.profession, this.state.institution = data.institution, this.state.phonenumbers = data.phonenumbers;
  },
  updateContactsData: function updateContactsData(data) {
    this.state.contacts = data;
  }
};

/***/ }),

/***/ "./resources/js/register-bundle/vue-i18n-locales.js":
/*!**********************************************************!*\
  !*** ./resources/js/register-bundle/vue-i18n-locales.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  "en": {
    "step1": "Student Data",
    "step2": "Additional Data",
    "step3": "Profile Picture",
    "step4": "Contacts",
    "step5": "Finish",
    "language": "Form language",
    "firstname": "First name",
    "lastname": "Last Name",
    "email": "Email",
    "idnumber": "ID Number",
    "password": "Password",
    "next": "Next",
    "birthdate": "Birth Date",
    "address": "Address",
    "phonenumber": "Phone Number",
    "delete": "Delete",
    "add": "Add one",
    "phonenumber_explainer": "You may add your other numbers here.",
    "profesion": "Profesion",
    "profesion_example": "e.g. student",
    "institution": "Institution",
    "institution_example": "e.g. University of Notre Dame",
    "contact": "Contact",
    "contact_explainer1": "Students under 18 need to add at least one additional contact.",
    "contact_explainer2": "You may also add an emergency contact",
    "contact_explainer3": "If you want your invoice with a different name, please add it here",
    "finish_subtitle": "Please check your data and then click on 'Create account'",
    "finish_action": "Create account",
    "student_data": "Student Data",
    "full_name": "Full name",
    "edit": "Edit",
    "cedula": "Cédula",
    "passport": "ID Number",
    "cedula_number": "Cédula number",
    "passport_number": "Passport number",
    "iddocument": "ID document",
    "institution_save": "Press ENTER to confirm your institution",
    "profile_picture": "Select a profile picture or take a picture.",
    "take_picture": "Take a picture",
    "snap_picture": "Capture",
    "click_upload": "Click to upload",
    "Use this data for invoices": "Use this data for invoices",
    "The form is invalid, please check the fields marked in red and try again": "The form is invalid, please check the fields marked in red and try again",
    "An account with this email already exists.": "An account with this email already exists.",
    "The user could not be created. Please get in touch with an administrator.": "The user could not be created. Please get in touch with an administrator.",
    "The account was created successfully": "The account was created successfully",
    "Click to pick a date": "Click to pick a date",
    "Skip without adding a picture": "Skip without adding a picture"
  },
  "es": {
    "step1": "Datos del estudiante",
    "step2": "Informacion addicional",
    "step3": "Foto de perfil",
    "step4": "Contactos addicionales",
    "step5": "Finalizacion",
    "language": "Idioma del formulario",
    "firstname": "Nombres",
    "lastname": "Apellidos",
    "email": "Correo electrónico",
    "idnumber": "Número de identificacion",
    "password": "Contraseña",
    "next": "Siguiente",
    "birthdate": "Fecha de nacimiento",
    "address": "Dirección",
    "phonenumber": "Número de teléfono",
    "delete": "Eliminar",
    "add": "Agregar otro",
    "phonenumber_explainer": "Si tiene otros números, los puede agregar también.",
    "profesion": "Profesión",
    "profesion_example": "e.g. estudiante, médico...",
    "institution": "Institución",
    "institution_example": "e.g. Universidad de Cuenca",
    "contact": "Contacto addicional",
    "contact_explainer1": "Los estudiantes menores de edad tienen que agregar el contacto de su representante",
    "contact_explainer2": "Si desea, puede agregar los datos de una persona que podemos contactar en caso de emergencia.",
    "contact_explainer3": "Si desea la factura con otros datos, por favor agregar un contacto en este espacio también.",
    "finish_subtitle": "Comprobe sus datos una última vez. Cuando todo esta correcto, haz click en 'confirmar la creación de la cuenta'",
    "finish_action": "Confirmar la creación de la cuenta",
    "student_data": "Datos del estudiante",
    "full_name": "Nombre completo",
    "edit": "Corregir",
    "cedula": "Cédula",
    "passport": "Número de identificación",
    "cedula_number": "Número de cédula",
    "passport_number": "Número de pasaporte",
    "iddocument": "Documento de identificación",
    "institution_save": "Pulse ENTER para continuar",
    "profile_picture": "Seleccione una foto de perfil o tome una foto",
    "take_picture": "Toma una foto",
    "snap_picture": "Capturar",
    "click_upload": "Haga clic para cargar",
    "Use this data for invoices": "Facturar a este nombre",
    "The form is invalid, please check the fields marked in red and try again": "El formulario no esta completo! Por favor verifique los campos en rojo.",
    "An account with this email already exists.": "Ya existe una cuenta registrada con este correo electrónico",
    "The user could not be created. Please get in touch with an administrator.": "Error al crear la cuenta.",
    "The account was created successfully": "La cuenta fue creada con exito.",
    "Click to pick a date": "Haz click para seleccionar una fecha",
    "Skip without adding a picture": "Continuar sin foto"
  },
  "fr": {
    "step1": "Informations de l'étudiant",
    "step2": "Informations complémentaires",
    "step3": "Image de profil",
    "step4": "Contacts",
    "step5": "Fin",
    "language": "Langue du formulaire",
    "firstname": "Prénom",
    "lastname": "Nom",
    "email": "Email",
    "idnumber": "Numéro de pièce d'identité",
    "password": "Mot de passe",
    "next": "Suivant",
    "birthdate": "Date de naissance",
    "address": "Addresse",
    "phonenumber": "Numéro de téléphone",
    "delete": "Supprimer",
    "add": "Ajouter",
    "phonenumber_explainer": "Si vous avez plusieurs numéros de téléphone, vous pouvez les ajouter ici",
    "profesion": "Profession",
    "profesion_example": "par exemple : étudiant",
    "institution": "Institution",
    "institution_example": "par exemple : Université Lyon-III",
    "contact": "Contact",
    "contact_explainer1": "Les étudiants de moins de 18 ans doivent obligatoirement ajouter le contact de leur représentant légal.",
    "contact_explainer2": "Vous pouvez aussi ajouter un contact d'urgence",
    "contact_explainer3": "Enfin, ajoutez ici les coordonnées de la personne au nom de qui émettre les factures.",
    "finish_subtitle": "Vérifiez vos données puis cliquez sur 'créer le compte'",
    "finish_action": "Créer le compte",
    "student_data": "Informations de l'étudiant",
    "full_name": "Nom complet",
    "edit": "Modifier",
    "cedula": "Cédula",
    "passport": "Pièce d'identité",
    "cedula_number": "Numéro de cédula",
    "passport_number": "Numéro de passeport",
    "iddocument": "Document d'identité",
    "institution_save": "Appuyez sur ENTRÉE pour confirmer",
    "profile_picture": "Sélectionnez une photo de profil ou prenez une photo.",
    "take_picture": "Prendre une photo",
    "snap_picture": "Capturer",
    "click_upload": "Cliquez pour télécharger",
    "Use this data for invoices": "Utiliser ces données pour les factures",
    "The form is invalid, please check the fields marked in red and try again": "Le formulaire est incomplet, vérifiez les champ en rouge et réessayez.",
    "An account with this email already exists.": "Il existe déjà un compte avec cette adresse mail.",
    "The user could not be created. Please get in touch with an administrator.": "Le compte n'a pas pu être créé. Veuillez contacter un administrateur.",
    "The account was created successfully": "Le compte a été créé avec succès",
    "Click to pick a date": "Cliquez pour choisir une date",
    "Skip without adding a picture": "Passer cette étape sans ajouter de photo"
  }
});

/***/ }),

/***/ 1:
/*!********************************************************!*\
  !*** multi ./resources/js/register-bundle/register.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/thomas/academico-sis/academico/resources/js/register-bundle/register.js */"./resources/js/register-bundle/register.js");


/***/ })

},[[1,"/js/manifest","/js/vendor"]]]);