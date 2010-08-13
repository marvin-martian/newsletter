"use strict";

Ext.ns("TYPO3.Newsletter.Store");

/**
 * @class TYPO3.Newsletter.Store.Bootstrap
 * @namespace TYPO3.Newsletter.Store
 * @extends TYPO3.Newsletter.Application.AbstractBootstrap
 *
 * Bootrap store
 *
 * $Id$
 */
TYPO3.Newsletter.Store.Bootstrap = Ext.apply(new TYPO3.Newsletter.Application.AbstractBootstrap(), {
	initialize: function() {
		TYPO3.Newsletter.Application.on('TYPO3.Newsletter.Application.afterBootstrap', this.initStore, this);
	},
	initStore: function() {
		var api;
		for (api in Ext.app.ExtDirectAPI) {
			if (Ext.app.ExtDirectAPI[api]) {
				Ext.Direct.addProvider(Ext.app.ExtDirectAPI[api]);
			}
		}
//		TYPO3.Newsletter.LogStore2.doRequest();

		TYPO3.Newsletter.Store.NewsletterList = TYPO3.Newsletter.Store.initNewsletterList();
		TYPO3.Newsletter.Store.Statistic = TYPO3.Newsletter.Store.initStatistic();
		TYPO3.Newsletter.Store.OverviewPieChart = TYPO3.Newsletter.Store.initOverviewPieChart();
		TYPO3.Newsletter.Store.ClickedLink = TYPO3.Newsletter.Store.initClickedLink();
		TYPO3.Newsletter.Store.SentEmail = TYPO3.Newsletter.Store.initSentEmail();
	}
});

TYPO3.Newsletter.Application.registerBootstrap(TYPO3.Newsletter.Store.Bootstrap);