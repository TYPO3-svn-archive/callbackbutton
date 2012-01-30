/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var alt = 0; 
var feUserNr ;
function start(){
	
	feUserNr = $('#-_85').attr('value');
	
	fakeID = document.dummy.fake.value; 
	capt = $('#captchaInput').attr('value');
	$.ajax({
	  type: "GET",
	  url: "index.php?eID=callbackbuttonStart",
	  data: {
		  plus : feUserNr,
		  fkt : 'start',
		  ct: capt
	  },
	  
	  success: function(){
		  $('[name = callStart]').attr('disabled', true);
		  $('[name = callStart]').css('background-color' , '#EEEEEE');
		  $('[name = callStart]').css('cursor', 'default');
		  $('[name = callStop]').attr('disabled', false);
		  $('[name = callStop]').css('background-color' , 'transparent');
		  $('[name = callStop]').css('cursor', 'pointer');
		 getData();
		 //newFreeCap(fakeID, 'Sorry, we cannot autoreload a new image. Submit the form and a new image will be loaded.');return false;
		
		}
  
	});
}

function getData(){

  $.ajax({
  type: "GET",
  url: "index.php?eID=callbackbuttonStart",
  data:{
	fkt : 'run'  
  },
  dataType: "json",
  success: function(data){
	 
	$("#timeLabel").html("...");
	$("#statLabel").html("...");
	
	var timer = setInterval(function(){
		
	  $("#buff").load('index.php?eID=callbackbuttonStart&fkt=run&' + 1*new Date(), function(data){
	 
	  var obj = jQuery.parseJSON(data);
	  //alert ($.type(obj));
	  if($.type(obj) == 'object'){
	   
	  $("#timeLabel").html(obj.conntime);
	  $("#statLabel").html(obj.stat);
	  
	  if(obj.stop){
		  
			
			clearInterval(timer);
			
		  alt = 0;
		  setTimeout("location.reload()", 2000, null);
	  } 
	  }else{
		  clearInterval(timer);
		 // $('#statLabel').html('Captcha falsch');
		  setTimeout("location.reload()", 1000, null);
	  }	
		} );
		 
	  },1000);
	
  }
	
 
  });
 
$(document).ajaxError(function(exception) {
	
			  });

}

function stop(){

	$.ajax({
	  type: "GET",
	  url: "index.php?eID=callbackbuttonStart",
	  data: {
		  //plus : feUserNr,
		  fkt : 'stop'
	  },
	  success: function(data){
		 $('#-_85').attr('value', '');	
		  $('[name = callStop]').attr('disabled', true);
		  $('[name = callStop]').css('background-color' , '#EEEEEE');
		  $('[name = callStop]').css('cursor', 'default');
		  $('[name = callStart]').attr('disabled', false);
		  $('[name = callStart]').css('background-color' , 'transparent'); 
		  $('[name = callStart]').css('cursor', 'pointer'); 
		 setTimeout($('#stat').html(data) , 1000, null);
		 location.reload();
	  }
  });
}
$(document).ready(function(){
	$('[name = callStop]').attr('disabled', true);
	$('[name = callStop]').css('background-color' , '#EEEEEE');
	$('[name = callStop]').css('cursor', 'default');
	$('[name = callStart]').attr('disabled', false);
	$('[name = callStart]').css('cursor', 'pointer');
	$('[name = callStart]').css('background-color' , 'transparent'); 

})


