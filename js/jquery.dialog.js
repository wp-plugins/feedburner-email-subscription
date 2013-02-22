 /*
	Copyright 2013 zourbuth (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

jQuery(document).ready(function($){
	// Background
	$(".totalControls").closest(".widget-inside").addClass("totalBackground");
	
	$.fn.fesLoadUtility = function(){
		var t = $(this);
		t.empty();
		t.append("<span class='fes-loading'>Loading item...</span>");
		$.post( ajaxurl, { action: fes.action, nonce : fes.nonce }, function(data){
			$(".fes-loading").remove();
			t.append(data);			
		});
	}
});