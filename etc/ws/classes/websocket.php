<?php
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;

    class WebSocket implements MessageComponentInterface {
        public function __construct() {
            $getConversations = sql("SELECT * FROM `conversations`");
            if ($getConversations) {
                foreach ($getConversations as $key => $data) {
                    $conversation = Conversations::new($data["id"]);
                }
            }

            $getChannels = sql("SELECT * FROM `channels`");
            if ($getChannels) {
                foreach ($getChannels as $key => $data) {
                    $conversation = Conversations::new($data["id"]);
                }
            }

            Users::updateAllUsers();
        }

        public function onOpen(ConnectionInterface $conn) {
            echo "Connect ({$conn->resourceId})\n";
        }

        public function onMessage(ConnectionInterface $from, $msg) {
            echo "Message ({$from->resourceId}): ".$msg."\n";

            parse($from, $msg);
        }

        public function onClose(ConnectionInterface $conn) {
            echo "Disconnect ({$conn->resourceId})\n";

            $conversations = @Users::userForID($conn->resourceId)->conversations;
            if ($conversations) {
                foreach ($conversations as $key => $conversation) {
                    $conversation->removeUser($conn->resourceId);
                }
            }

            Users::delete($conn->resourceId);
        }

        public function onError(ConnectionInterface $conn, \Exception $e) {

        }
    }
?>