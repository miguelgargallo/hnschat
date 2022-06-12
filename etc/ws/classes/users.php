<?php
    class Users {
        public static array $users = [];
        public static array $allUsers = [];

        function new($socket, $id, $domains) {
            $user = $socket->resourceId;

            if (!@self::$users[$user]) {
                $c = new User($socket, $id, $domains);
                self::$users[$user] = $c;
            }

            return self::$users[$user];
        }

        function delete($id) {
            $user = self::userForID($id);
            unset(self::$users[$id]);
            $user = null;
        }

        function usersForSession($id) {
            $users = [];

            foreach (self::$users as $key => $value) {
                if ($value->session == $id) {
                    $users[] = self::$users[$key];
                }
            }

            if ($users) {
                return $users;
            }
            return false;
        }

        function userForID($id) {
            if (@self::$users[$id]) {
                return self::$users[$id];
            }
            return false;
        }

        function active() {
            if (@self::$users) {
                $output = [];

                foreach (self::$users as $key => $user) {
                    $active = $user->active;

                    if ($active) {
                        $diff = time() - $active["time"];
                        if ($diff > 2) {
                            $user->active = false;
                        }

                        if ($active) {
                            $output[] = $active["from"];
                        }
                    }
                }

                return $output;
            }
        }

        function typing() {
            if (@self::$users) {
                $output = [];

                foreach (self::$users as $key => $user) {
                    $typing = $user->typing;

                    if ($typing) {
                        $diff = time() - $typing["time"];
                        if ($diff > 1.5) {
                            $user->typing = false;
                        }

                        if ($typing) {
                            $output[] = $user;
                        }
                    }
                }

                return $output;
            }
        }

        function allUsersContains($id) {
            foreach (self::$allUsers as $key => $user) {
                if ($user["id"] === $id) {
                    return true;
                }
            }
            return false;
        }

        function updateAllUsers() {
            self::$allUsers = getUsers();
        }

        function getAllUsers() {
            return self::$allUsers;
        }

        function sendAllUsers() {
            foreach (self::$users as $key => $client) {
                $client->socket->send("USERS ".json_encode(self::$allUsers));
            }
        }

        function userActiveForDomain($id) {
            if (@self::$users) {
                $output = [];

                foreach (self::$users as $key => $user) {
                    $active = $user->active;

                    if ($active) {
                        if ($id === $active["from"]) {
                            return true;
                        }
                    }
                }
            }

            return false;
        }
    }

    class User {
        public $id;
        public $socket;

        public $session;
        public $domains;
        public $conversations;

        public $active;
        public $typing;

        public function __construct($socket, $session, $domains) {
            $this->id = $socket->resourceId;
            $this->socket = $socket;
            $this->session = $session;
            $this->domains = $domains;
        }

        function addConversation($conversation) {
            $this->conversations[] = $conversation;
        }

        function setPing($from=null, $time=null) {
            if ($from) {
                $this->active = [
                    "from" => $from,
                    "time" => $time
                ];
            }
            else {
                $this->active = false;
            }
        }

        function setTyping($bool, $from=null, $to=null, $time=null) {
            if ($bool) {
                $this->typing = [
                    "time" => $time,
                    "from" => $from,
                    "to" => $to
                ];
            }
            else {
                $this->typing = false;
            }
        }
    }
?>