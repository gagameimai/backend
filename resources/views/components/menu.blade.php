<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        @foreach ($menu as $area => $row)
            @if (!isset($row['belongs']))
                <li class="nav-item has-treeview">
                    <a href="{{ route("{$guard}.{$row['route']}") }}" class="nav-link @yield("unit.$area")">
                        <i class="nav-icon {{ $row['icon'] }}"></i>
                        <p>{{ $row['name'] }}</p>
                    </a>
                </li>
            @else
                <li class="nav-item has-treeview @yield("nav.$area")">
                    <a class="nav-link @yield("unit_master.$area")">
                        <i class="nav-icon {{ $row['icon'] }}"></i>
                        <p>
                            <p>{{ $row['name'] }}</p>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach ($row['belongs'] as $unit)
                            <li class="nav-item">
                                <a href="{{ route("{$guard}.{$unit['route']}") }}" class="nav-link @yield("unit.{$unit['route']}")">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ $unit['name'] }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    </ul>
</nav>
