<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = "projects";
    protected $fillable = [
        'name',
        'del_flag'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ins_id = auth()->user()->id;
        });

        static::updating(function ($model) {
            $model->upd_id = auth()->user()->id;
        });
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            $query->where('del_flag', IS_NOT_DELETED);
        });
    }

    public function getQueries($builder)
    {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }

    public function teams()//relationship
    {
        return $this->belongsToMany(
            Team::class,
            'team_project',
            'project_id',
            'team_id'
        );
    }

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'employee_project',
            'project_id',
            'employee_id'
        );
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    //Update del_flag to 1, so that upd_datetime and upd_id are automatically updated
    public function delete()
    {
        $this->del_flag = IS_DELETED; // Update del_flag to 1
        $tasks = Task::where('project_id', $this->id)->get();
        foreach ($tasks as $task) {
            $task->delete();
        }
        return $this->save();
    }

    // Recover deleted record
    public function restore()
    {
        $this->del_flag = IS_NOT_DELETED; // Recover del_flag to 0
        return $this->save();
    }

    // Check is deleted
    public function trashed()
    {
        return $this->del_flag == IS_DELETED;
    }
    public static function getFieldById($id, $field)
    {
        return self::where('id', $id)->value($field);
    }
}
