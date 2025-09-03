<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CoverLetter;
use App\Services\HeadHunterService;

class CoverLetterController extends Controller
{
    /**
     * Управление сопроводительными письмами
     */
    public function index()
    {
        $headHunterService = new HeadHunterService();
        $userHhId = $headHunterService->getCurrentUserId();
        
        $coverLetters = CoverLetter::forUser($userHhId)->get();
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

        $headHunterService = new HeadHunterService();
        $userHhId = $headHunterService->getCurrentUserId();

        $newLetter = [
            'user_hh_id' => $userHhId,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ];

        $createdLetter = CoverLetter::create($newLetter);

        return response()->json([
            'success' => true,
            'letter' => $createdLetter
        ]);
    }

    /**
     * Удалить сопроводительное письмо
     */
    public function delete(Request $request)
    {
        $headHunterService = new HeadHunterService();
        $userHhId = $headHunterService->getCurrentUserId();
        
        $id = $request->input('id');
        
        // Находим письмо, принадлежащее текущему пользователю
        $coverLetter = CoverLetter::forUser($userHhId)->find($id);
        
        if (!$coverLetter) {
            return response()->json([
                'success' => false,
                'message' => 'Письмо не найдено или не принадлежит текущему пользователю'
            ]);
        }
        
        $coverLetter->delete();

        return response()->json(['success' => true]);
    }
}
