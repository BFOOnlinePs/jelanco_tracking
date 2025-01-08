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
}
