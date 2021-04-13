<?php

namespace App\Imports;

use App\Models\Millitary;
use Maatwebsite\Excel\Concerns\ToModel;

class MosImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
    //    dd($row);
        return new Millitary([
           'military_branch' =>$row[0],
           'code' => $row[1],
           'description' => $row[2],
           'rank' => $row[3],
           'section' => $row[4],
           'similar_codes' => $row[5],

        ]);
    }
}
