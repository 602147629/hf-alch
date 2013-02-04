<?php

interface OpenApi_Interface
{

    public function setUser($user_id, $session_key);

    public function usersGetInfo();

    public function friendGetAppFriends();

    public function friendGetFriends();

    public function pagesIsFan();

    public function payRegOrder($param);

    public function payIsOrderComplete($orderId);

    public function payQueryOrders($orderId);

}
