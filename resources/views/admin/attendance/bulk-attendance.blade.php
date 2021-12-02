<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.attendance.bulkAttendance')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

            {!! Form::open(['id'=>'bulk-attendance','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="form-body ">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" >@lang('app.department')</label>
                            <select class="select2 m-b-10 select2-multiple " multiple="multiple"
                                    data-placeholder="@lang('app.department')" name="group_id[]" id="groupID">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ ucwords($group->team_name) }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" >@lang('app.employees')</label>
                            <select class="select2 m-b-10 select2-multiple " multiple="multiple"
                                    data-placeholder="@lang('app.employees')" name="user_id[]" id="userID">
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)
                                            (YOU) @endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">@lang('app.select') @lang('app.month')</label>
                            <select class="select2 form-control" data-placeholder="" name="month" id="month">
                                <option @if($month == '01') selected @endif value="01">@lang('app.january')</option>
                                <option @if($month == '02') selected @endif  value="02">@lang('app.february')</option>
                                 <option @if($month == '03') selected @endif value="03">@lang('app.march')</option>
                                 <option @if($month == '04') selected @endif value="04">@lang('app.april')</option>
                                 <option @if($month == '05') selected @endif value="05">@lang('app.may')</option>
                                 <option @if($month == '06') selected @endif value="06">@lang('app.june')</option>
                                 <option @if($month == '07') selected @endif value="07">@lang('app.july')</option>
                                 <option @if($month == '08') selected @endif value="08">@lang('app.august')</option>
                                 <option @if($month == '09') selected @endif value="09">@lang('app.september')</option>
                                 <option @if($month == '10') selected @endif value="10">@lang('app.october')</option>
                                 <option @if($month == '11') selected @endif value="11">@lang('app.november')</option>
                                 <option @if($month == '12') selected @endif value="12">@lang('app.december')</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">@lang('app.select') @lang('app.year')</label>
                            <select class="select2 form-control" data-placeholder="" id="year" name="year">
                                @for($i = $year; $i >= ($year-4); $i--)
                                    <option @if($i == $year) selected @endif value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group bootstrap-timepicker timepicker">
                            <label>@lang('modules.attendance.clock_in') </label>
                            <input type="text" name="clock_in_time" class="form-control a-timepicker"   autocomplete="off"   id="clock-in" >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group bootstrap-timepicker timepicker">
                            <label>@lang('modules.attendance.clock_out')</label>
                            <input type="text" name="clock_out_time" id="clock-out"
                                   class="form-control b-timepicker"   autocomplete="off" >
                        </div>
                    </div>
                </div>
                <div class="row m-t-15">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" >@lang('modules.attendance.late')</label>
                            <div class="switchery-demo">
                                <input type="checkbox" class="js-switch change-module-setting" data-color="#ed4040" id="late"  />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label" >@lang('modules.attendance.halfDay')</label>
                            <div class="switchery-demo">
                                <input type="checkbox" class="js-switch change-module-setting" data-color="#ed4040" id="halfday"  />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.attendance.working_from')</label>
                            <input type="text" name="working_from" id="working-from"
                                   class="form-control" value="">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <button type="button" onclick="saveAttendance()" class="btn btn-success text-white save-attendance"><i
                                        class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                    </div>
                </div>

            </div>
            {!! Form::close() !!}
    </div>
</div>
<script>

    $("#userID").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    $("#groupID").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('.a-timepicker').timepicker({
        @if($global->time_format == 'H:i')
        showMeridian: false,
        @endif
        minuteStep: 1
    });
    $('.b-timepicker').timepicker({
        @if($global->time_format == 'H:i')
        showMeridian: false,
        @endif
        minuteStep: 1,
        defaultTime: false
    });

    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());

    });
    function saveAttendance() {
        $.easyAjax({
            url: '{{route('admin.attendances.bulk-store')}}',
            type: "POST",
            container: '#bulk-attendance',
            data: $('#bulk-attendance').serialize(),
            success: function (response) {

            }
        })
    }
</script>