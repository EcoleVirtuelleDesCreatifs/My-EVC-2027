<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Services\SaopQuestionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaopController extends Controller
{
    public function show(Candidature $candidature)
    {
        abort_unless($candidature->stage === 'eligible' || $candidature->saopClosed(), 403);

        return response()->view('site.questionnaire-saop', ['c' => $candidature, 'questions' => SaopQuestionnaire::QUESTIONS])
            ->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function save(Request $request, Candidature $candidature)
    {
        $data = $request->validate(['action' => 'required|in:start,save,submit', 'answers' => 'sometimes|array',
            'answers.*' => 'nullable|string|max:10000']);

        return DB::transaction(function () use ($request, $candidature, $data) {
            $c = Candidature::lockForUpdate()->findOrFail($candidature->id);
            abort_unless($c->stage === 'eligible' || $c->saopClosed(), 403);
            if ($c->saopClosed()) {
                return response()->json(['closed' => true, 'remaining' => 0]);
            }
            if ($data['action'] === 'start') {
                if (! $c->saop_started_at) {
                    $c->update(['saop_started_at' => now(), 'saop_answers' => []]);
                }
            } else {
                abort_unless($c->saop_started_at, 409, 'Démarrez le questionnaire avant de répondre.');
                $answers = $c->saop_answers ?? [];
                foreach ($data['answers'] ?? [] as $key => $answer) {
                    abort_unless(array_key_exists($key, SaopQuestionnaire::QUESTIONS), 422);
                    $answers[$key] = $answer ?? '';
                }
                if ($data['action'] === 'submit') {
                    $rules = [];
                    foreach (array_keys(SaopQuestionnaire::QUESTIONS) as $key) {
                        $rules['answers.'.$key] = 'required|string|max:10000';
                    }
                    validator(['answers' => $answers], $rules)->validate();
                }
                $c->saop_answers = $answers;
                if ($data['action'] === 'submit') {
                    $c->saop_submitted_at = now();
                }
                $c->save();
            }
            $remaining = max(0, $c->saop_started_at->copy()->addMinutes(30)->getTimestamp() - now()->getTimestamp());
            if ($request->expectsJson()) {
                return response()->json(['closed' => $c->saopClosed(), 'remaining' => $remaining]);
            }

            return redirect()->to($request->fullUrl());
        });
    }
}
