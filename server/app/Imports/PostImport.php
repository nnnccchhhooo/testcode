<?php

namespace App\Imports;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PostImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $action = Str::lower($row['actions']);
            if ($action == 'create') {
                $post = Post::create([
                    'user_id' => $row['user_id'],
                    'image' => $row['image'],
                    'title' => $row['title'],
                    'body' => $row['body']
                ]);
                $categories_arr = explode(", ", $row['categories']);
                $id_arr = Category::whereIn('name', $categories_arr)->pluck('id');
                $post->categories()->sync($id_arr);
            } else if ($action == 'update') {
                $post = Post::find($row['id']);
                $post->update([
                    'user_id' => $row['user_id'],
                    'image' => $row['image'],
                    'title' => $row['title'],
                    'body' => $row['body']
                ]);
                $categories_arr = explode(", ", $row['categories']);
                $id_arr = Category::whereIn('name', $categories_arr)->pluck('id');
                $post->categories()->sync($id_arr);
            } else if ($action == 'delete') {
                $post = Post::find($row['id']);
                if (File::exists(storage_path('app/public/img/posts/') . $post->image)) {
                    File::delete(storage_path('app/public/img/posts/') . $post->image);
                }
                $post->categories()->sync([]);
                Comment::where('post_id', $post->id)->delete();
                $post->delete();
            }
        }
    }

    public function rules(): array
    {
        return  [
            '*.user_id' => 'required',
            '*.image' => 'required',
            '*.title' => ['required', 'max:255'],
            '*.body' => 'required',
            '*.categories' => 'required',
            '*.actions' => 'required'
        ];
    }
}
