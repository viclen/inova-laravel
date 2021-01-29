<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qtd = request()->input('qtd');
        if ($qtd) {
            request()->session()->put('qtd', $qtd);
        } else {
            $qtd = request()->session()->get('qtd', 10);
        }

        return view('pages.user.index', [
            'dados' => User::select(['id', 'name', 'username', 'email'])->paginate($qtd),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'username' => ['required', 'string', 'min:3', 'unique:users'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect('/usuarios');
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = User::find($id);

        $attr = $user->getAttributes();

        unset($attr['password']);
        unset($attr['api_token']);
        unset($attr['remember_token']);

        return view('pages.padrao.verdados', [
            'dados' => [
                'usuario' => $attr,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);

        return view('pages.user.edit', [
            "dados" => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'username' => ['required', 'string', 'min:3'],
        ]);

        User::find($id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
        ]);

        return redirect(route('users'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        User::find($id)->delete();

        return [
            'status' => 1
        ];
    }
}
