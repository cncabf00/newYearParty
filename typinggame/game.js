var load;
var tt=0;
var ttt=0;
var p=10.9;
var q=70.1;
function loading(start) {
	if(start==true) {
		var i;
		for(i=0; i<10; i++) {
			$("#c"+i).fadeOut(100);
		}
		setTimeout(function(){$("#loading").fadeIn(100);}, 100);
		$("#caption").text("载入中");
		dot();
	}
	else {
		setTimeout(function(){
			for(i=0; i<10; i++) {
				$("#c"+i).fadeIn(100);
				$("#c"+i).css("color", "black");
			}
		},100);
		$("#loading").fadeOut(100);
		$("#caption").text("快！");
		clearTimeout(load);
	}
}
var d = 0;
function dot() {
	$("#l0").css("color", "black");
	$("#l1").css("color", "black");
	$("#l2").css("color", "black");
	$("#l"+d).css("color", "yellow");
	d++;
	if(d==3)
		d = 0;
	load = setTimeout("dot()", 150);
}

var key = 0;

function end_game() {
	$("#end_time").text($("#time").text());
	$("#end_wrong").text($("#wrong").text());
	var all = parseFloat($("#time").text()) + parseFloat($("#wrong").text())*0.3;
	$("#end_all_time").text( all.toFixed(2) );
	
	$("#caption").fadeOut(500);
	$("#status").fadeOut(500);
	$("#words").fadeOut(500, function() {
		$("#end").fadeIn(500);
	});
	end_page = true;

	$.ajax({

			type: 'POST',
			url: "getstring",
			data: {
					  'round':$("#round").text(),
					  'e':$("#wrong").text(),
					  't':$("#time").text(),
					  'k':ttt,
					  'p':p,
					  'q':q,
				  },
				  success: function(response, textStatus, jqXHR)
					{
						$("#end_rank").text(response[1]);
						if(data[0]==-1) {
							$("#new_record").text("新纪录！")
						}
						else {
							$("#fastest").text("最快");
							$("#new_record").text(response[0] + "秒");
						}
					},
					error:function(jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR['responseText']);
					},
				  dataType: "json"
	});
}

function next_round() {
	start = false;
	ttt=(key-ord("F"))*(key-ord("F"))%(65536+tt);
	q++;
	if(parseInt($("#round").text())==5) {
		end_game();
		return;
	}
	loading(true);
	$.ajax({

			type: 'POST',
			url: "getstring",
			data: {
					  'round':$("#round").text(),
					  'e':$("#wrong").text(),
					  't':$("#time").text(),
					  'k':ttt,
					  'p':p,
					  'q':q,
				  },
				  success: function(response, textStatus, jqXHR)
					{
						tt=ttt;
						for(i=0; i<10; i++) {
							$("#c"+i).text(response[i]);
						}
						
						key = response[10] + ord("F");
						
						setTimeout(function() {
							loading(false);
							next();
						}, 300);
					},
					error:function(jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR['responseText']);
					},
				  dataType: "json"
	});
}

var start=false;
var pos;
function next() {
	$("#round").text(parseInt($("#round").text())+1);
	setTimeout(function(){start = true;}, 150);
	pos = 0;
}

var time=0; //0.01
var tm;
function timer() {
	tm = setTimeout("timer()", 10);
	if(start==true) {
		time = time + 0.01;
	$("#time").text(time.toFixed(2));
	}
}

var end_page = false;
var start_page = true;
$(function(){
	$("#caption").ajaxError(function() {
		$(this).text("网络错误。请刷新页面。:-(");
		$(this).css("color", "red");
		$("#loading").remove();
		clearTimeout(load);
		start = false;
	});
	
	$("#start").click(function() {
		$("#rule").fadeOut(300, function() {
			$("#caption").fadeIn(300);
			$("#words").fadeIn(300);
			$("#loading").fadeIn(300);
			$("#status").fadeIn(300);
			next_round();
		});
		start_page = false;
	});
	
	timer();
	
	$(document).keydown(function(e)
	{
		if(start_page==true && e.which==13) {
			$("#rule").fadeOut(300, function() {
				$("#caption").fadeIn(300);
				$("#words").fadeIn(300);
				$("#loading").fadeIn(300);
				$("#status").fadeIn(300);
				next_round();
			});
			start_page = false;
		}
		
		if(end_page==true && e.which==13) {
			location.reload();
			return;
		}
	
		if(start==false)
			return;
		
		var input = e.which;
		var number = $("#c"+pos).text().charCodeAt();
		if(input==number) {
			$("#c"+pos).css("color", "green");
			$("#caption").text("快！");
			pos = pos + 1;
			tt=(tt<<1)%(tt+1);
		}
		else {
			$("#c"+pos).css("color", "red");
			$("#caption").text("错了？！");
			$("#wrong").text(parseInt($("#wrong").text())+1);
		}

		
		if(pos==10)
			next_round();
	});
});

function ord (string) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   input by: incidence
  // *     example 1: ord('K');
  // *     returns 1: 75
  // *     example 2: ord('\uD800\uDC00'); // surrogate pair to create a single Unicode character
  // *     returns 2: 65536
  var str = string + '',
    code = str.charCodeAt(0);
  if (0xD800 <= code && code <= 0xDBFF) { // High surrogate (could change last hex to 0xDB7F to treat high private surrogates as single characters)
    var hi = code;
    if (str.length === 1) {
      return code; // This is just a high surrogate with no following low surrogate, so we return its value;
      // we could also throw an error as it is not a complete character, but someone may want to know
    }
    var low = str.charCodeAt(1);
    return ((hi - 0xD800) * 0x400) + (low - 0xDC00) + 0x10000;
  }
  if (0xDC00 <= code && code <= 0xDFFF) { // Low surrogate
    return code; // This is just a low surrogate with no preceding high surrogate, so we return its value;
    // we could also throw an error as it is not a complete character, but someone may want to know
  }
  return code;
}