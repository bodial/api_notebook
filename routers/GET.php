<?php
function route($urlData, $formData)
{
    $urlDataAmount = count($urlData);
    if ($urlDataAmount == 1) {
        //вернуть запись с указанным id
        $id = $urlData['0'];
        $note = singleNoteInfo($id);
        echo json_encode($note, JSON_UNESCAPED_UNICODE);

    } elseif ($urlDataAmount == 0) {
        //вернуть все записи
        $notes = multiNotesInfo();
        echo json_encode($notes, JSON_UNESCAPED_UNICODE);

    } else {
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
}