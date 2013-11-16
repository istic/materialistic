
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><?PHP echo APPNAME ?></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/">Home</a></li>
          </ul>

            <ul class="nav navbar-nav navbar-right">
          <?PHP if($current_user->logged_in()){ ?>
              <li><a href="/auth/logout">Logout</a></li>
                        
          <?PHP } else { ?>
            
              <li><a href="/auth/register">Register</a></li>
                        <li class="divider-vertical"></li>
              <li class="dropdown">
                <a class="dropdown-toggle" href="#" data-toggle="dropdown">Log In <strong class="caret"></strong></a>
                <ul class="dropdown-menu">
                <li  style="padding: 15px;">
                  <form method="post" action="/auth/login" accept-charset="UTF-8">
                  <div class="form-group">
                    <input class="form-control" type="text" placeholder="Email" id="email" name="email">
                  </div>
                  <div class="form-group">
                    <input class="form-control" type="password" placeholder="Password" id="password" name="password">
                  </div>
                    <input class="btn btn-primary btn-block" type="submit" id="sign-in" value="Log In">
                   </form>
                  </li>
                   <li><a href="/auth/forgot_password">Forgot Password</a></li>
                </ul>

              </li>
          <?PHP } ?>
            </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>