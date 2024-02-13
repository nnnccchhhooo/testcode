<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($row['actions'] == 'create') {
                Category::create([
                    'name'     => $row['name']
                ]);
            } else if ($row['actions'] == 'update') {
                $category = Category::find($row['id']);
                $category->name = $row['name'];
                $category->save();
            } else if ($row['actions'] == 'delete') {
                Category::where('id', $row['id'])->delete();
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required',
            '*.actions' => 'required'
        ];
    }
}
