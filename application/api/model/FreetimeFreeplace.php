<?php
/**
 * Created by wangqing.
 * User: ZKRS
 * Date: 2018.4.27
 * Time: 20:02
 */

namespace app\api\model;


use app\lib\Exception\teacher\DatabaseOperationException;
use think\Model;

/**
 * Class FreetimeFreeplace
 * @package app\api\model
 * freetime_freeplace表可以在拆成teacher <--> freetime_freeplace关联
 */
class FreetimeFreeplace extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function insertItem($freetime_id, $freeplace_id, $teacher_id, $max_student, $detail, $memo)
    {
        $res = $this->isUpdate(false)->data([
            'freeplace_id' => $freeplace_id,
            'freetime_id' => $freetime_id,
            'teacher_id' => $teacher_id,
            'max_student' => $max_student,
            'detail' => $detail,
            'memo' => $memo
        ])->save();

        if ($res)
            return $res;
        else
            throw new DatabaseOperationException([
                'errorMessage' => '插入预约失败'
            ]);

    }

    public function freeplace()
    {
        return $this->belongsTo("FreePlace", "freeplace_id", "freeplace_id");
    }

    public function freetime()
    {
        return $this->belongsTo("FreeTime", "freetime_id", "freetime_id");
    }

    public function queryItem($id, $is_place_time_id = false)
    {
        $visible_array = ['freeplace_freetime_id', 'freetime.start_time', 'freetime.end_time', 'freeplace.freeplace', 'max_student', 'detail', 'memo'];

        if ($is_place_time_id)
            return $this->with(['freetime', 'freeplace'])->where('freeplace_freetime_id', '=', $id)->find()->visible(['max_student']);

        return $this->with(['freetime', 'freeplace'])->where('teacher_id', '=', $id)->select()->visible($visible_array);
    }

    public function updateCurrentNum($freeplace_freetime_id, $current)
    {
        $res = $this->where('freeplace_freetime_id', '=', $freeplace_freetime_id)->update(['current_student' => $current]);

        if ($res == 1)
            return true;
        else
            return false;
    }
}