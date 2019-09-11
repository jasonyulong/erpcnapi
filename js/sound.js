SOUND_ACTION=(function(){
    var html="<audio id='failSound'><source src='' type='audio/mp3'></audio><audio id='scanSound' ><source src='' type='audio/mp3'></audio>";
    html+="Sound:<select id='soundOpenSelect' onchange='SOUND_ACTION.changeSoundON(this)'><option value='0'>OFF</option><option value='1'>ON</option></select>";
    $("body").append($(html));
    var SoundON=false;
    var soundSuccess=function(){
        if(!SoundON){
            return;
        }
        var video	=	document.getElementById("failSound");
        video.src="../sound/ok.mp3";
        video.play();
    };

    var soundfail=function(){
        if(!SoundON){
            return;
        }
        var video	=	document.getElementById("failSound");
        video.src="../sound/f.mp3";
        video.play();
    };

    var changeSoundON=function(that){
        var bool=$(that).val();
        bool=bool=='1'?true:false;
        SoundON=bool;
        //console.log(SoundON);

    };

    return {
        'soundSuccess':soundSuccess,
        'soundFailure':soundfail,
        'changeSoundON':changeSoundON
    }
})();