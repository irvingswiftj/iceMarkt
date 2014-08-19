/**
 * Created by james on 18/08/2014.
 */

var IceMarkt = function () {
    'use strict';

    /**
     *
     * @type {Object}
     */
    var module = {

        '$': null,

        'config': {},

        'templateId': 0,

        'lastOffset': 0,

        'loading': false,

        /**
         * method to display the log section
         * @param logSectionId
         *
         * @return {void}
         *
         * @private
         */
        'displayLog': function (logSectionId) {
            module.$(logSectionId).slideDown();
        },

        /**
         * method to log to our list
         * @param listId
         * @param content
         *
         * @return {void}
         *
         * @private
         */
        'logToList': function(listId, content) {
            var entries = false;
            module.$(content).each(function (key, value) {
                module.$(listId + ' ul').append('<li>' + value + '</li>');
                if (entries === false) {
                    entries = true;
                }
            });

            if (entries) {
                module.displayLog(listId);
            }
        },

        /**
         * method to log our ajax results to screen
         *
         * @param emailStatusObject
         *
         * @return {void}
         *
         * @private
         */
        'logToScreen': function (emailStatusObject) {
            module.logToList('#batchSendSuccessLog', emailStatusObject.sent);
            module.logToList('#batchSendErrorLog', emailStatusObject.failed);
        },

        /**
         * method to get the url for a sending batch ajax call
         *
         *
         * @returns {string}
         *
         * @private
         */
        'getSendBatchUrl': function () {
            return module.config.baseUrl + 'marketing/sendBatch/' + module.templateId +'/' + module.lastOffset;
        },

        /**
         * Add loading state to element given
         *
         * @param element
         *
         * @return {void}
         *
         * @private
         */
        addLoadingState: function (element) {
            module.$(element).button('loading');
        },

        /**
         * Remove loading state to element given
         *
         * @param element
         *
         * @return {void}
         *
         * @private
         */
        removeLoadingState: function (element) {
            module.$(element).button('reset');
        },

        /**
         * Toggle the submit button from being in loading/non-loading states
         *
         * @return {void}
         *
         * @private
         */
        toggleLoadingState: function() {
            var element = '#form_Send';

            if (module.loading === true) {
                module.removeLoadingState(element);
            } else {
                module.addLoadingState(element);
            }

            module.loading = !module.loading;
        },

        /**
         * method to handle successful ajax call
         *
         * @param data
         *
         * @return {void}
         *
         * @private
         */
        'sendBatchDoneAction': function (data) {
            module.lastOffset = module.lastOffset + module.config.batchSize;
            if(data.recipients > 0) {
                module.logToScreen(data.emailStatus);
                module.sendBatch();
            } else {
                module.lastOffset = 0;
                module.toggleLoadingState();
            }
        },

        /**
         * Attach an needed listeners
         *
         * @return {void}
         *
         * @private
         */
        'sendBatch': function () {
            module.$.ajax(module.getSendBatchUrl())
                .done(module.sendBatchDoneAction);
        },

        /**
         * action for submitting a form
         *
         * @return {void}
         *
         * @private
         */
        'onFormSubmitAction': function(event)
        {
            module.templateId = module.$('#form_email_template_id').val();

            module.toggleLoadingState();

            module.sendBatch(module.config.batchSize);

            event.preventDefault();
        },

        /**
         * Attach an needed listeners
         *
         * @return {void}
         *
         * @private
         */
        'initiateListeners': function () {
            module.$('#batchSend').submit(module.onFormSubmitAction);
        }
    };

    /**
     *
     * @type {Object}
     */
    var api = {

        /**
         * Init method
         *
         * @param {Object} $
         * @param {Object} config
         *
         * @return {void}
         *
         * @public
         */
        'init': function ($, config) {
            module.$ = $;
            module.config = config;
            module.initiateListeners();
        }

    };


    return {
        'init': api.init
    };
};