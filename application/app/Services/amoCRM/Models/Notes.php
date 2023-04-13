<?php


namespace App\Services\amoCRM\Models;

abstract class Notes
{
    public static function add($model, array $values)
    {
        foreach ($values as $key => $value) {

            $array[] = ' - '.$key.' : '.$value;
        }

        $note = $model->createNote($type = 4);
        $note->text = implode("\n", $array);
        $note->save();

        return $note;
    }

    public static function addOne($model, $text)
    {
        $note = $model->createNote($type = 4);
        $note->text = $text;
        $note->save();

        return $note;
    }

    public static function formatCall(array $call) : string
    {
        return implode("\n", [
            'Звонок через приложение',
            '-------------------------------',
            "Дата и время - ". $call['datetime'],
            "Телефон - ". $call['phone'],
            "Продолжительность - ". (($call['duration'] % 60) % 60).' мин '.($call['duration'] % 60).' сек',
            "Источник - ". $call['type'],
            "Ссылка - ". env('APP_URL').'/'.$call['link'],
            $call['direction'] == 'Outgoing' ? 'Направление - Исходящий' : ' Направление -Входящий',
        ]);
    }
}
