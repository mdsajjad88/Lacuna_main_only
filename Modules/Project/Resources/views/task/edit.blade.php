<div class="modal-dialog modal-lg" role="document">
    {!! Form::open([
        'url' => action([\Modules\Project\Http\Controllers\TaskController::class, 'update'], $project_task->id),
        'id' => 'project_task_form',
        'method' => 'put',
    ]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('project::lang.edit_task')
            </h4>
        </div>
        <div class="modal-body">
            {{-- and {{ $project_task->project_id }} --}}
            @if ($leader)
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('project_id', 'Change Project') !!}
                        {!! Form::select('project_id', $projects, $project_task->project_id, [
                            'class' => 'form-control select2',
                            'style' => 'width: 100%;',
                        ]) !!}
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('Admin#' . session('business.id')))
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('project_id', 'Change Project') !!}
                    {!! Form::select('project_id', $projects, $project_task->project_id, [
                        'class' => 'form-control select2',
                        'style' => 'width: 100%;',
                    ]) !!}
                </div>
            </div>
            @else
                {!! Form::hidden('project_id', $project_task->project_id, ['class' => 'form-control']) !!}
            @endif

            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                        {!! Form::label('subject', __('project::lang.subject') . ':*' )!!}
                        {!! Form::text('subject', $project_task->subject, ['class' => 'form-control', 'required' ]) !!}
                   </div>
                </div>
            </div>
            {{-- {!! Form::hidden('project_id', $project_task->project_id, ['class' => 'form-control']) !!} --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', $project_task->description, [
                            'class' => 'form-control ',
                            'id' => 'description',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('start_date', __('business.start_date') . ':') !!}
                        {!! Form::text('start_date', !empty($project_task->start_date) ? @format_date($project_task->start_date) : '', [
                            'class' => 'form-control datepicker',
                            'readonly',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('due_date', __('project::lang.due_date') . ':') !!}
                        {!! Form::text('due_date', !empty($project_task->due_date) ? @format_date($project_task->due_date) : '', [
                            'class' => 'form-control datepicker',
                            'readonly',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('priority', __('project::lang.priority') . ':*') !!}
                        {!! Form::select('priority', $priorities, $project_task->priority, [
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
                        {!! Form::select('status', $statuses, $project_task->status, [
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
                        {!! Form::select('user_id[]', $project_members, $project_task->members->pluck('id'), [
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
                            @php
                                $formattedValue = '<span title="Label" style="color: ' . $level['color'] . '; background-color: ' . $level['bg'] . '; border-radius: 10%; padding: 0px 4px 2px 4px; font-weight: bold;"><small>' . $level['name'] . '</small></span>';
                            @endphp
                            <option
                                class="select2" style="color: {{ $level['color'] }}; background-color: {{ $level['bg'] }};"
                                value="{{ $formattedValue }}"
                                {{ $project_task->custom_field_1 === $formattedValue ? 'selected' : '' }}
                            >
                                {!! $formattedValue !!}
                            </option>
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
                            {!! Form::number('custom_field_2', $project_task->custom_field_2, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_3', __('project::lang.task_custom_field_3') . ':') !!}
                        {!! Form::text('custom_field_3', $project_task->custom_field_3, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('custom_field_4', __('project::lang.task_custom_field_4') . ':') !!}
                        {!! Form::text('custom_field_4', $project_task->custom_field_4, ['class' => 'form-control']) !!}
                    </div>
                </div> --}}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm ladda-button" data-style="expand-right">
                <span class="ladda-label">@lang('messages.update')</span>
            </button>
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
