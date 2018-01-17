$(document).ready(function () {
    $('.icon').click(function () {
      $('#tick').remove();
      $('.choose').removeClass('choose');
      $('<img/>',{
          id: 'tick',
          src: 'images/tick.png'
      }).prependTo(this);
      $(this).addClass('choose');
    });

});
function drawrule(){
  var myCanvas = document.getElementById('canvas');
  var ctx = myCanvas.getContext('2d');
  var img = new Image();

  img.onload = function(){
      ctx.drawImage(img,0,0,myCanvas.width,myCanvas.height); // Or at whatever offset you like
  };
  img.src = 'images/intro.png';
}
