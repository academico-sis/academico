    <!-- Add a book to student Modal-->
    <div class="modal fade" id="AddBookModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Add a book')</h4>
                <div class="modal-header-action">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
              
                <form action="{{ route('addBook') }}" method="post">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                
                <div class="form-group">
                    <label class="required">{{ __('Book') }}</label>
                    <select class="form-control" required name="book_id" id="book_id">
                        @foreach ($books as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="code">@lang('Code')</label>
                    <input class="form-control" type="text" id="code" name="code">
                </div>

                <div class="form-group">
                    <label for="expiry_date">@lang('Valid until')</label>
                    <input class="form-control" type="date" id="expiry_date" name="expiry_date">
                </div>

                <div class="form-group">
                    <label class="required">{{ __('Status') }}</label>
                    <select class="form-control" required name="status_id" id="status_id">
                        @foreach ($statuses as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
              <button type="submit" class="btn btn-success">@lang('Save')</button>
            </div>
        </form>
          </div>
        </div>
      </div>
    </div>