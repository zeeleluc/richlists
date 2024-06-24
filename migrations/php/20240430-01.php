<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => env('ADMIN_EMAIL'),
            'password' => $auth->hashPassword('hasmints' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'HasMints',
            'project_slug' => 'hasmints',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user->save();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => env('TEMP_EMAIL_CLIENT'),
            'password' => $auth->hashPassword('astronaughties' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'AstroNaughties',
            'project_slug' => 'astronaughties',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user->save();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => env('TEMP_EMAIL_CLIENT_2'),
            'password' => $auth->hashPassword('pixelastros' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'PixelAstros',
            'project_slug' => 'pixelastros',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user->save();
    }
}
