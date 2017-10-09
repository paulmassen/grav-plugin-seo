(function($) {
 
    var MyYoastPlugin = function()
    {
        YoastSEO.app.registerPlugin('myYoastPlugin', {status: 'loading'});
 
        this.getData();
    };
 
    MyYoastPlugin.prototype.getData = function()
    {
 
        var _self = this,
            $text = $('#mytitle');
 
        _self.custom_content = $text.val();
 
        YoastSEO.app.pluginReady('myYoastPlugin');
 
        YoastSEO.app.registerModification('content', $.proxy(_self.getCustomContent, _self), 'myYoastPlugin', 5);
 
    };
 
    MyYoastPlugin.prototype.getCustomContent = function (content)
    {
     
      return this.custom_content +  $('#mytitle').getContent() + content;
    };
 
    $(window).on('YoastSEO:ready', function ()
    {
      new MyYoastPlugin();
    });
})(jQuery);