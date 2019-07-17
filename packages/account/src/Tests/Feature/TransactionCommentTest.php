<?php

namespace Tests\Feature;

use Account\Models\Transaction;
use App\User;
use Tests\TestCase;

class TransactionCommentTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_get_correct_info_from_controller_index()
    {
        $transaction = factory(Transaction::class)->create();

        $response = $this->actingAs(User::first())->get(route('account.transaction.comment.modal.index', $transaction->id));

        $response->assertJson(['data' => $transaction->toArray()]);
    }

    /**
     * @group shouldRun
     */
    public function test_post_comment_to_controller()
    {
        $transaction = factory(Transaction::class)->create();
        $response    = $this->actingAs(User::first())->post(route('account.transaction.comment.modal.update', [
            'transaction_id' => $transaction->id,
            'comment'        => 'testing-comment',
        ]));
        $response->assertStatus(200);

        $transaction = Transaction::find($transaction->id);
        $this->assertEquals($transaction->comment, 'testing-comment');
    }
}
