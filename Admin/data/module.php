<?php


// All modules

function getAllmodules($conn)
{
    $sql = "SELECT *FROM modules";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() >= 1) {
        $modules = $stmt->fetchAll();
        return $modules;
    } else {
        return 0;
    }
}

function getmodulesById($module_id, $conn)
{
    $sql = "SELECT * FROM modules WHERE module_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$module_id]);

    if ($stmt->rowCount() == 1) {
        $module = $stmt->fetch();
        return $module;
    } else {
        return 0;
    }
}

