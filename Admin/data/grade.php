<?php
function getGradeById($grade_id, $conn)
{
    $sql = "SELECT * FROM grades WHERE grade_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$grade_id]);

    if ($stmt->rowCount() == 1) {
        $grade = $stmt->fetch();
        return $grade;
    } else {
        return 0;
    }
}
?>