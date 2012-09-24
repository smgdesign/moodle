YUI.add('moodle-tool_jsunit-jsunit', function(Y) {

    var JSUNITNAME = 'tool_jsunit_jsunit';

    var JSUNIT = function() {
        JSUNIT.superclass.constructor.apply(this, arguments);
    };

    Y.extend(JSUNIT, Y.Base, {

        /**
         * @var Y.Test.Suite
         */
        suite : null,

        /**
         * Creates the test suite and adds the tests cases
         */
        initializer : function(params) {

            if (this.get('testcases').length == 0) {
                Y.one('#junit_test_results').append(M.str.tool_jsunit.notests);

            } else {

                // Adding the test cases
                this.suite = new Y.Test.Suite("jsunit");
                for (var i = 0; i < this.get('testcases').length; i++) {

                    var testcase = eval(this.get('testcases')[i]);
                    this.suite.add(testcase);
                }

                // Execute the test suite
                var testrunner = Y.Test.Runner;
                testrunner.add(this.suite);
                testrunner.run();

                // Get results
                testrunner.on('complete', this.output_results, this);
            }
        },

        /**
         * Fills the results box with the test runner results
         */
        output_results : function(e) {

            /**
             * Returns the object data without sub tests / sub test cases of the object
             */
            var get_test_data = function(obj) {

                if (obj.name == undefined) {
                    return false;
                }

                var data = new Object();
                data.name = obj.name;
                data.passed = obj.passed;
                data.failed = obj.failed;
                data.result = obj.result;
                data.message = obj.message;
                return data;
            };

            /**
             * Returns the sub tests / sub test cases of the object
             */
            var get_tests = function(obj) {

                var objects = new Array();
                if (obj instanceof Object) {
                    for (var key in obj) {
                        if (key != 'name' && key != 'passed' && key != 'failed' && key != 'errors' && key != 'ignored' && key != 'result' &&
                                key != 'total' && key != 'duration' && key != 'type' && key != 'timestamp' && key != 'message') {
                            objects.push(obj[key]);
                        }
                    }
                }
                return objects;
            };

            var output = Y.one('#junit_test_results');
            var results = Y.JSON.parse(Y.Test.Runner.getResults(Y.Test.Format.JSON));

            // General info
            var content = Y.Node.create('<ul></ul>')
                .append(Y.Node.create('<li>'+M.str.tool_jsunit.passed+': '+results.passed+'</li>'))
                .append(Y.Node.create('<li>'+M.str.tool_jsunit.failed+': '+results.failed+'</li>'))
                .append(Y.Node.create('<li>'+M.str.tool_jsunit.ignored+': '+results.ignored+'</li>'))
                .append(Y.Node.create('<li>'+M.str.tool_jsunit.total+': '+results.total+'</li>'));

            // Each test case info
            for (var testcase in results) {

                var testcaseinfo = get_test_data(results[testcase]);
                var tests = get_tests(results[testcase]);

                if (testcaseinfo) {
                    var testcasecontent = Y.Node.create('<ul></ul>')
                        .append(Y.Node.create('<li>'+M.str.tool_jsunit.name+': '+testcaseinfo.name+'</li>'))
                        .append(Y.Node.create('<li>'+M.str.tool_jsunit.passed+': '+testcaseinfo.passed+'</li>'))
                        .append(Y.Node.create('<li>'+M.str.tool_jsunit.failed+': '+testcaseinfo.failed+'</li>'));
                }

                if (tests.length > 0) {
                    for (var i = 0; i < tests.length; i++) {

                        var testcontent = Y.Node.create('<ul></ul>')
                            .append(Y.Node.create('<li>'+M.str.tool_jsunit.name+': '+tests[i].name+'</li>'))
                            .append(Y.Node.create('<li class="'+tests[i].result+'">'+M.str.tool_jsunit.result+': '+tests[i].result+'</li>'))
                            .append(Y.Node.create('<li>'+M.str.tool_jsunit.message+': '+tests[i].message+'</li>'));

                        // Adding the test to the testcase
                        testcasecontent.append(testcontent);
                    }
                }

                content.append(testcasecontent);
            }

            output.append(content);
        },

    }, {
        NAME : JSUNITNAME,
        ATTRS : {
            testcases: {},
            output_html : {
                validator : Y.Lang.isBoolean,
                value : true
            },
        }
    });

    M.tool_jsunit = M.tool_jsunit || {};
    M.tool_jsunit.init = function(params) {
        return new JSUNIT(params);
    }

}, '@VERSION@', {
    requires:['test', 'json-parse']
});
