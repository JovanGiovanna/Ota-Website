# TODO List for Correcting Discount Logic and Comments

## products.blade.php
- [ ] Fix comments: Change {{-- END KOLOM HARGA BARU --}} to {{-- END KOLOM DISKON BARU --}} in the table header.
- [ ] Fix comments: Change {{-- END ISI KOLOM HARGA BARU --}} to {{-- END ISI KOLOM DISKON BARU --}} in the table body.

## addons.blade.php
- [ ] Update discount logic: Change the discount cell to check for discount_type and calculate accordingly (similar to products.blade.php).
- [ ] Fix comments: Change {{-- KOLOM DISKON DENGAN HANYA MENAMPILKAN discount_fixed --}} to {{-- KOLOM DISKON BARU --}} and add {{-- END KOLOM DISKON BARU --}}.
- [ ] Fix comments: Change {{-- KOLOM HARGA --}} to {{-- KOLOM HARGA BARU --}} and add {{-- END KOLOM HARGA BARU --}} after the price cell.
