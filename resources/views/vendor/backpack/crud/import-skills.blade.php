@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Importer des compétences</span>
<br>
            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')

    <div class="row">
        <div class="{{ $crud->getCreateContentClass() }}">
            <p>Vous devez utiliser un fichier CSV dans le même format que celui donné par le bouton "exporter" de la liste de compétences avec une colonne "Type", une colonne "Nom" (de la compétence) et une colonne "Niveau".</p>

            @include('crud::inc.grouped_errors')

            <div class="card">
                <div class="card-body row">
            <form method="post"
                  action="{{ url($crud->route.'/import') }}"
                  enctype="multipart/form-data"
            >
                {!! csrf_field() !!}

                <input
                        type="file"
                        name="file"
                        @include('crud::fields.inc.attributes', ['default_class' => 'file_input backstrap-file-input'])
                >
                <label class="backstrap-file-label" for="customFile">Fichier CSV des compétences à importer</label>

                <hr>

                <label for="group">Nom du preset de compétences à créer.</label>

                <input
                        type="text"
                        name="group"
                        @include('crud::fields.inc.attributes')
                >
                <p>Si vous remplissez le champ ci-dessus, un nouveau type d'évaluation sera créé et les compétences importées seront associées à ce type d'évaluation. Attention il ne faut pas que le nom soit déjà utilisé pour un autre type d'évaluation. Si vous laissez vide, les compétences seront importées sans créer de type d'évaluation.</p>

                <hr>

                <button class="btn btn-primary" type="submit">Importer</button>
            </form>
                </div>
            </div>
        </div>
    </div>

@endsection

