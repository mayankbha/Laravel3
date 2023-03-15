/*
 * Initilize js
 *
 */
var xjs = require('xjs');
xjs.ready().then(function() {
	//alert("Xjs ready");
	console.log("Xjs ready");
	var winInfo = findReplayView();
	//alert("winInfo: " + winInfo.title);
	if(winInfo !== undefined){
		// Create a new Source 
		createNewSource(winInfo);

		// Browse the new source for updating
		xjs.Scene.getSceneCount().then(function(count) {
			//alert("getSceneCount " + count);
			retrieveScene(0, count);
		});
	}
	var debug = false;
	if(!debug){
		// Remove the temp webpage source
		xjs.Source.getCurrentSource()
			.then(source => source.getItemList()) // gets the item list of current source
			.then(itemList => itemList[0].getId()) // you are sure there is exactly one item
			.then(id => {
				external.AttachVideoItem1(id); // prepare item for manipulation
				external.SetLocalProperty1('remove', ''); // remove the video item
		});
	}
	// Notify if not found ReplayView
	if(winInfo === undefined){
		alert("Replay View is not found. Please open BoomReplay and click Test Replay first!");
	}
});
/*---------------the end-----------------*/

/*
 * Retrieve scene
 */
function retrieveScene(index, total){
	//alert("No " + index + "/" + total);
	if(index < total){
		var id = index + 1; // index begin from 1
		findUpdateBoomSourceAtSence(id);
		retrieveScene(++index, total);// parallel on all scene
	}
}

/*
 * Browse the new source just added on scene
 */
function findUpdateBoomSourceAtSence(sceneId){
	//alert("findUpdateBoomSourceAtSence: " + sceneId);
	xjs.Scene.getById(sceneId).then(function(scene){			
		//alert(" sceneId " + sceneId + scene);
		scene.getItems().then(function(items) {
			if (items.length === 0) return;
			//alert("Number of sources: " + items.length);
			// There's a valid item, let's use that
			for(i = 0; i < items.length; ++i){
				var item = items[i];
				checkNewSource(xjs, item, function(isNew){
					if(isNew){
						//setSourceName(item, "Boom Replay");
						console.log("Set properties: ");
						item.setCustomName("Boom Replay")
						item.setStickToTitle(true);
						item.setShowMouse(false);
						item.setMaskEffect(1);
						item.setBorderEffectThickness(1);
						var color = xjs.Color.fromRGBString("#ff666666");
						console.log("color " + color.getBgr());
						item.setBorderColor(color);
						console.log("Set properties: done");
					} else {
						//alert("It not a screen source");
					}
				});
			}
		});
	});
}
