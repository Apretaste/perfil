$(document).ready(() => {
    if(ownProfile){
        $("#editar").click(function() {
            return apretaste.send({"command": "PERFIL EDITAR"})
        });
        $("#credito").click(function() {
            apretaste.send({"command": 'CREDITO'});
        });
        $("#rifa").click(function() {
            apretaste.send({"command": 'RIFA'});
        });
    }else{
        $("#chat").click(function() {
            apretaste.send({"command": 'CHAT', data: {"username":profile.username}});
        });
        let block = profile.blockedByMe?"DESBLOQUEAR":"BLOQUEAR";

        $("#bloquear").click(function() {
            apretaste.send({"command": 'PERFIL '+block, data: {"username":profile.username}});
        });
    }
});
