<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;
class Post extends Model
{

    public $title;
    public $excerpt;
    public $date;

    public $body;

    public $slug;

    public function __construct($title, $excerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function all_posts()
    {
        return cache()->rememberForever('all_posts' ,function(){
            return collect(File::files(resource_path("posts")))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                ))->sortByDesc('date');
        });

    }

    public static function find($slug)
    {
       return static::all_posts()->firstwhere('slug' , $slug) ;
    }

    public static function findorfail($slug){
        $post = static::find($slug);

        if(! $post){
            throw new ModelNotFoundException();
        }

        return $post ;
    }
}

