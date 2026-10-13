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
                        <div class="ibox-head">
                            <div class="ibox-title">{{ __('message.Recherche') }}</div>
                        </div>
                        <div class="ibox-body">
                            <form class="form-inline">
                                {{-- <label class="sr-only" for="ex-email">{{ __('message.Matricule') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text" placeholder=" {{ __('message.Matricule') }}"> --}}
                                <label class="sr-only" for="ex-email">{{ __('message.Nom') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                    placeholder="{{ __('message.Nom') }} ">
                                <label class="sr-only" for="ex-pass">{{ __('message.Prenom') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass" type="text"
                                    placeholder="{{ __('message.Prenom') }} ">
                                <label class="sr-only" for="ex-pass">{{ __('message.Telephone') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass" type="text"
                                    placeholder="{{ __('message.Telephone') }}">

                            </form>
                        </div>

                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_agence') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                    {{ __('message.Ajouter') }}
                                </button>

                            </div>
                        </div>


                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            {{ __('message.Ajouter_agence') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        ...
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                        <button type="button"
                                            class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="50px"></th>
                                            <th>{{ __('message.Prenom') }}</th>
                                            <th>{{ __('message.Nom') }}</th>
                                            <th>{{ __('message.Telephone') }}</th>
                                            <th>{{ __('message.Email') }}</th>

                                            <th>{{ __('message.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="ui-checkbox">
                                                    <input type="checkbox">
                                                    <span class="input-span"></span>
                                                </label>
                                            </td>
                                            <td>iphone case</td>
                                            <td>$1200</td>
                                            <td>33%</td>
                                            <td>02/08/2017</td>
                                            <td>
                                                <button class="btn btn-default btn-xs m-r-5" data-toggle="tooltip"
                                                    data-original-title="Edit"><i
                                                        class="fa fa-pencil font-14"></i></button>
                                                <button class="btn btn-default btn-xs" data-toggle="tooltip"
                                                    data-original-title="Delete"><i
                                                        class="fa fa-trash font-14"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="ui-checkbox">
                                                    <input type="checkbox">
                                                    <span class="input-span"></span>
                                                </label>
                                            </td>
                                            <td>Car covers</td>
                                            <td>$3280</td>
                                            <td>42%</td>
                                            <td>08/10/2017</td>
                                            <td>
                                                <button class="btn btn-default btn-xs m-r-5" data-toggle="tooltip"
                                                    data-original-title="Edit"><i
                                                        class="fa fa-pencil font-14"></i></button>
                                                <button class="btn btn-default btn-xs" data-toggle="tooltip"
                                                    data-original-title="Delete"><i
                                                        class="fa fa-trash font-14"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="ui-checkbox">
                                                    <input type="checkbox">
                                                    <span class="input-span"></span>
                                                </label>
                                            </td>
                                            <td>Compressors</td>
                                            <td>$7400</td>
                                            <td>56%</td>
                                            <td>14/11/2017</td>
                                            <td>
                                                <button class="btn btn-default btn-xs m-r-5" data-toggle="tooltip"
                                                    data-original-title="Edit"><i
                                                        class="fa fa-pencil font-14"></i></button>
                                                <button class="btn btn-default btn-xs" data-toggle="tooltip"
                                                    data-original-title="Delete"><i
                                                        class="fa fa-trash font-14"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
