    <!-- Add additional user data Modal-->
    <div class="modal fade" id="userDataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Add a new contact')</h4>
                <div class="modal-header-action">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
              
                <form action="{{ route('addContact') }}" method="post">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                
                <div class="form-group">
                    <label class="required">{{ __('Contact Type') }}</label>
                    <select class="form-control" required name="contact_type" id="contact_type">
                        @foreach ($contact_types as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="firstname">@lang('Firstname')</label>
                    <input class="form-control" required type="text" id="firstname" name="firstname">
                </div>

                <div class="form-group">
                    <label for="lastname">@lang('Name')</label>
                    <input class="form-control" required type="text" id="lastname" name="lastname">
                </div>

                <div class="form-group">
                    <label for="email">@lang('Email')</label>
                    <input class="form-control" required type="text" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">@lang('Address')</label>
                <input class="form-control" required type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="phone_number">@lang('Phone Number')</label>
                <input class="form-control" required type="text" id="phone_number" name="phone_number">
            </div>

            <div class="form-group">
                <label for="idnumber">@lang('ID Number')</label>
                <input class="form-control" required type="text" id="idnumber" name="idnumber">
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
              <button type="submit" class="btn btn-success">@lang('Save new Contact')</button>
            </div>
        </form>
          </div>
        </div>
      </div>
    </div>