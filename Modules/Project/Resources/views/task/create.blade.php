<div class="modal-dialog modal-lg" role="document">
    {!! Form::open([
        'action' => '\Modules\Project\Http\Controllers\TaskController@store',
        'id' => 'project_task_form',
        'method' => 'post',
    ]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('project::lang.create_task')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('subject', __('project::lang.subject') . ':*') !!}
                        {!! Form::text('subject', null, ['class' => 'form-control', 'required', 'maxlength' => '181']) !!}
                        <small id="subject-limit-message" style="display: none; color: red;">Maximum 180 characters allowed</small>
                    </div>
                </div>
            </div>
            {!! Form::hidden('project_id', $project_id, ['class' => 'form-control']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control ', 'id' => 'description']) !!}
                    </div>
                </div>
            </div>
           
            
            <div hidden class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="form-group toggleMedia">
                            <label for="fileupload">
                                @lang('project::lang.file_upload'):
                            </label>
                            
                        </div>
                        <input type="file" class="form-control" id="custom_field_1" name="custom_field_1[]" value="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('start_date', __('business.start_date') . ':') !!}
                        {!! Form::text('start_date', '', ['class' => 'form-control datepicker', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('due_date', __('project::lang.due_date') . ':') !!}
                        {!! Form::text('due_date', '', ['class' => 'form-control datepicker', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('priority', __('project::lang.priority') . ':*') !!}
                        {!! Form::select('priority', $priorities, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status', __('sale.status') . ':*') !!}
                        {!! Form::select('status', $statuses, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('user_id', __('project::lang.members') . ':*') !!}
                        {!! Form::select('user_id[]', $project_members, null, [
                            'class' => 'form-control select2',
                            'multiple',
                            'required',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('custom_field_1', __('Label') . ':') !!}
                    <select name="custom_field_1" class="form-control">
                        <option value="">Select Label</option>
                        @foreach($levels as $level)
                        <option class="select2" style="color: {{ $level['color'] }}; background-color: {{ $level['bg'] }};" value="{{ '<span title="Label" style="color: ' . $level['color'] . '; background-color: ' . $level['bg'] . '; border-radius: 10%; padding: 0px 4px 2px 4px; font-weight: bold;"><small>' . $level['name'] . '</small></span>' }}">{{ $level['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_2', __('Estimated Hours') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-clock"></i>
                            </span>
                            {!! Form::number('custom_field_2', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_3', __('project::lang.task_custom_field_3') . ':') !!}
                        {!! Form::text('custom_field_3', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_4', __('project::lang.task_custom_field_4') . ':') !!}
                        {!! Form::text('custom_field_4', null, ['class' => 'form-control']) !!}
                    </div>
                </div> --}}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm ladda-button" data-style="expand-right">
                <span class="ladda-label">@lang('messages.save')</span>
            </button>

            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
