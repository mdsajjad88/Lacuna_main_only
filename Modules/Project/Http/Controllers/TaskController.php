<?php

namespace Modules\Project\Http\Controllers;

use App\Media;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectMember;
use Modules\Project\Entities\ProjectTask;
use Modules\Project\Utils\ProjectUtil;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $projectUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param CommonUtil, ProjectUtil, ModuleUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ProjectUtil $projectUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->projectUtil = $projectUtil;
        $this->moduleUtil = $moduleUtil;
        $this->priority_colors = ProjectTask::priorityColors();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getProjectMembers($id)
    {
        $project_id = $id;
        $project_members = ProjectMember::projectMembersDropdown($project_id);
        return response()->json($project_members);
    }
    public function index(Request $request)
    {
        // check if user can crud task
        $project_id = request()->get('project_id');
        $project = Project::find($project_id);
        $is_lead = $this->projectUtil->isProjectLead(auth()->user()->id, $project_id);
        $is_member = $this->projectUtil->isProjectMember(auth()->user()->id, $project_id);
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);
        $user = request()->session()->get('user');
        $statuses = ProjectTask::taskStatuses($project_id);
        $statuses1 = ProjectTask::taskStatuses1($project_id);
        $statusesId = ProjectTask::statusesId($project_id);
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $project_task = ProjectTask::with(['members', 'createdBy', 'project', 'comments'])
                ->where('business_id', $business_id)
                ->select('*');
            //if user is not admin get assiged task only
            $user_id = $user['id'];
            if (empty(request()->get('project_id')) && !$is_admin) {
                $project_task->whereHas('members', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            }
            //filter by project id
            if (!empty(request()->get('project_id'))) {
                $project_task->where('project_id', request()->get('project_id'));
            }
            //filter by assigned to
            if (!empty(request()->get('user_id'))) {
                $user_id = request()->get('user_id');
                $project_task->whereHas('members', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            }

            // filter by status
            if (!empty(request()->get('status'))) {
                $project_task->where('status', request()->get('status'));
            }

            // filter by priority
            if (!empty(request()->get('priority'))) {
                $project_task->where('priority', request()->get('priority'));
            }

            // filter by due date
            if (!empty(request()->get('due_date'))) {
                if (request()->get('due_date') == 'overdue') {
                    $project_task->where('due_date', '<', Carbon::today())
                        ->where('status', '!=', 'completed');
                } elseif (request()->get('due_date') == 'today') {
                    $project_task->where('due_date', Carbon::today())
                        ->where('status', '!=', 'completed');
                } elseif (request()->get('due_date') == 'less_than_one_week') {
                    $project_task->whereBetween('due_date', [Carbon::today(), Carbon::today()->addWeek()])
                        ->where('status', '!=', 'completed');
                }
            }



            $can_crud = false;
            if ($is_admin || $is_lead) {
                $can_crud = true;
            } elseif ($is_member && (isset($project->settings['members_crud_task']) && $project->settings['members_crud_task'])) {
                $can_crud = true;
            }

            if ($request->get('task_view') == 'list_view') {
                return Datatables::of($project_task)
                    ->filter(function ($query) use ($request) {
                        // Exclude archived status
                        $query->where('status', '!=', 'archive');
        
                        // Apply search filter
                        if ($search = $request->get('search')['value']) {
                            $query->where(function ($q) use ($search) {
                                $q->where('subject', 'like', "%{$search}%");
                            });
                        }
                    })
                    ->addColumn('action', function ($row) use ($can_crud) {
                        $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button" data-toggle="dropdown" aria-expanded="false">
                                        ' . __('messages.action') . '
                                        <span class="caret"></span>
                                        <span class="sr-only">' . __('messages.action') . '</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                        <li>
                                            <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'show'], [$row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer view_a_project_task">
                                                <i class="fa fa-eye"></i> ' . __('messages.view') . '
                                            </a>
                                        </li>
                                        <li>
                                            <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'getTaskStatus'], ['id' => $row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer change_status_of_project_task">
                                                <i class="fa fa-check"></i> ' . __('project::lang.change_status') . '
                                            </a>
                                        </li>';
        
                        if ($can_crud) {
                            $html .= '<li>
                                        <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'getChangeProject'], ['id' => $row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer change_project_of_project_task">
                                            <i class="fas fa-project-diagram"></i> ' . __('project::lang.change_project') . '
                                        </a>
                                    </li>
                                    <li>
                                        <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'edit'], [$row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer edit_a_project_task">
                                            <i class="fa fa-edit"></i> ' . __('messages.edit') . '
                                        </a>
                                    </li>
                                    <li>
                                        <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'archiveTaskStatus'], [$row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer archive_project_task">
                                            <i class="fas fa-file-archive"></i> ' . __('project::lang.archive') . '
                                        </a>
                                    </li>';
                        }
        
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->editColumn('priority', function ($row) {
                        $priority = __('project::lang.' . $row->priority);
                        $html = '<span class="label ' . $this->priority_colors[$row->priority] . '">' . $priority . '</span>';
                        return $html;
                    })
                    ->editColumn('start_date', '
                        @if(isset($start_date))
                            {{@format_date($start_date)}}
                        @endif
                    ')
                    ->editColumn('due_date', '
                        @if(isset($due_date))
                            {{@format_date($due_date)}}
                        @endif
                    ')
                    ->editColumn('updated_at', function ($row) {
                        $updated_at = Carbon::parse($row->updated_at);
                        return $updated_at->format('M d, Y h:i A');
                    })
                    ->editColumn('created_at', function ($row) {
                        $created_at = Carbon::parse($row->created_at);
                        return $created_at->format('M d, Y h:i A');
                    })
                    ->editColumn('createdBy', function ($row) {
                        return $row->createdBy?->user_full_name;
                    })
                    ->editColumn('project', function ($row) {
                        return $row->project->name;
                    })
                    ->editColumn('members', function ($row) {
                        $html = '&nbsp;';
                        foreach ($row->members as $member) {
                            $html .= '<img class="user_avatar" src="' . ($member->media->display_url ?? 'https://ui-avatars.com/api/?name=' . $member->first_name) . '" data-toggle="tooltip" title="' . $member->user_full_name . '">';
                        }
                        return $html;
                    })
                    ->editColumn('status', function ($row) use ($statuses) {
                        $status = $statuses[$row->status] ?? '';
                        $bg = match ($row->status) {
                            'completed' => 'bg-green',
                            'cancelled' => 'bg-red',
                            'on_hold' => 'bg-yellow',
                            'in_progress' => 'bg-info',
                            'not_started' => 'bg-red',
                            default => '',
                        };
        
                        $href = action([\Modules\Project\Http\Controllers\TaskController::class, 'getTaskStatus'], ['id' => $row->id, 'project_id' => $row->project_id]);
                        $html = '<span class="cursor-pointer change_status_of_project_task label ' . $bg . '" data-href="' . $href . '">' . $status . '</span>';
                        return $html;
                    })
                    ->editColumn('subject', function ($row) {
                        $commentCount = $row->comments->count();
                        $media_count = $row->comments->sum(fn($comment) => Media::where('model_type', 'Modules\Project\Entities\ProjectTaskComment')->where('model_id', $comment->id)->count());
                        
                        $html = '<a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'show'], [$row->id, "project_id" => $row->project_id]) . '" class="cursor-pointer view_a_project_task text-black">' . $row->subject . ' <code>' . $row->task_id . '</code>';
                        
                        if ($commentCount > 0) {
                            $html .= '<span class="label-default label-default-bt" title="This card has comment"><i class="fas fa-comment ctn"><sup>' . $commentCount . '</sup></i></span>';
                        }
        
                        if ($media_count > 0) {
                            $html .= '<span class="label-default label-default-bt" title="This card has media"><i class="fas fa-paperclip ctn"><sup>' . $media_count . '</sup></i></span>';
                        }
                        $html .= '</a>';
                        return $html;
                    })
                    ->removeColumn('id')
                    ->rawColumns(['action', 'created_at', 'project', 'subject', 'members', 'priority', 'start_date', 'due_date', 'status', 'createdBy', 'updated_at'])
                    ->make(true);
            } elseif (request()->get('task_view') == 'kanban') {
                $project_task = $project_task->get()->groupBy('status');

                //sort array based on status
                $project_tasks = [];
                foreach ($statuses as $key => $value) {
                    if (!isset($project_task[$key])) {
                        $project_tasks[$key] = [];
                    } else {
                        $project_tasks[$key] = $project_task[$key];
                    }
                }

                $kanban_tasks = [];
                foreach ($project_tasks as $key => $tasks) {
                    //get all the card for particular board(status)
                    $cards = [];
                    foreach ($tasks as $task) {
                        $edit = '';
                        $delete = '';
                        if ($can_crud) {
                            $edit = action([\Modules\Project\Http\Controllers\TaskController::class, 'edit'], [$task->id, 'project_id' => $task->project_id]);

                            $delete = action([\Modules\Project\Http\Controllers\TaskController::class, 'archiveTaskStatus'], [$task->id, 'project_id' => $task->project_id]);
                        }
                        $view = action([\Modules\Project\Http\Controllers\TaskController::class, 'show'], [$task->id, 'project_id' => $task->project_id]);
                        // cHANGE Destroy/delete tasks to archive

                        //if member then get their avatar
                        if ($task->members->count() > 0) {
                            $assigned_to = [];
                            foreach ($task->members as $member) {
                                if (isset($member->media->display_url)) {
                                    $assigned_to[$member->user_full_name] = $member->media->display_url;
                                } else {
                                    $assigned_to[$member->user_full_name] = 'https://ui-avatars.com/api/?name=' . $member->first_name;
                                }
                            }
                        }


                        // Initialize media count
                        $media_count = 0;

                        // Iterate over each comment of the task
                        foreach ($task->comments as $comment) {
                            // Count the media associated with the current comment and add to total media count
                            $media_count += Media::where('model_type', 'Modules\Project\Entities\ProjectTaskComment')
                                ->where('model_id', $comment->id)->count();
                        }

                        $cards[] = [
                            'id' => $task->id,
                            'title' => $task->subject,
                            'project_id' => $task->project_id,
                            'project' => $task->project->name,
                            'subtitle' => $task->task_id,
                            'viewUrl' => $view,
                            'viewUrlClass' => 'view_a_project_task',
                            'editUrl' => $edit,
                            'editUrlClass' => 'edit_a_project_task',
                            'deleteUrl' => $delete,
                            'deleteUrlClass' => 'archive_project_task',
                            'hasDescription' => !empty($task->description) ?: false,
                            'hasComments' => ($task->comments->count() > 0) ?: false,
                            'commentCount' => $task->comments->count(),
                            'media_count' => $media_count,
                            'level' =>$task->custom_field_1,
                            'dueDate' => $task->due_date,
                            'assigned_to' => $assigned_to,
                            'tags' => [__('project::lang.' . $task->priority)],
                        ];
                    }

                    //get all the card & board title for particular board(status)
                    $kanban_tasks[] = [
                        'id' => $key,
                        'title' =>  $statuses[$key],
                        'cards' => $cards,
                    ];
                }
                $output = [
                    'success' => true,
                    'project_tasks' => $kanban_tasks,
                    'msg' => __('lang_v1.success'),
                ];
                return $output;
            } elseif (request()->get('task_view') == 'archive') {
                return Datatables::of($project_task)
                    ->filter(function ($query) {
                        // Include only rows with status 'archive'
                        $query->where('status', 'archive');
                    })
                    ->addColumn('action', function ($row) use ($can_crud) {
                        $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                        ' . __('messages.action') . '
                                        <span class="caret"></span>
                                        <span class="sr-only">'
                            . __('messages.action') . '
                                        </span>
                                    </button>
                                      <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                       <li>
                                            <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'show'], [$row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer view_a_project_task">
                                                <i class="fa fa-eye"></i>
                                                ' . __('messages.view') . '
                                            </a>
                                        </li>
                                        <li>
                                            <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'getTaskStatus'], ['id' => $row->id, 'project_id' => $row->project_id]) . '"class="cursor-pointer change_status_of_project_task">
                                                <i class="fa fa-check"></i>
                                                ' . __('project::lang.change_status') . '
                                            </a>
                                        </li>';

                        if ($can_crud) {
                            $html .= '<li>
                                    <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'edit'], [$row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer edit_a_project_task">
                                        <i class="fa fa-edit"></i>
                                        ' . __('messages.edit') . '
                                    </a>
                                </li>
                                <li>
                                    <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'destroy'], [$row->id, 'project_id' => $row->project_id]) . '" class="cursor-pointer delete_a_project_task">
                                        <i class="fas fa-trash"></i>
                                        ' . __('messages.delete') . '
                                    </a>
                                </li>';
                        }

                        $html .= '</ul>
                                </div>';

                        return $html;
                    })
                    ->editColumn('priority', function ($row) {
                        $priority = __('project::lang.' . $row->priority);

                        $html = '<span class="label ' . $this->priority_colors[$row->priority] . '">' .
                            $priority
                            . '</span>';

                        return $html;
                    })
                    ->editColumn('start_date', '
                            @if(isset($start_date))
                                {{@format_date($start_date)}}
                            @endif
                    ')
                    ->editColumn('due_date', '
                            @if(isset($due_date))
                                {{@format_date($due_date)}}
                            @endif
                    ')->editColumn('updated_at', function ($row) {
                        // Parse the updated_at timestamp using Carbon
                        $updatedat = Carbon::parse($row->updated_at);
                        // Format the date and time to your desired format
                        return $updatedat->format('M d, Y h:i A');
                    })
                    ->editColumn('created_at', function ($row) {
                        // Parse the updated_at timestamp using Carbon
                        $created_at = Carbon::parse($row->created_at);
                        // Format the date and time to your desired format
                        return $created_at->format('M d, Y h:i A');
                    })
                    ->editColumn('createdBy', function ($row) {
                        return $row->createdBy?->user_full_name;
                    })
                    ->editColumn('project', function ($row) {
                        return $row->project->name;
                    })
                    ->editColumn('members', function ($row) {
                        $html = '&nbsp;';
                        foreach ($row->members as $member) {
                            if (isset($member->media->display_url)) {
                                $html .= '<img class="user_avatar" src="' . $member->media->display_url . '" data-toggle="tooltip" title="' . $member->user_full_name . '">';
                            } else {
                                $html .= '<img class="user_avatar" src="https://ui-avatars.com/api/?name=' . $member->first_name . '" data-toggle="tooltip" title="' . $member->user_full_name . '">';
                            }
                        }

                        return $html;
                    })
                    ->editColumn('status', function ($row) {
                        // Modify the status column for 'archive' tasks
                        $status = __('project::lang.archive');
                        $bg = 'bg-primary';

                        $href = action([\Modules\Project\Http\Controllers\TaskController::class, 'getTaskStatus'], ['id' => $row->id, 'project_id' => $row->project_id]);

                        $html = '<span class="cursor-pointer change_status_of_project_task label ' . $bg . '" data-href="' . $href . '">
                                ' .
                            $status
                            . '</span>';

                        return $html;
                    })
                    ->editColumn('subject', function ($row) {
                        $commentCount = $row->comments->count();
                        $media_count = 0;
                        // Iterate over each comment of the task
                        foreach ($row->comments as $comment) {
                            // Count the media associated with the current comment and add to total media count
                            $media_count += Media::where('model_type', 'Modules\Project\Entities\ProjectTaskComment')
                                ->where('model_id', $comment->id)->count();
                        }
                        $html = '
                        <a data-href="' . action([\Modules\Project\Http\Controllers\TaskController::class, 'show'], [$row->id, "project_id" => $row->project_id]) . '" class="cursor-pointer view_a_project_task text-black">
                        ' . $row->subject . ' <code>' . $row->task_id . '</code>';

                        // Check if comment count is greater than 0
                        if ($commentCount > 0) {
                            $html .= '<span class="label-default label-default-bt" title="This card has comment"><i class="fas fa-comment ctn"><sup>' . $commentCount . '</sup></i></span>';
                        }

                        // Check if media count is greater than 0
                        if ($media_count > 0) {
                            $html .= '<span class="label-default label-default-bt" title="This card has media"><i class="fas fa-paperclip ctn"><sup>' . $media_count . '</sup></i></span>';
                        }

                        $html .= '</a>';

                        return $html;
                    })
                    ->removeColumn('id')
                    ->rawColumns(['action', 'created_at', 'updated_at', 'project', 'subject', 'members', 'priority', 'start_date', 'due_date', 'status', 'createdBy'])
                    ->make(true);
            }
        }
        $business_id = request()->session()->get('user.business_id');
        $users = User::forDropdown($business_id, false);
        $priorities = ProjectTask::prioritiesDropdown();
        $due_dates = ProjectTask::dueDatesDropdown();
        // if not admin get assigned project for filter
        $user_id = null;
        if (!$is_admin) {
            $user_id = $user['id'];
        }
        $projects = Project::projectDropdown($business_id, $user_id);
        return view('project::my_task.index')
            ->with(compact('users', 'statuses', 'priorities', 'due_dates', 'projects', 'is_admin'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $project_id = request()->get('project_id');
        $project_members = ProjectMember::projectMembersDropdown($project_id);
        $priorities = ProjectTask::prioritiesDropdown();
        $statuses = ProjectTask::taskStatuses($project_id);

        // Retrieve the project based on the project_id
        $project = Project::findOrFail($project_id);

        // Check if settings exist and if levels are set
        $levels = [];
        if (isset($project->settings['levels'])) {
            $levels = $project->settings['levels'];
        }

        $user_id = 1; // Example user ID

        // Count the number of projects associated with the user
        $project_count = ProjectMember::where('user_id', $user_id)->count();

        return view('project::task.create')
            ->with(compact('project_members','levels','priorities', 'project_id', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->only('subject', 'project_id', 'description', 'priority', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'status');

            $input['start_date'] = !empty($request->input('start_date')) ? $this->commonUtil->uf_date($request->input('start_date')) : null;
            $input['due_date'] = !empty($request->input('due_date')) ? $this->commonUtil->uf_date($request->input('due_date')) : null;
            $input['created_by'] = $request->user()->id;
            $input['business_id'] = request()->session()->get('user.business_id');
            $input['task_id'] = $this->projectUtil->generateTaskId($input['business_id'], $input['project_id']);
            $members = $request->input('user_id');

            $project_task = ProjectTask::create($input);
            $task_members = $project_task->members()->sync($members);

            // send notification to task members
            if (!empty($task_members['attached'])) {
                //check if user is a creator then don't notify him
                foreach ($task_members['attached'] as $key => $value) {
                    if ($value == $project_task->created_by) {
                        unset($task_members['attached'][$key]);
                    }
                }

                //Used for broadcast notification
                $project_task['title'] = __('project::lang.task');
                $project_task['body'] = strip_tags(__(
                    'project::lang.new_task_assgined_notification',
                    [
                        'created_by' => $request->user()->user_full_name,
                        'subject' => $project_task->subject,
                        'task_id' => $project_task->task_id,
                    ]
                ));
                $project_task['link'] = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project_task->project_id]);

                $this->projectUtil->notifyUsersAboutAssignedTask($task_members['attached'], $project_task);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     *
     * @return Response
     */
    public function show($id)
    {
        $project_id = request()->get('project_id');

        $project_task = ProjectTask::with([
            'members', 'createdBy', 'project',
            'comments' => function ($query) {
                $query->latest();
            },
            'comments.media', 'comments.commentedBy', 'timeLogs', 'timeLogs.user',
        ])
            ->where('project_id', $project_id)
            ->findOrFail($id);

        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);
        $is_lead = $this->projectUtil->isProjectLead(auth()->user()->id, $id);

        $is_lead_or_admin = false;
        if ($is_admin || $is_lead) {
            $is_lead_or_admin = true;
        }

        $can_crud_timelog = $this->projectUtil->canMemberCrudTimelog($business_id, auth()->user()->id, $project_id);

        return view('project::task.show')
            ->with(compact('project_task', 'is_lead_or_admin', 'can_crud_timelog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $project_id = request()->get('project_id');
        $project_task = ProjectTask::with('members')
            ->where('project_id', $project_id)
            ->findOrFail($id);

        $project_members = ProjectMember::projectMembersDropdown($project_id);
        $priorities = ProjectTask::prioritiesDropdown();
        $statuses = ProjectTask::taskStatuses($project_id);
        $leader = Project::where('lead_id', auth()->user()->id)->find($project_id); // Corrected
        if (auth()->user()->hasRole('Admin#' . session('business.id'))) {
            $projects = Project::pluck('name', 'id');
        } else {
            $projects = Project::whereHas('members', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->pluck('name', 'id');
        }
        // Retrieve the project based on the project_id
        $project = Project::findOrFail($project_id);

        // Check if settings exist and if levels are set
        $levels = [];
        if (isset($project->settings['levels'])) {
            $levels = $project->settings['levels'];
        }
        return view('project::task.edit')
            ->with(compact('project_members', 'levels', 'leader', 'projects', 'priorities', 'project_task', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->only('subject', 'project_id', 'description', 'priority', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'status');
            $input['start_date'] = !empty($request->input('start_date')) ? $this->commonUtil->uf_date($request->input('start_date')) : null;
            $input['due_date'] = !empty($request->input('due_date')) ? $this->commonUtil->uf_date($request->input('due_date')) : null;
            $members = $request->input('user_id');

            $project_id = $request->get('project_id');
            $project_task = ProjectTask::findOrFail($id);

            $project_task->update($input);
            $task_members = $project_task->members()->sync($members);

            // send notification to task members
            if (!empty($task_members['attached'])) {
                //check if user is a creator then don't notify him
                foreach ($task_members['attached'] as $key => $value) {
                    if ($value == $project_task->created_by) {
                        unset($task_members['attached'][$key]);
                    }
                }
                //Used for broadcast notification
                $project_task['title'] = __('project::lang.task');
                $project_task['body'] = strip_tags(__(
                    'project::lang.new_task_assgined_notification',
                    [
                        'created_by' => $request->user()->user_full_name,
                        'subject' => $project_task->subject,
                        'task_id' => $project_task->task_id,
                    ]
                ));
                $project_task['link'] = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project_task->project_id]);

                $this->projectUtil->notifyUsersAboutAssignedTask($task_members['attached'], $project_task);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $project_id = request()->get('project_id');

            $project_task = ProjectTask::where('project_id', $project_id)
                ->findOrFail($id);

            $project_task->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * get task status for update.
     *
     * @return Response
     */
    public function getTaskStatus()
    {
        $task_id = request()->get('id');
        $project_id = request()->get('project_id');
        $statuses = ProjectTask::taskStatuses($project_id);
        $project_task = ProjectTask::where('project_id', $project_id)
            ->findOrFail($task_id);

        return view('project::task.change_status')
            ->with(compact('project_task', 'statuses'));
    }

    /**
     * update task status
     *
     * @return Response
     */
    public function postTaskStatus($id)
    {
        try {
            $project_id = request()->get('project_id');

            $project_task = ProjectTask::where('project_id', $project_id)
                ->findOrFail($id);

            $project_task->status = request()->input('status');
            $project_task->save();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
        return response()->json($output);
        return $output;
    }
    public function getChangeProject()
    {
        $task_id = request()->get('id');
        $project_id = request()->get('project_id');
        $project_task = ProjectTask::with('members')
            ->where('project_id', $project_id)
            ->findOrFail($task_id);

        $project_members = ProjectMember::projectMembersDropdown($project_id);
        $priorities = ProjectTask::prioritiesDropdown();
        $statuses = ProjectTask::taskStatuses($project_id);
        $leader = Project::where('lead_id', auth()->user()->id)->find($project_id); // Corrected
        if (auth()->user()->hasRole('Admin#' . session('business.id'))) {
            $projects = Project::pluck('name', 'id');
        } else {
            $projects = Project::whereHas('members', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->pluck('name', 'id');
        }
        return view('project::task.change_task_project')
            ->with(compact('project_members', 'leader', 'projects', 'priorities', 'project_task', 'statuses'));
    }
    /**
     * update task status
     *
     * @return Response
     */
    public function postChangeProject($id)
    {
        try {
            $project_id = request()->get('project_id');

            $project_task = ProjectTask::findOrFail($id);

            $project_task->project_id = request()->input('project_id');
            $project_task->save();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
        return response()->json($output);
        return $output;
    }

    /**
     * update task description
     *
     * @return Response
     */
    public function postTaskDescription($id)
    {
        try {
            $project_id = request()->get('project_id');

            $project_task = ProjectTask::where('project_id', $project_id)
                ->findOrFail($id);

            $project_task->description = request()->input('description');
            $project_task->save();

            $project_task = ProjectTask::findOrFail($id);

            //dynamically change description in task view
            $task_description_html = view('project::task.partials.edit_description')
                ->with(compact('project_task'))
                ->render();

            $output = [
                'success' => true,
                'task_description_html' => $task_description_html,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    public function archiveTaskStatus($id)
    {
        try {
            $project_task = ProjectTask::findOrFail($id);
            $project_task->status = 'archive';
            $project_task->save();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
        return $output;
    }
}
