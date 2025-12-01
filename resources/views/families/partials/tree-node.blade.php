<li>
    <a href="{{ route('members.dashboard', $member->id) }}">
        <div class="member-card">
            <img src="{{ $member->profile_photo_url }}" class="member-img" alt="User Image">
            <span class="member-name">{{ $member->first_name }}</span>
            <span class="member-dates">
                {{ $member->date_of_birth ? $member->date_of_birth->format('Y') : '?' }} - 
                {{ $member->status == 'deceased' && $member->date_of_death ? $member->date_of_death->format('Y') : ($member->status == 'alive' ? 'Present' : '?') }}
            </span>
            @if($member->spouse)
                <div class="member-spouse">
                    <i class="fas fa-heart text-danger"></i> {{ $member->spouse->first_name }}
                </div>
            @endif
        </div>
    </a>
    @if($member->children->count() > 0)
        <ul>
            @foreach($member->children as $child)
                @include('families.partials.tree-node', ['member' => $child])
            @endforeach
        </ul>
    @endif
</li>
