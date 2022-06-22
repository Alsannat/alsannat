require(["jquery"], function($) {
   "use strict";
   $(document).on("click","a.elc-more-info.active", function() {
       $(this).toggleClass("active");
       $(this).next().toggleClass("active");
       $(this).next().next().toggleClass("active");
   });

   $(document).on("click","a.elc-less-info.active", function() {
       $(this).prev().toggleClass("active");
       $(this).toggleClass("active");
       $(this).next().toggleClass("active");
   });
});