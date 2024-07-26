<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name' => $row[0],
            'email' => $row[1],
            'password' => bcrypt($row[2]), 
        
        ]);
        
    }
    
            public function rules(): array
               {
            return [
                '0' => 'required|string|max:255',
                '1' => 'required|string|email|max:255|unique:users',
               
                '2' => 'required|string|min:6',
            ];
        }
    
        //public function customValidationMessages()
        //{
        //    return [
        //        '0.required' => 'Name is required',
          //      '0.string' => 'Name must be a string',
            //    '0.max' => 'Name must not exceed 255 characters',
              //  '1.required' => 'Email is required',
        //        '1.string' => 'Email must be a string',
        //        '1.email' => 'Email format is invalid',
        //        '1.max' => 'Email must not exceed 255 characters',
        //        '1.unique' => 'Email already exists',
         //       '2.required' => 'Password is required',
        //        '2.string' => 'Password must be a string',
        //        '2.min' => 'Password must be at least 6 characters',
        //    ];
        //}
    }