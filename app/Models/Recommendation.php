<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'prediction_id',
        'child_id',
        'recommendation_text',
        'category',
        'date_generated',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_generated' => 'date',
        ];
    }

    public function prediction(): BelongsTo
    {
        return $this->belongsTo(Prediction::class, 'prediction_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    /**
     * Build a structured, caregiver-friendly activity guide for this recommendation.
     */
    public function caregiverActivityGuide(string $childName = 'your child'): array
    {
        $catLower = strtolower((string) $this->category);

        if (str_contains($catLower, 'comm') || str_contains($catLower, 'speech') || str_contains($catLower, 'lang')) {
            return [
                'area' => 'Communication',
                'why_it_matters' => "Helping {$childName} express everyday wants, feelings, and choices reduces frustration and builds confidence during family routines.",
                'what_you_can_do' => [
                    'Offer two clear choices during meals or playtime (for example, holding up two favourite snacks or toys) and wait for a look, point, sound, or word.',
                    'Pair simple, everyday words with pictures or gestures so your child can see and hear the message at the same time.',
                    'Narrate what you and your child are doing together in short, warm phrases during bath time, dressing, or play.',
                ],
                'how_to_practise' => "Choose one relaxed moment each day (such as snack time or story time). Get down to {$childName}'s eye level, offer a choice, and pause for 5 seconds so they have unhurried time to respond in their own way.",
                'what_to_look_out_for' => "Notice if {$childName} points, reaches, makes eye contact, uses a picture card, or tries a sound or word to show what they want.",
                'helpful_reminder' => 'Every communication attempt counts — whether it is a glance, a gesture, a sound, or a word. Celebrate each small effort warmly.',
                'when_to_seek_guidance' => "If {$childName} seems frequently frustrated when trying to express needs, share your home notes with your clinician so they can suggest tailored visual or speech supports.",
            ];
        }

        if (str_contains($catLower, 'social') || str_contains($catLower, 'play') || str_contains($catLower, 'peer')) {
            return [
                'area' => 'Social Interaction',
                'why_it_matters' => "Shared play and warm back-and-forth moments help {$childName} feel connected, safe, and comfortable interacting with family members and peers.",
                'what_you_can_do' => [
                    'Follow your child\'s lead by sitting nearby and gently joining in with the toy or activity they are already enjoying.',
                    'Play simple turn-taking games such as rolling a ball back and forth, building a block tower together, or blowing bubbles.',
                    'Use playful facial expressions and a warm, predictable voice during songs or peek-a-boo routines.',
                ],
                'how_to_practise' => "Start with just 5 to 10 minutes of side-by-side play in a quiet space. Copy what {$childName} is doing with a similar toy, then gently invite one shared turn without forcing eye contact.",
                'what_to_look_out_for' => "Look for moments when {$childName} smiles, watches your turn, hands you an object, or stays engaged in the shared game a little longer.",
                'helpful_reminder' => 'Connection grows best when play feels fun and pressure-free. Even a brief shared smile is a meaningful step forward.',
                'when_to_seek_guidance' => "If {$childName} consistently withdraws or becomes distressed during shared play, mention this to your clinician for gentle social-play ideas.",
            ];
        }

        if (str_contains($catLower, 'indep') || str_contains($catLower, 'adapt') || str_contains($catLower, 'daily') || str_contains($catLower, 'self')) {
            return [
                'area' => 'Everyday Independence',
                'why_it_matters' => "Learning small daily self-care steps helps {$childName} feel capable and makes morning, mealtime, and bedtime routines smoother for the whole family.",
                'what_you_can_do' => [
                    'Break daily routines (like washing hands, putting on shoes, or tidying toys) into 2 or 3 simple, repeatable steps.',
                    'Use a simple picture checklist on the wall so your child can see what comes first and what comes next.',
                    'Do the first step together, then invite your child to finish the last small step on their own.',
                ],
                'how_to_practise' => "Pick one routine to focus on this week. Keep the steps in the exact same order each day, and praise {$childName} as soon as they complete their part.",
                'what_to_look_out_for' => "Notice if {$childName} starts reaching for the next item in the routine (like the towel or toothbrush) with fewer reminders.",
                'helpful_reminder' => 'Building independence takes patience and repetition. Focus on one small step at a time rather than the whole routine at once.',
                'when_to_seek_guidance' => 'Discuss daily living routines with your clinician or occupational therapist if certain textures, clothing, or steps cause ongoing distress.',
            ];
        }

        if (str_contains($catLower, 'sens') || str_contains($catLower, 'rout') || str_contains($catLower, 'reg') || str_contains($catLower, 'calm') || str_contains($catLower, 'trans')) {
            return [
                'area' => 'Calm Routines & Transitions',
                'why_it_matters' => "Predictable routines and gentle warnings before changes help {$childName} feel secure and reduce overwhelm when moving from one activity to another.",
                'what_you_can_do' => [
                    'Give a calm 5-minute and 2-minute heads-up before ending a favourite activity (using a visual timer or a simple "first–then" card).',
                    'Create a quiet, cozy corner at home with a soft blanket, favourite comfort item, or dim lighting for calming breaks.',
                    'Keep a short, familiar transition ritual — such as a tidy-up song or carrying a special toy to the next room.',
                ],
                'how_to_practise' => "Before switching activities, show {$childName} what is ending and what is coming next ('First put blocks in the basket, then snack time'). Allow a short calm break if they seem tired or overloaded.",
                'what_to_look_out_for' => "Watch for smoother transitions, quicker settling after a change in plans, or {$childName} choosing to take a quiet break when feeling overwhelmed.",
                'helpful_reminder' => 'Big feelings during transitions are a normal way children show that change is hard — your calm, steady presence is their best anchor.',
                'when_to_seek_guidance' => "If transitions or sensory environments regularly lead to intense distress that is hard to soothe, share your observations with {$childName}'s clinician.",
            ];
        }

        return [
            'area' => 'Engagement and Participation',
            'why_it_matters' => "Supporting {$childName}'s focus and enjoyment during everyday activities helps them explore new skills at a comfortable, encouraging pace.",
            'what_you_can_do' => [
                'Keep structured learning or table activities short (around 10 to 15 minutes) and follow them with a favourite movement or play break.',
                'Include your child\'s favourite interests (such as animals, vehicles, music, or colours) inside everyday learning games.',
                'Reduce background noise and extra clutter in the play area so it is easier for your child to focus on one activity at a time.',
            ],
            'how_to_practise' => "Set up one clear activity with a visible beginning and end (such as a 4-piece puzzle or matching game). Cheer {$childName} on and stop while the activity is still enjoyable.",
            'what_to_look_out_for' => "Notice how long {$childName} stays comfortable with the activity and which topics or materials spark the brightest interest.",
            'helpful_reminder' => 'Every child develops at their own pace. Short, happy practice sessions build much stronger habits than long, tiring ones.',
            'when_to_seek_guidance' => "Share what works best at home with your clinician during your next review so they can align therapy goals with {$childName}'s strengths.",
        ];
    }
}