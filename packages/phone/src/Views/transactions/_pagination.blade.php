@if($params->pageCount > 1)
    <div class="row">
        <div class="col">

            <nav aria-label="Page navigation example">
                <ul class="pagination">

                    <li class="page-item {{ $params->page == 1 ? 'disabled' : null }}"><a class="page-link"
                                                                                          href="{{ route('phone.transactions.index') . '/' . $params->getUrlQuery(['page' => $params->page-1]) }}">Previous</a>
                    </li>
                    @foreach($params->pages as $page)
                        <li class="page-item {{ $page->active ? 'active' : null }}">
                            <a href="{{ $page->url }}" class="page-link">{{ $page->page }}</a>
                        </li>
                    @endforeach
                    <li class="page-item {{ $params->page == $params->pageCount ? 'disabled' : null }}"><a class="page-link"
                                                                                                           href="{{ route('phone.transactions.index') . '/' . $params->getUrlQuery(['page' => $params->page+1]) }}">Next</a>
                    </li>
                </ul>
            </nav>

        </div>
    </div>
@endif