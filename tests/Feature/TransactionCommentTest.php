<?php

namespace Tests\Feature;

use App\AccountTransaction;
use App\User;
use Tests\TestCase;

class TransactionCommentTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_get_correct_info_from_controller_index()
    {
        $transaction = factory(AccountTransaction::class)->create();

        $response = $this->actingAs(User::first())->get(route('admin.transaction.comment.modal.index', $transaction->id));

        $response->assertJson(['data' => $transaction->toArray()]);
    }

    /**
     * @group shouldRun
     */
    public function test_post_comment_to_controller()
    {
        $transaction = factory(AccountTransaction::class)->create();
        $response    = $this->actingAs(User::first())->post(route('admin.transaction.comment.modal.update', [
            'transaction_id' => $transaction->id,
            'comment'        => 'testing-comment',
        ]));
        $response->assertStatus(200);

        $transaction = AccountTransaction::find($transaction->id);
        $this->assertEquals($transaction->comment, 'testing-comment');
    }
}
