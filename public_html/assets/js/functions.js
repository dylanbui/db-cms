/**
 * Created by dylanbui on 1/29/16.
 */

var UI = {};
UI.Framework = { hostInfo: null, hostUrl: null, siteUrl: null, bodyLoadingIndicator: null };

var FB_APP_ID_TEST = "534956693276758";
var GA_ID_TEST = "UA-65944313-1";
var MP_ID_TEST = "a8033cc3d0c8c8f505183e36b90bc976"; //"bc2f0b6d523c51a7224cfd0033b0d2ab";

$(document).ready(function() {

    // -- Init Framework --
    UI.Framework.init();

});

//------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------

UI.Log = { showLog: true };
UI.Log.show = function (content, show_time)
{
    if (show_time == undefined )
        show_time = false;

    if (UI.Log.showLog == true)
    {
        // if (typeof content == Object)
        if (typeof content === 'object')
        {
            console.log(content);
            return;
        }

        if (show_time)
            content = '[' + UI.DateTime.formatDate(new Date(),'MM-dd-yyyy HH:mm:ss') + '] ' + content;
        console.log(content);
    }
};

//------------------------------------------------------------------------------------------------------------

UI.Framework.init = function()
{
    var defaultPorts = { "http:": 80, "https:": 443 };
    this.hostUrl = window.location.protocol + "//" + window.location.hostname + (((window.location.port)&& (window.location.port != defaultPorts[window.location.protocol])) ? (":" + window.location.port) : "");
    this.siteUrl = window.location.href;
    this.hostInfo = UI.Framework.parseUri(window.location.href);
};

UI.Framework.testSite = function()
{
    if (document.location.hostname == "localhost"
        || document.location.hostname.indexOf("127.0.0.1") == 0
        || document.location.hostname.indexOf("192.168") == 0
        || document.location.hostname.indexOf("vn.test.propzy") == 0)
        return true;
    else
        return false;
};

/**
 * See: https://gist.github.com/1847816
 * Parse a URI, returning an object similar to Location
 * Usage: var uri = parseUri("hello?search#hash")
 */
UI.Framework.parseUri = function(url)
{
    var result = {};
    var anchor = document.createElement('a');
    anchor.href = url;

    var keys = 'protocol hostname host pathname port search hash href'.split(' ');
    for (keyIndex in keys) {
        var currentKey = keys[keyIndex];
        result[currentKey] = anchor[currentKey];
    }

    result.toString = function() { return anchor.href; };
    result.requestUri = result.pathname + result.search;

    var pathname = result['pathname'];
    var filename = pathname.substring(pathname.lastIndexOf('/')+1);
    result['fullpathname'] = result['pathname'];
    result['pathname'] = pathname.replace(filename,'');

    return result;
};

UI.Framework.changeAlias = function(alias)
{
    var str = alias;
    str= str.toLowerCase();
    str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
    str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
    str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
    str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ  |ợ|ở|ỡ/g,"o");
    str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
    str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
    str= str.replace(/đ/g,"d");
    str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g,"-");
    /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */
    str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1-
    str= str.replace(/^\-+|\-+$/g,"");
    //cắt bỏ ký tự - ở đầu và cuối chuỗi
    return str;
};

// <link rel="stylesheet" href="css/jquery.loading-indicator.css" />
// <script type="text/javascript" src="js/jquery.loading-indicator.js"></script>
UI.Framework.showLoading = function()
{
    if(this.bodyLoadingIndicator == null)
    {
        this.bodyLoadingIndicator = $('body').loadingIndicator({
            useImage: false,
        }).data("loadingIndicator");
    }
    else
    {
        this.bodyLoadingIndicator.show();
    }
};

UI.Framework.hideLoading = function()
{
    if(this.bodyLoadingIndicator != null)
    {
        this.bodyLoadingIndicator.hide();
    }
};

UI.Framework.showPageAlert = function(strTitle, strContent, extendOptions, confirmFunc)
{
    // http://craftpip.github.io/jquery-confirm/
    confirm = 'Đồng ý';
    if(typeof LANGUAGE.general.confirm !== 'undefined')
        confirm = LANGUAGE.general.confirm;

    cancel = 'Hủy bỏ';
    if(typeof LANGUAGE.general.cancel !== 'undefined')
        cancel = LANGUAGE.general.cancel;

    options = {
        title: strTitle,
        content: strContent,
        confirmButton: confirm,
        cancelButton: cancel
    };

    if (extendOptions != undefined && extendOptions != null)
        options = $.extend({}, options, extendOptions);

    options.confirm = function() {
        if (confirmFunc != null)
            confirmFunc();
    };

    $.alert(options);
};

UI.Framework.EmbedSWF = function (_gsk , liked , photo_id)
{
    $.getScript("template/Default/js/plugin-swf.js").done(function (e) {
        $("html body").css({ margin: "0", padding: "0", overflow: "hidden" });
        $("html body").append('<div id="my_flash">You need Flash Player 9+ and allow javascript to see the content of this site..</div>');
        var flashvars = {'gsk': _gsk , 'liked': liked , 'photoid': photo_id };
        var params = { bgcolor: "#000000", allowfullscreen: "true", wmode: "direct", allowscriptaccess: "always" };
        var attributes = { id: "my_flash", name: "my_flash" };
        swfobject.embedSWF("main.swf?sk="+Math.random(), "my_flash", "810", "654", "11.2.0", "", flashvars, params, attributes);
        //swffit.fit("my_flash", 1000, 650);
    });
};

// -- sprintf('%s , %s, and %s with %d', 'hay', 'carrots', 'bells', 1000)
// => hay , carrots, and bells eith 1000
UI.Framework.sprintf = function (s) {
    var bits = s.split('%');
    var out = bits[0];
    var re = /^([ds])(.*)$/;
    for (var i=1; i<bits.length; i++) {
        p = re.exec(bits[i]);
        if (!p || arguments[i]==null) continue;
        if (p[1] == 'd') {
            out += parseInt(arguments[i], 10);
        } else if (p[1] == 's') {
            out += arguments[i];
        }
        out += p[2];
    }
    return out;
}

UI.Framework.isBlank = function (str)
{
    return (!str || /^\s*$/.test(str));
};

UI.Framework.isEmail = function (s)
{
    if (s.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]{2,4}$/) != -1)
        return true;
    return false;
};

var exts = ['.jpg', '.gif', '.png'];
UI.Framework.hasExtension = function (fileName, exts)
{
    return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
};

UI.Framework.isNumeric = function (input)
{
    var isResult = false;
    var number = /^\-{0,1}(?:[0-9]+){0,1}(?:\.[0-9]+){0,1}$/i;
    var regex = RegExp(number);
    isResult = regex.test(input) && input.length > 0;
    if(isResult  && input <= 0){
        isResult = false;
    }
    return isResult;
};

/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 *  12345678.9.format(2, 3, '.', ',');  // "12.345.678,90"
 123456.789.format(4, 4, ' ', ':');  // "12 3456:7890"
 12345678.9.format(0, 3, '-');       // "12-345-679"

 */
UI.Framework.numberFormat = function(number, n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = number.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

// trim, rtrim, ltrim
UI.Framework.trim = function (str, chr) {
    var rgxtrim = (!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+chr+'+|'+chr+'+$', 'g');
    return str.replace(rgxtrim, '');
}

UI.Framework.rtrim = function (str, chr) {
    var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$');
    return str.replace(rgxtrim, '');
}

UI.Framework.ltrim = function (str, chr) {
    var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^'+chr+'+');
    return str.replace(rgxtrim, '');
}

UI.Framework.getQueryVariable = function (variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return undefined;
};

//------------------------------------------------------------------------------------------------------------

// -- http://krasimirtsonev.com/blog/article/Javascript-template-engine-in-just-20-line --
/*
 <div id="show_html"><h1>Demo Simple Template Engine</h1></div>

 <script type="text/template" id="cardTemplate">
 <div>
 <h2><% title_page %></h2>
 <% for(var index in this.urls) { %>
 <a href="<% this.urls[index] %>"><% this.urls[index] %></a><br>
 <% } %>
 <% if (this.showSkills) { %>
 <h3>Show gia tri TRUE/FALSE</h3>
 <% } %>
 </div>
 </script>

 <script language="JavaScript">
 $(function(){
 var cardTemplate = $("#cardTemplate").html();
 console.log(cardTemplate);
 html = UI.Framework.Tmpl(cardTemplate,{
 title_page: 'Simple Template Engine',
 urls: ["http://google.com", "http://yahoo.com", "http://apple.com"],
 showSkills: true
 });
 console.log(html);
 $('#show_html').append(html);
 });
 </script>

 * */

UI.Framework.Tmpl = function(html, options) {
    var re = /<%(.+?)%>/g,
        reExp = /(^( )?(var|if|for|else|switch|case|break|{|}|;))(.*)?/g,
        code = 'with(obj) { var r=[];\n',
        cursor = 0,
        result;
    var add = function(line, js) {
        js? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') :
            (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
        return add;
    }
    while(match = re.exec(html)) {
        add(html.slice(cursor, match.index))(match[1], true);
        cursor = match.index + match[0].length;
    }
    add(html.substr(cursor, html.length - cursor));
    code = (code + 'return r.join(""); }').replace(/[\r\t\n]/g, ' ');
    try { result = new Function('obj', code).apply(options, [options]); }
    catch(err) { console.error("'" + err.message + "'", " in \n\nCode:\n", code, "\n"); }
    return result;
}

//------------------------------------------------------------------------------------------------------------

UI.Cookie = { prefix_name: '', exdays: 365 };

UI.Cookie.set = function (cname, cvalue, exdays)
{
    if (exdays == undefined)
        exdays = UI.Cookie.exdays;

    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();

    // if (typeof cvalue == Object)
    if (typeof cvalue === 'object')
        cvalue = JSON.stringify(cvalue);

    document.cookie = UI.Cookie.prefix_name + cname + "=" + cvalue + "; " + expires;
};

UI.Cookie.get = function(cname,  isObject)
{
    if (isObject == undefined)
        isObject = false;

    var name = UI.Cookie.prefix_name + cname + "=";
    var ca = document.cookie.split(';');
    // console.debug("document.cookie = " + document.cookie);
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0)
        {
            value = c.substring(name.length, c.length);
            if (value.toLocaleLowerCase() == "null")
            {
                return null;
            }
            return isObject ? JSON.parse(value) : value;
        }
    }
    return undefined;
};

UI.Cookie.remove = function (cname)
{
    UI.Cookie.set(cname, '', -1);
};

UI.Cookie.removeAll = function ()
{
    // Get an array of cookies
    var arrSplit = document.cookie.split(";");
    for(var i = 0; i < arrSplit.length; i++)
    {
        var cookie = arrSplit[i].trim();
        var cookieName = cookie.split("=")[0];
        // If the prefix of the cookie's name matches the one specified, remove it
        if(cookieName.indexOf(UI.Cookie.prefix_name) === 0) {
            // Remove the cookie
            document.cookie = cookieName + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
        }
    }
};

//------------------------------------------------------------------------------------------------------------

// Define FB Hook functions
// -- UI.Facebook.getLoadPageLoginStatus(response);
// -- UI.Facebook.beforeLogin();
// -- UI.Facebook.afterLogin(fbUserInfo);
// -- UI.Facebook.loginError(response);
// -- UI.Facebook.afterLogout();

UI.Facebook = { appId: null,  facebook_canvas: null, userInfo: null, totalFriends:0, accessToken: null, permissions: null};

UI.Facebook.init = function (facebook_appid)
{
    $("html body").append('<div id="fb-root"></div>');

    if (document.location.hostname == "localhost"
        || document.location.hostname.indexOf("127.0.0.1") == 0
        || document.location.hostname.indexOf("192.168") == 0)
    {
        // Account FB Test
        //console.debug('Hien dang su dung App ID Test : ' + FB_APP_ID_TEST);
        FB_APP_ID = facebook_appid = FB_APP_ID_TEST;
    }

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/vi_VN/all.js#xfbml=1&appId=" + facebook_appid;
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    UI.Facebook.appId = facebook_appid;
    window.fbAsyncInit = function() {
        FB.init({appId: facebook_appid, cookie: true,xfbml: true,oauth: true, status:true, version: 'v2.2'});
        FB.getLoginStatus(function (response)
        {
            // console.log('getLoadPageLoginStatus');
            // console.log(response);

            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().

            if (typeof UI.Facebook.getLoadPageLoginStatus == 'function')
                UI.Facebook.getLoadPageLoginStatus(response);

//           if (response.status === 'connected') {
//               // Logged into your app and Facebook.
//               console.log('conected');
//           } else if (response.status === 'not_authorized') {
//               // The person is logged into Facebook, but not your app.
//               console.log('not conected');
//           } else {
//               // The person is not logged into Facebook, so we're not sure if
//               // they are logged into this app or not.
//               console.log('not conectedd');
//           }
        });
    };
};

UI.Facebook.initFacebookApp = function (facebook_appid , facebook_canvas_url)
{
    $("html body").append('<div id="fb-root"></div>');

    if (document.location.hostname == "localhost"
        || document.location.hostname.indexOf("127.0.0.1") == 0
        || document.location.hostname.indexOf("192.168") == 0)
    {
        // Account FB Test
        console.debug('Hien dang su dung App ID Test : 534956693276758');
        FB_APP_ID = facebook_appid = '534956693276758';
    }

    this.facebook_canvas = facebook_canvas_url;
    (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol + '//connect.facebook.net/vi_VN/all.js';
        document.getElementById('fb-root').appendChild(e);
    }());
    window.fbAsyncInit = function() {
        FB.init({appId: facebook_appid, cookie: true,xfbml: true,oauth: true, status:true});
        FB.Canvas.setAutoGrow();
        FB.Canvas.setSize({ width: 810, height: 654 });

        FB.getLoginStatus(function (response)
        {
            // console.log('getLoadPageLoginStatus');
            // console.log(response);

            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().

            if (typeof UI.Facebook.getLoadPageLoginStatus == 'function')
                UI.Facebook.getLoadPageLoginStatus(response);
        });
    };

    function NotInFacebookFrame() {
        return top === self;
    }
    function ReferrerIsFacebookApp() {
        if(document.referrer) {
            return document.referrer.indexOf("apps.facebook.com") != -1;
        }
        return false;
    }
    if (NotInFacebookFrame() || ReferrerIsFacebookApp()) {
        top.location.href = facebook_canvas_url;
    }
};

UI.Facebook.login = function (callBackFunc)
{
    if (typeof UI.Facebook.beforeLogin == 'function')
        UI.Facebook.beforeLogin();

    FB.login(function (response)
    {
        if (response.status === 'connected')
        {
            console.log(response);
            UI.Facebook.accessToken = response.authResponse.accessToken;

            UI.Facebook.getUserPermissions(function (permissions) {
                UI.Facebook.permissions = permissions.data;
            });

            FB.api('/me?fields=id,name,email,birthday,gender,picture.width(160).height(160)', function (response)
            {
                UI.Facebook.userInfo = response;

                console.debug(UI.Facebook.userInfo);
                FB.api(
                    "/me/friends",
                    function (data) {
                        if (data && !data.error) {
                            console.log('Friend count = ', data.data.length);
                            UI.Facebook.totalFriends = data.data.length;
                        }
                        if (callBackFunc != null)
                            callBackFunc(response);
                        else
                        {
                            if (typeof UI.Facebook.afterLogin == 'function')
                                UI.Facebook.afterLogin(response);
                        }
                    }
                );

            });
        }
        else
        {
            if (typeof UI.Facebook.loginError == 'function')
                UI.Facebook.loginError(response);
            else
                console.debug(response);
            // alert('Login FB Error');

        }
    }, { scope: 'email, user_friends, publish_actions, user_birthday, read_stream' });
};

UI.Facebook.loginWithCallbackLink = function (postDataLink, sucessFunc)
{
    UI.Facebook.login(function (fbUserInfo) {
        console.debug(fbUserInfo);
        $.post(postDataLink, {"fbid": fbUserInfo.id, "username": fbUserInfo.username, "fullname": fbUserInfo.name, "email": fbUserInfo.email, "birthday": fbUserInfo.birthday, "gender": fbUserInfo.gender, "accesstoken": UI.Facebook.accessToken, "totalFriends" : UI.Facebook.totalFriends}, function (respone) {
            UI.Log.show('Thong tin tra ve sau khi callBackLink');
            UI.Log.show(respone);
            UI.Log.show(JSON.parse(respone));
            if (sucessFunc != null)
                sucessFunc(JSON.parse(respone));
            else
                location.reload();
        });
    });
}

UI.Facebook.logout = function ()
{
    FB.logout(function ()
    {
        document.location.reload();
        if (typeof UI.Facebook.afterLogout == 'function')
            UI.Facebook.afterLogout();
        else
            document.location.reload();
    });
};

UI.Facebook.getUserInfo = function (afterGetUserInfo)
{
    FB.api('/me?fields=id,name,email,birthday,gender,picture.width(160).height(160)', function (response)
    {
        UI.Facebook.userInfo = response;

        if (afterGetUserInfo != null)
            afterGetUserInfo(response);
    });
};

UI.Facebook.getUserPermissions = function (callback)
{
    FB.api('/me/permissions', function(permissions) {
        // var permission = permissions.data[0];
        // console.debug(permission);
        callback(permissions);
        // for(var i = 0; i < permissions.data.length; i++)
        // {
        //  var obj = permissions.data[i];
        //  console.debug(obj.permission + ' -- ' + obj.status);
        // }
    });
};

UI.Facebook.postPhotoFacebook = function (picture , type)
{
    FB.api('/me/photos', 'post', { url: picture }, function (result) {
        var obj = document.getElementById("my_flash");
        if (result.id != 'undefined' && result.id != '') {
            setTimeout(function () {
                if(type == 'cover'){
                    obj.flashChangeCover('https://www.facebook.com/profile.php?preview_cover=' + result.id);
                }else if(type == 'avatar'){
                    obj.flashChangeAvatar('https://www.facebook.com/photo.php?fbid=' + result.id + '&makeprofile=1');
                }
            }, 500);
        }
        else {
            obj.flashChangeCancel('Lỗi kết nối với Facebook , hoặc bạn không cho phép đăng ảnh lên tường của bạn.');
        }
    });
};

UI.Facebook.shareFacebook = function (publish_data , callback)
{
    FB.login(function (response) {
        if (response.status === 'connected')
        {
            var accessToken = response.authResponse.accessToken;
            //var publish_data = {
            //  'message': '',
            //  'name': 'name',
            //  'picture': img,
            //  'link': facebook_canvas + "&app_data=" + photo_id,
            //  'description': 'description'
            // }
            FB.api('/me/feed', 'post', publish_data, function (response) {
                callback(response);
            });
        }
        else {
            callback(response);
        }
    }, { scope: 'email, user_friends, publish_actions, user_birthday, read_stream' });
};

UI.Facebook.shareFacebook_old = function (img , photo_id)
{
    FB.login(function (response) {
        if (response.status === 'connected') {
            var accessToken = response.authResponse.accessToken;
            FB.api('/me?fields=id,name,username,email,birthday,gender', function (result) {
                var publish = {
                    'message': '',
                    'name': "CÙNG SAMSUNG VUI TẾT DIỆU KỲ",
                    'picture': img,
                    'link': facebook_canvas + "&app_data=" + photo_id,
                    'description': result.name + " vừa tham gia Samsung Galaxy Quà Tết Diệu Kỳ. Hãy truy cập "+facebook_canvas +" và bình chọn cho Ảnh bìa của "+result.name+" để bạn ấy có cơ h sở hữu những giải thưởng giá trị từ Samsung. Cùng Samsung đón mùa lễ hội đầy may mắn!"
                };
                $.post(webRoot + "member/login-social", { "fbid": result.id, "username": result.username, "fullname": result.name, "email": result.email, "birthday": result.birthday, "gender": result.gender, "accesstoken": accessToken }, function (respone) {
                    FB.api('/me/feed', 'post', publish, function (response) {
                        var obj = document.getElementById("my_flash");
                        if (!response || response.error) {
                            obj.sharePhotoCancel();
                        } else {
                            obj.sharePhotoComplete();
                        }
                    });
                }, "json");
            });
        }
        else {
            var obj = document.getElementById("my_flash");
            obj.sharePhotoCancel();
        }
    }, { scope: 'email, user_friends, publish_actions, user_birthday, read_stream' });
};

UI.Facebook.shareLink = function (caption_data, link_data, callback)
{
    /*
     Rat de bi cache fb phai tao link co title de phan biet
     link: siteUrl + '/index.php/title/how-to-clear-the-facebook-share-cache-or-update-a/id/'+photo_id,
     */
    // Share link
    FB.ui({
        method: 'feed',
        //link: siteUrl + '/samsung-spirit/index.php/home/share-facebook/cover/'+photo_id,
        link: link_data,
        caption: caption_data
    }, function(response){
        callback(response);
    });
};

//------------------------------------------------------------------------------------------------------------

UI.GA = {identify_people: null, ga_page: null,  ga_event: null, ga_content: null};

UI.GA.init = function (account)
{
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date(); a = s.createElement(o),
            m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
    ga('create', account, 'auto');
    ga('send', 'pageview');

    // -- Show debug content --
    window.ga_debug = {trace: true};

    $('.ga_tracking').bind('click',function()
    {
        // var page = $(this).attr('rel');
        // alert(page);
        // return;

        if (typeof UI.GA.TrackingObjectClick == 'function')
            UI.GA.TrackingObjectClick($(this));
    });

    $('.ga_tracking_auto').bind('click',function()
    {
        ga_page = $(this).data('ga-page');
        ga_event = $(this).data('ga-event');
        ga_content = $(this).data('ga-content');
        UI.GA.ga_post(ga_page, ga_event, ga_content);
    })
};

UI.GA.startPeopleIdentify = function (identify_key, people_info)
{
    if(!UI.Cookie.get(identify_key))
    {
        identify = "Anonymous";
        UI.GA.identify_people = identify;
    }else
    {
        identify = UI.Cookie.get('name');
        identify = "User logined [" + identify + "]";
        UI.GA.identify_people = identify;
    }
};

UI.GA.ga_post = function (ga_page, ga_event, ga_content)
{
    if (ga_page == null)
        ga_page = UI.GA.ga_page;

    if (ga_event == null)
        ga_event = UI.GA.ga_event;

    if (ga_content == null)
        ga_content = UI.GA.ga_content;

    var currentDate = new Date();
    var stringDate = (currentDate.getMonth() + 1) + "/" + currentDate.getDate() + "/" + currentDate.getFullYear();

    // -- Neu chi co function (ga_page, ga_content) thi event la ten dinh danh --
    if (ga_content == undefined)
    {
        ga_content = UI.GA.parseHashToString(ga_event);
        ga_event = UI.GA.identify_people + " - " + stringDate;
    }
    else
    {
        if (ga_event.length > 0)
            ga_event = "[" + ga_event + "] - ";

        ga_content = ga_event + UI.GA.parseHashToString(ga_content);
        ga_event = UI.GA.identify_people + " - " + stringDate;
    }

    ga('send', 'event', ga_page, ga_event, ga_content);
    UI.Log.show('[GA Tracking] ga_page : ' + ga_page + ' - ga_event : ' + ga_event + ' - ga_content : ' + ga_content);
};

UI.GA.parseHashToString = function (data)
{
    strData = data;
    if (typeof data === 'object')
    {
        strData = '';
        for (var key in data)
            strData += '['+key+']='+data[key]+'; ';
    }
    return strData;
};

//------------------------------------------------------------------------------------------------------------

UI.MP = { mp_page: null,  mp_event: null, mp_content: null};

UI.MP.init = function (account)
{
    (function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");
        for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]);
    mixpanel.init(account);
};

UI.MP.startPeopleIdentify = function (identify_key, people_info) // ('socialUid', {"name": getCookie('name'), "login": "yes"})
{
    if(!UI.Cookie.get(identify_key))
    {
        if(!UI.Cookie.get("UI.MP.trackMixpanel"))
        {
            identify = "Anonymous - [" + (new Date()).toLocaleString() +']';
            // console.log("New identify : " + identify);
            UI.Cookie.set("UI.MP.trackMixpanel", identify);
            mixpanel.identify(identify);
            mixpanel.people.set({
                "name": identify
            });
        }
        else
        {
            identify = UI.Cookie.get('UI.MP.trackMixpanel');
            mixpanel.identify(identify);
            mixpanel.people.set({
                "name": identify
            });
        }
    }else
    {
        // -- Remove cookie : UI.GA.trackMixpanel --
        UI.Cookie.remove("UI.MP.trackMixpanel");
        identify = UI.Cookie.get(identify_key);
        mixpanel.identify(identify);

        if (people_info == undefined)
        {
            var name = UI.Cookie.get('name');
            if (name == null || name == undefined)
                name = "User logined";
            people_info = { "name": name, "login": "yes"};
            mixpanel.people.set(people_info);
            return;
        }
        UI.Log.show(people_info);
        // mixpanel.people.append(people_info);
        mixpanel.people.set(people_info);
    }
};

// UI.MP.mp_post = function (content, content_object)
UI.MP.mp_post = function (page, content, content_object)
{
    UI.Log.show(content_object);

    //if (content_object != undefined) {
    //    if (typeof content_object !== 'object')
    //        content_object = {'_default value': content_object};
    //}

    //mixpanel.track(page, content_object);

    //mixpanel.track(content, content_object);

    if (content_object != undefined) {
        if (typeof content_object !== 'object')
            content_object = {'Value': content_object};
        mixpanel.track(content, content_object);
    }
    else
        mixpanel.track(content);
    UI.Log.show('MP Tracking : ' + page + ' <==> ' + content);
};

//------------------------------------------------------------------------------------------------------------

// -- Default la GA
UI.Tracking = { identify_key:null, ga_account:null, mp_account:null ,page: null, content: null };

UI.Tracking.init = function (ga_account, mp_account)
{
    if (UI.Framework.testSite())
    {
        ga_account = GA_ID_TEST;
        if (mp_account != undefined)
            mp_account = MP_ID_TEST;

        UI.Log.show("Test GA : " + ga_account);
        UI.Log.show("Test MP : " + mp_account);
    }

    UI.Tracking.ga_account = ga_account;
    UI.GA.init(ga_account);
    if (mp_account != undefined)
    {
        UI.Tracking.mp_account = mp_account;
        UI.MP.init(mp_account);
    }

    $('.ui_tracking').bind('click',function()
    {
        // var page = $(this).attr('rel');
        // alert(page);
        // return;

        if (typeof UI.Tracking.TrackingObjectClick == 'function')
            UI.Tracking.TrackingObjectClick($(this));
    });

    $('.ui_tracking_auto').bind('click',function()
    {
        // <a class="ui_tracking_auto" onclick="" data-tracking-page="page" data-tracking-content="content"></a>
        tracking_page = $(this).data('tracking-page');
        tracking_content = $(this).data('tracking-content');
        UI.Log.show(tracking_page + " ---- " + tracking_content);
        if (tracking_page == null)
            UI.Tracking.post(tracking_content);
        else
            UI.Tracking.post(tracking_page, tracking_content);
    })

};

UI.Tracking.startPeopleIdentify = function (identify_key)
{
    if (UI.Tracking.identify_key == null)
        UI.Tracking.identify_key = identify_key;

    UI.GA.startPeopleIdentify(UI.Tracking.identify_key);

    if (UI.Tracking.mp_account != null)
        UI.MP.startPeopleIdentify(UI.Tracking.identify_key);
};

// UI.Tracking.post = function (track_page, track_content)
// {
//   if (track_content == undefined)
//   {
//     track_content = track_page;
//     track_page = UI.Tracking.page;
//   }

//   UI.GA.ga_post(track_page, track_content);

//   if (UI.Tracking.mp_account != null)
//     // UI.MP.mp_post(track_content);
//     UI.MP.mp_post(track_content);

// }

UI.Tracking.post = function (track_event, track_content, track_page)
{
    if (track_page == undefined)
    {
        track_page = UI.Tracking.page;
    }

    UI.GA.ga_post(track_page, track_event, track_content);

    if (UI.Tracking.mp_account != null || mixpanel != null) {
        // UI.MP.mp_post(track_content);
        UI.MP.mp_post(track_page, track_event, track_content);
    }

};

//------------------------------------------------------------------------------------------------------------
// http://mattkruse.com/javascript/date/source.html
UI.DateTime = {};
var MONTH_NAMES=['January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];var DAY_NAMES=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
UI.DateTime.LZ = function (x){return(x<0||x>9?"":"0")+x};
UI.DateTime.isDate = function (val,format){var date=UI.DateTime.getDateFromFormat(val,format);if(date==0){return false;}return true;};
UI.DateTime.compareDates = function (date1,dateformat1,date2,dateformat2){var d1=UI.DateTime.getDateFromFormat(date1,dateformat1);var d2=UI.DateTime.getDateFromFormat(date2,dateformat2);if(d1==0 || d2==0){return -1;}else if(d1 > d2){return 1;}return 0;};
UI.DateTime.formatDate = function (date,format){format=format+"";var result="";var i_format=0;var c="";var token="";var y=date.getYear()+"";var M=date.getMonth()+1;var d=date.getDate();var E=date.getDay();var H=date.getHours();var m=date.getMinutes();var s=date.getSeconds();var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;var value={};if(y.length < 4){y=""+(y-0+1900);}value["y"]=""+y;value["yyyy"]=y;value["yy"]=y.substring(2,4);value["M"]=M;value["MM"]=UI.DateTime.LZ(M);value["MMM"]=MONTH_NAMES[M-1];value["NNN"]=MONTH_NAMES[M+11];value["d"]=d;value["dd"]=UI.DateTime.LZ(d);value["E"]=DAY_NAMES[E+7];value["EE"]=DAY_NAMES[E];value["H"]=H;value["HH"]=UI.DateTime.LZ(H);if(H==0){value["h"]=12;}else if(H>12){value["h"]=H-12;}else{value["h"]=H;}value["hh"]=UI.DateTime.LZ(value["h"]);if(H>11){value["K"]=H-12;}else{value["K"]=H;}value["k"]=H+1;value["KK"]=UI.DateTime.LZ(value["K"]);value["kk"]=UI.DateTime.LZ(value["k"]);if(H > 11){value["a"]="PM";}else{value["a"]="AM";}value["m"]=m;value["mm"]=UI.DateTime.LZ(m);value["s"]=s;value["ss"]=UI.DateTime.LZ(s);while(i_format < format.length){c=format.charAt(i_format);token="";while((format.charAt(i_format)==c) &&(i_format < format.length)){token += format.charAt(i_format++);}if(value[token] != null){result=result + value[token];}else{result=result + token;}}return result;};
function _isInteger(val){var digits="1234567890";for(var i=0;i < val.length;i++){if(digits.indexOf(val.charAt(i))==-1){return false;}}return true;}
function _getInt(str,i,minlength,maxlength){for(var x=maxlength;x>=minlength;x--){var token=str.substring(i,i+x);if(token.length < minlength){return null;}if(_isInteger(token)){return token;}}return null;}
UI.DateTime.getDateFromFormat = function (val,format){val=val+"";format=format+"";var i_val=0;var i_format=0;var c="";var token="";var token2="";var x,y;var now=new Date();var year=now.getYear();var month=now.getMonth()+1;var date=1;var hh=now.getHours();var mm=now.getMinutes();var ss=now.getSeconds();var ampm="";while(i_format < format.length){c=format.charAt(i_format);token="";while((format.charAt(i_format)==c) &&(i_format < format.length)){token += format.charAt(i_format++);}if(token=="yyyy" || token=="yy" || token=="y"){if(token=="yyyy"){x=4;y=4;}if(token=="yy"){x=2;y=2;}if(token=="y"){x=2;y=4;}year=_getInt(val,i_val,x,y);if(year==null){return 0;}i_val += year.length;if(year.length==2){if(year > 70){year=1900+(year-0);}else{year=2000+(year-0);}}}else if(token=="MMM"||token=="NNN"){month=0;for(var i=0;i<MONTH_NAMES.length;i++){var month_name=MONTH_NAMES[i];if(val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()){if(token=="MMM"||(token=="NNN"&&i>11)){month=i+1;if(month>12){month -= 12;}i_val += month_name.length;break;}}}if((month < 1)||(month>12)){return 0;}}else if(token=="EE"||token=="E"){for(var i=0;i<DAY_NAMES.length;i++){var day_name=DAY_NAMES[i];if(val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()){i_val += day_name.length;break;}}}else if(token=="MM"||token=="M"){month=_getInt(val,i_val,token.length,2);if(month==null||(month<1)||(month>12)){return 0;}i_val+=month.length;}else if(token=="dd"||token=="d"){date=_getInt(val,i_val,token.length,2);if(date==null||(date<1)||(date>31)){return 0;}i_val+=date.length;}else if(token=="hh"||token=="h"){hh=_getInt(val,i_val,token.length,2);if(hh==null||(hh<1)||(hh>12)){return 0;}i_val+=hh.length;}else if(token=="HH"||token=="H"){hh=_getInt(val,i_val,token.length,2);if(hh==null||(hh<0)||(hh>23)){return 0;}i_val+=hh.length;}else if(token=="KK"||token=="K"){hh=_getInt(val,i_val,token.length,2);if(hh==null||(hh<0)||(hh>11)){return 0;}i_val+=hh.length;}else if(token=="kk"||token=="k"){hh=_getInt(val,i_val,token.length,2);if(hh==null||(hh<1)||(hh>24)){return 0;}i_val+=hh.length;hh--;}else if(token=="mm"||token=="m"){mm=_getInt(val,i_val,token.length,2);if(mm==null||(mm<0)||(mm>59)){return 0;}i_val+=mm.length;}else if(token=="ss"||token=="s"){ss=_getInt(val,i_val,token.length,2);if(ss==null||(ss<0)||(ss>59)){return 0;}i_val+=ss.length;}else if(token=="a"){if(val.substring(i_val,i_val+2).toLowerCase()=="am"){ampm="AM";}else if(val.substring(i_val,i_val+2).toLowerCase()=="pm"){ampm="PM";}else{return 0;}i_val+=2;}else{if(val.substring(i_val,i_val+token.length)!=token){return 0;}else{i_val+=token.length;}}}if(i_val != val.length){return 0;}if(month==2){if( ((year%4==0)&&(year%100 != 0) ) ||(year%400==0) ){if(date > 29){return 0;}}else{if(date > 28){return 0;}}}if((month==4)||(month==6)||(month==9)||(month==11)){if(date > 30){return 0;}}if(hh<12 && ampm=="PM"){hh=hh-0+12;}else if(hh>11 && ampm=="AM"){hh-=12;}var newdate=new Date(year,month-1,date,hh,mm,ss);return newdate.getTime();};
UI.DateTime.parseDate = function (val){var preferEuro=(arguments.length==2)?arguments[1]:false;generalFormats=['y-M-d','MMM d, y','MMM d,y','y-MMM-d','d-MMM-y','MMM d'];monthFirst=['M/d/y','M-d-y','M.d.y','MMM-d','M/d','M-d'];dateFirst =['d/M/y','d-M-y','d.M.y','d-MMM','d/M','d-M'];var checkList=['generalFormats',preferEuro?'dateFirst':'monthFirst',preferEuro?'monthFirst':'dateFirst'];var d=null;for(var i=0;i<checkList.length;i++){var l=window[checkList[i]];for(var j=0;j<l.length;j++){d=UI.DateTime.getDateFromFormat(val,l[j]);if(d!=0){return new Date(d);}}}return null;};

