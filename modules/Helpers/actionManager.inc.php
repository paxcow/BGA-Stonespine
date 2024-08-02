<?php

namespace Helpers;

use BgaUserException;

trait Undo
{
    function undo($unpass = false, $steps = 1, $changeStateAfter = null,)
    {
        $player_id = $this->getCurrentPlayerId();

        print_r(func_get_args());
        
        if ($unpass) {
            $players[] = $player_id;
            //reactivate player
            $this->gamestate->setPlayersMultiactive($players, null);
        }
        if ($this->gamestate->isPlayerActive($player_id)) {

            //rebuild game situation from db

            $state = $this->gamestate->state()["name"];

            $hand = new \Classes\Hand($player_id);

            $dungeon = new \Classes\Dungeon($player_id);

            $tokens = $this->tokens;

            $handlers = ['state' => &$state, 'hand' => &$hand, 'dungeon' => &$dungeon, 'tokens' => &$tokens];

            //calculate how many actions can be retraced
            $nbr_actions = count(\Helpers\ActionManager::getAllActions($player_id));
            $steps = min($steps, $nbr_actions);


            //reload pending actions to update volatile game situation
            \Helpers\ActionManager::reloadAllActions($player_id, $handlers);

            //undo the last $step actions

            for ($steps; $steps > 0; $steps--) {
                $last_action = \Helpers\ActionManager::getLastAction($player_id);
                if ($last_action) {
                    self::trace("********** UNDO " . $last_action::class . "*******************");
                    $last_action->setHandlers($handlers);
                    $notifier = new \Helpers\ActionNotifier($player_id);
                    $last_action->undo($notifier);
                    self::trace("********** REMOVE ACTION " . $last_action->action_id . "*******************");

                    \Helpers\ActionManager::removeAction($last_action->action_id);
                }
            }

            //go to a different state
            if ($changeStateAfter) {
                $this->gamestate->nextState($changeStateAfter);
            }
        }
    }
}

class ActionManager
{ //class to manager ActionRow writing and reading from action_table DB
    private static $db;

    public static function init()
    {
        self::$db = DBManagerRegister::addManger("actions", ActionRow::class);
    }

    public static function getAllActions($player_id = null): array
    {
        $actions = [];
        $rows = self::$db->getAllRows();
        foreach ($rows as $row) {
            $action = new ActionRow($row);
            $actions[$action->actionId] = $action->getAction();
        }
        if (!$player_id) return $actions;
        $pl_actions = array_filter($actions, function ($a) use ($player_id) {
            return $a->getPlayerId() == $player_id;
        });
        return $pl_actions;
    }

    public static function reloadAllActions($player_id = null, &$handlers)
    {
        $actions = self::getAllActions($player_id);
        foreach ($actions as $action) {
            $action->setHandlers($handlers);
            $action->reload();
        }
    }

    public static function committAllActions($player_id = null, &$handlers)
    {
        $actions = self::getAllActions($player_id);
        foreach ($actions as $action) {
            $notifier = new \Helpers\ActionNotifier($player_id);
            $action->setHandlers($handlers);
            $action->committ($notifier);
        }
    }

    public static function getActionByKey(string $key_value): ActionCommand|null
    {
        $actionRow = self::$db->createObjectFromDB($key_value);
        return $actionRow->getAction();
    }

    public static function getLastAction(string $player_id = null): ActionCommand|null
    {
        if (!$player_id) {
            $action = new ActionRow(self::$db->getLastRow());
            return $action->getAction() ?? null;
        } else {
            $actions = self::getAllActions($player_id);
            return $actions[array_key_last($actions)] ?? null;
        }
    }

    public static function saveAction(ActionCommand $action)
    {

        $row = new ActionRow($action);
        self::$db->saveObjectToDB($row);
    }
    public static function removeAction(string $action_id)
    {
        self::$db->deleteObjectFromDb($action_id);
    }

    public static function clearAll($player_id = null)
    {
        if ($player_id) {
            $actions = self::getAllActions($player_id);
            foreach ($actions as $id => $action) {
                self::$db->deleteObjectFromDb($id);
            }
        } else {
            self::$db->clearAll();
        }
    }
}



class ActionRow
{
    #[dbKey(name: "action_id")]
    public $actionId;
    #[dbColumn(name: "action_json")]
    public $actionEncoded;

    private const MAX_ACTION_JSON_LENGTH = 65535;

    public function getAction()
    {

        $action = rebuildAction($this->actionEncoded);

        $action->action_id = $this->actionId;
        return $action;
    }

    public function setAction(ActionCommand $action)
    {
        $this->actionEncoded = serializeAction($action);
    }

    public function replaceAction(ActionCommand $action)
    {
        $this->actionEncoded = serializeAction($action);
        if (strlen($this->actionEncoded) > self::MAX_ACTION_JSON_LENGTH) throw new \BgaSystemException("Serialized Action JSON is too long!");
    }

    public function __construct($data)
    {
        if (is_object($data)) {
            $this->setAction($data);
        } else if ($data) {
            $this->actionId = $data["action_id"];
            $this->actionEncoded = $data["action_json"];
        }
    }
}

abstract class ActionCommand
{
    public $action_id;
    protected $player_id;

    public function __construct($player_id)
    {
        $this->player_id = $player_id;
    }
    public function getPlayerId()
    {
        return $this->player_id;
    }

    public function setHandlers($args)
    {

        if (is_array($args)) {
            foreach ($args as $propertyName => &$propertyValue) {
                if (property_exists($this, $propertyName)) {
                    $this->$propertyName = &$propertyValue;
                }
            }
        }
    }



    abstract public function do($notifier);
    abstract public function reload($notifier);
    abstract public function undo($notifier);
    abstract public function committ();
}

class ActionNotifier
{

    private static $game;

    public static function setGame(\Table $game)
    {
        self::$game = $game;
    }

    private $player_id;
    public function __construct(string $player_id = null)
    {
        $this->player_id = $player_id;
    }
    public function notifyPlayerAndOthers(string $notifType, string $notifLog, array $notifArgs)
    {
        if ($this->player_id) $this->notifyCurrentPlayer("{$notifType}_private", $notifLog, $notifArgs);
        $this->notifyAllPlayers($notifType, $notifLog, $notifArgs);
    }
    public function notifyAll(string $notifType, string $notifLog, array $notifArgs)
    {
        $this->notifyAllPlayers($notifType, $notifLog, $notifArgs);
    }
    public function notifyAllNoMessage(string $notifType, array $notifArgs)
    {
        $this->notifyAllPlayers($notifType, "", $notifArgs);
    }
    public function notify(string $notifType, string $notifLog, array $notifArgs)
    {
        if ($this->player_id) {
            $this->notifyCurrentPlayer($notifType, $notifLog, $notifArgs);
        } else {
            $this->notifyAllPlayers($notifType, $notifLog, $notifArgs);
        }
    }
    public function notifyNoMessage(string $notifType, array $notifArgs)
    {
        if ($this->player_id) {
            $this->notifyCurrentPlayer($notifType, "", $notifArgs);
        } else {
            $this->notifyAllPlayers($notifType, "", $notifArgs);
        }
    }

    protected function notifyCurrentPlayer(string $notifType, string $notifLog, array $notifArgs)
    {
        self::$game->notifyPlayer($this->player_id, $notifType, $notifLog, $this->processNotifArgs($notifArgs));
    }

    protected function notifyAllPlayers(string $notifType, string $notifLog, array $notifArgs)
    {
        self::$game->notifyAllPlayers($notifType, $notifLog, $this->processNotifArgs($notifArgs));
    }


    protected function processNotifArgs(array $notifArgs)
    {
        $info = self::$game->loadPlayersBasicInfos();
        $playerName = '';
        if (array_key_exists($this->player_id, $info)) {
            $playerName = $info[$this->player_id]['player_name'];
        }
        return json_decode(json_encode(
            array_merge(
                [
                    'playerId' => $this->player_id,
                    'player_id' => $this->player_id,
                    'playerName' => $playerName,
                    'player_name' => $playerName,
                ],
                $notifArgs
            )
        ), true);
    }
}




function serializeAction(ActionCommand $action): string
{

    // Convert the action to an array including metadata about class names
    $result = serializeObjectToArray($action);
    //encode the array in JSON
    return json_encode($result);
}
function serializeObjectToArray($object, &$seenObject = [])
{
    if (!is_object($object)) {
        return $object;
    }

    //check for circular references
    $objectId = spl_object_id($object);
    if (isset($seenObject[$objectId])) {
        return ["__recursive_red" => get_class($object)];
    }

    $reflectionClass = new \ReflectionClass($object);
    $properties = $reflectionClass->getProperties();
    $data = ['__class' => get_class($object)]; // Store the class name for reconstruction

    $seenObject[$objectId] = true; //mark the object as seen

    foreach ($properties as $property) {
        $property->setAccessible(true);
        $value = $property->getValue($object);
        // Recursively serialize objects
        $data[$property->getName()] = serializeObjectToArray($value);
    }
    return $data;
}

function rebuildAction(string $actionEncoded): ActionCommand
{
    if ($actionEncoded) {
        $data = json_decode($actionEncoded, true);
        return deserializeArrayToObject($data);
    }
}

function deserializeArrayToObject($array)
{
    if (is_array($array) && isset($array['__class'])) {

        $class = $array['__class'];
        if (class_exists($class)) {
            $reflect = new \ReflectionClass($class);
            $object = $reflect->newInstanceWithoutConstructor();
            foreach ($array as $propName => $propValue) {
                if ($propName !== '__class') {
                    $property = $reflect->getProperty($propName);
                    $property->setAccessible(true);
                    $property->setValue($object, deserializeArrayToObject($propValue));
                }
            }
            return $object;
        }
    }
    return $array; // Return as is if not an object array
}
