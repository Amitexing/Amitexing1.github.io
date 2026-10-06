<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|max:3000',
        ]);

        // Пример: отправка письма на вашу почту
        Mail::raw("Сообщение от {$validated['name']} <{$validated['email']}>:\n\n{$validated['message']}", function ($message) {
            $message->to('you@example.com')->subject('Новое сообщение с сайта');
        });

        return back()->with('success', 'Сообщение отправлено!');
    }
}
