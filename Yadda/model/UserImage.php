<?php
    set_include_path('.:./model');
    require_once 'DbH.php';

    $dbh = DbH::getDbH();

    foreach($_GET as $key => $value) {
        $$key = trim($value);  // vars with names as in form - keeyboard safer #ack
    }
    if(isset($uid)) {
            $sql  = "select mimetype, imageitself";
            $sql .= " from user";
            $sql .= " where uid = :uid";
        try {
            $q = $dbh->prepare($sql);
            $q->bindValue(':uid', $uid);
            $q->execute();
            $out = $q->fetch();
        } catch(PDOException $e)  {
            printf("Error getting image.<br/>". $e->getMessage(). '<br/>' . $sql);
            die('');
        }
        $out['imageitself'] = stripslashes($out['imageitself']); // strip slashes that was added when inserting to the database
        header("Content-type: " . $out['mimetype']);
        echo $out['imageitself'];
    } else {
        echo 'Fail to output image in getImage.php';
    }
