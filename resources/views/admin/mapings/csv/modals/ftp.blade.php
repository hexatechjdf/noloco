

<div class="modal fade" id="ftpModal1" tabindex="-1" aria-labelledby="ftpModal1Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ftpModal1Label">Manage FTP Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 <ul class="nav nav-tabs" id="ftpTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                            Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
                            Manage Account
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade" id="settings" role="tabpanel">
                        <div id="ftpSettings">
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Port:</strong> <span id="ftpUsername">{{@$setting['ftp_port']}}</span></li>
                                <li class="list-group-item"><strong>IP:</strong> <span id="ftpIP">{{@$setting['ftp_ip']}}</span></li>
                                {{-- <li class="list-group-item"><strong>Server:</strong> <span id="ftpServer">{{@$setting['ftp_server']}}</span></li> --}}
                            </ul>
                        </div>
                    </div>

                    <!-- Create Account Tab -->
                    <div class="tab-pane fade show active" id="create" role="tabpanel">
                        <form method="POST" class="submitForm" action="{{ route('admin.mappings.csv.ftp') }}">
                            @csrf
                            <input type="hidden" class="csv_id" name="csv_id">
                            <input type="hidden" class="id" name="id">

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control username" name="username" placeholder="noloco">
                            </div>

                            <div class="mb-3 password_area">
                                <label for="password" class="form-label">Password</label>
                                <input type="text" class="form-control" id="password" name="password"  placeholder="********">
                            </div>
                            <div class="mb-3">
                                <label  class="form-label">Location Id</label>
                                <input type="text" class="form-control location"  name="location_id" required placeholder="Location Id">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Set Account</button>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

