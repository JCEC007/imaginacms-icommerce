<div class="filters">

	<div id="staticdiv">

		<div id="contenttomove">

			<livewire:icommerce::filter-categories :key="filter-categories"
            	:categoryBreadcrumb="$categoryBreadcrumb"
            	:manufacturer="$manufacturer ?? null"
            	:category="$category ?? null" />

			<livewire:icommerce::filter-range-prices :key="filter-range-prices" />

			@if(config("asgard.icommerce.config.filters.manufacturers.status"))
				@if(!isset($manufacturer->id))
					<livewire:icommerce::filter-manufacturers :key="filter-manufacturers" />
				@endif
			@endif

			@if(config("asgard.icommerce.config.filters.product-options.status"))
				<livewire:icommerce::filter-product-options :key="filter-product-options" />
			@endif

			@if(config("asgard.icommerce.config.filters.product-types"))
				@if(config("asgard.icommerce.config.filters.product-types.status"))
					<livewire:icommerce::filter-product-types :key="filter-product-types" />
			@endif
			@endif

		</div>
	</div>

	{{--
	<a data-toggle="modal" data-target="#modalFilter"
	   class="btn btn-primary btn-sm cursor-pointer float-right d-lg-none mb-4 mr-2">
		{{trans('icommerce::common.filters.title')}} <i class="fa fa-filter"></i>
	</a>
	--}}

	<div class="modal  fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title">{{trans('icommerce::common.filters.title')}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body" id="modal-body">

				</div>
			</div>
		</div>

	</div>



</div>

@section('scripts')
	@parent
	<script>

		$(document).ready(function () {

			function divtomodal() {

				var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
				if(width <= 992) {
					$('#modal-body').append($("#contenttomove"));
				} else {
					$('#staticdiv').append($("#contenttomove"));
				}

			}

			$(window).resize(divtomodal);
			var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
			if(width<=992)
				divtomodal()
		});
	</script>

@stop