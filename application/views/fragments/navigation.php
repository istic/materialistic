<?PHP
function xs($that, $n){
  if($that == $n){
    echo ' active';
  }
}

$n = isset($navsection) ? $navsection : '';
?>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
    <img src="/assets/img/gift-icon.png" height="40" />
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="<?PHP xs('stats', $n)     ?>"><a href="/"><span class="glyphicon glyphicon-stats"></span> Stats</a></li>
        <li class="<?PHP xs('projects', $n)  ?>"><a href="/my/projects"><span class="glyphicon glyphicon-list"></span> Campaigns</a></li>
        <?PHP /*<li class="<?PHP xs('inflight', $n)  ?>"><a href="/my/in-flight"><span class="glyphicon glyphicon-plane"></span> In-Flight</a></li> */?>
        <li class="<?PHP xs('category', $n)  ?>"><a href="/my/by-category"><span class="glyphicon glyphicon-book"></span> By Category</a></li>
        <li class="<?PHP xs('monthly', $n)   ?>"><a href="/my/by-month"><span class="glyphicon glyphicon-calendar"></span> Monthly Spend</a></li>
        <li class="<?PHP xs('lateness', $n)  ?>"><a href="/my/by-lateness"><span class="glyphicon glyphicon-time"></span> Lateness</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="<?PHP xs('account', $n)     ?>"><a href="/auth/account"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
        <li><a href="/auth/logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>