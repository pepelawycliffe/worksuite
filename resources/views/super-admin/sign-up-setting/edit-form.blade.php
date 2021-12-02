<form id="editSettings" class="ajax-form" data-language-id="{{ $registrationMessage ? $registrationMessage->language_setting_id : ''}}">
  @csrf
  <div class="row">
      <div class="col-sm-12 col-md-12 col-xs-12">
          <div class="form-group">
              <label for="title">@lang('app.menu.message')</label>
              <textarea class="form-control summernote" rows="6" name="message" id="message">{{ $registrationMessage ? $registrationMessage->message : '' }}</textarea>
          </div>
      </div>
  </div>

  <button type="button" id="save-form"
          class="btn btn-success waves-effect waves-light m-r-10">
      @lang('app.update')
  </button>
</form>