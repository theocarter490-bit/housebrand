@extends('layouts.master')

@section('title', $title ?? __('Sitemap'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Sitemap List', [
            '#' => 'SEO Content',
            'sitemap' => 'Sitemap List',
        ]) !!}
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('sitemap.generate') }}" class="btn btn-success">
                <i class="bx bx-refresh me-1"></i> Regenerate Sitemap
            </a>
        </div>
        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <table class="table table-bordered bg-white shadow-sm">
                    <thead class="table-dark">
                    <tr>
                        <th>File Name</th>
                        <th>Sitemap URL</th>
                        <th>Items (Links)</th>
                        <th style="width:160px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sitemaps as $map)
                        <tr>
                            <td>{{ $map['file'] }}</td>
                            <td><a href="{{ url('/').'/'.$map['file'] }}" target="_blank">{{ url('/').'/'.$map['file'] }}</a></td>
            
                            <td>
                                <ul class="small">
                                    @foreach($map['items'] as $item)
                                        <li><a href="{{url($item)}}" target="_blank">{{ $item }}</a></li>
                                    @endforeach
                                </ul>
                            </td>
            
                            <td>
                                <a href="#" 
                                   class="btn btn-sm btn-primary">
                                   Sync to Google
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
