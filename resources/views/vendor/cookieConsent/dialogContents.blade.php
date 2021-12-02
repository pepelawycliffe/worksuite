<div class="js-cookie-consent cookie-consent alert alert-info text-center mb-0 border-radius-0">

    <span class="cookie-consent__message">
        @lang('modules.front.allowCookies')
        {{-- {!! trans('cookieConsent::texts.message') !!} --}}
    </span>

    <button class="js-cookie-consent-agree cookie-consent__agree btn btn-success">
        @lang('modules.front.cookiesButton')

        {{-- {{ trans('cookieConsent::texts.agree') }} --}}
    </button>

</div>
