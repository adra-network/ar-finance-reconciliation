<div class="row">
    <div class="col">

        <nav aria-label="Page navigation example">
            <ul class="pagination">

                @if($params->page > 1)
                    <li class="page-item"><a class="page-link" href="{{ route('phone.transactions.index') . '/' . $params->getUrlQuery(['page' => $params->page-1]) }}">Previous</a></li>
                @endif
                @php
                    $pagesStart = 1;
                    $pagesEnd = 6;
                    $page = $params->page;
                    if ($page > 1) { $pagesStart = -2 + $page; $pagesEnd = 3 + $page; }
                    if ($page == 2) { $pagesStart = -1 + $page; $pagesEnd = 4 + $page; }
                @endphp

                @for($i = $pagesStart; $i < $pagesEnd; $i++)
                    <li class="page-item {{ $i == $params->page ? 'active' : null }}">
                        <a href="{{ route('phone.transactions.index') . '/' . $params->getUrlQuery(['page' => $i]) }}" class="page-link">{{ $i }}</a>
                    </li>
                @endfor
                <li class="page-item"><a class="page-link" href="{{ route('phone.transactions.index') . '/' . $params->getUrlQuery(['page' => $params->page+1]) }}">Next</a></li>
            </ul>
        </nav>

    </div>
</div>