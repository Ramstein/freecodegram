<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Renderable;
use Intervention\Image\Facades\Image;

class ProfilesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index($user)
    {
//        dd($user); // dd= die and dump, returns the content user on the page and returns
//        dd(User::find($user));
//        $user = User::find($user); // will throw error if id is not available
//        $user = User::findOrFail($user); // will fail is id is not available and will error 404 not found error.


        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        $postCount = Cache::remember(
            'count.posts.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->posts->count();
            });

        $followersCount = Cache::remember(
            'count.followers.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->profile->followers->count();
            });

        $followingCount = Cache::remember(
            'count.following.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->following->count();
            });

        return view('profiles.index', compact('user', 'follows',
            'postCount', 'followersCount', 'followingCount'));

    }

    public function edit(User $user)
    {
        $this->authorize('update', $user->profile); // authrizing with the Policy ProfilePolicy

        return view('profiles.edit', compact('user'));
    }

    /**
     * @throws AuthorizationException
     */
    public function update(User $user)
    {
        $this->authorize('update', $user->profile); // authrizing with the Policy ProfilePolicy

        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);

        if (request('image')) {
            $imagePath = request('image')->store('profile', 'public');

            $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
            $image->save();

            $imageArray = ['image' => $imagePath];
        }

        auth()->user()->profile->update(array_merge( // auth for making sure user is logged in
            $data,
            $imageArray ?? []
        ));

        return redirect("/profile/{$user->id}");
    }
}
