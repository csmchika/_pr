<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:post-list|post-create|post-edit|post-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:post-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:post-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:post-delete', ['only' => ['destroy']]);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = Post::latest()->paginate(25);

        return view('posts.index',compact('data'));
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('posts.create');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->extracted_file($request);

        $input = $request->except(['_token']);

        Post::create($input);

        return redirect()->route('posts.index')
            ->with('success','Запись успешно создана');
    }

    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $post = Post::find($id);

        return view('posts.show', compact('post'));
    }

    public function edit(int $id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application
    {
        $post = Post::find($id);

        return view('posts.edit',compact('post'));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->extracted_file($request);

        $post = Post::find($id);

        $post->update($request->all());

        return redirect()->route('posts.index')
            ->with('success', 'Запись успешно изменена');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        Post::find($id)->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Запись удалена');
    }

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
//    Валидация и перенос изображений в storage как отдельная функция
    public function extracted_file(Request $request): void
    {
        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $request->file('image')->storeAs('public/image', $fileNameToStore);
        }

        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png,gif, svg'
        ]);
    }
}
