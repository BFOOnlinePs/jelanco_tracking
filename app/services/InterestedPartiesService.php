<?php

namespace App\Services;

use App\Models\InterestedPartyModel;

class InterestedPartiesService
{
    public function addRemoveInterestedParties($article_type, $article_id, $interested_party_ids)
    {
        $userId = auth()->user()->id;

        // Handle adding new interested parties
        foreach ($interested_party_ids as $ipInterestedPartyId) {
            // Check if this interested party is already associated with the article
            $exists = InterestedPartyModel::where('ip_article_type', $article_type)
                ->where('ip_article_id', $article_id)
                ->where('ip_interested_party_id', $ipInterestedPartyId)
                ->exists();

            if (!$exists) {
                InterestedPartyModel::create([
                    'ip_article_type' => $article_type,
                    'ip_article_id' => $article_id,
                    'ip_interested_party_id' => $ipInterestedPartyId,
                    'ip_added_by_id' => $userId,
                ]);
            }
        }

        // Handle removing old interested parties
        $existingParties = InterestedPartyModel::where('ip_article_type', $article_type)
            ->where('ip_article_id', $article_id)
            ->get();

        foreach ($existingParties as $existingParty) {
            if (!in_array($existingParty->ip_interested_party_id, $interested_party_ids)) {
                // Check if the current user added this interested party, and if so, delete it
                if ($existingParty->ip_added_by_id == $userId) {
                    $existingParty->delete();
                }
            }
        }
    }

    public function getInterestedParties($article_type, $article_id)
    {
        // get the ipInterestedPartyId with user object
        return InterestedPartyModel::where('ip_article_type', $article_type)
            ->where('ip_article_id', $article_id)
            ->with('user:id,name')
            ->get();
    }

    public function getOnlyInterestedPartiesUsers($article_type, $article_id)
    {
        return InterestedPartyModel::where('ip_article_type', $article_type)
            ->where('ip_article_id', $article_id)
            ->with('user:id,name')
            ->get()
            ->pluck('user');
    }

    public function getArticlesOfInterest($interested_party_id, $article_type)
    {
        $interested_parties = InterestedPartyModel::where('ip_interested_party_id', $interested_party_id)
            ->where('ip_article_type', $article_type)
            ->with('addedByUser:id,name')
            ->when($article_type === 'task', function ($query) {
                $query->with(['task' => function ($query) {
                    $query->select('t_id', 't_content', 'created_at', 't_added_by')
                        ->with('addedByUser:id,name,image');
                }]);
            })
            ->when($article_type === 'submission', function ($query) {
                $query->with([
                    'submission' => function ($query) {
                        $query->select('ts_id', 'ts_content', 'ts_submitter', 'created_at')
                            ->with('submitterUser:id,name,image');
                    }
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        // getInterestedParties
        foreach ($interested_parties as $interested_party) {
            if ($article_type === 'task') {
                $interested_party->task->interested_party_users = $this->getOnlyInterestedPartiesUsers('task', $interested_party->task->t_id);
            } elseif ($article_type === 'submission') {
                $interested_party->submission->interested_party_users = $this->getOnlyInterestedPartiesUsers('submission', $interested_party->submission->ts_id);
            }
        }

        return $interested_parties;
    }
}
