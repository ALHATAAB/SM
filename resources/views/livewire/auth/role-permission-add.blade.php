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
                            <div class="ibox-title">{{ __('message.Ajouter_role') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                <a href="{{url('/role/permission')}}"> <button type="button" class="btn btn-secondary" ><i class="feather ti-arrow-left "></i>
                                    {{ __('message.Retour') }}
                                </button>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-body">
                                            @include('livewire.alert.message')
                                            <form wire:submit='addRole'>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label class="floating-label" for="Role"><b>{{ __('message.Role') }}</b></label>
                                                            <input type="text" wire:model='nom' class="mb-2 form-control mr-sm-2 mb-sm-0"  id="nom" >
                                                            <div>
                                                                @error('nom')
                                                                <span class="text-error">{{$message}}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div class="form-group">
                                                            <label style="display:block; margin-bottom:20px" class="floating-label" for="Permission"><b>{{ __('message.Permission') }}</b></label>
                                                            <br>
                                                            @foreach ($getPermission as $value)
                                                            <div class='row' style="margin-bottom:20px;">
                                                                    <div class="col-sm-3">
                                                                        {{ __('message.' . $value['nom']) }}
                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        <div class='row'>
                                                                            @foreach ($value['group'] as $group )
                                                                            <div class="col-sm-2">
                                                                                <label class="ui-checkbox ui-checkbox-primary">
                                                                                    <input type="checkbox" wire:model="permission_id" value="{{ $group['id'] }}" >
                                                                                    <span class="input-span"></span> {{__('message.' . $group['nom'])  }}</label>

                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                            <hr>
                                                            @endforeach

                                                        </div>

                                                    </div>

                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                                </div>
                                            </form>

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







