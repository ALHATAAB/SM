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

                    <div class="ibox ibox-primary">
                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Role') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                <a href="{{url('/role/permission/add')}}"> <button type="button" class="btn btn-ligth" ><i class="fa fa-plus"></i>
                                    
                                </button>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-body">
                            <form class="form-inline">
                                {{-- <label class="sr-only" for="ex-email">{{ __('message.Matricule') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text" placeholder=" {{ __('message.Matricule') }}"> --}}
                                <label class="sr-only" for="ex-email">{{ __('message.Role') }}</label>
                                <input type="search" wire:model.live='recherche' class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                wire:model.live='recherche'    placeholder="{{ __('message.Recherche') }} {{ __('message.Role') }} ">

                            </form>
                            <div class="table-responsive">
                                @include('livewire.alert.message')

                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Role</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                        <tr>
                                            <td>{{$role->nom}}</td>
                                            <td>
                                                <a href="{{url('/role/permission/edit/'.encrypt($role->id))}}"><button type="button" class="btn btn-sm  btn-info"><i class="feather fa fa-edit "></i></button></a>
                                                {{-- <a href="{{url('/role/view/'.encrypt($role->id))}}"><button type="button" class="btn btn-sm btn-secondary"><i class="feather icon-eye"></i></button></a> --}}
                                             </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                 <!-- Liens de pagination -->
                                 <div>
                                    {{ $roles->links() }}
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <!-- BEGIN PAGA BACKDROPS-->
        @include('components.layouts.loading')
        <!-- END PAGA BACKDROPS-->
    </body>
</div>
