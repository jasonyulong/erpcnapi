var CW=(function(cw){
	var INTERVAL=300;
	cw.CONNECTED=1;
	cw.DISCONNECTED=0;
	var SCRIPT_ID="_LD_CW_X_SCRIPT_";
	var lastConnected=new Date().getTime();
	var listener=null;
	var stateCallback=null;
	function nextCall(){
		var lastScript=document.getElementById(SCRIPT_ID);
		while(lastScript){
			lastScript.parentNode.removeChild(lastScript);
			lastScript=document.getElementById(SCRIPT_ID);
		}
		var script=document.createElement("script");
		script.src="http://127.0.0.1:38383/weight?"+lastConnected;
		script.type="text/javascript";
		script.id=SCRIPT_ID;
		var node=document.body;
		var heads=document.getElementsByTagName("head");
		if(heads&&heads.length>0){
			node=heads[0];
		}
		node.appendChild(script);
	}
	var monThread=setInterval(function(){
		var now=new Date().getTime();
		if(now-lastConnected>2000){
			cw.notifyState(cw.DISCONNECTED);
			nextCall();
		}
	},1000);
	cw.listen=function(f){
		listener=f;
	}
	cw.listenState=function(_stateCallback){
		stateCallback=_stateCallback;
	}
	cw.notifyState=function(state){
		if(typeof(stateCallback)=="function"){
			stateCallback(state);
		}
	}
	cw.notify=function(msg){
		cw.notifyState(cw.CONNECTED);
		var now=new Date().getTime();
		var delay=0;
		if(now-lastConnected<INTERVAL){
			delay=INTERVAL-(now-lastConnected);
		}
		lastConnected=now;
		if(typeof(listener)=="function"){
			listener(msg);
		}
		if(delay>0){
			setTimeout(function(){
				nextCall();
			},delay);
		}else{
			nextCall();
		}
	}
	nextCall();
	return cw;
})({});
