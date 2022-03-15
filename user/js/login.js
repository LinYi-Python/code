var loginObj = {}

// 校验数据
loginObj.checkInput = function() {
  var sUserName = $("#username").val(),
    sPwd = $("#password").val()

  if (!sUserName || !sUserName.trim()) {
    showErrMsg("请输入账户")
    return false
  }
  if (!sPwd || !sPwd.trim()) {
    showErrMsg("请输入密码")
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

// 登陆事件
loginObj.loginEvent = function() {
  var isValid = loginObj.checkInput()

  if (!isValid) {
    return
  }

  hideErrMsg()

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

  showErrMsg("正在登录中...")

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
          buildLoginQRCode()
          return
        }

        // 保存起对象
        // Util.setUserInfo(res.oRet)
        localStorage.setItem("__USER__", JSON.stringify(res.oRet))
        // localStorage.setItem("username", res.oRet.sUserName)
        // localStorage.setItem("userAddr", res.oRet.sUserAddr)
        // localStorage.setItem("userAvatar", res.oRet.sAvatar)

        window.location = "../user/success-login.html"
      },
      error: function(err) {
        showErrMsg("网络繁忙，请重新再试")
        buildLoginQRCode()
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

function buildLoginQRCode() {
  DengLu1.login({
    appId: 1, //接入方企业ID号
    width: 200, // 生成2维码宽度
    height: 200, // 生成2维码高度
    password: "password", // 密码填充到控件ID
    username: "username", // 用户名填充到控件ID
    QRCodeImageId: "DengLu1_QRCode", //二维码生成到页面的哪个区域
    success: function(data) {
      // 登录信息回调 data包含一些登录信息
      // alert('login success');
      // $("#lsform").submit();
      loginObj.loginEvent()
    }
  })
}
