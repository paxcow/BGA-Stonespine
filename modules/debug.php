<?php

trait Debug
{
    function debugSendNotif($player_id = null, $notification_type, $notification_log, $notification_args)
    {
        if ($player_id) {
            $this->notifyPlayer($player_id, $notification_type, $notification_log, $notification_args);
        } else {
            $this->notifyAllPlayers($notification_type, $notification_log, $notification_args);
        }
    }

    function debugChangeState($state)
    {
        $this->gamestate->jumpToState($state);
    }
}
