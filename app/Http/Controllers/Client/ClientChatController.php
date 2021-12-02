<?php

namespace App\Http\Controllers\Client;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Client\ClientBaseController;
use App\Http\Requests\Message\ClientChatStore;
use App\User;
use App\UserChat;
use App\UserchatFile;
use Pusher\Pusher;

/**
 * Class MemberChatController
 * @package App\Http\Controllers\Member
 */
class ClientChatController extends ClientBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.message';
        $this->pageIcon = 'icon-envelope';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('messages', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
        if ($this->messageSetting->allow_client_admin == 'no' && $this->messageSetting->allow_client_employee == 'no') {
            abort(403);
        }

        $this->userList = $this->userListLatest();

        $userID = request()->get('userID');
        $id     = $userID;
        $name   = '';

        if (count($this->userList) != 0) {
            if (($userID == '' || $userID == null)) {
                $id   = $this->userList[0]->id;
                $name = $this->userList[0]->name;
            } else {
                $id = $userID;
                $name = User::findOrFail($userID)->name;
            }

            $updateData = ['message_seen' => 'yes'];
            UserChat::messageSeenUpdate($this->user->id, $id, $updateData);
        }

        $this->dpData = $id;
        $this->dpName = $name;
        $this->upload = can_upload();
        $this->chatDetails = UserChat::chatDetail($id, $this->user->id);

        if (request()->ajax()) {
            return $this->userChatData($this->chatDetails, 'user');
        }

        return view('client.user-chat.index', $this->data);
    }

    /**
     * @param $chatDetails
     * @param $type
     * @return string
     */
    public function userChatData($chatDetails)
    {
        $chatMessage = '';

        $this->chatDetails = $chatDetails;

        $chatMessage .= view('client.user-chat.ajax-chat-list', $this->data)->render();

        $chatMessage .= '<li id="scrollHere"></li>';

        return Reply::successWithData(__('messages.fetchChat'), ['chatData' => $chatMessage]);
    }

    /**
     * @return mixed
     */
    public function postChatMessage(ClientChatStore $request)
    {
        $this->user = auth()->user();

        $message = $request->get('message');

        if ($request->user_type == 'admin') {
            $userID = $request->get('admin_id');
        } else {
            $userID = $request->get('user_id');
        }

        $allocatedModel = new UserChat();
        $allocatedModel->message         = $message;
        $allocatedModel->user_one        = $this->user->id;
        $allocatedModel->user_id         = $userID;
        $allocatedModel->from            = $this->user->id;
        $allocatedModel->to              = $userID;
        $allocatedModel->save();

        $this->userLists = $this->userListLatest();

        $this->userID = $userID;

        if($this->pusherSettings->message_status)
        {
            $this->triggerPusher('message-updated-channel', 'message-updated', ['user_from' => $allocatedModel->from, 'user_to' => $userID]);
        }

        $users = view('admin.user-chat.ajax-user-list', $this->data)->render();

        $lastLiID = '';
        return Reply::successWithData(__('messages.fetchChat'), ['chatData' => $this->index(), 'dataUserID' => $this->user->id, 'userList' => $users, 'liID' => $lastLiID, 'chat_id' => $allocatedModel->id]);
    }

    /**
     * @return mixed
     */
    public function userListLatest($term = null)
    {
        $result = User::userListLatest($this->user->id, $term);

        return $result;
    }

    public function getUserSearch()
    {
        $term = request()->get('term');
        $this->userLists = $this->userListLatest($term);

        $users = '';

        $users = view('client.user-chat.ajax-user-list', $this->data)->render();

        return Reply::dataOnly(['userList' => $users]);
    }

    public function create()
    {
        $this->members = User::join('project_members', 'project_members.user_id', '=', 'users.id')
            ->join('projects', 'projects.id', '=', 'project_members.project_id')
            ->where('projects.client_id', $this->user->id)
            ->where('users.company_id', company()->id)
            ->select('users.id', 'users.name')
            ->groupBy('users.id')
            ->get();
        $this->admins = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email_notifications', 'users.email', 'users.created_at', 'users.image')
            ->where('users.company_id', company()->id)
            ->where('roles.name', 'admin')
            ->groupBy('users.id')
            ->get();
        return view('client.user-chat.create', $this->data);
    }

    public function destroy($id)
    {
        $chatFiles = UserchatFile::where('users_chat_id', $id)->get();
        foreach ($chatFiles as $file) {
            Files::deleteFile($file->hashname, 'user-chat-files/');
            $file->delete();
        }

        UserChat::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
