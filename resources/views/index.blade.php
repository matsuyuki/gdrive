@include('section.header')
<div class="container" style="margin-top:20px">
  <div class="row">
    <div class="col-md-12 text-center">
      <h1>MEGA XXI</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <div class="form-group">
        <label>Sync Folder GDrive</label><br />
        <button style="padding:10px 50px" class="btn btn-primary" type="button" onclick="sync()">Sync</button>
      </div>
    </div>
    <div class="col-md-5">
      <?php /*
       <form method="get" action="{{ url('api/gdrive/list') }}">
       */?>
        <div class="form-group">
          <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
          <label for="">Json</label>
          <select style="width:100%" id="folder_id" class="folder" name="folder_id">
            <?php foreach($sub as $k => $v){  ?>
              <option value="<?=$v->folder_id?>"><?=$v->name?></option>
            <?php } ?>
          </select>
        </div>
        <button  onclick="create_json()" class="btn btn-primary">Create Json</button>
    </div>
    <div class="col-md-5">
      <div class="form-group">
        <label for="">Curl</label>
        <select style="width:100%" id="folder_epi" class="folder" name="folder_id">
          <?php foreach($sub as $k => $v){  ?>
            <option value="<?=$v->folder_id?>"><?=$v->name?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="">Episode</label>
        <input type="number" id="episode" required>
        <button id="btn_curl"  onclick="curl_api()" class="btn btn-primary">Run Curl</button>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group" id="json" style="display:none">
        <label>Json result:</label><br />
        <textarea class="form-control" id="json_result" rows="10"></textarea>
      </div>
    </div>
  </div>
  <div class="modal"></div>
</div>
<script>
  $(document).ready(function() {
    $('.folder').select2();
  });
  function sync () {
    $('body').toggleClass('loading');
      $.ajax({
        url:"{{ url('sync')}}", //the page containing php script
        type: "get", //request type,
        dataType: 'text',
        success:function(result){
          $('body').toggleClass('loading');
          console.log('sync done');
          $('#folder_id').find('option').remove().end();
          var sub = JSON.parse(result);
          var i;
          for (i = 0; i < sub.length; ++i) {
              $( "#folder_id" ).append( "<option value='"+sub[i].folder_id+"'>"+sub[i].name+"</option>" );
          }
       }
     });
   }
   function create_json(e){
    var folder_id =  $('#folder_id').val();
    $.ajax({
      url:"{{ route('list_file')}}", //the page containing php script
      type: "post", //request type,
      data : {'folder': folder_id,'_token': $('meta[name="csrf-token"]').attr('content'),},
      dataType: 'text',
      async: false,
      success:function(result){
        console.log('json done');
        $('#json').show();
        var sub = JSON.parse(result);
       // using JSON.stringify pretty print capability:
       //var str = JSON.stringify(result, undefined, 4);
       document.getElementById('json_result').innerHTML='';
       document.getElementById('json_result').innerHTML=result;
     }
   });
  }

    function curl_api(){
      var folder_epi =  $('#folder_epi').val();
      var episode = $('#episode').val();
      if(episode ==''){
        alert('Jumlah episode harus diisi');
        return false;
      }
      $('body').toggleClass('loading');
      $.ajax({
        url:"{{ route('curl_api')}}", //the page containing php script
        type: "post", //request type,
        data : {'episode':episode,'folder': folder_epi,'_token': $('meta[name="csrf-token"]').attr('content'),},
        dataType: 'text',
        success:function(result){
        console.log(result);
          $('body').toggleClass('loading');
        }
      });
    }
</script>
@include('section.footer')
