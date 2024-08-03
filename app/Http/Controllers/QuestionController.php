<?php

namespace App\Http\Controllers;

use App\Models\Inquire;
use App\Models\Owner;
use App\Models\Question;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuestionController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'tagged' => 'required|string',
            'fromdate' => 'numeric',
            'todate' => 'numeric|gte:fromdate',
        ]);

        // If inquire exists, return the questions
        $inquire = Inquire::query()
            ->where('tagged', $request->input('tagged'))
            ->where('fromdate', $request->input('fromdate'))
            ->where('todate', $request->input('todate'))
            ->first();

        if ($inquire) {
            return response()->json([
                'data' => $inquire->questions()->with('owner', 'tags')->get(),
            ]);
        }

        // Otherwise call the API and store the data
        $response = Http::get('https://api.stackexchange.com/2.3/questions', [
            'site' => 'stackoverflow.com',
            'tagged' => $request->input('tagged'),
            'fromdate' => $request->input('fromdate'),
            'todate' => $request->input('todate'),
        ]);

        $items = $response->json()['items'];
        $question_ids = [];

        foreach ($items as $item) {

            $owner = $item['owner']['user_type'] == 'does_not_exist'
                ? Owner::updateOrCreate([
                    'display_name' => $item['owner']['display_name'],
                ], [
                    'user_type' => 'does_not_exist',
                ])
                : Owner::updateOrCreate([
                    'user_id' => $item['owner']['user_id'],
                ], [
                    'account_id' => $item['owner']['account_id'] ?? null,
                    'reputation' => $item['owner']['reputation'] ?? null,
                    'user_type' => $item['owner']['user_type'],
                    'accept_rate' => $item['owner']['accept_rate'] ?? null,
                    'profile_image' => $item['owner']['profile_image'] ?? null,
                    'display_name' => $item['owner']['display_name'],
                    'link' => $item['owner']['link'] ?? null,
                ]);

            $tags = [];
            foreach ($item['tags'] as $tag) {
                $tags[] = Tag::firstOrCreate([
                    'name' => $tag,
                ]);
            }

            $question = Question::updateOrCreate([
                'question_id' => $item['question_id'],
            ], [
                'owner_id' => $owner->id,
                'is_answered' => $item['is_answered'],
                'view_count' => $item['view_count'],
                'accepted_answer_id' => $item['accepted_answer_id'] ?? null,
                'answer_count' => $item['answer_count'],
                'score' => $item['score'],
                'last_activity_date' => $item['last_activity_date'],
                'creation_date' => $item['creation_date'],
                'last_edit_date' => $item['last_edit_date'] ?? null,
                'content_license' => $item['content_license'] ?? null,
                'link' => $item['link'],
                'title' => $item['title'],
            ]);

            $question_ids[] = $question->id;
            $question->tags()->sync(collect($tags)->pluck('id'));
        }

        $inquire = Inquire::updateOrCreate([
            'tagged' => $request->input('tagged'),
            'fromdate' => $request->input('fromdate'),
            'todate' => $request->input('todate'),
        ]);

        $inquire->questions()->sync($question_ids);

        return response()->json([
            'data' => $inquire->questions()->with('owner', 'tags')->get(),
        ]);
    }
}
