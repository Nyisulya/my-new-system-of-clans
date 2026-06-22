@extends('layouts.app')

@section('title', 'Matangazo')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bullhorn"></i> Matangazo</h1>
        @auth
        <a href="{{ route('announcements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tangazo Jipya
        </a>
        @endauth
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Kichwa</th>
                        <th>Maelezo</th>
                        <th>Aina</th>
                        <th>Tarehe ya Kuanza</th>
                        <th>Tarehe ya Kumalizika</th>
                        <th>Hali</th>
                        @auth
                        <th>Vitendo</th>
                        @endauth
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                        <tr>
                            <td><strong>{{ $announcement->title }}</strong></td>
                            <td style="white-space: normal; min-width: 200px; max-width: 400px;">
                                {{ Str::limit($announcement->content, 100) }}
                                @if(strlen($announcement->content) > 100)
                                    <button type="button" class="btn btn-xs btn-link p-0 text-primary" data-toggle="modal" data-content="{{ htmlspecialchars($announcement->content) }}" data-title="{{ htmlspecialchars($announcement->title) }}" onclick="showAnnouncementContent(this)">Soma zaidi</button>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $announcement->type }}">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                            </td>
                            <td>{{ $announcement->start_date->format('d M, Y') }}</td>
                            <td>
                                {{ $announcement->end_date ? $announcement->end_date->format('d M, Y') : 'Bila kikomo' }}
                            </td>
                            <td>
                                @php
                                    $isActive = now()->startOfDay()->between($announcement->start_date, $announcement->end_date ?? now()->addYears(100));
                                    $isFuture = $announcement->start_date->isFuture();
                                @endphp
                                
                                @if($isFuture)
                                    <span class="badge badge-warning">Imepangwa</span>
                                @elseif($isActive)
                                    <span class="badge badge-success">Hai</span>
                                @else
                                    <span class="badge badge-secondary">Imeisha</span>
                                @endif
                            </td>
                            @auth
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-xs btn-info" title="Hariri">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Je, una uhakika?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Futa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endauth
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Hakuna matangazo yaliyopatikana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right text-sm">
                    {{ $announcements->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal for Announcement Content -->
    <div class="modal fade" id="announcementModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalTitle">Kichwa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="announcementModalContent" style="white-space: pre-wrap;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function showAnnouncementContent(button) {
        let content = $(button).data('content');
        let title = $(button).data('title');
        $('#announcementModalTitle').text(title);
        $('#announcementModalContent').text(content);
        $('#announcementModal').modal('show');
    }
</script>
@stop
