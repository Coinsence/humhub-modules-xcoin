<?php

/** @var $url string */

?>

<div class="social-share">
    <span class="share-btn" data-sharer="fb" data-link="<?= $url ?>"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAANCAYAAACZ3F9/AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAACuSURBVHgBtZKxDQMhDEVxdIiWbHAbJAX0lxGySUbIRjdCkoaGhg2SbEBDhYRiS2cpKeBy0p0lhI39ZH9hMMaMAHAU/1vMOZ+6CeoXgEJKedjN1LzwDNhhj8U9+oETTbCUcvXeP7TW5AONybmuBSql7nSnlEbq/J2bG7VqNfBCmpxzbwpQ43nS2B4V9USGyEIIBERrreY3wOApKt/BXbHmJn41Dqtr3BaMSyFc0/gBNpo32+BuSmQAAAAASUVORK5CYII=" alt="fb" /></span>
    <span class="share-btn" data-sharer="tw" data-link="<?= $url ?>"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAANCAYAAABPeYUaAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAFGSURBVHgBlZI/ToRAFMZnYElMaCgNFaWdGAih073BegLDCfQG4g30BOoN9ASyHQkEsbPb6WgpqPjr9zZIcAPu7pc8ZoY37zdv5j3OINd1jTAMBZuR4zh+13W3mGoYg8Vi4dV1bZAvjuOA27Z9JUnSM2w5BeoB9xNswTn3y7J8l7CB6Ebbth8IuNndOQMgUYyRpmnOTdPUFEXZUKrkAV1goNO/ZFnOm6bZTBHgf8NVrmkuado29mXkNGC0/pwD9Ep/J1JRFCbGO3akcJAYIPS6GAN2pJDleoDQp6oqutsrLD8EQGXGg4o/kF6XrH/cfUKPeOP1FkJlQjZLTMU+ALLwx1kMkB4kVFW9oNL9B8AbPuz+55ZlrdAb5zAD6xWbuBKCc+rOKIqepuBylmXfuq5r2HSG9QnstPcJBKfoykdUwkuSZM1m9ANS1awKjDcpaAAAAABJRU5ErkJggg==" alt="tw" /></span>
    <span class="share-btn" data-sharer="in" data-link="<?= $url ?>"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAAOCAYAAAD0f5bSAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAC9SURBVHgBnZJNCsIwEIXzY4vudFfowniIdluP4BH0JvUE4gk8gzeI6/YOxiO4Tkh8gQgWAjZ5EObNMB8Dj9C2bZ/OOUFmilKqWArg5fcZydAE0lrvcL5Pgsqy7HB+8w9a/DbWWoGyxTUJWKIKzCRjbI/5MXppHMczlu4e8B6ja1EUj2EYTvAqCkV0M8ZcgpdzoXV4E2VFzuu67r9NVVUvznkHK+BXCOCAMJbw7xCG8Hu0aRpHEpX9IxRJk/oAutk+UahQ1wgAAAAASUVORK5CYII=" alt="in" /></span>
</div>

<script>
    $('.social-share .share-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let sharer = $(this).data('sharer');
        let link = $(this).data('link');
        console.log();
        switch (sharer) {
            case 'fb':
                window.open("https://www.facebook.com/sharer.php?u="+link,"","height=368,width=600,left=100,top=100,menubar=0");
                break;
            case 'tw':
                window.open("https://twitter.com/share?url="+link+"&text=Come%20join","","height=260,width=500,left=100,top=100,menubar=0");
                break;
            case 'in':
                window.open("https://www.linkedin.com/sharing/share-offsite/?url="+link,"","height=260,width=500,left=100,top=100,menubar=0");
                break;
        
            default:
                break;
        }
    });
</script>