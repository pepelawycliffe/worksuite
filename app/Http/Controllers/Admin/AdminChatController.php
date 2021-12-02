<?php

namespace App\Http\Controllers\Admin;

use App\DiscussionFile;
use App\DiscussionReply;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\ChatStoreRequest;
use App\MessageSetting;
use App\User;
use App\UserChat;
use App\UserchatFile;

/**
 * Class MemberChatController
 * @package App\Http\Controllers\Member
 */
class AdminChatController extends AdminBaseController
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

        return view('admin.user-chat.index', $this->data);
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

        $chatMessage .= view('admin.user-chat.ajax-chat-list', $this->data)->render();

        $chatMessage .= '<li id="scrollHere"></li>';

        return Reply::successWithData(__('messages.fetchChat'), ['chatData' => $chatMessage]);
    }

    /**
     * @return mixed
     */
    public function postChatMessage(ChatStoreRequest $request)
    {
        $this->user = auth()->user();

        $message = $request->get('message');

        if ($request->user_type == 'client') {
            $userID = $request->get('client_id');
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
        return Reply::successWithData(__('messages.fetchChat'), ['chatData' => $this->index(), 'dataUserID' => $this->user->id, 'userList' => $users, 'liID' => $lastLiID,'chat_id' => $allocatedModel->id]);
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
        $users = view('admin.user-chat.ajax-user-list', $this->data)->render();

        return Reply::dataOnly(['userList' => $users]);
    }

    public function create()
    {
        $this->members = User::allEmployees($this->user->id);
        $this->clients = User::allClients();
        $this->messageSetting = MessageSetting::first();
        return view('admin.user-chat.create', $this->data);
    }

    public function destroy($id)
    {
        $chatFiles = UserchatFile::where('users_chat_id', $id)->get();
        foreach ($chatFiles as $file) {
            Files::deleteFile($file->hashname, 'user-chat-files/');
            $file->delete();
        }
        UserChat::destroy($id);

        $lastLiID = '';
        $this->userLists = $this->userListLatest();
        $users = view('admin.user-chat.ajax-user-list', $this->data)->render();
        return Reply::successWithData(__('messages.deleteSuccess'), ['chatData' => $this->index(), 'dataUserID' => $this->user->id, 'userList' => $users, 'liID' => $lastLiID]);
    }

}
