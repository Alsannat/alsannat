var fabricApp = angular.module('example', [
    'common.fabric',
    'common.fabric.utilities',
    'common.fabric.constants'
])

    .controller('ExampleCtrl', ['$scope', 'Fabric', 'FabricConstants', 'Keypress', function($scope, Fabric, FabricConstants, Keypress) {

        $scope.fabric = {};
        $scope.FabricConstants = FabricConstants;

        //
        // Creating Canvas Objects
        // ================================================================
        $scope.addShape = function(path) {
            $scope.fabric.addShape('http://fabricjs.com/assets/15.svg');
        };

        $scope.addImage = function(image) {
            $scope.fabric.addImage('http://127.0.0.1/fa/example/daniel-season-nine.jpg');
        };

        $scope.addImageUpload = function(data) {
            var obj = angular.fromJson(data);
            $scope.addImage(obj.filename);
        };

        //
        // Editing Canvas Size
        // ================================================================
        $scope.selectCanvas = function() {
            $scope.canvasCopy = {
                width: $scope.fabric.canvasOriginalWidth,
                height: $scope.fabric.canvasOriginalHeight
            };
        };

        $scope.setCanvasSize = function() {
            $scope.fabric.setCanvasSize($scope.canvasCopy.width, $scope.canvasCopy.height);
            $scope.fabric.setDirty(true);
            delete $scope.canvasCopy;
        };

        //
        // Init
        // ================================================================
        $scope.init = function() {
            $scope.fabric = new Fabric({
                JSONExportProperties: FabricConstants.JSONExportProperties,
                textDefaults: FabricConstants.textDefaults,
                shapeDefaults: FabricConstants.shapeDefaults,
                json: {}
            });
            $scope.fabric.addImage('http://127.0.0.1/fa/example/daniel-season-nine.jpg');

            $scope.fabric.addText('To : Nguyen Thuy Duong');
            $scope.fabric.setBackgroundImage('http://127.0.0.1/magento2.1/pub/static/frontend/Magento/luma/en_US/Magenest_GiftCard/images/bg2.png');

           // var object = new FabricWindow.Text(str, self.textDefaults);
            //object.id = self.createId();

            //self.addObjectToCanvas(object);

           // $scope.fabric.addText('To : Nguyen Thuy Duong');
        };

        $scope.$on('canvas:created', $scope.init);

        Keypress.onSave(function() {
            $scope.updatePage();
        });

    }]);