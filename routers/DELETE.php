<?php
function route($urlData, $formData)
{
    showData($formData);
    showData($urlData);
    $urlDataAmount = count($urlData);
    if ($urlDataAmount == 1) {
        //удалить запись с указанным id
        $id = $urlData['0'];
        deleteNote($id);
        echo json_encode(array(
            'status' => 'deleted'
        ), JSON_UNESCAPED_UNICODE);


    } else {
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
}