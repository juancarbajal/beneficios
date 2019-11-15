angular.module('mainApp', [])
    .controller('MainController', function ($scope, $window, $http, MainService) {
        var isInvalidForm,
            wasNotRegisterBefore;

        $scope.showForms = true;
        $scope.formValid = false;
        $scope.disabledFields = {};
        $scope.user = {};
        $scope.banner = "";
        $scope.popup = "";

        if (getAllUrlParams(location.url)["name"] && getAllUrlParams(location.url)["name"].trim()) {
            $scope.user.name = getAllUrlParams(location.url)["name"];
            $scope.disabledFields.name = true;
        }

        if (getAllUrlParams(location.url)["document"] && getAllUrlParams(location.url)["document"].trim()) {
            $scope.user.document = getAllUrlParams(location.url)["document"];
            $scope.disabledFields.document = true;
        }

        if (getAllUrlParams(location.url)["email"] && getAllUrlParams(location.url)["email"].trim()) {
            $scope.user.email = getAllUrlParams(location.url)["email"];
            $scope.disabledFields.email = true;
        }

        if (getAllUrlParams(location.url)["telephone"] && getAllUrlParams(location.url)["telephone"].trim()) {
            $scope.user.telephone = getAllUrlParams(location.url)["telephone"];
            $scope.disabledFields.telephone = true;
        }

        if (getAllUrlParams(location.url)["specialist"] && getAllUrlParams(location.url)["specialist"].trim()) {
            $scope.user.specialist = getAllUrlParams(location.url)["specialist"];
            $scope.disabledFields.specialist = true;
        }

        $scope.user.members = [];
        $scope.newMember = {showNameError: false, showTelephoneError: false};

        $scope.verifyIfUserWasRegistered = function ($event) {
            if ($event.target.value.length <= 8) {
                // if(!isNaN(String.fromCharCode($event.keyCode))){
                MainService.verifyIfUserWasRegistered($event.target.value)
                    .then(function (response) {
                        if (response.data.result !== "warning") {
                            var data = response.data;

                            if (data.name) {
                                $scope.user.name = data.name;
                                $scope.disabledFields.name = true;
                            }

                            if (data.email) {
                                $scope.user.email = data.email;
                                $scope.disabledFields.email = true;
                            }

                            if (data.specialist) {
                                $scope.user.specialist = data.specialist;
                                $scope.disabledFields.specialist = true;
                            }

                            if (data.telephone) {
                                $scope.user.telephone = data.telephone;
                                $scope.disabledFields.telephone = true;
                            }
                        } else {
                            $scope.disabledFields = {};
                            $scope.user.name = "";
                            $scope.user.email = "";
                            $scope.user.specialist = "";
                            $scope.user.telephone = "";
                        }
                    });
            }
        };

        $scope.isAllIsOk = function (formValid) {
            return $scope.user.members.length > 0 && formValid;
        };

        $scope.addMember = function () {
            $scope.showErrorNameRegistered = false;

            if (!wasNotRegisterBefore($scope.user.members, $scope.newMember)) {
                $scope.showErrorNameRegistered = true;
                return true;
            }

            if (!isInvalidForm()) {
                MainService.wasNotRegisterBeforeByOtherClient($scope.newMember)
                    .then(function (response) {
                        if (response.data.result === "success") {
                            $scope.showErrorName2Registered = false;
                            if ($scope.newMember.name && $scope.newMember.telephone) {
                                $scope.user.members.push($scope.newMember);
                                $scope.newMember = {};
                            }
                        } else {
                            $scope.showErrorName2Registered = true;
                        }
                    })
            }
        };

        isInvalidForm = function () {
            if (!$scope.newMember.name) $scope.newMember.showNameError = true;
            if (!$scope.newMember.telephone) $scope.newMember.showTelephoneError = true;

            return $scope.newMember.showNameError && $scope.newMember.showTelephoneError;
        };

        wasNotRegisterBefore = function (list, object) {
            var newList = list.filter(function (item) {
                return item.name === object.name;
            });

            return newList.length === 0;
        };

        $scope.deleteItem = function (index) {
            $scope.user.members.splice(index, 1);
        };

        $scope.sendAllData = function () {
            $('.btn-send').text("Enviando ...");
            MainService.saveDataClient($scope.user)
                .then(function (response) {
                    if (response.data.result === "success") {
                        var affiliates = $scope.user.members;
                        for (var i in affiliates) {
                            var affiliate = affiliates[i];

                            affiliate.client_id = response.data.id;
                            MainService.saveDataAffiliate(affiliate);
                        }

                        if (response.data.new) {
                            MainService.sendNotificationClient($scope.user);
                        }

                        MainService.sendNotificationAdmin($scope.user)
                            .then(function (response) {
                                $scope.showForms = false;
                            });
                    } else {
                        alert(response.data.message);
                        $('.btn-send').text("Enviar Listado");
                    }
                })
        };

        $scope.loadBannerImage = function () {
            MainService.loadBanner()
                .then(function (response) {
                    if (response.data.result !== "warning") {
                        var data = response.data;
                        if (data[0].banner) {
                            $scope.banner = $scope.landing + data[0].banner.replace(/\"/g, '');
                            var myEl = angular.element(document.querySelector('#link_web'));
                            if (typeof data[1] !== 'undefined') {
                                if (data[1].url !== '') {
                                    myEl.attr('href', data[1].url);
                                }
                            }
                        }

                        if (typeof data[2] !== 'undefined') {
                            $scope.popup = $scope.landing + data[2].popup.replace(/\"/g, '');
                            if ($scope.popup !== "") {
                                $('#myModal').modal('show');
                            }
                        }
                    }
                })
        };

        function getAllUrlParams(url) {
            // get query string from url (optional) or window
            var queryString = url ? url.split('?')[1] : decodeURIComponent(window.location.search.slice(1));

            // we'll store the parameters here
            var obj = {};

            // if query string exists
            if (queryString) {

                // stuff after # is not part of query string, so get rid of it
                queryString = queryString.split('#')[0];

                // split our query string into its component parts
                var arr = queryString.split('&');

                for (var i = 0; i < arr.length; i++) {
                    // separate the keys and the values
                    var a = arr[i].split('=');

                    // in case params look like: list[]=thing1&list[]=thing2
                    var paramNum = undefined;
                    var paramName = a[0].replace(/\[\d*\]/, function (v) {
                        paramNum = v.slice(1, -1);
                        return '';
                    });

                    // set parameter value (use 'true' if empty)
                    var paramValue = typeof(a[1]) === 'undefined' ? true : a[1];

                    // (optional) keep case consistent
                    paramName = paramName.toLowerCase();
                    paramValue = paramValue.toLowerCase();

                    // if parameter name already exists
                    if (obj[paramName]) {
                        // convert value to array (if still string)
                        if (typeof obj[paramName] === 'string') {
                            obj[paramName] = [obj[paramName]];
                        }
                        // if no array index number specified...
                        if (typeof paramNum === 'undefined') {
                            // put the value on the end of the array
                            obj[paramName].push(paramValue);
                        }
                        // if array index number specified...
                        else {
                            // put the value at that index number
                            obj[paramName][paramNum] = paramValue;
                        }
                    }
                    // if param name doesn't exist yet, set it
                    else {
                        obj[paramName] = paramValue;
                    }
                }
            }

            return obj;
        }
    })
    .constant('constants', {
        BASE_URL: window.location.origin + '//app/api.php'
    })
    .service('MainService', function ($http, constants) {
        var baseUrl = constants.BASE_URL;

        function _verifyIfUserWasRegistered(document) {
            return $http.get(baseUrl + '?op=5&document=' + document);
        }

        function _saveDataClient(client) {
            return $http({
                method: 'POST',
                url: baseUrl + '/?op=1',
                data: $.param(client),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }

        function _wasNotRegisterBeforeByOtherClient(affiliate) {
            return $http({
                method: 'POST',
                url: baseUrl + '/?op=3',
                data: $.param(affiliate),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }

        function _saveDataAffiliate(affiliate) {
            return $http({
                method: 'POST',
                url: baseUrl + '/?op=2',
                data: $.param(affiliate),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }

        function _sendNotificationClient(client) {
            return $http({
                method: 'POST',
                url: baseUrl + '/?op=6',
                data: $.param(client),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }

        function _sendNotificationAdmin(client) {
            return $http({
                method: 'POST',
                url: baseUrl + '/?op=7',
                data: $.param(client),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }

        function _loadBanner() {
            return $http.get(baseUrl + '?op=8');
        }

        return {
            verifyIfUserWasRegistered: _verifyIfUserWasRegistered,
            saveDataClient: _saveDataClient,
            saveDataAffiliate: _saveDataAffiliate,
            sendNotificationClient: _sendNotificationClient,
            sendNotificationAdmin: _sendNotificationAdmin,
            wasNotRegisterBeforeByOtherClient: _wasNotRegisterBeforeByOtherClient,
            loadBanner: _loadBanner
        }
    });
