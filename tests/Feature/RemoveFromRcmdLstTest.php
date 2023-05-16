<?php

namespace Tests\Feature;

use App\Models\Recommendation;
use Tests\AppBaseTestCase;

/**
 * Remove an user from an user's recommended list of users tests
 */
class RemoveFromRcmdLstTest extends AppBaseTestCase
{
    public function test_remove_user_from_rcmd_lst()
    {
        Recommendation::factory()->count(5)->create();
        $recommended = Recommendation::query()->first();
        $fromUser = $recommended->recommended_to_user_id; // from this user it will be removed
        $removeUserId = $recommended->recommended_user_id; // this user will be removed
        $endpoint = sprintf('api/v1/recommended-users/%s/%s', $fromUser, $removeUserId);
        $this->authStaff();
        $req = $this->delete($endpoint);
        $req->assertNoContent();

        $deleted = Recommendation::query()
            ->find($recommended->id); // should be deleted

        $this->assertTrue(is_null($deleted)); // since it was deleted it should return null
    }
}
