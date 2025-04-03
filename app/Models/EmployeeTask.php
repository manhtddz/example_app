<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = "employee_task";
    protected $fillable = [
        'employee_id',
        'task_id',
        'del_flag'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($employeeTask) {
            $projectOfTask = Task::where('id', $employeeTask->task_id)->value('project_id');
            $isEmployeeInProject = EmployeeProject::where('employee_id', $employeeTask->employee_id)
                ->where('project_id', $projectOfTask)
                ->exists();

            if (!$isEmployeeInProject) {
                throw new \Exception('Employee can only take tasks of their projects.');
            }

            $employeeTask->ins_id = auth()->user()->id;
            $employeeTask->del_flag = IS_NOT_DELETED;
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
    //Update del_flag to 1, so that upd_datetime and upd_id are automatically updated
    public function delete()
    {
        $this->del_flag = IS_DELETED; // Update del_flag to 1
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
}
