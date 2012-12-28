// obsolete - and not used - code that are likely to be removed soon!
TYPO3.TYPO3.Core.Registry.set('vidi/mainModule/collectionManagement/ExtDirectEndPoints/Static', 'TYPO3.FileList.Service.ExtDirect.CollectionManagement', 99);
TYPO3.TYPO3.Core.Registry.set('vidi/mainModule/collectionManagement/ExtDirectEndPoints/Filter', 'TYPO3.FileList.Service.ExtDirect.FilterManagement', 99);
TYPO3.TYPO3.Core.Registry.set('vidi/columnConfiguration', {
	"sys_file":[
		{"text":"","dataIndex":"icon","hidden":false,"xtype":"iconColumn"},
		{"text":"Name","dataIndex":"name","hidable":false},
		{"text":"","xtype":"fileActionColumn"},
		{"text":"Gr\u00f6sse","dataIndex":"size","xtype":"byteColumn"},
		{"text":"Extension","dataIndex":"extension"},
		{"text":"Mimetype","dataIndex":"type"},
		{"text":"Erstellt am","dataIndex":"creationDate","xtype":"datecolumn","format":"d.m.Y H:i"},
		{"text":"\u00c4nderungsdatum","dataIndex":"creationDate","xtype":"datecolumn","format":"d.m.Y H:i"},
		{"text":"Thumbnail","dataIndex":"url","xtype":"thumbnailColumn"}
	]}, 99);