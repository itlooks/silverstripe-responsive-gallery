          <% if $ShowAllComponents == '1' || $DisplayCarousel %>
          <div id="blueimp-gallery-carousel{$ID}" class="blueimp-gallery blueimp-gallery-controls blueimp-gallery-carousel">
              <div class="slides"></div>
              <% if $ShowAllComponents == '1' || $DisplayCarouselTitle %><h3 class="title"></h3><% end_if %>
              <% if $ShowAllComponents == '1' || $DisplayCarouselPrevNext %>
              <a class="prev">‹</a>
              <a class="next">›</a>
              <% end_if %>
              <% if $ShowAllComponents == '1' || $DisplayCarouselPlayPause %><a class="play-pause"></a><% end_if %>
              <% if $ShowAllComponents == '1' || $DisplayCarouselIndicator %><ol class="indicator"></ol><% end_if %>
          </div>
          <% end_if %>

          <% if $ShowAllComponents == '1' || $DisplayModal %>
          <div id="blueimp-gallery{$ID}" class="blueimp-gallery blueimp-gallery-controls borderless" data-use-bootstrap-modal="false">
            <div class="slides"></div>
            <% if $ShowAllComponents == '1' || $DisplayModalTitle %><h3 class="title"></h3><% end_if %>
            <% if $ShowAllComponents == '1' || $DisplayModalPrevNext %>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <% end_if %>
            <a class="close">×</a>
            <% if $ShowAllComponents == '1' || $DisplayCarouselPlayPause %><a class="play-pause"></a><% end_if %>
            <% if $ShowAllComponents == '1' || $DisplayCarouselIndicator %><ol class="indicator"></ol><% end_if %>
          </div>
          <% end_if %>

          <div id="links{$ID}" class="responsive-gallery links <% if $ShowAllComponents != '1' %><% if $DisplayModal %><% else %>hide<% end_if %><% end_if %>">
              <% loop $getImages %>
              <% if $Up.Source == 'sf' %>
              <a href="$URL" title="$Title" data-gallery="#blueimp-gallery{$Up.ID}">
                  <img src="$CroppedImage(50, 50).URL" class="img-responsive" title="$Title">
              </a>
              <% else %>
              <a href="$GalleryImage.URL" title="$Title" data-gallery="#blueimp-gallery{$Up.ID}">
                  <img src="$GalleryImage.CroppedImage(50, 50).URL" class="img-responsive" title="$GalleryImage.Title">
              </a>
              <% end_if %>
              <% end_loop %>
          </div>
