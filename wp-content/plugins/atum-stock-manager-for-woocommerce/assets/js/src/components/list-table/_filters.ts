/* =======================================
   FILTERS FOR LIST TABLES
   ======================================= */

import Settings from '../../config/_settings';
import Globals from './_globals';
import ListTable from './_list-table';
import Router from './_router';
import Tooltip from '../_tooltip';
import DateTimePicker from '../_date-time-picker';
import { Utils } from '../../utils/_utils';
import showFilters from './_show-filters';

export default class Filters {
	
	showFilters: showFilters;
	
	constructor(
		private settings: Settings,
		private globals: Globals,
		private listTable: ListTable,
		private router: Router,
		private tooltip: Tooltip,
		private dateTimePicker: DateTimePicker
	) {
		
		//
		// Ajax filters.
		// -------------
		if (this.settings.get('ajaxFilter') === 'yes') {
			
			this.globals.$atumList
				
				// Dropdown filters.
				.on( 'change', '.auto-filter', ( evt: JQueryEventObject ) => {

					const $selected = $( evt.currentTarget ).find( 'option:selected' );

					if ( ! $selected.length || $selected.data( 'auto-filter' ) !== 'no' ) {
						this.keyUp( evt, true );
					}

				} )
				
				// Search filter.
				.on('keyup paste search input', '.atum-post-search', (evt: JQueryEventObject) => {
					
					const searchColumnBtnVal: string      = this.globals.$searchColumnBtn.data('value'),
					      searchInputVal: string | number = $(evt.currentTarget).val();
					
					Utils.delay( () => this.pseudoKeyUpAjax(searchColumnBtnVal, searchInputVal), 500);
					
				})
				
				// Pagination input changes.
				.on('change', '.current-page', (evt: JQueryEventObject) => {
					
					$.address.parameter('paged', parseInt( $( evt.currentTarget ).val() ) );
					this.keyUp(evt);
					
				} );
				
			this.globals.$searchColumnBtn.on('atum-search-column-data-changed', (evt: JQueryEventObject) => {
				this.pseudoKeyUpAjax( $(evt.currentTarget).data('value'), decodeURIComponent( this.globals.$searchInput.val() ) );
			});
			
		}
		
		//
		// Non-ajax filters.
		// -----------------
		else {
			
			let $searchSubmitBtn: JQuery = this.globals.$searchInput.siblings('.search-submit');
			
			if (!this.globals.$searchInput.val()) {
				$searchSubmitBtn.prop('disabled', true);
			}
			
			// If s is empty, search-submit must be disabled and ?s removed.
			// If s and searchColumnBtnVal have values, then we can push over search.
			this.globals.$searchInput.on('input', (evt: JQueryEventObject) => {
				
				const searchColumnBtnVal: string = this.globals.$searchColumnBtn.data('value'),
				      inputVal: any              = $(evt.currentTarget).val();
				
				if (!inputVal) {
					
					$searchSubmitBtn.prop('disabled', true);
					
					if (inputVal != $.address.parameter('s')) {
						$.address.parameter('s', '');
						$.address.parameter('search_column', '');
						this.router.updateHash(); // Force clean search.
					}
					
				}
				// Uncaught TypeError: Cannot read property 'length' of undefined (redundant check fails).
				else if ( typeof searchColumnBtnVal !== 'undefined' && searchColumnBtnVal.length > 0) {
					$searchSubmitBtn.prop('disabled', false);
				}
				else if (inputVal) {
					$searchSubmitBtn.prop('disabled', false);
				}
				
			});
			
			
			// When a search_column changes, set ?s and ?search_column if s has value. If s is empty, clean this two parameters.
			// TODO: IS THIS WORKING HERE? IS NOT ONLY FOR AJAX FILTERS?
			this.globals.$searchColumnBtn.on('atum-search-column-data-changed', (evt: JQueryEventObject) => {
				
				let searchInputVal: any        = this.globals.$searchInput.val(),
				    searchColumnBtnVal: string = $(evt.currentTarget).data('value');
				
				if (searchInputVal.length > 0) {
					$.address.parameter('s', searchInputVal);
					$.address.parameter('search_column', searchColumnBtnVal);
					this.keyUp(evt, true);
				}
				// Force clean s when required.
				else {
					$.address.parameter('s', '');
					$.address.parameter('search_column', '');
				}
				
			});
			
			this.globals.$atumList.on('click', '.search-category, .search-submit', () => {
				
				const searchInputVal: string     = this.globals.$searchInput.val(),
				      searchColumnBtnVal: string = this.globals.$searchColumnBtn.data('value');
				
				$searchSubmitBtn.prop('disabled', typeof searchColumnBtnVal !== 'undefined' && searchColumnBtnVal.length === 0 ? true : false);
				
				if (searchInputVal.length > 0) {
					$.address.parameter('s', searchInputVal);
					$.address.parameter('search_column', searchColumnBtnVal);
					
					this.router.updateHash();
				}
				// Force clean s when required.
				else {
					$.address.parameter('s', '');
					$.address.parameter('search_column', '');
					this.router.updateHash();
				}
				
			});
			
		}
		
		//
		// Events common to all filters.
		// -----------------------------
		this.globals.$atumList
		
			//
			// Reset Filters button.
			// ---------------------
			.on('click', '.reset-filters', (evt: JQueryEventObject) => {
				
				this.tooltip.destroyTooltips();
				
				$.address.queryString('');
				this.globals.$searchInput.val('');
				
				if (this.globals.$searchColumnBtn.data('value')) {
					this.globals.$searchColumnBtn.trigger('atum-search-column-set-data', ['', this.globals.$searchColumnDropdown.data('no-option')]);
				}
				
				this.listTable.updateTable();
				$(evt.currentTarget).addClass('hidden');
				
			})
			
			.on('atum-table-updated', () => this.addDateSelectorFilter());
		
		this.showFilters = new showFilters( this.globals.$atumList, this.settings);
		
		//
		// Add date selector filter.
		// -------------------------
		this.addDateSelectorFilter();
		
	}
	
	/**
	 * Search box keyUp event callback
	 *
	 * @param Object  evt       The event data object.
	 * @param Boolean noTimer   Optional. Whether to delay before triggering the update (used for autosearch).
	 */
	keyUp( evt: JQueryEventObject, noTimer?: boolean ) {
		
		let delay: number       = 500,
		    searchInputVal: any = this.globals.$searchInput.val();
		
		noTimer = noTimer || false;
		
		/*
		 * If user hits enter, we don't want to submit the form.
		 * We don't preventDefault() for all keys because it would also prevent to get the page number!
		 *
		 * Also, if the 's' param is empty, we don't want to search anything.
		 */
		if ( evt.type !== 'keyup' || searchInputVal.length > 0 ) {
			
			if ( 13 === evt.which ) {
				evt.preventDefault();
			}
			
			if ( noTimer ) {
				this.router.updateHash();
			}
			else {
				/*
				 * Now the timer comes to use: we wait half a second after
				 * the user stopped typing to actually send the call. If
				 * we don't, the keyup event will trigger instantly and
				 * thus may cause duplicate calls before sending the intended value.
				 */
				Utils.delay( () => this.router.updateHash(), delay );
				
			}
			
		}
		else {
			evt.preventDefault();
		}
		
	}
	
	pseudoKeyUpAjax( searchColumnBtnVal: string, searchInputVal: any ) {
		
		if (searchInputVal.length === 0) {
			
			if (searchInputVal != $.address.parameter('s')) {
				$.address.parameter('s', '');
				$.address.parameter('search_column', '');
				this.router.updateHash(); // Force clean search.
			}
			
		}
		else if (typeof searchColumnBtnVal !== 'undefined' && searchColumnBtnVal.length > 0) {
			$.address.parameter('s', searchInputVal);
			$.address.parameter('search_column', searchColumnBtnVal);
			this.router.updateHash();
		}
		else if (searchInputVal.length > 0) {
			$.address.parameter('s', searchInputVal);
			this.router.updateHash();
		}
		
	}
	
	/**
	 * Add the date selector filter
	 */
	addDateSelectorFilter() {
		
		let linkedFilters: string[] = this.settings.get('dateSelectorFilters'),
		    $dateSelector: JQuery   = this.globals.$atumList.find('.date-selector'),
		    dateFromVal: string     = $.address.parameter('date_from') ? $.address.parameter('date_from') : $('.date_from').val(),
		    dateToVal: string       = $.address.parameter('date_to') ? $.address.parameter('date_to') : $('.date_to').val();
		
		if ( ! dateToVal ) {
			
			let today: Date = new Date(),
			    dd: any     = today.getDate(),
			    mm: any     = today.getMonth() + 1, // January is 0.
			    yyyy: any   = today.getFullYear();
			
			if (dd < 10) {
				dd = '0' + dd.toString();
			}
			
			if (mm < 10) {
				mm = '0' + mm.toString();
			}
			
			dateToVal = yyyy + '-' + mm + '-' + dd;
			
		}
		
		$dateSelector.on( 'select2:select', ( evt: JQueryEventObject ) => {

			const $select: JQuery = $( evt.currentTarget );
		
			if ( $.inArray( $select.val(), linkedFilters ) > -1 ) {
				
				const popupClass: string = 'filter-range-dates-modal',
				      swal: any          = window['swal'];
				
				swal({
					customClass    : popupClass,
					width          : 440,
					showCloseButton: true,
					title          : `<h1 class="title">${ this.settings.get('setTimeWindow') }</h1><span class="sub-title">${ this.settings.get('selectDateRange') }</span>`,
					html           : `
						<div class="input-date">
							<label for="date_from">${ this.settings.get('from') }</label><br/>
							<input type="text" placeholder="Beginning" class="atum-datepicker date_from" name="date_from" id="date_from" maxlength="10" value="${ dateFromVal }">
						</div>
						<div class="input-date">
							<label for="date_to">${ this.settings.get('to') }</label><br/>
							<input type="text" class="atum-datepicker date_to" name="date_to" id="date_to" maxlength="10" value="${ dateToVal }">
						</div>
						<button class="btn btn-warning apply">${ this.settings.get('apply') }</button>
					`,
					showConfirmButton: false,
					onOpen           : () => {

						const $popup: JQuery = $( '.' + popupClass );
						
						// Init date time pickers.
						this.dateTimePicker.addDateTimePickers( $popup.find( '.atum-datepicker' ), { minDate: false } );

						$popup.find( '.swal2-content .apply' ).click( () => {
							this.globals.filterData['date_from'] = $popup.find( '.date_from' ).val();
							this.globals.filterData['date_to'] = $popup.find( '.date_to' ).val();
							this.keyUp( evt, true );
							$select.val('');
							swal.close();
						});

						$popup.find( '.swal2-close' ).click( () => $popup.find( '.date_to, .date_from' ).val( '' ) );
					
					},
					onClose: () => {
						
						if ( this.settings.get( 'ajaxFilter' ) === 'yes' ) {
							this.keyUp( evt, true );
							$select.val('');
						}
						
					},
					
				})
				.catch( swal.noop );
				
			}
			
		});
		
	}
	
}
