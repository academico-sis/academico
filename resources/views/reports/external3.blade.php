@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>@lang('Partnerships')</h2>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                
                <div class="card-body">

                    <div class="alert alert-info" role="alert">Cliquez sur les partenariats pour voir les détails</div>

                    <table class="table table-striped">
                        <thead>
                            <th>@lang('Name')</th>
                            <th>@lang('Start date')</th>
                            <th>@lang('End date')</th>
                        </thead>

                        <tbody>
                            @foreach ($partners as $partner)
                                <tr>
                                    <td>
                                        <a href="{{ route('partnerReport', ['partner' => $partner->id]) }}">{{ $partner->name }}</a>
                                    </td>

                                    <td>
                                        {{ $partner->formatted_start_date }}
                                    </td>

                                    <td>
                                        {{ $partner->formatted_end_date }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

    
        </div>


    </div>
@endsection
