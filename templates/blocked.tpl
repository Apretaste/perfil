<p>Lo sentimos, el perfil que usted nos solicita no puede ser mostrado debido a que usted ha sido bloqueado por esa persona</p>
<center>
{if $profile->blockedByMe}
{button href="PERFIL DESBLOQUEAR @{$profile->username}" caption="Desbloquear" color="red"}
{else}
{button href="PERFIL BLOQUEAR @{$profile->username}" caption="Bloquear" color="red"}
{/if}
</center>