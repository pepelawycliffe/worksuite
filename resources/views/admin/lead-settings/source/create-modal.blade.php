<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.addNew') @lang('modules.lead.leadSource')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('app.name')</th>
                        <th>@lang('app.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leadSources as $key=>$leadSource)
                        <tr id="cat-{{ $leadSource->id }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ ucwords($leadSource->type) }}</td>
                            <td><a href="javascript:;" data-type-id="{{ $leadSource->id }}"
                                    class="btn btn-sm btn-danger btn-rounded delete-type">@lang("app.remove")</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">@lang('messages.noProjectCategory')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {!! Form::open(['id' => 'addLeadSource', 'class' => 'ajax-form', 'method' => 'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('modules.lead.leadSource')</label>
                        <input type="text" name="type" id="type" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" id="save-group" class="btn btn-success"> <i class="fa fa-check"></i>
                @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    // Store lead source
    $('#addLeadSource').on('submit', (e) => {
        e.preventDefault();
        $.easyAjax({
            url: '{{ route('admin.lead-source-settings.store') }}',
            container: '#addLeadSource',
            type: "POST",
            data: $('#addLeadSource').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    var options = [];
                    var rData = [];
                    rData = response.optionData;
                    $('#source_id').html(rData);
                    $("#source_id").select2();
                    $('#projectCategoryModal').modal('hide');
                }
            }
        })
    });
    $('body').on('click', '.delete-type', function() {
        var id = $(this).data('type-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.leadSource')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('messages.deleteConfirmation')",
            cancelButtonText: "@lang('messages.confirmNoArchive')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.lead-source-settings.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            var options = [];
                            var rData = [];
                            rData = response.optionData;
                            $('#source_id').html(rData);
                            $("#source_id").select2();
                            $('#projectCategoryModal').modal('hide');
                        }
                    }
                });
            }
        });
    });
</script>
