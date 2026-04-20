namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with('organizer')->find($id);

        if (!$event) {
            return redirect()->back()->with('error', 'Event tidak ditemukan');
        }

        return view('events.detail', compact('event'));
    }
}