var registerObj = {}

registerObj.checkInput = function() {
  var sUserName = $("#username").val(),
    sPwd = $("#password").val(),
    sPwd2 = $("#password2").val()

  if (!sUserName || !sUserName.trim()) {
    showErrMsg("请输入账户")
    return false
  }
  if (!sPwd || !sPwd.trim()) {
    showErrMsg("请输入密码")
    return false
  }
  if (sPwd !== sPwd2) {
    showErrMsg("两次密码输入不一致")
    return false
  }
  return true
}

function showErrMsg(msg) {
  $("#msg").html(msg)
}

function hideErrMsg() {
  $("#msg").html("")
}
function login() {
  var params = {
    action: "login"
  }

  var data = {
    sUserName: $("#username").val(),
    sPwd: $("#password").val()
  }

  var paramsStr = decodeURIComponent($.param(params))
  // var url = '/controller/userController.php?' + paramsStr
  var url = "/dev/controller/exUserController.php?" + paramsStr

  return new Promise(function(resolve, reject) {
    $.ajax({
      type: "POST",
      url: url,
      data: data,
      // cache: false,
      // processData: false, 这个参数是指不让jquery序列化对象，传递formData有文件时最好和contentType一起设置为false
      // contentType: false,
      success: function(res) {
        res = JSON.parse(res)
        if (res.retCode != 0) {
          showErrMsg(res.retMsg)
          buildRegisterQRCode()
          return
        }

        localStorage.setItem("__USER__", JSON.stringify(res.oRet))

        window.location = "../user/success-login.html"
      },
      error: function(err) {
        showErrMsg("网络繁忙，请重新再试")
      }
    })
  })

  // JsonpEx.sendData('/controller/userController.php', params, function (data) {
  //   if (retInfo.retCode !== 0) {
  //     showErrMsg(retInfo.retMsg)
  //     return;
  //   }

  //   localStorage.setItem('username', retInfo.oRet.sUserName)
  //   localStorage.setItem('userAddr', retInfo.oRet.sUserAddr)
  //   console.log(retInfo)
  //   window.location = '../user/success-login.html'
  // });
}

// 注册事件
registerObj.registerEvent = function() {
  var isValid = registerObj.checkInput()

  if (!isValid) {
    return
  }

  hideErrMsg()

  var params = {
    action: "register"
  }

  var data = {
    sUserName: $("#username").val(),
    sPwd: $("#password").val(),
    sPwd2: $("#password2").val()
  }

  var paramsStr = decodeURIComponent($.param(params))
  // var url = "/controller/userController.php?" + paramsStr
  var url = "/dev/controller/exUserController.php?" + paramsStr

  showErrMsg("正在注册中...")

  return new Promise(function(resolve, reject) {
    $.ajax({
      type: "POST",
      url: url,
      data: data,
      success: function(res) {
        res = JSON.parse(res)
        if (res.retCode != 0) {
          showErrMsg(res.retMsg)
          setTimeout(function() {
            hideErrMsg()
            buildRegisterQRCode()
          }, 3000)
          return
        }

        showErrMsg("注册成功，正在自动登录中...")
        // login
        setTimeout(function() {
          login()
        }, 2000)
        // window.location = '../user/success-register.html'
      },
      error: function(err) {
        showErrMsg("网络繁忙，请重新再试")
        buildRegisterQRCode()
      }
    })
  })

  // JsonpEx.sendData('/controller/userController.php', params, function () {

  //   if (retInfo.retCode !== 0) {
  //     showErrMsg(retInfo.retMsg)
  //     return;
  //   }

  //   console.log('注册成功:', retInfo)

  //   window.location = '../user/success-register.html'
  // });
}

function buildRegisterQRCode() {
  // 注册事件处理
  DengLu1.register({
    appId: 1, //接入方企业ID号
    width: 200, // 生成2维码宽度
    height: 200, // 生成2维码高度
    QRCodeImageId: "DengLu1_QRCode", //二维码生成到页面的哪个区域
    password: "password", // 密码填充到控件ID
    confirmPassword: "password2", // 确认密码填充到控件ID
    username: "username", // 用户名填充到控件ID
    success: function(data) {
      //        jQuery("#lsform").submit();
      registerObj.registerEvent()
    }
  })
}
