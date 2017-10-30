$(document).ready(function(){
	bgConfig.forEach((item) => {
		createDiv(item.imgPath, item.speed, item.possibleAngle, item.possibleBlur);
	})
});

function createDiv(imgPath, speed, possibleAngle, possibleBlur) {
	var imgLoader = new Image(); // create a new image object
	imgLoader.onload = function() { // assign onload handler
		var height = imgLoader.height;
		var width = imgLoader.width;
		$("#animated-background-wrapper").append("<div></div>");
		var div = $("#animated-background-wrapper > div:last"); // select last div created in wrapper
		div.width(width).height(height);
		div.css({"background-image": "url("+imgPath+")", "background-size": "contain", "background-repeat": "no-repeat", "position": "fixed"});
		div.css({top: makeNewPosition(div)[1], left: makeNewPosition(div)[0]});
		var initialRotation = makeNewRotation(possibleAngle);
		div.css({'transform' : 'rotate('+ initialRotation +'deg)'});
		var initialBlur = makeNewBlur(possibleBlur);
		div.css({'filter': 'blur('+ blur +'px)'});
		animateDiv(div, speed, initialRotation, initialBlur, possibleAngle, possibleBlur);
	}
	imgLoader.src = imgPath; // set the image source
}

function animateDiv(element, speed, initialRotation, initialBlur, possibleAngle, possibleBlur){
	var oldPosition = element.offset();
	var newPosition = makeNewPosition(element);
	var duration = calcDuration([oldPosition.top, oldPosition.left], newPosition, speed);
	var rotation = initialRotation;
	var finalRotation = makeNewRotation(possibleAngle);
	var stepRotation = (finalRotation-rotation)/(duration/jQuery.fx.interval); // angle to change for each step = total rotation to achieve / number of animation steps
	var blur = initialBlur;
	var finalBlur = makeNewBlur(possibleBlur);
	var stepBlur = (finalBlur-blur)/(duration/jQuery.fx.interval);
	element.velocity(
		{	left: newPosition[0], top: newPosition[1] }, // destination point
		{	duration: duration,
			step: function(){
				rotation += stepRotation;
				element.css({'transform' : 'rotate('+ rotation +'deg)'});
				blur += stepBlur;
				element.css({'filter': 'blur('+ blur +'px)'});
			},
			complete: function(){
				animateDiv(element, speed, rotation, blur, possibleAngle, possibleBlur);        
			}
		}
	);
};

function makeNewPosition(element){
	// Get viewport dimensions (remove the dimension of the div)
	var w = $(window).width() - element.width();
	var h = $(window).height() - element.height();
	var nw = Math.floor(Math.random() * w);
	var nh = Math.floor(Math.random() * h);
	return [nw,nh];    
}

function makeNewRotation(possibleAngle){
	angle = Math.floor(Math.random() * (possibleAngle[1]-possibleAngle[0])) + possibleAngle[0];
	return angle;
}

function makeNewBlur(possibleBlur){
	blur = Math.floor(Math.random() * (possibleBlur[1]-possibleBlur[0]) * 100)/100 + possibleBlur[0];
	return blur;
}

function calcDuration(prev, next, speed) {
	var x = Math.abs(prev[0] - next[0]);
	var y = Math.abs(prev[1] - next[1]);
	var greatest = x > y ? x : y;
	var duration = Math.ceil(greatest/speed*100);
	return duration;
}