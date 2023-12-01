<div class="modal" id="modal-company-verify" tabindex="-1" role="dialog">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
<div class="modal-content">
<form action="/company/verify" enctype="multipart/form-data" method="post">
  <div class="modal-header">
	<h5 class="modal-title">Company Verification</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>
  </div>
  <div class="modal-body">
	  <div class="form-group">
		  <label>Contact Email</label>
		<input class="form-control" name="contact-email" inputmode="email" type="email" value="">
		<p>Email to Send this Verification To</p>
      </div>
  </div>
  <div class="modal-footer">
	<input name="company-id" type="hidden" value="<?= __h($Company['id']) ?>">
	<button type="submit" class="btn btn-outline-primary" name="a" value="company-verify-link-create"><i class="fas fa-link"></i> Get Link</button>
  </div>
</form>
</div>
</div>
</div>
