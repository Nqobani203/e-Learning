<?php

function getlecturesById($lecture_id, $conn)
{
    $sql = "SELECT * FROM lecture WHERE lecture_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$lecture_id]);

    if ($stmt->rowCount() == 1) {
        $lecture = $stmt->fetch();
        return $lecture;
    } else {
        return 0;
    }
}

// All Lectures
function getAlllectures($conn){
    $sql ="SELECT *FROM lecture";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() >= 1) {
        $lectures = $stmt->fetchAll();
        return $lectures;
    }else{
        return 0;
    }
}

function unameIsUnique($uname, $conn, $lecture_id=0){
    $sql ="SELECT username, lecture_id FROM lecture WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$uname]);

    if($lecture_id == 0){
        if ($stmt->rowCount() >= 1) {
            return 1;
        }else{
            return 0;
        }
    }else{
        if ($stmt->rowCount() >= 1) {
           $lecture = $stmt ->fetch();
           if($lecture['lecture_id']==$lecture_id){
            return 1;
           }else return 0;
        }else{
            return 0;
        }
    }
}
//Delete lecture
function deletelecture($id, $conn){
    $sql ='DELETE FROM lecture WHERE lecture_id=?';
    $stmt = $conn->prepare($sql);
    $re = $stmt->execute([$id]);

    if ($re) {
        return 1;
    }else{
        return 0;
    }
}
 /*function removelecture($id, $conn){
    $stmt = $conn->prepare('DELETE FROM lecture WHERE lecture_id = ?');
    if (!$stmt->execute(array($id))){
        $stmt = NULL;
        exit();
    }
    header("location:lecture.php?delete=true");
    exit();
}*/