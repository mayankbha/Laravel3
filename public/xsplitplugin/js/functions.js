/*
 * Find the replay view window
 * return: found window info
 */
function findReplayView(){
	//alert("Find Boom Replay window");
	var windowInfo = undefined;
	var hwndsStr = external.CallDll('xsplit.EnumParentWindows') // list all window handles of visible, non-transparent, non-minimized windows for all processes
	var hwnds = hwndsStr.split(',');
	//alert(hwnds.length)
	var i = 0;
	for (i = 0; i < hwnds.length; i++) { 
		// get the window handle
		var hwnd = hwnds[i];//'2296804';
		var pid = external.CallDll('xsplit.GetWindowProcessId', hwnd)
		if (pid != undefined){
			//alert("hwnd: " + hwnds[i] + ", pid: " + pid);
		}
		
		//alert(pid);
		//SAMPLE RESULT: "12488"
		var details = external.CallDll('xsplit.GetProcessDetailsKernel', pid)
		//SAMPLE RESULT: "\Device\HarddiskVolume4\Windows\System32\notepad.exe"
		if (typeof details != "undefined"){
			//alert("hwnd: " + hwnds[i] + ", pid: " + pid + ", details:" + details);
		}
		var title = external.CallDll('xsplit.GetWindowTitle', hwnd)
		//SAMPLE RESULT: "Untitled - Notepad"
		var wclass = external.CallDll('xsplit.GetWindowClassName', hwnd)
		//SAMPLE RESULT: "Notepad"
		
		if (typeof title != "undefined"){
			//alert("hwnd: " + hwnds[i] + ", pid: " + pid + ", details:" + details + ",\ntitle: " + title);
			if(title === "Boom Replay (Resizing affects replays in broadcast)"){
				windowInfo = {title: title, wclass: wclass, details: details, pid: pid};
				break;
			}
		}
	}
	if(i >= hwnds.length){
		//addLog("Not found the replay view. Please open BoomReplay => click Test Replay first");
	}
	return windowInfo;
}

function createNewSource(winInfo){
	if (winInfo !== undefined){
		//alert("hwnd: " + hwnds[i] + ", pid: " + pid + ", details:" + details + ",\ntitle: " + title);
		if(winInfo.title === "Boom Replay (Resizing affects replays in broadcast)"){
			//alert("hwnd: " + hwnd + ", pid: " + pid + ", details:" + details + ",\ntitle: " + title);
			//Create an XML string:
			var xml = `<screen module="${winInfo.details}" window="${winInfo.title}" class="${winInfo.wclass}" hwnd="${winInfo.hwnd}" wclient="1" left="0" top="0" width="0" height="0" tag="boomtag"/>`
			//alert("xml: " + xml);
			external.AppCallFunc('addscreen', xml)
		}
	}
}
/*
 * Identify BoomReplay source
 * xjs: xjs framework object
 * source: stream source to checked
 * handleFunc: handle function when finished
 */
function checkNewSource(xjs, source, handleFunc){
	if (source instanceof xjs.ScreenSource || source instanceof xjs.ScreenItem) {
		source.getValue().then(function(value) {
			var valueStr = value.toString();
			//prompt(valueStr.indexOf("boomtag"), valueStr);
			var isRight = valueStr.indexOf("boomtag") >= 0;
			if(isRight){
				source.getCustomName().then(function(name) {
					handleFunc(name === "");
				});
			} else {
				handleFunc(false);
			}
		});
		return;
	} else {
		handleFunc(false);
	}
}
/*
 * Set custom name of screen source
 * source: stream source to update
 * name: string name
 */
/* function setSourceName(source, name){
	alert("setSourceName");
	source.setCustomName(name).then(function(source) {
		// Promise resolves with same Source instance when custom name has been set
		return source.getCustomName();
	});
} */