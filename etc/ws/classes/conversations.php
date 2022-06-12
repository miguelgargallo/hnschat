<?php
    class Conversations {
        public static array $conversations = [];

        function new($conversation) {
            if (!@self::$conversations[$conversation]) {
                $c = new Conversation($conversation);
                self::$conversations[$conversation] = $c;
            }

            return self::$conversations[$conversation];
        }

        function conversationForID($conversation) {
            if (@self::$conversations[$conversation]) {
                return self::$conversations[$conversation];
            }
            return false;
        }
    }

    class Conversation {
        public $id;
        public $users = [];

        public function __construct($id) {
            $this->id = $id;
        }

        function addUser($user) {
            $alreadyHere = false;

            if ($this->users) {
                foreach ($this->users as $key => $info) {
                    if ($user->id == $info->id) {
                        $alreadyHere = true;
                    }
                }
            }

            if (!$alreadyHere) {
                $this->users[] = $user;
                return true;
            }
            return false;
        }

        function removeUser($user) {
            if ($this->users) {
                foreach ($this->users as $key => $info) {
                    if ($user == $info->id) {
                        unset($this->users[$key]);
                    }
                }
            }
        }
    }
?>