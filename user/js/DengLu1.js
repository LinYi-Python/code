var DengLu1 = {};

DengLu1.Http = 'https://app.denglu1.com/msg.php';
DengLu1.HttpToken = 'https://app.denglu1.com/token.php';

/*
DengLu1.Http = 'http://127.0.0.1/msg.php';
DengLu1.HttpToken = 'http://127.0.0.1/token.php';*/

DengLu1.createMask = function (imgWidth, imgHeight, param) {

    var objBody = document.body;

    var objMask = document.createElement("div");
    objMask.setAttribute('id', 'mask-section');
    objMask.style.cssText="position: fixed;width: 100%;height: 100%;top: 0;left: 0;background-color: rgba(255, 255, 255, 1.0);z-index:1000;";

    var vMTop = -(imgWidth/2 + 50);
    var vMLeft = -(imgHeight/2 + 50);

    var objBorder = document.createElement("div");
    objBorder.style.cssText="position: absolute;width: " + (imgWidth + 100) + "px;height: " + (imgHeight + 100) + "px;top: 50%;left: 50%;margin-top: "+vMTop+"px;margin-left: "+vMLeft+"px;border:1px solid #2a72bd;";
    objMask.appendChild(objBorder);

    vMTop = -(imgWidth/2);
    vMLeft = -(imgHeight/2);
    var objImg = document.createElement("div");
    objImg.setAttribute('id', 'DengLu1_QRCode');
    objImg.style.cssText="position: absolute;width: "+imgWidth+"px;height: "+imgHeight+"px;top: 50%;left: 50%;margin-top: "+vMTop+"px;margin-left: "+vMLeft+"px;";
    objMask.appendChild(objImg);

    objBody.appendChild(objMask);

    vMTop = -(imgWidth/2 - 200);
    var span = document.createElement("div");
    span.setAttribute('id', 'DengLu1_QRCode_TEXT');
    span.style.cssText="position: absolute;width: " + (imgWidth) + "px;height: " + 50 + "px;top: 50%;left: 50%;margin-top:100px;margin-left: "+vMLeft+"px;line-height:50px;text-align:center;font-size:16px;";
    objMask.appendChild(span);

    objMask.onclick = function () {
        DengLu1.hideMask();
    }

    jQuery("#DengLu1_QRCode_TEXT").html(param.text);

}

DengLu1.hideMask = function () {
    jQuery('#mask-section').remove();
}

DengLu1.login = function (param) {
    param.text = "登录易扫码登录";
    DengLu1.event(param, 0);
}

DengLu1.register = function (param) {
    param.text = "登录易扫码注册";
    DengLu1.event(param, 1);
}

DengLu1.changePwd = function (param) {
    param.text = "登录易扫码修改密码";
    DengLu1.event(param, 2);
}

DengLu1.event = function (param, iCode) {

    jQuery('#' + param.QRCodeImageId).children().remove();
    retInfo = undefined;

    param.width = param.width || 184;
    param.height = param.height || 184;

    param.QRCodeImageId = 'DengLu1_QRCode';

    // DengLu1.createMask(param.width, param.height, param);

    jQuery.getScript(DengLu1.HttpToken + "?iCode=" + iCode + "&title=" + encodeURIComponent(window.document.title)+"&domain="+document.domain, function() {

        if (typeof retInfo == "undefined") {
            alert('网络繁忙，稍后重试!');
            DengLu1.hideMask();
            return;
        }

        jQuery.getScript('https://app.denglu1.com/js/qrcode.js?v=2.0', function() {
            var qrcode = new QRCode(document.getElementById(param.QRCodeImageId), {
                width : param.width, //设置宽高
                height : param.height
            });
            qrcode.makeCode(retInfo.data);

            // console.log(retInfo.data);

            DengLu1.listen(param, retInfo.data.split(':')[0], iCode);
        });

    });

}

// 开始监听事件
DengLu1.listen = function (param, sToken, iCode) {

    jQuery.getScript(DengLu1.Http + "?sToken=" + sToken, function() {
        if (typeof msgInfo == "undefined") {
            DengLu1.listen(param, sToken, iCode);
            return;
        }

        DengLu1.hideMask();

        if (typeof param.success != undefined) {

            if (msgInfo.iCode == 0) {
                // 登录信息
                jQuery("#"+param.password).val(msgInfo.sPwd);
                jQuery("#"+param.username).val(msgInfo.sUserName);
            }

            if (msgInfo.iCode == 1) {
                // 注册信息
                jQuery("#"+param.password).val(msgInfo.sPwd);
                jQuery("#"+param.username).val(msgInfo.sUserName);
                jQuery("#"+param.confirmPassword).val(msgInfo.sPwd);
                jQuery("#"+param.email).val(decodeURIComponent(msgInfo.sMail));
                jQuery("#"+param.phone).val(msgInfo.sPhone);
            }

            if (msgInfo.iCode == 2) {
                // 修改信息
                jQuery("#"+param.password).val(msgInfo.sPwd);
                jQuery("#"+param.repassword).val(msgInfo.sPwd);
                jQuery("#"+param.oldPassword).val(msgInfo.sOldPsw);
            }


            var data = msgInfo;
            param.success(data);
        }

        jQuery('#' + param.QRCodeImageId).children().remove();
        msgInfo = undefined;
    });

}

DengLu1.login_bg = function (param) {
    DengLu1.event_bg(param, 0);
}

DengLu1.register_bg = function (param) {
    DengLu1.event_bg(param, 1);
}

DengLu1.changePwd_bg = function (param) {
    DengLu1.event_bg(param, 2);
}

DengLu1.event_bg = function (param, iCode) {

    jQuery('#' + param.QRCodeImageId).children().remove();
    retInfo = undefined;

    param.width = param.width || 184;
    param.height = param.height || 184;

    param.QRCodeImageId = 'DengLu1_QRCode';

    // DengLu1.createMask(param.width, param.height, param);

    jQuery.getScript(DengLu1.HttpToken, function() {

        if (typeof retInfo == "undefined") {
            alert('网络繁忙，稍后重试!');
            DengLu1.hideMask();
            return;
        }

        jQuery.getScript('https://app.denglu1.com/js/qrcode.js?v=2.0', function() {
            var qrcode = new QRCode(document.getElementById(param.QRCodeImageId), {
                width : param.width, //设置宽高
                height : param.height
            });

            qrcode.makeCode(retInfo.token+":"+iCode);
            DengLu1.listen_bg(param, retInfo.token, iCode);
        });

    });

}

DengLu1.listen_bg = function (param, sToken, iCode) {

    // console.log(sToken);

    jQuery.getScript(DengLu1.Http + "?sToken=" + sToken + "&iCode=" + iCode, function() {

        if (typeof msgInfo == "undefined") {
            DengLu1.listen_bg(param, sToken, iCode);
            return;
        }

        DengLu1.hideMask();

        if (typeof param.success != undefined) {

            if (msgInfo.iCode == 0) {
                // 登录信息

            }

            if (msgInfo.iCode == 1) {
                // 注册信息
            }

            if (msgInfo.iCode == 2) {
                // 修改密码信息
            }

            var data = msgInfo;
            param.success(data);
        }

        jQuery('#' + param.QRCodeImageId).children().remove();
        msgInfo = undefined;
    });

}

