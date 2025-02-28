<div class="modal fade" id="ftpModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" class="submitForm" action="{{ route('admin.mappings.csv.ftp') }}">
                    @csrf
                    <input type="hidden" class="csv_id" name="csv_id" >
                    <div class="mb-3">
                        <label  class="form-label">Username</label>
                        <input type="text" class="form-control"  name="username" placeholder="noloco">
                        {{-- <div class="input-group">
                            <input type="text" class="form-control"  name="username" placeholder="noloco">
                            <span class="input-group-text">-</span>
                            <select class="form-select" name="domain">
                                <option selected>gostarauto.com</option>
                            </select>
                        </div> --}}
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Your password should be secure"></i>
                        </label>
                        <div class="">
                            <input type="text" class="form-control" id="password" name="password" required placeholder="********">
                            {{-- <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-eye"></i>
                            </button> --}}
                        </div>
                    </div>

                    <!-- Password Again -->
                    {{-- <div class="mb-3">
                        <label for="passwordAgain" class="form-label">Password (Again)</label>
                        <input type="password" required class="form-control" id="passwordAgain" name="password_confirmation" placeholder="********">
                    </div> --}}

                    <!-- Quota -->
                    {{-- <div class="mb-3">
                        <label class="form-label">Quota</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quota" id="quotaUnlimited" value="unlimited" checked>
                                <label class="form-check-label" for="quotaUnlimited">Unlimited</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quota" id="quotaLimited" value="limited">
                                <input type="text" class="form-control d-inline-block w-25" placeholder="1000" disabled>
                                <label class="form-check-label ms-1" for="quotaLimited">MB</label>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Directory -->
                    {{-- <div class="mb-3">
                        <label for="login" class="form-label">Directory</label>
                        <div class="input-group">
                            <span class="input-group-text">/home/gostarauto/csvfiles</span>
                            <input type="text" required class="form-control" name="directory" id="directory" value="">
                        </div>
                    </div> --}}

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
