<meta name="description" content="{{ Str::limit($kost->description, 150) }}">
<meta property="og:title" content="{{ $kost->name }} - Kost Bandung">
<meta property="og:description" content="{{ Str::limit($kost->description, 150) }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ route('kost.show', $kost->slug) }}">

@php
$schemaData = [
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    'name' => $kost->name,
    'description' => Str::limit($kost->description, 500),
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'IDR',
        'price' => (float) ($kost->price_monthly ?? 0),
    ],
];
@endphp
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
