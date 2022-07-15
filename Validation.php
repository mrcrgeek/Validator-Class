<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Exceptions\ValidationException;

class Validation
{
    public object $model;

    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @param array $validation_rules
     * @param string|null $url_id
     * @param bool $return_data
     * @return array
     * @throws CustomException
     */

    public function validate(Request $request, array $validation_rules, string $url_id = null, bool $return_data = true, bool $return_error = true): mixed
    {
        $final_data = [];

        $errors = [];

        foreach ($validation_rules as $key => $validation)
        {
            $validations_of_input = $validation_rules[$key];

            foreach ($validations_of_input as $input)
            {
                if($request->has($key))
                {
                    if($input == "required")
                    {
                        if(is_array($message = $this->check_required($request->input($key), $key))) $errors[$key] [] = $message;
                    }

                    else if(isset($input['unique']))
                    {
                        if(is_array($message = $this->check_unique($request->input($key), $key, $url_id, $input['unique']))) $errors[$key] [] = $message;
                    }

                    else if(isset($input['collection']))
                    {
                        if(is_array($message = $this->check_in_collection($request->input($key), $input['collection'], $key))) $errors[$key] [] = $message;
                    }

                    else if($input == "string")
                    {
                        if(is_array($message = $this->check_string($request->input($key), $key))) $errors[$key] [] = $message;
                    }

                    else if($input == "int")
                    {
                        if(is_array($message = $this->check_integer($request->input($key), $key))) $errors[$key] [] = $message;
                    }

                    else if($input == "bool")
                    {
                        if(is_array($message = $this->check_bool($request->input($key), $key))) $errors[$key] [] = $message;
                    }

                    else if($input == "email")
                    {
                        if(is_array($message = $this->check_email($request->input($key), $key))) $errors[$key] [] = $message;
                    }

                    else if(isset($input['types']))
                    {
                        if(is_array($message = $this->check_valid_file_types($request->file($key), $input['types'], $key))) $errors[$key] [] = $message;
                    }

                    else if(isset($input['max']) || isset($input['min']))
                    {
                        $max = isset($input['max']) ? $input['max'] : null;
                        $min = isset($input['min']) ? $input['min'] : null;

                        if(is_array($message = $this->check_min_max($request->input($key), $min, $max, $key))) $errors[$key] [] = $message;
                    }

                    else if(isset($input['file']))
                    {
                        if(isset($input['file'] ['max']))
                        {
                            if(is_array($message = $this->check_file_size($request->file($key), $input['file'] ['max'], $key))) $errors[$key] [] = $message;
                        }
                        elseif (isset($input['file'] ['types']))
                        {
                            if(is_array($message = $this->check_valid_file_types($request->file($key), $input['file'] ['types'], $key))) $errors[$key] [] = $message;
                        }
                    }

                    else
                    {
                        throw new CustomException("the validation key $input not found", 0, 500);
                    }

                    $final_data[$key] = $request->input($key);
                }
                else if(!$request->has($key) && in_array('required', $validation_rules[$key]))
                {
                    $errors[$key] = [
                        'message' => "the $key field is required",
                        'code' => 1
                    ];
                }
            }
        }

        if(!$return_data && !$return_error) return [];

        if(count($errors) > 0) throw new ValidationException(json_encode($errors), 422);

        return $final_data;
    }

    /**
     * @param $input
     * @param string $key
     * @return mixed
     */

    protected function check_required($input, string $key):mixed
    {
        if(is_null($input))
        {
            return [
                "message" => "the $key field is required",
                "code" => 1
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param string $input_key
     * @param string|null $id
     * @param bool $exept
     * @return mixed
     */

    protected function check_unique($input, string $input_key, string $id = null, bool $except):mixed
    {
        if(!$except)
        {
            if($this->model::where($input_key, $input)->exists())
            {
                return [
                    'message' => "the $input_key is already taken",
                    'code' => 2
                ];
            }
        }
        else if($except && isset($id))
        {
            if($this->model::where('id', '!=' , $id)->where($input_key, $input)->exists())
            {
                return [
                    'message' => "the $input_key is already taken",
                    'code' => 2
                ];
            }
        }

        return true;
    }

    /**
     * @param $input
     * @param array $collection
     * @param string $key
     * @return mixed
     */

    protected function check_in_collection($input, array $collection, string $key):mixed
    {
        // if(count($collection) < 1)
        // {
        //     throw new CustomException("the Collection list should more than ones", 90, 500);
        // }

        if(!in_array($input, $collection))
        {
            return [
                'message' => "the $key is not in collection",
                'code' => 3
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param string $key
     * @return mixed
     */

    protected function check_string($input, string $key):mixed
    {
        if(!is_string($input))
        {
            return [
                'message' => "the $key should be string",
                'code' => 4
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param string $key
     * @param bool $throw_exception
     * @return mixed
     */

    protected function check_integer($input, string $key, $throw_exception = true):mixed
    {
        if(!is_int($input) && $throw_exception)
        {
            return [
                'message' => "the $key should be int",
                'code' => 5
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param string $key
     * @return mixed
     */

    protected function check_email($input, string $key):mixed
    {
        if(!filter_var($input, FILTER_VALIDATE_EMAIL))
        {
            return [
                'message' => "the $key should be valid email",
                'code' => 6
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param int|null $min
     * @param int|null $max
     * @param string $key
     * @return mixed
     */

    protected function check_min_max($input, int $min = null, int $max = null, string $key):mixed
    {
        $input = (string) $input;

        if(!is_null($min) && strlen($input) < $min)
        {
            return [
                'message' => "the $key Minimum is $min",
                'code' => 7
            ];
        }

        if(!is_null($max) && strlen($input) > $max)
        {
            return [
                'message' => "the $key Maximum is $max",
                'code' => 8
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param string $key
     * @return mixed
     */

    protected function check_bool($input, string $key):mixed
    {
        if(!is_bool($input))
        {
            return [
                'message' => "the $key should be boolean [0&1 or true&false]",
                'code' => 9
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param string $key
     * @return mixed
     */

    protected function check_is_file($input, string $key):mixed
    {
        if(!is_file($input))
        {
            return [
                'message' => "the $key should be file type",
                'code' => 10
            ];
        }

        return true;
    }

    /**
     * @param $input
     * @param int|null $max
     * @param string $key
     * @return mixed
     */

    public function check_file_size($input, int $max = null, string $key):mixed
    {
        if($this->check_is_file($input, $key))
        {
            $input_file_size = round(filesize($input) / 1024000, 1);

            $max = round($max / 1024, 1);

            if(!is_null($max))
            {
                if($input_file_size > $max)
                {
                    return [
                        'message' => "The file size is larger than the value you specified",
                        'code' => 11
                    ];
                }
            }
        }
        else
        {
            return $this->check_is_file($input, $key);
        }

        return true;
    }

    /**
     * @param $input
     * @param array $file_types
     * @param string $key
     * @return mixed
     */

    public function check_valid_file_types($input, array $file_types, string $key):mixed
    {
        if($this->check_is_file($input, $key))
        {
            $get_type_of_file = $input->extension();

            if(!in_array($get_type_of_file, $file_types))
            {
                $convert_to_json = json_encode($file_types);

                return [
                    'message' => "the file type should be in $convert_to_json collection",
                    'code' => 12
                ];
            }
        }
        else
        {
            return $this->check_is_file($input, $key);
        }

        return true;
    }
}
?>
