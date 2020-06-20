@extends(config('dotenveditor.template', 'dotenv-editor::master'))

{{--
Feel free to extend your custom wrapping view.
All needed files are included within this file, so nothing could break if you extend your own master view.
--}}

@section('content')
<style>
  .fade:not(.show) {
    opacity: 0.9 !important
  }

  .modal.fade .modal-dialog {
    transition: transform .3s ease-out;
    transform: none;
  }
</style>
<div id="app">
  <div class="container">
    <div class="card p-20 mt-30">
      <h1><a href="{{ url(config('dotenveditor.route.prefix')) }}">{{ trans('dotenv-editor::views.title') }}</a></h1>

      <div class="row">
        <div class="col-md-12">
          <ul class="nav nav-tabs">
            <li v-for="view in views" role="presentation" class="nav-item">
              <a href="javascript:;" @click="setActiveView(view.name)"
                class="nav-link @{{ view.active ? 'active' : '' }}">@{{ view.name }}</a>
            </li>
          </ul>
        </div>
      </div>

      <br><br>

      <div class="row">

        <div class="col-md-12 col-sm-12">

          {{-- Error-Container --}}
          <div>
            {{-- VueJS-Errors --}}
            <div class="alert alert-success" role="alert" v-show="alertsuccess">
              <button type="button" class="close" @click="closeAlert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              @{{ alertmessage }}
            </div>
            {{-- Errors from POST-Requests --}}
            @if(session('dotenv'))
            <div class="alert alert-success alert-dismissable" role="alert">
              <button type="button" class="close" aria-label="Close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              {{ session('dotenv') }}
            </div>
            @endif
          </div>

          {{-- Overview --}}
          <div v-show="views[0].active">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h2 class="panel-title">
                  {{ trans('dotenv-editor::views.overview_title') }}
                </h2>
              </div>
              <div class="panel-body">
                <p>
                  {!! trans('dotenv-editor::views.overview_text') !!}
                </p>
                <p>
                  <a href="javascript:;" v-show="loadButton" class="btn btn-primary" @click="loadEnv">
                    {{ trans('dotenv-editor::views.overview_button') }}
                  </a>
                </p>
              </div>
              <div class="table-responsive" v-show="!loadButton">
                <table class="table table-striped">
                  <tr>
                    <th>{{ trans('dotenv-editor::views.overview_table_key') }}</th>
                    <th>{{ trans('dotenv-editor::views.overview_table_value') }}</th>
                    <th>{{ trans('dotenv-editor::views.overview_table_options') }}</th>
                  </tr>
                  <tr v-for="entry in entries">
                    <td>@{{ entry.key }}</td>
                    <td>@{{ entry.value }}</td>
                    <td>
                      <a class="btn btn-primary" href="javascript:;" @click="editEntry(entry)"
                        title="{{ trans('dotenv-editor::views.overview_table_popover_edit') }}">
                        <span aria-hidden="true"><svg class="bi bi-pencil-square" width="1em" height="1em"
                            viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                              d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                          </svg>
                        </span>
                      </a>
                      <a class="btn btn-danger" href="javascript:;" @click="modal(entry)"
                        title="{{ trans('dotenv-editor::views.overview_table_popover_delete') }}">
                        <span aria-hidden="true"><svg class="bi bi-trash-fill" width="1em" height="1em"
                            viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                              d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z" />
                          </svg></span>
                      </a>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            {{-- Modal delete --}}
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLiveLabel">@{{ deleteModal.title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p>{!! trans('dotenv-editor::views.overview_delete_modal_text') !!}</p>
                    <p class="text text-warning">
                      <strong>@{{ deleteModal.content }}</strong>
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                      {!! trans('dotenv-editor::views.overview_delete_modal_no') !!}
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteEntry">
                      {!! trans('dotenv-editor::views.overview_delete_modal_yes') !!}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            {{-- Modal edit --}}
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">

                    <h5 class="modal-title" id="exampleModalLiveLabel">{!!
                      trans('dotenv-editor::views.overview_edit_modal_title') !!}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <strong>{!! trans('dotenv-editor::views.overview_edit_modal_key') !!}:</strong>
                    @{{ toEdit.key }}<br><br>
                    <div class="form-group">
                      <label for="editvalue">{!! trans('dotenv-editor::views.overview_edit_modal_value') !!}</label>
                      <input type="text" v-model="toEdit.value" id="editvalue" class="form-control">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                      {!! trans('dotenv-editor::views.overview_edit_modal_quit') !!}
                    </button>
                    <button type="button" class="btn btn-primary" @click="updateEntry">
                      {!! trans('dotenv-editor::views.overview_edit_modal_save') !!}
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          {{-- Add new --}}
          <div v-show="views[1].active">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h2 class="panel-title">{!! trans('dotenv-editor::views.addnew_title') !!}</h2>
              </div>
              <div class="panel-body">
                <p>
                  {!! trans('dotenv-editor::views.addnew_text') !!}
                </p>

                <form @submit.prevent="addNew()">
                  <div class="form-group">
                    <label for="newkey">{!! trans('dotenv-editor::views.addnew_label_key') !!}</label>
                    <input type="text" name="newkey" id="newkey" v-model="newEntry.key" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="newvalue">{!! trans('dotenv-editor::views.addnew_label_value') !!}</label>
                    <input type="text" name="newvalue" id="newvalue" v-model="newEntry.value" class="form-control">
                  </div>
                  <button class="btn btn-default" type="submit">
                    {!! trans('dotenv-editor::views.addnew_button_add') !!}
                  </button>
                </form>
              </div>
            </div>
          </div>

          {{-- Backups --}}
          <div v-show="views[2].active">
            {{-- Create Backup --}}
            <div class="panel panel-default">
              <div class="panel-heading">
                <h2 class="panel-title">{!! trans('dotenv-editor::views.backup_title_one') !!}</h2>
              </div>
              <div class="panel-body">
                <a href="{{ url(config('dotenveditor.route.prefix') . "/createbackup") }}" class="btn btn-primary">
                  {!! trans('dotenv-editor::views.backup_create') !!}
                </a>
                <a href="{{ url(config('dotenveditor.route.prefix') . "/download") }}" class="btn btn-primary">
                  {!! trans('dotenv-editor::views.backup_download') !!}
                </a>
              </div>
            </div>

            {{-- List of available Backups --}}
            <div class="panel panel-default">
              <div class="panel-heading">
                <h2 class="panel-title">{!! trans('dotenv-editor::views.backup_title_two') !!}</h2>
              </div>
              <div class="panel-body">
                <p>
                  {!! trans('dotenv-editor::views.backup_restore_text') !!}
                </p>
                <p class="text-danger">
                  {!! trans('dotenv-editor::views.backup_restore_warning') !!}
                </p>
                @if(!$backups)
                <p class="text text-info">
                  {!! trans('dotenv-editor::views.backup_no_backups') !!}
                </p>
                @endif
              </div>
              @if($backups)
              <div class="table-responsive">
                <table class="table table-striped">
                  <tr>
                    <th>{!! trans('dotenv-editor::views.backup_table_nr') !!}</th>
                    <th>{!! trans('dotenv-editor::views.backup_table_date') !!}</th>
                    <th>{!! trans('dotenv-editor::views.backup_table_options') !!}</th>
                  </tr>
                  <?php $c = 1;?>
                  @foreach($backups as $backup)

                  <tr>
                    <td>{{ $c++ }}</td>
                    <td>{{ $backup['formatted'] }}</td>
                    <td>
                      <a class="btn btn-primary" href="javascript:;"
                        @click="showBackupDetails('{{ $backup['unformatted'] }}', '{{ $backup['formatted'] }}')"
                        title="{!! trans('dotenv-editor::views.backup_table_options_show') !!}">
                        <svg class="bi bi-search"
                          title="{!! trans('dotenv-editor::views.backup_table_options_show') !!}" width="1em"
                          height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                            d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z" />
                          <path fill-rule="evenodd"
                            d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                        </svg>
                      </a>
                      <a class="btn btn-warning" href="javascript:;"
                        @click="restoreBackup({{ $backup['unformatted'] }})"
                        title="{!! trans('dotenv-editor::views.backup_table_options_restore') !!}">
                        <svg title="{!! trans('dotenv-editor::views.backup_table_options_restore') !!}"
                          class="bi bi-arrow-repeat" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                          xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                            d="M2.854 7.146a.5.5 0 0 0-.708 0l-2 2a.5.5 0 1 0 .708.708L2.5 8.207l1.646 1.647a.5.5 0 0 0 .708-.708l-2-2zm13-1a.5.5 0 0 0-.708 0L13.5 7.793l-1.646-1.647a.5.5 0 0 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0 0-.708z" />
                          <path fill-rule="evenodd"
                            d="M8 3a4.995 4.995 0 0 0-4.192 2.273.5.5 0 0 1-.837-.546A6 6 0 0 1 14 8a.5.5 0 0 1-1.001 0 5 5 0 0 0-5-5zM2.5 7.5A.5.5 0 0 1 3 8a5 5 0 0 0 9.192 2.727.5.5 0 1 1 .837.546A6 6 0 0 1 2 8a.5.5 0 0 1 .501-.5z" />
                        </svg>
                      </a>
                      <a class="btn btn-info"
                        href="{{ url(config('dotenveditor.route.prefix') . "/download/" . $backup['unformatted']) }}"
                        title="{!! trans('dotenv-editor::views.backup_table_options_download') !!}">
                        <svg title="{!! trans('dotenv-editor::views.backup_table_options_download') !!}"
                          class="bi bi-cloud-download" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M4.887 5.2l-.964-.165A2.5 2.5 0 1 0 3.5 10H6v1H3.5a3.5 3.5 0 1 1 .59-6.95 5.002 5.002 0 1 1 9.804 1.98A2.501 2.501 0 0 1 13.5 11H10v-1h3.5a1.5 1.5 0 0 0 .237-2.981L12.7 6.854l.216-1.028a4 4 0 1 0-7.843-1.587l-.185.96z" />
                          <path fill-rule="evenodd"
                            d="M5 12.5a.5.5 0 0 1 .707 0L8 14.793l2.293-2.293a.5.5 0 1 1 .707.707l-2.646 2.646a.5.5 0 0 1-.708 0L5 13.207a.5.5 0 0 1 0-.707z" />
                          <path fill-rule="evenodd" d="M8 6a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0v-8A.5.5 0 0 1 8 6z" />
                        </svg>
                      </a>
                      <a class="btn btn-danger"
                        href="{{ url(config('dotenveditor.route.prefix') . "/deletebackup/" . $backup["unformatted"]) }}"
                        title="{!! trans('dotenv-editor::views.backup_table_options_delete') !!}">
                        <svg title="{!! trans('dotenv-editor::views.backup_table_options_delete') !!}"
                          class="bi bi-trash-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                          xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                            d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z" />
                        </svg>
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </table>
              </div>
              @endif
            </div>

            @if($backups)
            {{-- Details Modal --}}
            <div class="modal fade" id="showDetails" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{!! trans('dotenv-editor::views.backup_modal_title') !!}</h4>
                  </div>
                  <div class="modal-body">
                    <table class="table table-striped">
                      <tr>
                        <th>{!! trans('dotenv-editor::views.backup_modal_key') !!}</th>
                        <th>{!! trans('dotenv-editor::views.backup_modal_value') !!}</th>
                      </tr>
                      <tr v-for="entry in details">
                        <td>@{{ entry.key }}</td>
                        <td>@{{ entry.value }}</td>
                      </tr>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <a href="javascript:;" @click="restoreBackup(currentBackup.timestamp)"
                      title="Stelle dieses Backup wieder her" class="btn btn-primary">
                      {!! trans('dotenv-editor::views.backup_modal_restore') !!}
                    </a>

                    <button type="button" class="btn btn-default" data-dismiss="modal">{!!
                      trans('dotenv-editor::views.backup_modal_close') !!}</button>

                    <a href="{{ url(config('dotenveditor.route.prefix') . "/deletebackup/" . $backup["unformatted"]) }}"
                      class="btn btn-danger">
                      {!! trans('dotenv-editor::views.backup_modal_delete') !!}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif

        </div>

        {{-- Upload --}}
        <div v-show="views[3].active">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">{!! trans('dotenv-editor::views.upload_title') !!}</h2>
            </div>
            <div class="panel-body">
              <p>
                {!! trans('dotenv-editor::views.upload_text') !!}<br>
                <span class="text text-warning">
                  {!! trans('dotenv-editor::views.upload_warning') !!}
                </span>
              </p>
              <form method="post" action="{{ url(config('dotenveditor.route.prefix') . "/upload") }}"
                enctype="multipart/form-data">
                <div class="form-group">
                  <label for="backup">{!! trans('dotenv-editor::views.upload_label') !!}</label>
                  <input type="file" name="backup">
                </div>
                <button type="submit" class="btn btn-primary" title="Ein Backup von deinem Computer hochladen">
                  {!! trans('dotenv-editor::views.upload_button') !!}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.js"></script>
<script>
  new Vue({
    el: '#app',
    data: {
      loadButton: true,
      alertsuccess: 0,
      alertmessage: '',
      views: [
      {name: "{{ trans('dotenv-editor::views.overview') }}", active: 1},
      {name: "{{ trans('dotenv-editor::views.addnew') }}", active: 0},
      {name: "{{ trans('dotenv-editor::views.backups') }}", active: 0},
      {name: "{{ trans('dotenv-editor::views.upload') }}", active: 0}
      ],
      newEntry: {
        key: "",
        value: ""
      },
      details: {},
      currentBackup: {
        timestamp: ''
      },
      toEdit: {},
      toDelete: {},
      deleteModal: {
        title: '',
        content: ''
      },
      token: "{!! csrf_token() !!}",
      entries: [

      ]
    },
    methods: {
      loadEnv: function(){
        var vm = this;
        this.loadButton = false;
        $.getJSON("/{{ $url }}/getdetails", function(items){
          vm.entries = items;
        });
      },
      setActiveView: function(viewName){
        $.each(this.views, function(key, value){
          if(value.name == viewName){
            value.active = 1;
          } else {
            value.active = 0;
          }
        })
      },
      addNew: function(){
        var vm = this;
        var newkey = this.newEntry.key;
        var newvalue = this.newEntry.value;
        $.ajax({
          url: "/{{ $url }}/add",
          type: "post",
          data: {
            _token: this.token,
            key: newkey,
            value: newvalue
          },
          success: function(){
            vm.entries.push({
              key: newkey,
              value: newvalue
            });
            var msg = "{{ trans('dotenv-editor::views.new_entry_added') }}";
            vm.showAlert("success", msg);
            vm.alertsuccess = 1;
            $("#newkey").val("");
            vm.newEntry.key = "";
            vm.newEntry.value = "";
            $("#newvalue").val("");
            $('#newkey').focus();
          }
        })
      },
      editEntry: function(entry){
        this.toEdit = {};
        this.toEdit = entry;
        $('#editModal').modal('show');
      },
      updateEntry: function(){
        var vm = this;
        $.ajax({
          url: "/{{ $url }}/update",
          type: "post",
          data: {
            _token: this.token,
            key: vm.toEdit.key,
            value: vm.toEdit.value
          },
          success: function(){
            var msg = "{{ trans('dotenv-editor::views.entry_edited') }}";
            vm.showAlert("success", msg);
            $('#editModal').modal('hide');
          }
        })
      },
      makeBackup: function(){
        var vm = this;
        $.ajax({
          url: "/{{ $url }}/createbackup",
          type: "get",
          success: function(){
            vm.showAlert('success', "{{ trans('dotenv-editor::views.backup_created') }}");
          }
        })
      },
      showBackupDetails: function(timestamp, formattedtimestamp){
        this.currentBackup.timestamp = timestamp;
        var vm = this;
        $.getJSON("/{{ $url }}/getdetails/" + timestamp, function(items){
          vm.details = items;
          $('#showDetails').modal('show');
        });
      },
      restoreBackup: function(timestamp){
        var vm = this;
        $.ajax({
          url: "/{{ $url }}/restore/" + timestamp,
          type: "get",
          success: function(){
            vm.loadEnv();
            $('#showDetails').modal('hide');
            vm.setActiveView('overview');
            vm.showAlert('success', '{{ trans('dotenv-editor::views.backup_restored') }}');
          }
        })
      },
      deleteEntry: function(){
        var entry = this.toDelete;
        var vm = this;

        $.ajax({
          url: "/{{ $url }}/delete",
          type: "post",
          data: {
            _token: this.token,
            key: entry.key
          },
          success: function(){
            var msg = "{{ trans('dotenv-editor::views.entry_deleted') }}";
            vm.showAlert("success", msg);
          }
        });
        this.entries.$remove(entry);
        this.toDelete = {};
        $('#deleteModal').modal('hide');
      },
      showAlert: function(type, message){
        this.alertmessage = message;
        this.alertsuccess = 1;
      },
      closeAlert: function(){
        this.alertsuccess = 0;
      },
      modal: function(entry){
        this.toDelete = entry;
        this.deleteModal.title = "{{ trans('dotenv-editor::views.delete_entry') }}";
        this.deleteModal.content = entry.key + "=" + entry.value;
        $('#deleteModal').modal('show');
      }
    }
  })
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
  integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script>
  $(document).ready(function(){
    $(function () {
      $('[data-toggle="popover"]').popover()
    });
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  })
</script>

@endsection