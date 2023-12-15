$(document).ready(function(){
//queda comentado porque al haber muchos inputs iguales, no sirve. Se añade esto mismo en el js cuando se crean dinámicamente los input type file
$(".adjunto").fileinput({
          language: 'es',
          browseLabel: 'Seleccione archivo',
          showRemove: false,
          showUpload: false,
          previewFileIcon: '<i class="glyphicon glyphicon-eye"></i>',
          previewFileIconClass: 'file-icon-4x'
      });



});