<?php

namespace App\Http\Controllers\Member;

use App\Helper\Reply;
use App\Http\Controllers\Member\MemberBaseController;
use App\Http\Requests\Tasks\StoreTaskComment;
use App\Notifications\TaskCommentClient;
use App\Task;
use App\TaskComment;
use App\User;
use App\TaskCommentFile;
use App\Helper\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class MemberTaskCommentController extends MemberBaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskComment $request)
    {
        $comment = new TaskComment();
        $comment->comment = $request->comment;
        $comment->task_id = $request->taskId;
        $comment->user_id = $this->user->id;
        $comment->save();

        $this->comments = TaskComment::where('task_id', $request->taskId)->orderBy('id', 'desc')->get();
        $this->totalComment = count($this->comments);
        $view = view('member.all-tasks.task_comment', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view,'taskID' => $comment->task_id,'commentID' => $comment->id,'data' => $this->data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = TaskComment::findOrFail($id);
        $comment->delete();
        $this->comments = TaskComment::where('task_id', $comment->task_id)->orderBy('id', 'desc')->get();
        $this->totalComment = count($this->comments);
        $view = view('member.all-tasks.task_comment', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view,'data' => $this->data]);
    }

    public function storeCommentFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $limitReached = false;
            foreach ($request->file as $fileData){
                $upload = can_upload($fileData->getSize() / (1000 * 1024));
                if($upload) {
                    $file = new TaskCommentFile();
                    $file->user_id = $this->user->id;
                    $file->task_id = $request->task_id;
                    $file->comment_id = $request->comment_id;

                    $filename = Files::uploadLocalOrS3($fileData, 'task-files/' . $request->task_id);

                    $file->filename = $fileData->getClientOriginalName();
                    $file->hashname = $filename;
                    $file->size = $fileData->getSize();
                    $file->save();
                } else {
                    $limitReached = true;
                }
            }

            if($limitReached) {
                return Reply::error(__('messages.storageLimitExceed'));
            }
        }

        $this->comments = TaskComment::where('task_id', $request->task_id)->orderBy('id', 'desc')->get();
        $this->totalComment = count($this->comments);
        $view = view('member.all-tasks.task_comment', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $this->data ,'view' => $view,'taskID' => $request->task_id,'commentID' => $request->comment_id]);
    }

    public function destroyCommentFile(Request $request, $id)
    {
        $file = TaskCommentFile::findOrFail($id);
        Files::deleteFile($file->hashname, 'task-files/'.$file->task_id);

        TaskCommentFile::destroy($id);

        $this->taskFiles = TaskCommentFile::where('task_id', $file->task_id)->get();
        $this->comments = TaskComment::where('task_id', $request->task_id)->orderBy('id', 'desc')->get();
        $view = view('member.all-tasks.task_comment', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['html' => $view, 'totalFiles' => sizeof($this->taskFiles)]);
    }

    public function download($id)
    {
        $file = TaskCommentFile::findOrFail($id);
        return download_local_s3($file, 'task-files/' . $file->task_id.'/'.$file->hashname);
    }

}
