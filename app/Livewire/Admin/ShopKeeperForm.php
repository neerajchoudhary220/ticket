<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class ShopKeeperForm extends Component
{
    public $first_name;

    public $last_name;

    public $email;

    public $password;

    public $password_confirmation;

    public $mobile_number;

    public $showPassword = false;

    public $showConfirmPassword = false;

    public $rules = [
        'first_name' => 'required|string|min:3|max:25',
        'last_name' => 'required|string|min:3|max:25',
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'mobile_number' => ['required', 'string', 'min:10', 'max:12', 'unique:users,mobile_number'],
        'password' => ['required', 'string', 'min:6', 'max:20', 'confirmed'],
    ];

    public function togglePasswordVisibility($field)
    {
        if ($field === 'password') {
            $this->showPassword = ! $this->showPassword;
        } elseif ($field === 'confirm') {
            $this->showConfirmPassword = ! $this->showConfirmPassword;
        }
    }

    public function save()
    {
        $shop_keeper_input_data = $this->validate($this->rules);
        User::create($shop_keeper_input_data);

        return redirect()->route('admin.shopkeepers');

    }

    public function render()
    {
        return view('livewire.admin.shop-keeper-form');
    }
}
