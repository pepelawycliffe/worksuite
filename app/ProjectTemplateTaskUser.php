<?php

namespace App;

class ProjectTemplateTaskUser extends BaseModel
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function task()
    {
        return $this->belongsTo(ProjectTemplateTask::class, 'project_template_task_id');
    }

}
