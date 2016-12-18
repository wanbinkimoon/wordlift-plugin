var config = {

    //
    // =================
    // Service Providers
    // =================
    // WebdriverIO supports Sauce Labs, Browserstack, and Testing Bot (other cloud providers
    // should work too though). These services define specific user and key (or access key)
    // values you need to put in here in order to connect to these services.
    //
    // user: process.env.SAUCE_USERNAME,
    // key: process.env.SAUCE_ACCESS_KEY,

    // `sauceConnect` must be false when running in Travis-CI, since Travis-CI is providing SauceConnect.
    // Remember: !undefined === true
    // For a list of Travis provided environment variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables
    sauceConnect: !process.env.CI,

    //
    // ==================
    // Specify Test Files
    // ==================
    // Define which test specs should run. The pattern is relative to the directory
    // from which `wdio` was called. Notice that, if you are calling `wdio` from an
    // NPM script (see https://docs.npmjs.com/cli/run-script) then the current working
    // directory is where your package.json resides, so `wdio` will be called from there.
    //
    specs: [
        './tests/e2e/**/*Spec.js'
    ],
    // Patterns to exclude.
    exclude: [
        // 'path/to/excluded/files'
    ],
    //
    // ============
    // Capabilities
    // ============
    // Define your capabilities here. WebdriverIO can run multiple capabilities at the same
    // time. Depending on the number of capabilities, WebdriverIO launches several test
    // sessions. Within your capabilities you can overwrite the spec and exclude options in
    // order to group specific specs to a specific capability.
    //
    // First, you can define how many instances should be started at the same time. Let's
    // say you have 3 different capabilities (Chrome, Firefox, and Safari) and you have
    // set maxInstances to 1; wdio will spawn 3 processes. Therefore, if you have 10 spec
    // files and you set maxInstances to 10, all spec files will get tested at the same time
    // and 30 processes will get spawned. The property handles how many capabilities
    // from the same test should run tests.
    //
    maxInstances: 10,
    //
    // If you have trouble getting all important capabilities together, check out the
    // Sauce Labs platform configurator - a great tool to configure your capabilities:
    // https://docs.saucelabs.com/reference/platforms-configurator
    //
    capabilities: [
        // // maxInstances can get overwritten per capability. So if you have an in-house Selenium
        // // grid with only 5 firefox instances available you can make sure that not more than
        // // 5 instances get started at a time.
        // maxInstances: 5,
        // name: 'WordLift Cross-Browsing Tests (#' + process.env.TRAVIS_BUILD_NUMBER + ')',
        // 'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
        // build: process.env.TRAVIS_BUILD_NUMBER,

        // If using Open Sauce (https://saucelabs.com/opensauce/),
        // capabilities must be tagged as "public" for the jobs's status
        // to update (failed/passed). If omitted on Open Sauce, the job's
        // status will only be marked "Finished." This property can be
        // be omitted for commercial (private) Sauce Labs accounts.
        // Also see https://support.saucelabs.com/customer/portal/articles/2005331-why-do-my-tests-say-%22finished%22-instead-of-%22passed%22-or-%22failed%22-how-do-i-set-the-status-
        // 'public': true,

        // See http://webdriver.io/guide/usage/multiremote.html
        //
        // See:
        // * https://wiki.saucelabs.com/display/DOCS/Platform+Configurator#/
        // * https://github.com/SeleniumHQ/selenium/wiki/DesiredCapabilities
        // * https://github.com/webdriverio/webdriverio/blob/master/examples/cloudservices/webdriverio.saucelabs.js
        {
            browserName: 'chrome'
        }],
    //
    // ===================
    // Test Configurations
    // ===================
    // Define all options that are relevant for the WebdriverIO instance here
    //
    // By default WebdriverIO commands are executed in a synchronous way using
    // the wdio-sync package. If you still want to run your tests in an async way
    // e.g. using promises you can set the sync option to false.
    sync: true,
    //
    // Level of logging verbosity: silent | verbose | command | data | result | error
    logLevel: 'error',
    //
    // Enables colors for log output.
    coloredLogs: true,
    //
    // Saves a screenshot to a given path if a command fails.
    screenshotPath: './errorShots/',
    //
    // Set a base URL in order to shorten url command calls. If your url parameter starts
    // with "/", then the base url gets prepended.
    baseUrl: 'http://wordpress.localhost',
    //
    // Default timeout for all waitFor* commands.
    waitforTimeout: 10000,
    //
    // Default timeout in milliseconds for request
    // if Selenium Grid doesn't send response
    connectionRetryTimeout: 90000,
    //
    // Default request retries count
    connectionRetryCount: 3,
    //
    // Initialize the browser instance with a WebdriverIO plugin. The object should have the
    // plugin name as key and the desired plugin options as properties. Make sure you have
    // the plugin installed before running any tests. The following plugins are currently
    // available:
    // WebdriverCSS: https://github.com/webdriverio/webdrivercss
    // WebdriverRTC: https://github.com/webdriverio/webdriverrtc
    // Browserevent: https://github.com/webdriverio/browserevent
    // plugins: {
    //     webdrivercss: {
    //         screenshotRoot: 'my-shots',
    //         failedComparisonsRoot: 'diffs',
    //         misMatchTolerance: 0.05,
    //         screenWidth: [320,480,640,1024]
    //     },
    //     webdriverrtc: {},
    //     browserevent: {}
    // },
    //
    // Test runner services
    // Services take over a specific job you don't want to take care of. They enhance
    // your test setup with almost no effort. Unlike plugins, they don't add new
    // commands. Instead, they hook themselves up into the test process.
    services: ['selenium-standalone'],
    //
    // Framework you want to run your specs with.
    // The following are supported: Mocha, Jasmine, and Cucumber
    // see also: http://webdriver.io/guide/testrunner/frameworks.html
    //
    // Make sure you have the wdio adapter package for the specific framework installed
    // before running any tests.
    framework: 'jasmine',
    //
    // Test reporter for stdout.
    // The only one supported by default is 'dot'
    // see also: http://webdriver.io/guide/testrunner/reporters.html
    reporters: ['dot'],

    //
    // Options to be passed to Jasmine.
    jasmineNodeOpts: {
        //
        // Jasmine default timeout
        defaultTimeoutInterval: 10000,
        //
        // The Jasmine framework allows interception of each assertion in order to log the state of the application
        // or website depending on the result. For example, it is pretty handy to take a screenshot every time
        // an assertion fails.
        expectationResultHandler: function (passed, assertion) {
            // do something
        }
    },

    //
    // =====
    // Hooks
    // =====
    // WebdriverIO provides several hooks you can use to interfere with the test process in order to enhance
    // it and to build services around it. You can either apply a single function or an array of
    // methods to it. If one of them returns with a promise, WebdriverIO will wait until that promise got
    // resolved to continue.
    //
    // Gets executed once before all workers get launched.
    onPrepare: function (config, capabilities) {

        console.log('config', config);
        console.log('capabilities', capabilities);

    },
    //
    // Gets executed before test execution begins. At this point you can access all global
    // variables, such as `browser`. It is the perfect place to define custom commands.
    // before: function (capabilities, specs) {
    //
    //     browser.options.baseUrl = 'http://testme.localhost';
    //     console.log('capabilities', capabilities);
    //     console.log('browser', browser);
    //
    // }
    //
    // Hook that gets executed before the suite starts
    // beforeSuite: function (suite) {
    // },
    //
    // Hook that gets executed _before_ a hook within the suite starts (e.g. runs before calling
    // beforeEach in Mocha)
    // beforeHook: function () {
    // },
    //
    // Hook that gets executed _after_ a hook within the suite starts (e.g. runs after calling
    // afterEach in Mocha)
    // afterHook: function () {
    // },
    //
    // Function to be executed before a test (in Mocha/Jasmine) or a step (in Cucumber) starts.
    // beforeTest: function (test) {
    // },
    //
    // Runs before a WebdriverIO command gets executed.
    // beforeCommand: function (commandName, args) {
    // },
    //
    // Runs after a WebdriverIO command gets executed
    // afterCommand: function (commandName, args, result, error) {
    // },
    //
    // Function to be executed after a test (in Mocha/Jasmine) or a step (in Cucumber) starts.
    // afterTest: function (test) {
    // },
    //
    // Hook that gets executed after the suite has ended
    // afterSuite: function (suite) {
    // },
    //
    // Gets executed after all tests are done. You still have access to all global variables from
    // the test.
    // after: function (result, capabilities, specs) {
    // },
    //
    // Gets executed after all workers got shut down and the process is about to exit. It is not
    // possible to defer the end of the process using a promise.
    // onComplete: function(exitCode) {
    // }
};


// Tweak the configuration if we're in Travis-CI.
if (process.env.CI) {

    // Set the tests' base url.
    const BASE_URL = 'http://localhost';

    // Remove any previously set baseUrl (we use one different URL for each browser).
    delete config.baseUrl;

    // Configure Sauce Labs.
    config.user = process.env.SAUCE_USERNAME;
    config.key = process.env.SAUCE_ACCESS_KEY;

    // Use Sauce.
    config.services = ['sauce'];

    // Add browsers: Firefox, Internet Explorer.
    config.capabilities = [
        {
            desiredCapabilities: {
                // baseUrl: BASE_URL + '/1/',
                browserName: 'chrome',
                version: '54.0',
                platform: 'Windows 10',
                // 'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
                // build: process.env.TRAVIS_BUILD_NUMBER,
                'public': true,
                name: 'WordLift Cross-Browsing Tests (#' + process.env.TRAVIS_BUILD_NUMBER + ')'
            }
        }, {
            desiredCapabilities: {
                // baseUrl: BASE_URL + '/2/',
                browserName: 'firefox',
                version: '50.0',
                platform: 'Windows 10',
                // 'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
                // build: process.env.TRAVIS_BUILD_NUMBER,
                'public': true,
                name: 'WordLift Cross-Browsing Tests (#' + process.env.TRAVIS_BUILD_NUMBER + ')'
            }

        }, {
            desiredCapabilities: {
                // baseUrl: BASE_URL + '/3/',
                browserName: 'internet explorer',
                version: '8.0',
                platform: 'Windows XP',
                // 'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
                // build: process.env.TRAVIS_BUILD_NUMBER,
                'public': true,
                name: 'WordLift Cross-Browsing Tests (#' + process.env.TRAVIS_BUILD_NUMBER + ')'
            }
        }];

    // Set Travis job and build numbers.
    for (var i = 0; i < config.capabilities.length; i++) {
        config.capabilities[i].desiredCapabilities['tunnel-identifier'] = process.env.TRAVIS_JOB_NUMBER;
        config.capabilities[i].desiredCapabilities['build'] = process.env.TRAVIS_BUILD_NUMBER;
        config.capabilities[i].baseUrl = BASE_URL + '/' + (i + 1);
    }

}

exports.config = config;