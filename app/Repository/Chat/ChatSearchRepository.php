<?php

namespace App\Repository\Chat;

use App\Contracts\Chat\ChatMessageSearchInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ChatSearchRepository
 *
 * @package App\Repository\Chat
 */
class ChatSearchRepository implements ChatMessageSearchInterface
{
    /**
     * Search messages in all dialogs
     *
     * @param  string   $searchText
     * @param  int      $limit
     *
     * @return Collection
     */
    public function searchAll(string $searchText, int $limit = 10): Collection
    {
        return DB::table('messages as m')
            ->select(
                'u.id',
                'u.name',
                'u.avatar',
                'm.dialog_id',
                'm.created_at',
                'm.text'
            )
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->where('u.id', auth()->id())
            ->where('m.text', 'LIKE', "%{$searchText}%")
            ->whereNull('m.deleted_at')
            ->orderByDesc('m.created_at')
            ->orderBy('u.name')
            ->limit($limit)
        ->get();
    }
}
