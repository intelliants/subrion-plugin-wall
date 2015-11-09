Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/wall-posts/';

	if (Ext.get('js-grid-placeholder'))
	{
		var urlParam = intelli.urlVal('status');

		intelli.comments =
		{
			columns: [
				'selection',
				{name: 'author', title: _t('author'), width: 140},
				{name: 'body', title: _t('body'), width: 1, editor: 'text-wide'},
				{name: 'ip', title: _t('ip_address'), width: 150, hidden: true},
				{name: 'date', title: _t('date'), width: 180, editor: 'date'},
				'status',
				'delete'
			],
			storeParams: urlParam ? {status: urlParam} : null,
			url: pageUrl
		};

		intelli.comments = new IntelliGrid(intelli.comments, false);
		intelli.comments.toolbar = Ext.create('Ext.Toolbar', {items:
		[
			{
				emptyText: _t('text'),
				name: 'text',
				listeners: intelli.gridHelper.listener.specialKey,
				xtype: 'textfield'
			}, {
				displayField: 'title',
				editable: false,
				emptyText: _t('status'),
				id: 'fltStatus',
				name: 'status',
				store: intelli.comments.stores.statuses,
				typeAhead: true,
				valueField: 'value',
				xtype: 'combo'
			}, {
				handler: function()
				{
					intelli.gridHelper.search(intelli.comments);
				},
				id: 'fltBtn',
				text: '<i class="i-search"></i> ' + _t('search')
			}, {
				handler: function()
				{
					intelli.gridHelper.search(intelli.comments, true);
				},
				text: '<i class="i-close"></i> ' + _t('reset')
			}
		]});

		if (urlParam)
		{
			Ext.getCmp('fltStatus').setValue(urlParam);
		}

		intelli.comments.init();
	}
});