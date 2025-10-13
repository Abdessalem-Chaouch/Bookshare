<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications()
    {
      $user = auth()->user();

        // Crée les notifications si besoin
        \App\Services\BorrowNotificationService::handle();

        // Récupère les 10 dernières notifications
        $notifications = $user->notifications()->latest()->take(10)->get();

        return view('FrontOffice.Borrows.Borrows', compact('borrows', 'notifications'));

    }

    public function delete($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    public function clearAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }
}
