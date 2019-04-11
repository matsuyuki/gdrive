@include('section.header')
<div class="container">
  <div class="row">
    <div class="col-md-12" style="margin-top:20px">
      <h1>Form Input Folder Mega XXI</h1>
      <form id="folder" method="post" action="">
        <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email">
        </div>
        <div class="form-group">
          <label for="folder_id">Folder ID</label>
          <input type="text" class="form-control" id="folder_id" name="folder_id" required placeholder="Folder ID">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
      </form>
      <script>
        $("#folder").submit(function(event) {
          event.preventDefault();
          var folder =  $('#folder_id').val();
          var email = $('#email').val();
          if(email ==''){
            alert('Email tidak boleh kosong');
            return false;
          }
          if(folder ==''){
            alert('Folder ID tidak boleh kosong');
            return false;
          }
          $('body').toggleClass('loading');
          $.ajax({
            url:"{{ route('save_folder')}}", //the page containing php script
            type: "post", //request type,
            data : {'folder':folder,'email': email,'_token': $('meta[name="csrf-token"]').attr('content'),},
            dataType: 'text',
            success:function(result){
              if(result == 'sukses'){
                alert('Data berhasil disimpan');
                $('#folder_id').val('');
                $('#email').val('');
              }else if(result == 'gagal'){
                alert('Data gagal disimpan');
              }else{
                alert('Email / Folder ID salah');
              }
              $('body').toggleClass('loading');
            }
          });
        });
      </script>
    </div>
  </div>
</div>
@include('section.footer')
