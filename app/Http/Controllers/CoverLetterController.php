<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CoverLetter;

class CoverLetterController extends Controller
{
    /**
     * Управление сопроводительными письмами
     */
    public function index()
    {
        $coverLetters = CoverLetter::all();
        return view('pages.cover-letters', ['coverLetters' => $coverLetters]);
    }

    /**
     * Сохранить сопроводительное письмо
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $coverLetters = CoverLetter::all();
        $newLetter = [
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ];

        $coverLetters[] = $newLetter;
        CoverLetter::create($newLetter);

        return response()->json([
            'success' => true,
            'letter' => $newLetter
        ]);
    }

    /**
     * Удалить сопроводительное письмо
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');
        $coverLetters = CoverLetter::all();

        $coverLetters = $coverLetters->filter(function($letter) use ($id) {
            return $letter->id !== $id;
        });

        $coverLetters->each(function($letter) {
            $letter->delete();
        });

        return response()->json(['success' => true]);
    }
}
