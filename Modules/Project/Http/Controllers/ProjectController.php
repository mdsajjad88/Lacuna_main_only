<?php

namespace Modules\Project\Http\Controllers;

use App\Charts\CommonChart;
use App\Contact;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectCategory;
use Modules\Project\Entities\ProjectMember;
use Modules\Project\Entities\ProjectTask;
use Modules\Project\Entities\ProjectTimeLog;
use Modules\Project\Entities\ProjectTransaction;
use Modules\Project\Utils\ProjectUtil;

class ProjectController extends Controller
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
     * @param CommonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ProjectUtil $projectUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->projectUtil = $projectUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);
        $user_id = auth()->user()->id;
        $statuses = Project::statusDropdown();

        //if project view is NULL, set default to list_view
        if (is_null(request()->get('project_view'))) {
            $project_view = 'list_view';
        } else {
            $project_view = request()->get('project_view');
        }

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $projects = Project::with('customer', 'members', 'lead', 'categories')->where('business_id', $business_id);

                //if not admin get assigned project only
                if (!$is_admin) {
                    $projects->whereHas('members', function ($q) use ($user_id) {
                        $q->where('user_id', $user_id);
                    });
                }

                // filter by status
                if (!empty(request()->get('status'))) {
                    $projects->where('status', request()->get('status'));
                }

                // filter by end date
                if (!empty(request()->get('end_date'))) {
                    if (request()->get('end_date') == 'overdue') {
                        $projects->where('end_date', '<', Carbon::today())
                            ->where('status', '!=', 'completed');
                    } elseif (request()->get('end_date') == 'today') {
                        $projects->where('end_date', Carbon::today())
                            ->where('status', '!=', 'completed');
                    } elseif (request()->get('end_date') == 'less_than_one_week') {
                        $projects->whereBetween('end_date', [Carbon::today(), Carbon::today()->addWeek()])
                            ->where('status', '!=', 'completed');
                    }
                }

                // filter by category id
                if (!empty(request()->get('category_id'))) {
                    $category_id = request()->get('category_id');
                    $projects->whereHas('categories', function ($q) use ($category_id) {
                        $q->where('id', $category_id);
                    });
                }

                if ($project_view == 'list_view') {
                    $projects = $projects->latest()
                        ->simplePaginate(9);

                    //check if user is lead/admin for the project
                    foreach ($projects as $key => $project) {
                        $is_lead = $this->projectUtil->isProjectLead($user_id, $project->id);

                        $projects[$key]['is_lead_or_admin'] = false;
                        if ($is_lead || $is_admin) {
                            $projects[$key]['is_lead_or_admin'] = true;
                        }
                    }

                    //dynamically render projects
                    $projects_html = view('project::project.partials.index')
                        ->with(compact('projects'))
                        ->render();
                }
                elseif ($project_view == 'archive') {
                    $projects = $projects->onlyTrashed()->latest()
                        ->simplePaginate(9);

                    //check if user is lead/admin for the project
                    foreach ($projects as $key => $project) {
                        $is_lead = $this->projectUtil->isProjectLead($user_id, $project->id);

                        $projects[$key]['is_lead_or_admin'] = false;
                        if ($is_lead || $is_admin) {
                            $projects[$key]['is_lead_or_admin'] = true;
                        }
                    }

                    //dynamically render projects
                    $projects_html = view('project::project.partials.index')
                        ->with(compact('projects'))
                        ->render();
                }
                
                elseif ($project_view == 'kanban') {
                    $projects = $projects->get()->groupBy('status');
                    //sort projects based on status
                    $sorted_projects = [];
                    foreach ($statuses as $key => $value) {
                        if (!isset($projects[$key])) {
                            $sorted_projects[$key] = [];
                        } else {
                            $sorted_projects[$key] = $projects[$key];
                        }
                    }

                    $projects_html = [];
                    foreach ($sorted_projects as $key => $projects) {
                        //get all the project for particular board(status)
                        $cards = [];
                        foreach ($projects as $project) {
                            $edit = '';
                            if (auth()->user()->can('project.edit_project')) {
                                $edit = action([\Modules\Project\Http\Controllers\ProjectController::class, 'edit'], [$project->id]);
                            }

                            $delete = '';
                            if (auth()->user()->can('project.delete_project')) {
                                $delete = action([\Modules\Project\Http\Controllers\ProjectController::class, 'destroy'], [$project->id]);
                            }

                            $view = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]);

                            $overviewTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=overview';

                            $activitiesTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=activities';

                            $taskTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=project_task';

                            $timeLogTabUrl = '';
                            if (isset($project->settings['enable_timelog']) && $project->settings['enable_timelog']) {
                                $timeLogTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=time_log';
                            }

                            $docNoteTabUrl = '';
                            if (isset($project->settings['enable_notes_documents']) && $project->settings['enable_notes_documents']) {
                                $docNoteTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=documents_and_notes';
                            }

                            // check if user is lead
                            $is_lead = $this->projectUtil->isProjectLead($user_id, $project->id);

                            $invoiceTabUrl = '';
                            if ((isset($project->settings['enable_invoice']) && $project->settings['enable_invoice']) && ($is_lead || $is_admin)) {
                                $invoiceTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=project_invoices';
                            }
                            $archiveUrl = '';
                            if ((isset($project->settings['enable_archive'])) && ($project->settings['enable_archive']) && ($is_lead || $is_admin)) {
                                $archiveUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=project_archives';
                            }
                            $settingsTabUrl = '';
                            if ($is_lead || $is_admin) {
                                $settingsTabUrl = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]) . '?view=project_settings';
                            }

                            //if member then get their avatar
                            if ($project->members->count() > 0) {
                                $assigned_to = [];
                                foreach ($project->members as $member) {
                                    if (isset($member->media->display_url)) {
                                        $assigned_to[$member->user_full_name] = $member->media->display_url;
                                    } else {
                                        $assigned_to[$member->user_full_name] = 'https://ui-avatars.com/api/?name=' . $member->first_name;
                                    }
                                }
                            }

                            //display category as tag
                            $tags = [];
                            if ($project->categories->count() > 0) {
                                foreach ($project->categories as $category) {
                                    $tags[] = $category->name;
                                }
                            }

                            $cards[] = [
                                'id' => $project->id,
                                'title' => $project->name,
                                'viewUrl' => $view,
                                'editUrl' => $edit,
                                'editUrlClass' => 'edit_a_project',
                                'deleteUrl' => $delete,
                                'deleteUrlClass' => 'delete_a_project',
                                'assigned_to' => $assigned_to,
                                'hasDescription' => !empty($project->description) ?: false,
                                'endDate' => $project->end_date,
                                'tags' => $tags,
                                'customer' => !empty($project->customer) ? $project->customer->name : '',
                                'lead' => $project->lead->user_full_name,
                                'overviewTabUrl' => $overviewTabUrl,
                                'activitiesTabUrl' => $activitiesTabUrl,
                                'taskTabUrl' => $taskTabUrl,
                                'timeLogTabUrl' => $timeLogTabUrl,
                                'docNoteTabUrl' => $docNoteTabUrl,
                                'invoiceTabUrl' => $invoiceTabUrl,
                                'settingsTabUrl' => $settingsTabUrl,
                            ];
                        }

                        //get all the card & board title for particular board(status)
                        $projects_html[] = [
                            'id' => $key,
                            'title' => __('project::lang.' . $key),
                            'cards' => $cards,
                        ];
                    }
                }

                $output = [
                    'success' => true,
                    'projects_html' => $projects_html,
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

        //project statistics by status
        $project_stats = new Project;
        if (!$is_admin) {
            $project_stats = $project_stats->whereHas('members', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }
        $project_stats = $project_stats->select(DB::raw('count(id) as count, status'))
            ->where('business_id', $business_id)
            ->groupBy('status')
            ->get();

        $due_dates = ProjectTask::dueDatesDropdown();
        $categories = ProjectCategory::forDropdown($business_id, 'project');

        return view('project::project.index')
            ->with(compact('statuses', 'due_dates', 'project_stats', 'categories', 'project_view'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module') && auth()->user()->can('project.create_project')))) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $statuses = Project::statusDropdown();
        $categories = ProjectCategory::forDropdown($business_id, 'project');

        return view('project::project.create')
            ->with(compact('users', 'customers', 'statuses', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module') && auth()->user()->can('project.create_project')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only('name', 'description', 'contact_id', 'status', 'lead_id');
            $input['start_date'] = !empty($request->input('start_date')) ? $this->commonUtil->uf_date($request->input('start_date')) : null;
            $input['end_date'] = !empty($request->input('end_date')) ? $this->commonUtil->uf_date($request->input('end_date')) : null;
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->user()->id;

            // default settings for project
            $input['settings'] = [
                'enable_timelog' => 1,
                'enable_invoice' => 0,
                'enable_notes_documents' => 1,
                'enable_archive' => 'archive',
                'members_crud_task' => 0,
                'members_crud_note' => 0,
                'members_crud_timelog' => 0,
                'task_view' => 'kanban',
                'task_id_prefix' => '#',
                'not_started' => [
                    'id' => 1,
                    'name' => 'Not Started',
                ],
                'in_progress' => [
                    'id' => 1,
                    'name' => 'In Progress',
                ],
                'on_hold' => [
                    'id' => 1,
                    'name' => 'On Hold',
                ],
                'cancelled' => [
                    'id' => 0,
                    'name' => 'Cancelled',
                ],
                'completed' => [
                    'id' => 1,
                    'name' => 'Completed',
                ],
            ];

            $members = $request->input('user_id');
            array_push($members, $request->input('lead_id'));

            $project = Project::create($input);
            $project_members = $project->members()->sync($members);

            //save project category
            $categories = $request->input('category_id');
            $project->categories()->sync($categories);

            // send notification to project members
            if (!empty($project_members['attached'])) {
                //check if user is a creator then don't notify him
                foreach ($project_members['attached'] as $key => $value) {
                    if ($value == $project->created_by) {
                        unset($project_members['attached'][$key]);
                    }
                }

                //Used for broadcast notification
                $project['title'] = __('project::lang.project');
                $project['body'] = __(
                    'project::lang.new_project_assgined_notification',
                    [
                        'created_by' => $request->user()->user_full_name,
                        'project' => $project->name,
                    ]
                );
                $project['link'] = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], [$project->id]);

                $this->projectUtil->notifyUsersAboutAssignedProject($project_members['attached'], $project);
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

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
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;

        //Get time project details.
        $query = Project::with('customer', 'members', 'categories')
            ->withCount([
                'tasks as incomplete_task' => function ($query) {
                    $query->where('status', '!=', 'completed');
                },
                'documentsAndnote as note_and_documents_count' => function ($query) use ($user_id) {
                    $query->where('is_private', 0)
                        ->orWhere(function ($query) use ($user_id) {
                            $query->where('is_private', 1)
                                ->where('created_by', $user_id);
                        });
                },
            ])
            ->where('business_id', $business_id);

        //if not admin, check if project is assigned to user or not
        if (!$this->commonUtil->is_admin(auth()->user(), $business_id)) {
            $query->whereHas('members', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        $project = $query->findOrFail($id);

        //Get time log details.
        $timelog = ProjectTimeLog::where('project_id', $id)
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, start_datetime, end_datetime)) as total_seconds'))
            ->first();

        //Invoice paid.
        $invoice = ProjectTransaction::leftJoin('transaction_payments as TP', 'transactions.id', '=', 'TP.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('pjt_project_id', $id)
            ->select(DB::raw('SUM(TP.amount) as paid'))
            ->first();

        //Invoice total.
        $transaction = ProjectTransaction::where('business_id', $business_id)
            ->where('pjt_project_id', $id)
            ->select(DB::raw('SUM(final_total) as total'))
            ->first();

        //check if user can create settings & task & time log
        $is_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);
        $is_lead = $this->projectUtil->isProjectLead(auth()->user()->id, $id);
        $is_member = $this->projectUtil->isProjectMember(auth()->user()->id, $id);

        $is_lead_or_admin = false;
        if ($is_admin || $is_lead) {
            $is_lead_or_admin = true;
            // if admin get all project members for task filter
            $user_id = null;
        }

        $can_crud_task = $this->projectUtil->canMemberCrudTask($business_id, $user_id, $id);
        $can_crud_docus_note = $this->projectUtil->canMemberCrudNotes($business_id, $user_id, $id);
        $can_crud_timelog = $this->projectUtil->canMemberCrudTimelog($business_id, $user_id, $id);

        $due_dates = ProjectTask::dueDatesDropdown();
        $project_members = ProjectMember::projectMembersDropdown($id, $user_id);
        $project_members_card = ProjectMember::projectMembersCard($id, $user_id);
        $statuses = ProjectTask::taskStatuses($id);
        $priorities = ProjectTask::prioritiesDropdown();
        $values = [];
        $labels = [];

        foreach ($project_members_card as $member) {
            $values[] = $member['task_count'];
            $labels[] = $member['name'];
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->dataset('Task Count', 'column', $values);

        //if view is NULL, set default to overview
        if (is_null(request()->get('view'))) {
            $tab_view = 'overview';
        } else {
            $tab_view = request()->get('view');
        }
        return view('project::project.show')
            ->with(compact('project', 'chart', 'project_members_card', 'project_members', 'statuses', 'due_dates', 'priorities', 'timelog', 'can_crud_task', 'can_crud_docus_note', 'can_crud_timelog', 'is_lead_or_admin', 'transaction', 'invoice', 'tab_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module') && auth()->user()->can('project.edit_project')))) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $statuses = Project::statusDropdown();
        $categories = ProjectCategory::forDropdown($business_id, 'project');
        $project = Project::with('members', 'categories')
            ->where('business_id', $business_id)
            ->findOrFail($id);

        return view('project::project.edit')
            ->with(compact('users', 'customers', 'statuses', 'project', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module') && auth()->user()->can('project.edit_project')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only('name', 'description', 'contact_id', 'status', 'lead_id');
            $input['start_date'] = !empty($request->input('start_date')) ? $this->commonUtil->uf_date($request->input('start_date')) : null;
            $input['end_date'] = !empty($request->input('end_date')) ? $this->commonUtil->uf_date($request->input('end_date')) : null;
            $members = $request->input('user_id');
            array_push($members, $request->input('lead_id'));

            $project = Project::where('business_id', $business_id)
                ->findOrFail($id);

            $project->update($input);
            $project_members = $project->members()->sync($members);

            //update project category
            $categories = $request->input('category_id');
            $project->categories()->sync($categories);

            // send notification to project members
            if (!empty($project_members['updated'])) {
                //check if user is a creator then don't notify him
                foreach ($project_members['updated'] as $key => $value) {
                    if ($value == $project->created_by) {
                        unset($project_members['updated'][$key]);
                    }
                }

                //Used for broadcast notification
                $project['title'] = __('project::lang.project');
                $project['body'] = __(
                    'project::lang.new_project_assgined_notification',
                    [
                        'created_by' => $request->user()->user_full_name,
                        'project' => $project->name,
                    ]
                );
                $project['link'] = action([\Modules\Project\Http\Controllers\ProjectController::class, 'show'], ['id' => $project->id]);

                $this->projectUtil->notifyUsersAboutAssignedProject($project_members['updated'], $project);
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

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
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user() || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module') && auth()->user()->can('project.archive_project')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $project = Project::where('business_id', $business_id)
                ->findOrFail($id);

            $project->delete();

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

    public function permanentDelete($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module') && auth()->user()->can('project.delete_project')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                // Retrieve the project
                $project = Project::withTrashed()->where('business_id', $business_id)->where('id', $id)->firstOrFail();

                // Permanently delete the project
                $project->forceDelete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::error('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }


    public function restore($id)
    {
        $project = Project::withTrashed()->findOrFail($id);

        // Restore the soft deleted project item
        $project->restore();
        $output = [
            'success' => true,
            'msg' => __('lang_v1.success'),
        ];

        return $output;
    }

    /**
     * Update the project settings.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postSettings(Request $request)
    {
        try {
            $input = $request->only('task_view');
            $input['enable_timelog'] = !empty($request->enable_timelog) ? 1 : 0;
            $input['enable_notes_documents'] = !empty($request->enable_notes_documents) ? 1 : 0;
            $input['enable_invoice'] = !empty($request->enable_invoice) ? 1 : 0;
            $input['enable_archive'] = !empty($request->enable_archive) ? 'archive' : 0;
            $input['members_crud_task'] = !empty($request->members_crud_task) ? 1 : 0;
            $input['members_crud_note'] = !empty($request->members_crud_note) ? 1 : 0;
            $input['members_crud_timelog'] = !empty($request->members_crud_timelog) ? 1 : 0;

            $input['task_id_prefix'] = !empty($request->task_id_prefix) ? $request->task_id_prefix : '#';
            // Status Dynamically Setting

            $input['not_started'] = ['id' => !empty($request->not_started_id) ? 1 : 0, 'name' => $request->not_started];
            $input['in_progress'] = ['id' => !empty($request->in_progress_id) ? 1 : 0, 'name' => $request->in_progress];
            $input['on_hold'] = ['id' => !empty($request->on_hold_id) ? 1 : 0, 'name' => $request->on_hold];
            $input['cancelled'] = ['id' => !empty($request->cancelled_id) ? 1 : 0, 'name' => $request->cancelled];
            $input['completed'] = ['id' => !empty($request->completed_id) ? 1 : 0, 'name' => $request->completed];
            
            // Store level name, color, and background color as arrays
            $levelNames = $request->get('level_name');
            $colors = $request->get('color');
            $bgs = $request->get('bg');

            $levelData = [];
            foreach ($levelNames as $index => $name) {
                if (!empty($name)) {
                    $levelData[] = [
                        'name' => $name,
                        'color' => $colors[$index],
                        'bg' => $bgs[$index]
                    ];
                }
            }

            $input['levels']=$levelData;
            
            $project_id = $request->get('project_id');
            $business_id = request()->session()->get('user.business_id');
            $project = Project::where('business_id', $business_id)
                ->findOrFail($project_id);
            //status filter set
            $projectTaskCounts = ProjectTask::where('project_id', $project_id)
                ->select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $notStartedCount = $projectTaskCounts->get('not_started', 0);
            $inProgressCount = $projectTaskCounts->get('in_progress', 0);
            $onHoldCount = $projectTaskCounts->get('on_hold', 0);
            $cancelledCount = $projectTaskCounts->get('cancelled', 0);
            $completedCount = $projectTaskCounts->get('completed', 0);

            if (($notStartedCount > 0 && is_null($request->not_started_id)) ||
                ($inProgressCount > 0 && is_null($request->in_progress_id)) ||
                ($onHoldCount > 0 && is_null($request->on_hold_id)) ||
                ($cancelledCount > 0 && is_null($request->cancelled_id)) ||
                ($completedCount > 0 && is_null($request->completed_id))
            ) {
                $output = [
                    'success' => false,
                    'msg' => 'Cannot disable Status with existing tasks.',
                ];
                return redirect()->action(
                    [\Modules\Project\Http\Controllers\ProjectController::class, 'show'],
                    [$project_id]
                )->with('status', $output);
            }
            DB::beginTransaction();

            //Log activity
            activity()
                ->performedOn($project)
                ->withProperties(['from' => $project->settings, 'to' => $input])
                ->log('settings_updated');

            $project->settings = $input;
            $project->disableLogging();
            $project->save();
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action(
            [\Modules\Project\Http\Controllers\ProjectController::class, 'show'],
            [$project_id]
        )->with('status', $output);
    }

    /**
     * update project status
     *
     * @return Response
     */
    public function postProjectStatus($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $status = request()->get('status');

            $project = Project::where('business_id', $business_id)
                ->findOrFail($id);

            $project->status = $status;
            $project->save();

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
