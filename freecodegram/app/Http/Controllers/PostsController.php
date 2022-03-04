<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');

        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(5);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        $data = request()->validate([
            'caption' => 'required',
//            'image' => 'required|image', or
            'image' => ['required', 'image'],
        ]);
//        $post = new \App\Models\Post();
//        $post->caption = $data['caption'];
//        $post->save(); // other way

//        \App\Models\Post::create($data);

//        $imagePath = request('image')->store('uploads', 's3');// for uploading to s3
        $imagePath = request('image')->store('uploads', 'public');

        $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200, 1200);
        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);
//        dd(request()->all());

        return redirect('/profile/' . auth()->user()->id);

    }

    public function show(Post $post) // putting the type here find the exact type and also do findOrFail
    {
        return view('posts.show', compact('post'));
    }
}
