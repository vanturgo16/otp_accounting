<div data-simplebar class="h-100">
    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li>
                <a href="{{ route('dashboard') }}">
                    <i data-feather="home"></i>
                    <span data-key="t-dashboard">Dashboard</span>
                </a>
            </li>

            @can('Akunting_master_data') 
                <li class="menu-title" data-key="t-menu">Master</li>
                <li>
                    <a href="{{ route('ppn.index') }}" class="{{ request()->is('ppn*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-percent"></i>
                        <span>Default PPN</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('bankaccount.index') }}" class="{{ request()->is('bankaccount*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-bank"></i>
                        <span>Bank Account</span>
                    </a>
                </li>
                <li class="menu-title" data-key="t-menu">Manage</li>
                <li>
                    <a href="{{ route('accounttype.index') }}" class="{{ request()->is('accounttype*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-format-list-bulleted-type"></i>
                        <span>Account Type</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('accountcode.index') }}" class="{{ request()->is('accountcode*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-barcode-scan"></i>
                        <span>Account Code</span>
                    </a>
                </li>
                {{-- <li class="{{ request()->is('entitylist*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-format-list-group"></i>
                        <span>Entity List Formula</span>
                    </a>
                    <ul class="sub-menu {{ request()->is('entitylist*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->is('entitylist*') ? 'true' : 'false' }}">
                        <li>
                            <a href="{{ route('entitylist.neraca') }}" class="{{ request()->is('entitylist/neraca*') ? 'bg-light text-primary active' : '' }}">
                                <i class="mdi mdi-format-list-bulleted-square"></i>
                                <span>Neraca</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('entitylist.hpp') }}" class="{{ request()->is('entitylist/hpp*') ? 'bg-light text-primary active' : '' }}">
                                <i class="mdi mdi-format-list-bulleted-square"></i>
                                <span>HPP</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="mdi mdi-format-list-bulleted-square"></i>
                                <span>Laba Rugi</span>
                            </a>
                        </li>
                    </ul>
                </li> --}}
            @endcan
            
            <li class="menu-title" data-key="t-menu">Accounting</li>
            @can('Akunting_generalledger') 
                <li>
                    <a href="{{ route('generalledger.index') }}" class="{{ request()->is('general-ledger*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-file-cabinet"></i>
                        <span>General Ledger</span>
                    </a>
                </li>
            @endcan
            @can('Akunting_sales') 
                <li class="{{ request()->is('transsales*') ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-file-upload"></i>
                        <span>Sales</span>
                    </a>
                    <ul class="sub-menu {{ request()->is('transsales*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->is('transsales*') ? 'true' : 'false' }}">
                        <li>
                            <a href="{{ route('transsales.local.index') }}" class="{{ request()->is('transsales/local*') ? 'bg-light text-primary active' : '' }}">
                                <i class="mdi mdi-ballot-outline"></i>
                                <span>Local</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('transsales.export.index') }}" class="{{ request()->is('transsales/export*') ? 'bg-light text-primary active' : '' }}">
                                <i class="mdi mdi-ballot"></i>
                                <span>Export</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            @can('Akunting_purchase') 
                <li>
                    <a href="{{ route('transpurchase.index') }}" class="{{ request()->is('transpurchase*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-file-download"></i>
                        <span>Purchase</span>
                    </a>
                </li>
            @endcan
            @can('Akunting_generalledger') 
                <li>
                    <a href="{{ route('cashbook.index') }}" class="{{ request()->is('cashbook*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-cash-multiple"></i>
                        <span>Cash Book</span>
                    </a>
                </li>
            @endcan
            {{-- @can('Akunting_import') 
                <li>
                    <a href="{{ route('transimport.index') }}" class="{{ request()->is('transimport*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-import"></i>
                        <span>Import</span>
                    </a>
                </li>
            @endcan --}}

            {{-- @can('Akunting_report') 
                <li class="menu-title" data-key="t-menu">Report</li>
                <li>
                    <a href="{{ route('report.neraca') }}" class="{{ request()->is('report/neraca*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-file-chart-outline"></i>
                        <span>Neraca</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.hpp') }}" class="{{ request()->is('report/hpp*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-file-chart-outline"></i>
                        <span>HPP</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="mdi mdi-file-chart-outline"></i>
                        <span>Laba Rugi</span>
                    </a>
                </li>
            @endcan --}}

            {{-- <li>
                <a href="{{ route('transdatakas.index') }}">
                    <i class="mdi mdi-script-text"></i>
                    <span>Kas Transaction</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transdatabank.index') }}">
                    <i class="mdi mdi-script-text"></i>
                    <span>Bank Transaction</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="mdi mdi-script-text"></i>
                    <span>Sale Transaction</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="mdi mdi-script-text"></i>
                    <span>Purchase Transaction</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="mdi mdi-script-text"></i>
                    <span>Expor Transaction</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="mdi mdi-script-text"></i>
                    <span>Impor Transaction</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="mdi mdi-script-text"></i>
                    <span>Nr Dr Transaction</span>
                </a>
            </li> --}}

            @can('Akunting_master_data') 
                <li class="menu-title" data-key="t-menu">Report</li>
                <li>
                    <a href="{{ route('report.monthly.index') }}" class="{{ request()->is('report*') ? 'bg-light text-primary active' : '' }}">
                        <i class="mdi mdi-finance"></i>
                        <span>Monthly Summary</span>
                    </a>
                </li>
            @endcan

        </ul>
    </div>
    <!-- Sidebar -->
</div>