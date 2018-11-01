<?php

require_once 'ModelA.php';

class User extends Model {
    private $uid;
    private $first;
    private $last;
    private $email;
    private $password;
    private $mimetype;
    private $imageitself;
    private $activated;
    private $pwd;

    public function __construct($uid, $first, $last, $email, $imageitself, $mimetype, $activated) {
        $this->uid = $uid;
        $this->first = $first;
        $this->last = $last;
        $this->email = $email;
        $this->mimetype = $mimetype;
        $this->imageitself = $imageitself;
        $this->activated = $activated;
    }

    public function setPwd($pwd) {
        $this->pwd = $pwd;
    }
    public function getPwd() {
        return $this->pwd;
    }

    public function getUid() {
        return $this->uid;
    }

    public function getFirst() {
      return $this->first;
    }

    public function getLast() {
      return $this->last;
    }

    public function getEmail() {
      return $this->email;
    }

    public function getMimetype() {
      return $this->mimetype;
    }

    public function getImageitself() {
      return $this->imageitself;
    }

    public function create() {
        $sql = "insert into user (uid, first, last, email, password, imageitself, mimetype)
                        values (:uid, :first, :last, :email, :pwd, :imageitself, :mimetype)";

        $dbh = Model::connect();
        try {
            $q = $dbh->prepare($sql);
            $q->bindValue(':uid', $this->getUid());
            $q->bindValue(':first', $this->getFirst());
            $q->bindValue(':last', $this->getLast());
            $q->bindValue(':email', $this->getEmail());
            $q->bindValue('imageitself', $this->getImageitself());
            $q->bindValue('mimetype', $this->getMimetype());
            $q->bindValue(':pwd', password_hash($this->getPwd(), PASSWORD_DEFAULT));
            $q->execute();
        } catch(PDOException $e) {
            printf("<p>Insert of user failed: <br/>%s</p>\n",
                $e->getMessage());
        }
        $dbh->query('commit');
    }

    public function changePwd() {
      $dbh = Model::connect();
      try {
        $sql = "update user";
        $sql .= " set password = :pwd ";
        $sql .= " where uid  ='" . $this->getUid() . "'";
        $q = $dbh->prepare($sql);
        $q->bindValue(':pwd', password_hash($this->getPwd(), PASSWORD_DEFAULT));
        $q->execute();
      } catch(PDOException $e) {
          printf("<p>Update of user failed: <br/>%s</p>\n",
            $e->getMessage());
      }
    }



    public function update() { /*nop*/ }

    public function delete() {
      $dbh = Model::connect();
      try {
        $sql = "delete from  user where uid = '" . $this->getUid() . "'";
       // $sql .= " where uid = '" . $this->getUid() . "'";
        $q = $dbh->prepare($sql);
        $q->execute();
      } catch(PDOException $e) {
          printf("<p>Update of user failed: <br/>%s</p>\n",
            $e->getMessage());
      }
    }

    public function activate() {
      $dbh = Model::connect();
      try {
        $sql = "update user";
        $sql .= " set activated = true";
        $sql .= " where uid = '" . $this->getUid() . "'";
        $q = $dbh->prepare($sql);
        $q->execute();
      } catch(PDOException $e) {
          printf("<p>Update of user failed: <br/>%s</p>\n",
            $e->getMessage());
      }
    }


    public function deactivate() {
      $dbh = Model::connect();
      try {
        $sql = "update user";
        $sql .= " set activated = false";
        $sql .= " where uid = '" . $this->getUid() . "'";
        $q = $dbh->prepare($sql);
        $q->execute();
      } catch(PDOException $e) {
          printf("<p>Update of user failed: <br/>%s</p>\n",
            $e->getMessage());
      }
    }


  public function __toString() {
        return sprintf("%s%s", $this->uid, $this->activated ? ' is activated' : ' is not activated');
    }

    public static function retrievem() {
        $users = array();
        $dbh = Model::connect();

        $sql = "select *";
        $sql .= " from user";
        try {
            $q = $dbh->prepare($sql);
            $q->execute();
            while ($row = $q->fetch()) {
                $user = self::createObject($row);
                array_push($users, $user);
            }
        } catch(PDOException $e) {
            printf("<p>Query of users failed: <br/>%s</p>\n",
                $e->getMessage());
        } finally {
            return $users;
        }
    }

 public static function createObject($a, $f) {
          $image = addslashes(file_get_contents($f['imageitself']['tmp_name'])); // Hiver hele temp billedet ud i en variabel.
          $imagetype = $_FILES['imageitself']['type']; // laver mimetype om til en variabel
          $act = isset($a['activated']) ? $a['activated'] : null; // laver activated om til en variabel
               $user = new User($a['uid'], $a['first'], $a['last'], $a['email'], $image, $imagetype, $act);
               if (isset($a['pwd1'])) {
                   $user->setPwd($a['pwd1']);
               }
               return $user;
           }
}
