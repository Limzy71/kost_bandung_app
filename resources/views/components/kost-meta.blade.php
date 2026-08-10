<meta name="description" content="{{ Str::limit($kost->description, 150) }}">
<meta property="og:title" content="{{ $kost->name }} - Kost Bandung">
<meta property="og:description" content="{{ Str::limit($kost->description, 150) }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ route('kost.show', $kost->slug) }}">

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": {{ json_encode($kost->name) }},
  "description": {{ json_encode(Str::limit($kost->description, 500)) }},
  "offers": {
    "@type": "Offer",
    "priceCurrency": "IDR",
    "price": {{ $kost->price_monthly ?? 0 }}
  }
}
</script>
