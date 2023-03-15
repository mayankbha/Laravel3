var mainHeight = document.getElementsByTagName('main')[0].offsetHeight;
var videoHolder = document.getElementsByClassName('related_cards')[0];
var loadBtn = document.getElementsByClassName('load_more')[0];
var loadNum = 12;
var startNum = 12;
var currentNum = 0;
var autoCounter = 0;
var previousTop = 0;
var screenHeight = document.documentElement.clientHeight;

function startVideos(event){
  for (var x= 0; x < startNum; x++) {
  	var card = document.createElement('li');
	card.className = "card";
	card.innerHTML = '<a class="card_image" href="#">'+
							'<img src="assets/video-small-1.png">'+
							'<ul class="card_play">'+
								'<li class="play_circle one"></li>'+
								'<li class="play_circle two"></li>'+
								'<li class="play_triangle"></li>'+
							'</ul>'+
							/* 360 tag bellow*/
							'<label class="vr_type two">360</label>'+
						'</a>'+
						'<ul class="card_properties">'+
							'<li class="card_name">'+
								'<a class="name_icon" href="#">'+
									'<img src="assets/icon-1.png">'+
								'</a>'+
								'<a class="name_context video-title-nowrap" href="#">User 2582</a>'+
							'</li>'+
							'<a class="card_type" href="#">League of Legends</a>'+
							'<li class="card_date">3 Nov 2016 at 5:52PM</li>'+
						'</ul>'+
						'<div class="card_stats">'+
							'<div class="stats_views">100.5K views</div>'+
							'<div class="stats_shares">'+
								'<a class="stats_shares_btn" href="#tooltip-1">'+
									'<div class="stats_shares_icon">'+
										'<img class="share_image" src="assets/share.png">'+
										'<img class="shared_image" src="assets/shared.png">'+
									'</div>'+
									'<div class="stats_shares_context">Share</div>'+
								'</a>'+
								'<div class="stats_shares_number">( 1.5K )</div>'+
								'<div class="tooltip" id="tooltip-1">'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Twitter</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-twitter.png">'+
										'</div>'+
									'</a>'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Facebook</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-facebook.png">'+
										'</div>'+
									'</a>'+
								'</div>'+
							'</div>'+
						'</div>';

	videoHolder.appendChild(card);
	currentNum++;
  }
  	mainHeight = document.getElementsByTagName('main')[0].offsetHeight;
  	if (mainHeight < screenHeight)  {
  		for (var x= 0; x < loadNum; x++) {
		  	var card = document.createElement('li');
			card.className = "card";
			card.innerHTML = '<a class="card_image" href="#">'+
							'<img src="assets/video-small-1.png">'+
							'<ul class="card_play">'+
								'<li class="play_circle one"></li>'+
								'<li class="play_circle two"></li>'+
								'<li class="play_triangle"></li>'+
							'</ul>'+
							/* 360 tag bellow*/
							'<label class="vr_type two">360</label>'+
						'</a>'+
						'<ul class="card_properties">'+
							'<li class="card_name video-title-nowrap">'+
								'<a class="name_icon" href="#">'+
									'<img src="assets/icon-1.png">'+
								'</a>'+
								'<a class="name_context" href="#">User 2582</a>'+
							'</li>'+
							'<a class="card_type" href="#">League of Legends</a>'+
							'<li class="card_date">3 Nov 2016 at 5:52PM</li>'+
						'</ul>'+
						'<div class="card_stats">'+
							'<div class="stats_views">100.5K views</div>'+
							'<div class="stats_shares">'+
								'<a class="stats_shares_btn" href="#tooltip-1">'+
									'<div class="stats_shares_icon">'+
										'<img class="share_image" src="assets/share.png">'+
										'<img class="shared_image" src="assets/shared.png">'+
									'</div>'+
									'<div class="stats_shares_context">Share</div>'+
								'</a>'+
								'<div class="stats_shares_number">( 1.5K )</div>'+
								'<div class="tooltip" id="tooltip-1">'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Twitter</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-twitter.png">'+
										'</div>'+
									'</a>'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Facebook</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-facebook.png">'+
										'</div>'+
									'</a>'+
								'</div>'+
							'</div>'+
						'</div>';

			videoHolder.appendChild(card);
			currentNum++;
		}
  		autoCounter++;
  	}
}

function onVideosEnd(event){
	mainHeight = document.getElementsByTagName('main')[0].offsetHeight;
	var mainOffset = (window.pageYOffset || document.documentElement.scrollTop) + screenHeight;
	if (mainOffset >= mainHeight) {
		if (autoCounter < 3) {
			for (var i = 0; i < loadNum; i++){
				var card = document.createElement('li');
				card.className = "card";
				card.innerHTML = '<a class="card_image" href="#">'+
							'<img src="assets/video-small-1.png">'+
							'<ul class="card_play">'+
								'<li class="play_circle one"></li>'+
								'<li class="play_circle two"></li>'+
								'<li class="play_triangle"></li>'+
							'</ul>'+
							/* 360 tag bellow*/
							'<label class="vr_type two">360</label>'+
						'</a>'+
						'<ul class="card_properties">'+
							'<li class="card_name">'+
								'<a class="name_icon" href="#">'+
									'<img src="assets/icon-1.png">'+
								'</a>'+
								'<a class="name_context video-title-nowrap" href="#">User 2582</a>'+
							'</li>'+
							'<a class="card_type" href="#">League of Legends</a>'+
							'<li class="card_date">3 Nov 2016 at 5:52PM</li>'+
						'</ul>'+
						'<div class="card_stats">'+
							'<div class="stats_views">100.5K views</div>'+
							'<div class="stats_shares">'+
								'<a class="stats_shares_btn" href="#tooltip-1">'+
									'<div class="stats_shares_icon">'+
										'<img class="share_image" src="assets/share.png">'+
										'<img class="shared_image" src="assets/shared.png">'+
									'</div>'+
									'<div class="stats_shares_context">Share</div>'+
								'</a>'+
								'<div class="stats_shares_number">( 1.5K )</div>'+
								'<div class="tooltip" id="tooltip-1">'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Twitter</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-twitter.png">'+
										'</div>'+
									'</a>'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Facebook</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-facebook.png">'+
										'</div>'+
									'</a>'+
								'</div>'+
							'</div>'+
						'</div>';
						
				videoHolder.appendChild(card);
				currentNum++;
			}
			autoCounter++;
		}
	}
	else if (autoCounter >= 3)  {
		loadBtn.style.cssText = "opacity:1;";
	}
}

function loadMore(){
	autoCounter= 1;
	for (var x= 0; x < loadNum; x++) {
	  	var card = document.createElement('li');
		card.className = "card";
		card.innerHTML = '<a class="card_image" href="#">'+
							'<img src="assets/video-small-1.png">'+
							'<ul class="card_play">'+
								'<li class="play_circle one"></li>'+
								'<li class="play_circle two"></li>'+
								'<li class="play_triangle"></li>'+
							'</ul>'+
							/* 360 tag bellow*/
							'<label class="vr_type two">360</label>'+
						'</a>'+
						'<ul class="card_properties">'+
							'<li class="card_name">'+
								'<a class="name_icon" href="#">'+
									'<img src="assets/icon-1.png">'+
								'</a>'+
								'<a class="name_context video-title-nowrap" href="#">User 2582</a>'+
							'</li>'+
							'<a class="card_type" href="#">League of Legends</a>'+
							'<li class="card_date">3 Nov 2016 at 5:52PM</li>'+
						'</ul>'+
						'<div class="card_stats">'+
							'<div class="stats_views">100.5K views</div>'+
							'<div class="stats_shares">'+
								'<a class="stats_shares_btn" href="#tooltip-1">'+
									'<div class="stats_shares_icon">'+
										'<img class="share_image" src="assets/share.png">'+
										'<img class="shared_image" src="assets/shared.png">'+
									'</div>'+
									'<div class="stats_shares_context">Share</div>'+
								'</a>'+
								'<div class="stats_shares_number">( 1.5K )</div>'+
								'<div class="tooltip" id="tooltip-1">'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Twitter</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-twitter.png">'+
										'</div>'+
									'</a>'+
									'<a class="tooltip_btn" href="#" onclick="sharedBTN(1)">'+
										'<div class="tooltip_btn_text">Facebook</div>'+
										'<div class="tooltip_btn_image">'+
											'<img src="assets/tt-facebook.png">'+
										'</div>'+
									'</a>'+
								'</div>'+
							'</div>'+
						'</div>';

		videoHolder.appendChild(card);
		currentNum++;
	}  
}

window.addEventListener('load', startVideos);
window.addEventListener('scroll', onVideosEnd);









