<!-- #END# Search Bar -->
<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header" style="padding: 0; margin: 0; height: 70px;">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" style="margin: 5px;padding: 0; height: 60px;" href="{{ route('admin.home') }}"><img src="{{ asset('/adm/img/logo-01.png') }}" /></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="pull-right">
                    <a href="{{ route('admin.logout') }}" class="dropdown-toggle" title="Salir">
                        <i class="material-icons">input</i>
                    </a>
                </li>
                <li class="pull-right">
                    <a href="{{ route('admin.home') }}" class="dropdown-toggle" title="Mis datos">
                        <i class="material-icons">person</i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">

            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->email }}</div>
                <div class="email">{{ Auth::user()->email }}</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="<?= url('my-user') ?>"><i class="material-icons">person</i>Mis datos</a></li>                      
                        <li role="seperator" class="divider"></li>
                        <li><a href="<?= url('logout') ?>"><i class="material-icons">input</i>Salir</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            {!! $MainMenu->asUl(['class' => 'list']) !!}
           
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">

            <div class="copyright">
                &copy; 2017 .
            </div>
            <div class="copyright">
                <b>Made with <3 by </b><a href="http://pjramirez.com" target="_blank">@pablor21</a>
            </div>
            <div class="version">
                <b>Version: </b> 
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->

</section>