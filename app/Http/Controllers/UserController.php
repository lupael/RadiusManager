<?php

namespace App\Http\Controllers;

use App\MacAddress;
use App\User;
use App\Apartment;
use DataTables;
use Illuminate\Http\Request;
use function foo\func;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $apartments = Apartment::pluck('name', 'id');
        return view('Admin.User.view', compact('users', 'apartments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255',
            'username' => 'required|string|max:20|unique:users',
            'password' => 'required|string|max:255',
        ]);

        $user = User::create($request->all());
        $user->default_password = $request->get('password');
        $user->save();

        $user->radreplies()->create([
            'attribute' => 'Tunnel-Type',
            'value' => '13'
        ]);
        $user->radreplies()->create([
            'attribute' => 'Tunnel-Medium-Type',
            'value' => '6'
        ]);

        $user->radreplies()->create([
            'attribute' => 'Tunnel-Private-Group-Id',
            'value' => $user->apartment->vlan_id
        ]);

        return response('success');

    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->has('is_enabled')) {
            $request->validate([
                'is_enabled' => 'required'
            ]);
        } else {
            $request->validate([
                'apartment_id' => 'required',
                'name' => 'required|string|max:255',
                'email' => 'required|email|string|max:255',
                'username' => 'required|string|max:20',
            ]);
        }

        if (empty($request->password)) {
            $request->request->remove('password');
        }

        $user = User::findOrFail($id);
        $user->update($request->all());

        if (!$user->radreplies()->where('attribute', 'Tunnel-Type')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Type',
                'value' => '13'
            ]);
        }

        if (!$user->radreplies()->where('attribute', 'Tunnel-Medium-Type')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Medium-Type',
                'value' => '6'
            ]);
        }

        if (!$user->radreplies()->where('attribute', 'Tunnel-Private-Group-Id')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Private-Group-Id',
                'value' => $user->apartment->vlan_id
            ]);
        } else {
            $user->radreplies()->where('attribute', 'Tunnel-Private-Group-Id')->first()->update([
                'value' => $user->apartment->vlan_id
            ]);
        }

        return response('success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        User::destroy($request->id);

        return response('Success');
    }

    public function macDestroy(Request $request)
    {
        MacAddress::where('user_id', '=', $request->user_id)
            ->where('is_permanent', '=', 0)
            ->delete();

        return response('Success');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->password = $user->default_password;
        $user->save();
        return response('Success');
    }

    public function resetAll(Request $request)
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->password = $user->default_password;
            $user->save();
        }

        return response('Success');
    }

    public function getDataTable()
    {
        $users = User::with('apartment');

        return DataTables::of($users)
            ->addColumn('status', function ($user) {
                if ($user->is_enabled) {
                    return '<i class="la la-check-circle text-success">Active</i>';
                }
                return '<i class="la la-times-circle text-danger">Inactive</i>';
            })
            ->addColumn('action', function ($user) {
                if ($user->is_enabled) {
                    return '<button type="button" class="action btn btn-sm btn-danger" data-token="' . csrf_token() . '" data-id="' . $user->id . '" data-enabled="' . $user->is_enabled . '">De-Activate</button>';
                }
                return '<button type="button" class="action btn btn-sm btn-success" data-token="' . csrf_token() . '" data-id="' . $user->id . '" data-enabled="' . $user->is_enabled . '">Activate</button>';
            })
            ->addColumn('reset', function ($user) {
                return '<button type="button" class="reset btn btn-sm btn-warning" data-user-id="' . $user->id . '" data-token="' . csrf_token() . '">Reset Mac Address</button>';
            })
            ->addColumn('reset_pwd', function ($user) {
                return '<button type="button" class="reset_pwd btn btn-sm btn-warning" data-user-id="' . $user->id . '" data-token="' . csrf_token() . '">Reset Password</button>';
            })
            ->addColumn('edit', function ($user) {
                return '<button type="button" class="edit btn btn-sm btn-primary" data-email="' . $user->email . '" data-apartment-id="' . $user->apartment_id . '" data-name="' . $user->name . '" data-username="' . $user->username . '" data-id="' . $user->id . '">Edit</button>';
            })
            ->addColumn('delete', function ($user) {
                return '<button type="button" class="delete btn btn-sm btn-danger" data-delete-id="' . $user->id . '" data-token="' . csrf_token() . '" >Delete</button>';
            })
            ->rawColumns(['status', 'action', 'reset', 'reset_pwd', 'edit', 'delete'])
            ->make(true);
    }
}
