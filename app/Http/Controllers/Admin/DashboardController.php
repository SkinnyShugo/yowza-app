<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Models\Post;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-user|edit-user|delete-user', ['only' => ['index','show']]);
        $this->middleware('permission:create-user', ['only' => ['create','store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);

        // Additional permissions
        $additionalPermissions = [
            'user_management_access',
            'user_management_create',
            'user_management_edit',
            'user_management_view',
            'user_management_delete',
            'user_access',
            'user_create',
            'user_edit',
            'user_view',
            'user_delete',
        ];

        $this->middleware('permission:' . implode('|', $additionalPermissions), ['only' => ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    //
    public function index(Request $request)
    {
        $blog = Post::with('categories')->get();
        $perPage = $request->input('entries', 10);
        $activities = UserActivity::paginate($perPage);
        return view('admin.index',compact('blog', 'activities', 'perPage'));
    }


}
