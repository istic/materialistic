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
            <li class=""><a href="/"><span class="glyphicon glyphicon-home"></span> Front Page</a></li>
          </ul>
          

            <ul class="nav navbar-nav navbar-right">
          
              <li><a href="/auth/register">
                <span class="glyphicon glyphicon-user"></span> Register</a>
              </li>
              <li class="divider-vertical"></li>
              <li class="dropdown">
                <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                  <span class="glyphicon glyphicon-log-in"></span> Log In <strong class="caret"></strong>
                </a>
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
                   <li><a href="/auth/forgot">Forgot Password</a></li>
                </ul>

              </li>
            </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>