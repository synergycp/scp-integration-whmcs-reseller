<div class="row">
  <div class="col-sm-5 text-right">
    <strong>Bandwidth Usage</strong>
  </div>
  <div class="col-sm-7 text-left">
    {if $bandwidth.limit}
      <div class="progress">
        <div class="progress-bar" role="progressbar"
          aria-valuenow="2" aria-valuemin="0" aria-valuemax="100"
          style="min-width: 2em; width: {$bandwidth.percent}%;">
          {$bandwidth.percent}%
        </div>
      </div>
      {$bandwidth.used} of {$bandwidth.limit} used
    {else}
      {$bandwidth.used} used
    {/if}
    <br /><br />
  </div>
</div>

{if $server->access->ipmi && $server->access->is_active && $server->ipmi}
  <div class="row">
    <div class="col-sm-5 text-right">
      <strong>IPMI Details</strong>
    </div>
    <div class="col-sm-7 text-left">
      <a href="http://{$server->ipmi->ip}" target="_blank">{$server->ipmi->ip}</a><br />
      - Username: {$server->ipmi->client->username|default:'None'}
      (<a href="{$url_action}{if $server->ipmi->client->username}btn_ipmi_client_delete{else}btn_ipmi_client_create{/if}"
        >{if $server->ipmi->client->username}Delete{else}Create{/if}</a>)<br />
      - Password: {$server->ipmi->client->password|default:'None'}<br />
      <br />
    </div>
  </div>
{/if}

<hr />
