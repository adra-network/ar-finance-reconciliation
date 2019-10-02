<?php


namespace Account\Models;


use Illuminate\Database\Eloquent\Model;

class CommentTemplate extends Model
{

    protected $fillable = ['comment'];

    public $timestamps = false;

}