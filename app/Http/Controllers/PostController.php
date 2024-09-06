<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    //
    public function storePostWithComments(Request $request)
    {
        DB::beginTransaction();
        try
        {
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content
            ]);

            foreach($request->comments as $comment)
            {
                Comment::created([
                    'post_id' => $post->id,
                    'content' => $comment['content']
                ]);
            }
            
            DB::commit();
            return response()->json(['success' => 'Insert success']);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['error' => 'Fail'] , 500);
        }
    }

    public function storeMultipleComments()
    {
        $comments = [
            ['post_id' => 1 , 'content' => '1'],
            ['post_id' => 2 , 'content' => '2'],
            ['post_id' => 3 , 'content' => '3'],
            ['post_id' => 4 , 'content' => '4']
        ];

        Comment::insert($comments);

        return response()->json(['success' => 'Insert success']);
    }

    //use SoftDelete
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $post->delete();
    }

    public function getDeletedPosts()
    {
        $posts = Post::onlyTrashed()->get();

        return $posts;
    }

    public function restoreDeletedPost()
    {
        $posts = $this->getDeletedPosts();

        $posts->restore();
    }

    // Thêm số lượng data lớn
    public function insertMultipleData()
    {
        $data = [];

        for($i = 0 ; $i <= 10000 ; $i++)
        {
            $data[] = [
                'title' => 'Title' . $i,
                'content' => 'Content' .$i
            ];
        }
        
        DB::table('posts')->insert($data);

        return response()->json(['success' => 'Insert success']);
    }

    // Insert bằng cách chạy Seeder để thêm nhiều dữ liệu
}
