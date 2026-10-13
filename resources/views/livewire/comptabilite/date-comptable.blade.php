<div>
    <body class="fixed-navbar  ">
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

                    <div class="ibox">
                        

                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_date_comptable') }}</div>
                            <div class="ibox-tools">
                                @if (($anneeScolaireActive ?? true) && $mode !== 'AUTOMATIQUE')
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                        {{ __('message.Ajouter') }}
                                    </button>
                                @endif
                            </div>
                        </div>


                        <!-- Modal -->
                        <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit='addDateComptable'>
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            {{ __('message.Ajouter_date_comptable') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                       <div class="form-group">
                                                                    <label>{{ __('message.Date_comptable') }}</label>
                                                                    <input class="form-control" type="date"
                                                                        wire:model='date_comptable'>
                                                                    <div>
                                                                        @error('date_comptable')
                                                                            <span class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="ibox-body">
                            <div class="table-responsive">
                                  @include('livewire.alert.message')

                                <div class="m-b-20">
                                    <label class="font-strong">{{ __('message.Mode_date_comptable') }}</label>
                                    <div class="btn-group d-block">
                                        <button type="button"
                                            class="btn {{ $mode === 'MANUELLE' ? 'btn-primary' : 'btn-outline-primary' }}"
                                            @if ($mode !== 'MANUELLE') wire:click="changerMode('MANUELLE')"
                                                wire:confirm="{{ __('message.Mode_manuelle_confirm') }}" @endif>
                                            <i class="fa fa-hand-pointer-o"></i> {{ __('message.Mode_manuelle') }}
                                        </button>
                                        <button type="button"
                                            class="btn {{ $mode === 'AUTOMATIQUE' ? 'btn-primary' : 'btn-outline-primary' }}"
                                            @if ($mode !== 'AUTOMATIQUE') wire:click="changerMode('AUTOMATIQUE')"
                                                wire:confirm="{{ __('message.Mode_automatique_confirm') }}" @endif>
                                            <i class="fa fa-clock-o"></i> {{ __('message.Mode_automatique') }}
                                        </button>
                                    </div>
                                    <div class="text-muted m-t-5">
                                        {{ $mode === 'AUTOMATIQUE' ? __('message.Mode_automatique_aide') : __('message.Mode_manuelle_aide') }}
                                    </div>
                                </div>


                                  @unless ($anneeScolaireActive ?? true)
                                    <div class="alert alert-warning" role="alert">
                                        {{ __('message.Aucune_annee_active_avertissement') }}
                                    </div>
                                  @endunless

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="50px"></th>
                                            <th>{{ __('message.Date_comptable') }}</th>
                                            {{-- <th>{{ __('message.Statuts') }}</th> --}}
                                            <th>{{ __('message.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($date_comptables as $date_comptable )
                                            <tr>
                                            <td>
                                                <label class="ui-checkbox">
                                                    <input type="checkbox">
                                                    <span class="input-span"></span>
                                                </label>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($date_comptable->date_comptable)->format('d/m/Y') }}
</td>
                                            {{-- <td>
                                                @if($date_comptable->statuts=="OUVERTE")
                                                 <span class="badge badge-success m-r-5 m-b-5">{{ $date_comptable->statuts }}</span>
                                                @else
                                                 <span class="badge badge-danger m-r-5 m-b-5">{{ $date_comptable->statuts }}</span>
                                                @endif
                                            </td> --}}
                                            <td>
                                            @if ($mode === 'AUTOMATIQUE')
                                                <span class="badge {{ $date_comptable->statuts == 'OUVERTE' ? 'badge-success' : 'badge-default' }}">
                                                    {{ $date_comptable->statuts == 'OUVERTE' ? __('message.Date_statut_ouverte') : __('message.Date_statut_fermee') }}
                                                </span>
                                            @elseif ($date_comptable->statuts == 'OUVERTE')
                                               
                                                     <button wire:click.prevent="clotureDate({{ $date_comptable->id }})" wire:confirm="{{ __('message.Cloturer_date_confirm') }}" class="btn btn-success btn-xs m-r-5" data-toggle="tooltip"
                                                    data-original-title="{{ __('message.Fermer_la_date') }}" title="{{ __('message.Fermer_la_date') }}"><i
                                                        class="ti ti-unlock font-14"></i></button>
                                            @else
                                               
                                                    <button wire:click.prevent="ouvertureDate({{ $date_comptable->id }})" wire:confirm="{{ __('message.Ouvrir_date_confirm') }}" class="btn btn-danger btn-xs" data-toggle="tooltip"
                                                    data-original-title="{{ __('message.Ouvrir_la_date') }}" title="{{ __('message.Ouvrir_la_date') }}"><i
                                                        class="ti ti-lock font-14"></i></button>
                                            @endif
                                               
                                                
                                            </td>
                                        </tr>
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                                <!-- Liens de pagination -->
                                <div>
                                    {{ $date_comptables->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PAGE CONTENT-->
                    <!-- END PAGE CONTENT-->
                    @include('components.layouts.footer')
                </div>
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->

    </body>
</div>