<?php

namespace App\Http\Controllers;

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

        $response = Http::get('https://api.stackexchange.com/2.3/questions', [
            'site' => 'stackoverflow.com',
            'tagged' => $request->input('tagged'),
            'fromdate' => $request->input('fromdate'),
            'todate' => $request->input('todate'),
        ]);

        $items = $response->json()['items'];

        foreach ($items as $item) {

            $owner = Owner::firstOrCreate([
                'user_id' => $item['owner']['user_id'],
            ], [
                'account_id' => $item['owner']['account_id'],
                'reputation' => $item['owner']['reputation'],
                'user_type' => $item['owner']['user_type'],
                'accept_rate' => $item['owner']['accept_rate'] ?? null,
                'profile_image' => $item['owner']['profile_image'],
                'display_name' => $item['owner']['display_name'],
                'link' => $item['owner']['link'],
            ]);

            $tags = [];
            foreach ($item['tags'] as $tag) {
                $tags[] = Tag::firstOrCreate([
                    'name' => $tag,
                ]);
            }

            $question = Question::firstOrCreate([
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

            $question->tags()->sync(collect($tags)->pluck('id'));

            return response()->json([
                'data' => $response->json(),
            ]);
        }
    }
}
