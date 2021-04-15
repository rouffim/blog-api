<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Create a new ArticleController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:sanctum', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ArticleResource::collection(
            $this->pageableRequest(
                $request,
                Article::class,
                'title'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->user()->tokenCan(PermissionEnum::AddArticle)) {
            return response()->json("You don't have the permission to add an article.", 405);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:10,200',
            'excerpt' => 'string|max:500',
            'body' => 'required|string',
            'image' => 'image',
            'is_pinned' => 'boolean',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $article = new Article;
        $article->uuid = Str::uuid();

        $this->updateArticle($request, $article);

        return response()->json([
            'message' => 'Article successfully stored',
            'article' => $article
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return ArticleResource::make($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        if($request->user() === $article->user()->id) {
            if (!$request->user()->tokenCan(PermissionEnum::EditOwnArticle)) {
                return response()->json("You don't have the permission to edit this article.", 405);
            }
        } else if (!$request->user()->tokenCan(PermissionEnum::EditAllArticle)) {
            return response()->json("You don't have the permission to edit this article.", 405);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:10,200',
            'excerpt' => 'string|max:500',
            'body' => 'required|string',
            'image' => 'image',
            'is_pinned' => 'boolean',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $this->updateArticle($request, $article);

        return response()->json([
            'message' => 'Article successfully updated',
            'article' => $article
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
    }

    /**
     * Common method store and update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     */
    private function updateArticle(Request $request, Article $article) {
        $article->title = $request->title;
        $article->body = $request->body;
        $article->user()->associate($request->user());

        if ($request->has('excerpt')) {
            $article->excerpt = $request->excerpt;
        }

        if ($request->has('is_pinned') && $request->user()->tokenCan(PermissionEnum::PinArticle)) {
            $article->is_pinned = $request->is_pinned;
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if(Storage::exists(Article::$image_location . '/' . $article->getFilename())) {
                Storage::delete(Article::$image_location . '/' . $article->getFilename());
            }
            $article->image_extension = $request->image->extension();
            $request->image->storeAs(Article::$image_location, $article->getFilename());
        }

        $article->save();
    }
}
