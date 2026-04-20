namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'location',
        'date',
        'time',
        'quota',
        'description',
        'organizer_id'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
}