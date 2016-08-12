// Global $
jQuery(document).ready(function($) {

  var clicked = 0
  //Instantiate Fullcalendar
  $('#calendar').fullCalendar({
    selectable: true,
    selectHelper: true,
    selectOverlap: false,
    header: { center: 'month,agendaDay' },
    dayClick: function(date, jsEvent, view, resourceObj) {
       $('#calendar').fullCalendar( 'gotoDate', date );
       $('#calendar').fullCalendar( 'changeView', 'agendaDay' );
       jsEvent.preventDefault();
       clicked=1;     
    },
    select: function (start, end) {
      if (clicked===0) {
        $('.trailercalendar-modal-background').css("height", $('body').height());
        var modaltop = $(document).scrollTop() + ($(window).height()/2)-($('.trailercalendar-modal').height()/2)
        console.log(modaltop);
        $('.trailercalendar-modal').css("top", modaltop);
        $('.trailercalendar-modal-background').show();
        $('#starthidden').val(start);
        $('#sluthidden').val(end);
        setalltimesanddates();
      } else {
        clicked=0;
      }
    },
    events: bookingURL.pluginurl
  });
  
  $('#annuller').click(function() {
    $('.trailercalendar-modal-background').hide();
  });
  
  $('#submit').click(function() {
    startdato = $('#startaar').val() + '-' + $('#startmaaned').val() + '-' + $('#startdato').val() + ' ' + $('#starttime').val() + ':' + $('#startminut').val() + ':00';
    slutdato = $('#slutaar').val() + '-' + $('#slutmaaned').val() + '-' + $('#slutdato').val() + ' ' + $('#sluttime').val() + ':' + $('#slutminut').val() + ':00';
    $('#starthidden').val(startdato);
    $('#sluthidden').val(slutdato);
    $('.trailercalendar-modal-background').hide();
  });
  
  
  
  function setalltimesanddates() {
    var ft = findtid($('#starthidden').val());  
    $('#startdato').val(ft.d);
    $('#startaar').val(ft.a);
    $('#startmaaned').val(ft.m+1)
    $('#starttime').val(ft.t)
    $('#startminut').val(ft.mi)

    ft = findtid($('#sluthidden').val());  
    $('#slutdato').val(ft.d);
    $('#slutaar').val(ft.a);
    $('#slutmaaned').val(ft.m+1)
    $('#sluttime').val(ft.t)
    $('#slutminut').val(ft.mi)
  }
});

function findtid(tidspunkt) {
  var maanedforkortelse = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  var maaned = tidspunkt.slice(4, 7);
  var dato = tidspunkt.slice(8, 10);
  var aar = tidspunkt.slice(11, 15);
  var time = tidspunkt.slice(16, 18);
  var minut = tidspunkt.slice(19, 21);
  var t = 0;
  while (maaned !== maanedforkortelse[t]) {
    t++;
  }

  return {
    "d": dato,
    "m": t,
    "a": aar,
    "t": time,
    "mi": minut
  }
}