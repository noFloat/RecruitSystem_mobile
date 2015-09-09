	

	var metaEl = document.querySelector('meta[name="viewport"]');
	var width = document.documentElement.clientWidth;
	metaEl.setAttribute('content', 'width=' + width * 2 + 'px,user-scalable=no,initial-scale=0.5');