
Ext.define('TYPO3.Media.GridToolbar', {
	extend: 'TYPO3.Vidi.View.Content.GridToolbar',
	alias: 'widget.TYPO3-Media-GridToolbar',
	initComponent: function() {
		this.items = [
			{
				xtype: 'button',
				iconCls: 't3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-open',
				text: 'edit',
				action: 'edit'
			},
			{
				xtype: 'button',
				action: 'delete',
				iconCls: 't3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-delete',
				text: 'delete selected records'
			},
			{
				xtype: 'button',
				action: 'upload',
				iconCls: 't3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-upload',
				text: 'upload new file'
			},
			{
				xtype: 'button',
				action: 'reIndex',
				iconCls: 't3-icon t3-icon-status t3-icon-status-status t3-icon-status-reference-hard',
				text: 'reindex checked files'
			},
			'->',
			{xtype: 'thumbnailColumnResizer'}
		];
		this.callParent(arguments);
	},
	afterRender: function() {
		this.callParent();

		this.down('button[action=upload]').setHandler(this.uploadNewFiles);
		this.down('button[action=upload]').setHandler(this.reIndexCheckedFiles);
	},
	uploadNewFiles: function() {

	},
	reIndexCheckedFiles: function() {
		Ext.create(
				'TYPO3.Vidi.Components.Overlay',
				'mod.php?M=user_MediaIndexM1',
				'indexers',
				function() {me.refreshGrid();}
			);
	}
});

TYPO3.TYPO3.Core.Registry.set('vidi/mainModule/gridToolbar', 'TYPO3-Media-GridToolbar', 99);