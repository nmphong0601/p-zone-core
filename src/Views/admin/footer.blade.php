<footer class="main-footer">
  @if (!pz_config('hidden_copyright_footer_admin'))
    <div class="float-right d-none d-sm-inline-block">
      <strong>Env</strong>
      {{ config('app.env') }}
      &nbsp;&nbsp;
      <strong>Version</strong> 
      {{ config('p-zone.sub-version') }} ({{ config('p-zone.core-sub-version') }})
    </div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ config('p-zone.homepage') }}">P-Zone: {{ config('p-zone.title') }}</a>.</strong> All rights
    reserved.
  @endif
</footer>
