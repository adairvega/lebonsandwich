<?php

namespace lbs\command\model;

class Client extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'client';
    protected $primary_key = 'id';
    public $timestamps = true;
}