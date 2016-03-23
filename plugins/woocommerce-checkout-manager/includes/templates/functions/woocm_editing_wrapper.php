<?php
/**
 * WooCommerce Checkout Manager 
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'wooccm_photo_editor_content');
function wooccm_photo_editor_content() {
	if (is_checkout()) {
?>
<div id="caman_content" style="display:none;">

	<div id="wooccmtoolbar">
        <div class="button" id="save">Save</div>
		<div class="button" id="close">Close</div>
		<h3>Photo Editing</h3>
    </div>

  <div class="wooccmsidebar">
  <div id="Filters">
  
    <div class="Filter">
      <div class="FilterName">
        <p>Brightness</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Contrast</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Saturation</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Vibrance</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Exposure</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Hue</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="100" step="1" value="0" data-filter="hue">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Sepia</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="100" step="1" value="0" data-filter="sepia">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Gamma</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Noise</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="100" step="1" value="0" data-filter="noise">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Clip</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="100" step="1" value="0" data-filter="clip">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>Sharpen</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen">
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>StackBlur</p>
      </div>

      <div class="FilterSetting">
        <input type="range" min="0" max="20" step="1" value="0" data-filter="stackBlur">
        <span class="FilterValue">0</span>
      </div>
    </div>
  

  <div class="Clear"></div>
  </div>

  <div id="PresetFilters">
  
    <a data-preset="vintage">Vintage</a>
  
    <a data-preset="lomo">Lomo</a>
  
    <a data-preset="clarity">Clarity</a>
  
    <a data-preset="sinCity">Sin City</a>
  
    <a data-preset="sunrise">Sunrise</a>
  
    <a data-preset="crossProcess">Cross Process</a>
  
    <a data-preset="orangePeel">Orange Peel</a>
  
    <a data-preset="love">Love</a>
  
    <a data-preset="grungy">Grungy</a>
  
    <a data-preset="jarques">Jarques</a>
  
    <a data-preset="pinhole">Pinhole</a>
  
    <a data-preset="oldBoot">Old Boot</a>
  
    <a data-preset="glowingSun">Glowing Sun</a>
  
    <a data-preset="hazyDays">Hazy Days</a>
  
    <a data-preset="herMajesty">Her Majesty</a>
  
    <a data-preset="nostalgia">Nostalgia</a>
  
    <a data-preset="hemingway">Hemingway</a>
  
    <a data-preset="concentrate">Concentrate</a>
  
  </div>
   
   </div>
   
   <div class="wooccmimageeditor" id="wooccmimageeditorpro">
   </div>
   
  
</div>

<?php	
	}
}