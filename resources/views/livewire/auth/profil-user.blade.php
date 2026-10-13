<div>

    <body class="fixed-navbar ">
        <div class="page-wrapper">
            <!-- START HEADER-->
            @include('components.layouts.header')
            <!-- END HEADER-->
            <!-- START SIDEBAR-->
            @include('components.layouts.sidebar')
            <!-- END SIDEBAR-->
            <div class="content-wrapper">
                <!-- START PAGE CONTENT-->
               <div class="page-content fade-in-up">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="ibox color-danger">
                                <div class="ibox-body text-center">
                                    <h2 class="font-strong m-b-10 m-t-10">{{ $user->nom }} {{ $user->prenom }}</h2>
                                    {{-- <div class="m-b-20 text-muted">Web Developer</div>
                                    <div>
                                        <button class="btn btn-info btn-rounded m-b-5"><i class="fa fa-plus"></i>
                                            Follow</button>
                                        <button class="btn btn-default btn-rounded m-b-5">Message</button>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="ibox">
                                <ul class="list-group list-group-bordered">
                                    <strong>
                                        <h4>
                                            <li class="list-group-item">Code user
                                                <span class=" float-right">{{ $user->id }}</span>
                                            </li>
                                        </h4>
                                    </strong>
                                    <li class="list-group-item">Role
                                        <span class=" float-right">{{ $user->role->nom }}</span>
                                    </li>
                                     <li class="list-group-item">Nom
                                        <span class=" float-right">{{ $user->nom }}</span>
                                    </li>
                                     <li class="list-group-item">Prenom
                                        <span class=" float-right">{{ $user->prenom }}</span>
                                    </li>
                                     <li class="list-group-item">Telephone
                                        <span class=" float-right">{{ $user->telephone }}</span>
                                    </li>
                                     <li class="list-group-item">Email
                                        <span class=" float-right">{{ $user->email }}</span>
                                    </li>
                                    
                                </ul>

                            </div>

                        </div>
                        <div class="col-lg-8 col-md-6">
                            <div class="ibox">
                                <div class="ibox-body">
                                    <ul class="nav nav-tabs tabs-line">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#tab-1" data-toggle="tab"><i
                                                    class="ti-settings"></i> Modifier Mot de passe</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " href="#tab-2" data-toggle="tab"><i
                                                    class="ti-bar-chart"></i> Information generale</a>
                                        </li>
                                        
                                        
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active color-red" id="tab-1">
                                            <h4>Change ton mot de passe</h4>
                                            <br>
                                            <form action="javascript:void(0)" wire:submit='changePassword' >
                                                <div class="form-group">
                                                    {{-- <label>Ancien mot de passe</label> --}}
                                                    <input class="form-control" type="password" wire:model="password"
                                                        placeholder="Ancien mot de passe">
                                                    <div>
                                                            @error('password')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        {{-- <label>Nouveau mot de passe</label> --}}
                                                        <input class="form-control" type="password" wire:model="new_password"
                                                            placeholder="Nouveau mot de passe">
                                                            <div>
                                                            @error('new_password')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        {{-- <label>Confirmer nouveau mot de passe</label> --}}
                                                        <input class="form-control" type="password" wire:model="confirm_password"
                                                            placeholder="Confirmer nouveau mot de passe">
                                                        <div>
                                                            @error('confirm_password')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-success" type="submit">Valider</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade show " id="tab-2">
                                            <div class="row">
                                                <div class="col-md-6" style="border-right: 1px solid #eee;">
                                                    <ul class="list-group list-group-full list-group-divider">
                                                        <li class="list-group-item">Nom
                                                            <span class="pull-right color-green">{{ $user->nom }}</span>
                                                        </li>
                                                        <li class="list-group-item">Prenom
                                                            <span class="pull-right color-green">{{ $user->prenom }}</span>
                                                        </li>
                                                        <li class="list-group-item">Telephone
                                                            <span class="pull-right color-green">{{ $user->telephone }}</span>
                                                        </li>
                                                    </ul>
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                     <ul class="list-group list-group-full list-group-divider">
                                                        
                                                        <li class="list-group-item">E mail
                                                            <span class="pull-right color-red">{{ $user->email }}</span>
                                                        </li>
                                                        <li class="list-group-item">Adresse
                                                            <span class="pull-right color-red">{{ $user->adresse }}</span>
                                                        </li>
                                                        <li class="list-group-item">Role
                                                            <span class="pull-right color-red">{{ $user->role->nom }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .profile-social a {
                            font-size: 16px;
                            margin: 0 10px;
                            color: #999;
                        }

                        .profile-social a:hover {
                            color: #485b6f;
                        }

                        .profile-stat-count {
                            font-size: 22px
                        }
                    </style>

                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <!-- BEGIN PAGA BACKDROPS-->
        {{-- @include('components.layouts.loading') --}}
        <!-- END PAGA BACKDROPS-->
    </body>
</div>
