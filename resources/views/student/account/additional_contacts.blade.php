@extends('backpack::blank')

@section('after_styles')
    <style media="screen">
        .backpack-profile-form .required::after {
            content: ' *';
            color: red;
        }
    </style>
@endsection

@section('header')
    <section class="container-fluid">

        <h2>
            {{ trans('backpack::base.my_account') }}
        </h2>

    </section>
@endsection

@section('content')
    <div id="app">
        <div class="row">
            <div class="col-md-4">
                @include('student.account.sidemenu')
            </div>

            <div class="col-md-8">
                <div class="row">
                    <div class="card">
                        <div class="card-body text-center">

                            <h4>@lang('Please check the additional contact data associated to your account')</h4>

                            <h4>@lang('You also need to add the invoice information here')</h4>

                            <h4>@lang('Students under 18, please add contact data from your legal representatives')</h4>

                            <a class="btn btn btn-primary" data-toggle="modal" data-target="#userDataModal">
                                <i class="la la-plus"></i> @lang('Create another Contact')
                            </a>

                        </div>
                    </div>
                </div><!-- /.row -->

                <div class="row">

                    @foreach ($user->student->contacts as $contact)

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    @lang('Additional Contact')
                                    @if(isset($contact->relationship))
                                        ({{ $contact->relationship->name }})
                                    @endif

                                    <div class="card-header-actions">
                                        <a class="btn btn-sm btn-warning" href="/contact/{{$contact->id}}/edit">
                                            <i class="la la-pencil"></i>
                                        </a>

                                        <a class="btn btn-sm btn-danger" href="#"
                                           onclick="if(confirm('Seguro que quiere eliminar este contacto?')) deleteContact({{ $contact->id }})">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <p>@lang('Firstname') :
                                        <span class="input-lg" id="firstname">{{ $contact->firstname }}</span></p>

                                    <p>@lang('Lastname') :
                                        <span class="input-lg" id="lastname">{{ $contact->lastname }}</span></p>


                                    <p>@lang('ID Number') :
                                        <span class="input-lg" id="idnumber">{{ $contact->idnumber }}</span></p>


                                    <p>@lang('Address') :
                                        <span class="input-lg" id="address">{{ $contact->address }}</span></p>


                                    <p>@lang('Email') :
                                        <span class="input-lg" id="email">{{ $contact->email }}</span></p>

                                    <contact-phone-number-update-component
                                        :contact="{{ $contact->id }}"></contact-phone-number-update-component>

                                </div><!-- /.card-body -->
                            </div><!-- /.card -->
                        </div><!-- /.col -->
                    @endforeach

                </div><!-- /.row -->

            </div><!-- /.col md-9 -->
        </div><!-- row -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h4>@lang('When everything is ready, please confirm that your data is up-to-date')</h4>

                        <form action="/edit-contacts" method="post">
                            @csrf
                            <button class="btn btn-lg btn-success" type='submit'><i
                                    class="la la-check"></i> @lang('Finish update')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_scripts')
    @include('partials.create_new_contact', ['student' => $user->student])

    <script src="/js/app.js"></script>


    <script>
        function deleteContact(contact) {
            axios.delete(`/contact/${contact}/delete`)
                .then(response => {
                    window.location.reload()
                })
                .catch(error => {
                    console.log(error);
                });
        }
    </script>
@endsection
