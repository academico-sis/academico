<template>
<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                {{ $t('Books') }}

                <div class="card-header-actions">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#AddBookModal">
                        <i class="la la-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>{{ $t('Book') }}</th>
                        <th>{{ $t('Status') }}</th>
                        <th>{{ $t('Code') }}</th>
                        <th>{{ $t('Expiry Date') }}</th>
                        <th></th>
                    </thead>

                    <tbody>
                        <tr v-for="book in this.studentBooks" v-bind:key="book.pivot.id">
                            <td>{{ book.name }}</td>

                            <td v-if="editing == book.pivot.id">
                                <select v-model="status">
                                    <option value="1">NON PAYÉ</option>
                                    <option value="2">PAYÉ</option>
                                </select>
                            </td>
                            <td v-else>
                                <span v-if="book.pivot.status_id == 1">NON PAYÉ</span>
                                <span v-if="book.pivot.status_id == 2">PAYÉ</span>
                            </td>

                            <td v-if="editing == book.pivot.id">
                                <input type="text" v-model="code">
                            </td>
                            <td v-else>{{ book.pivot.code }}</td>

                            <td v-if="editing == book.pivot.id">
                                <input type="date" v-model="expiry_date">
                            </td>
                            <td v-else>{{ book.pivot.expiry_date }}</td>

                            <td v-if="editing == book.pivot.id">
                                <button @click="saveBook()" class="btn btn-sm btn-success">
                                    <i class="la la-save"></i>
                                </button>
                            </td>
                            <td v-else>
                                <button @click="editBook(book)" class="btn btn-sm btn-warning">
                                    <i class="la la-pencil"></i>
                                </button>

                                <a :href="'/bookstudent?book_student_id='+book.pivot.id" class="btn btn-sm btn-primary">
                                    <i class="la la-share"></i>
                                </a>

                                <button @click="deleteBook(book.pivot.id)" class="btn btn-sm btn-danger">
                                    <i class="la la-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</template>

<script>
export default {

    props: ['student', 'books'],

    data () {
        return {
            studentBooks: this.books,
            editing: null,
            status: null,
            code: null,
            expiry_date: null,
        }
    },

    methods: {
        editBook(book)
        {
            this.editing = book.pivot.id
            this.status = book.pivot.status_id
            this.code = book.pivot.code
            this.expiry_date = book.pivot.expiry_date
        },

        saveBook()
        {
            axios.put(`/bookstudent`, {
                book_student_id: this.editing,
                status: this.status,
                code: this.code,
                expiry_date: this.expiry_date,
            }).then(response => {
                this.studentBooks = response.data
                this.editing = null
            })
        },

        deleteBook(book_student_id) {
		// ask for confirmation before deleting an item

		swal({
		  title: "Attention !",
		  text: "Voulez-vous vraiment supprimer ce livre ?",
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
		}).then((value) => {
			if (value) {
                axios.delete(`/bookstudent`, {
                    data: {
                        book_student_id: book_student_id
                    }
                }).then(response => {
                    this.studentBooks = response.data
                })
			}
		});

      },
    }
}
</script>

<style>

</style>
