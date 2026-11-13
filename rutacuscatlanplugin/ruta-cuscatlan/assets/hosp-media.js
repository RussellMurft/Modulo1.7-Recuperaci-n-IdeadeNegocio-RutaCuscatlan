jQuery(function($){
  $(document).on('click','.rc-hosp-media-select',function(e){
    e.preventDefault();
    var row=$(this).closest('.rc-hosp-media-row');
    var frame=wp.media({title:'Seleccionar fotografía del hospedaje',button:{text:'Usar esta fotografía'},multiple:false,library:{type:'image'}});
    frame.on('select',function(){
      var a=frame.state().get('selection').first().toJSON();
      row.find('.rc-hosp-media-id').val(a.id);
      row.find('.rc-hosp-media-preview').html($('<img>',{src:a.sizes&&a.sizes.thumbnail?a.sizes.thumbnail.url:a.url,alt:''}));
      row.find('.rc-hosp-media-select').text('Cambiar foto');
      row.find('.rc-hosp-media-remove').prop('hidden',false);
    });
    frame.open();
  });
  $(document).on('click','.rc-hosp-media-remove',function(e){
    e.preventDefault();var row=$(this).closest('.rc-hosp-media-row');
    row.find('.rc-hosp-media-id').val('');
    row.find('.rc-hosp-media-preview').html('<span class=\"rc-hosp-no-image\">Sin foto seleccionada</span>');
    row.find('.rc-hosp-media-select').text('Elegir foto');$(this).prop('hidden',true);
  });
});
