# Death of a notable person banner
`dxw-mourning-banner`

A stop gap solution while we come up with something more solid / integrated to address how client sites can display a banner marking the death of a notable person.

Oringinally requested by GSS and UKSA

[Black mourning banner](https://dxw.zendesk.com/agent/tickets/13933)
[Adding banner to UKSA and OSR websites](https://dxw.zendesk.com/agent/tickets/13864)

and reflecting [GOV.UK's Types of emergency banners](https://docs.publishing.service.gov.uk/manual/emergency-publishing.html#types-of-emergency-banners)

This plugin is a modded version of [HD Banner](https://github.com/dxw/hd-banner) originally developed by Phil Banks at Helpful Digital.

All customisation options previously available are hidden except for the banner text itself.

Defaults are set as follows,

- When to display: always
- Background colour: #000000
- Text and link colours: #ffffff
- Position: prepend Body / fixed: no
- Show in admin: false
 
## Planned iterations

- Replace banner text field with 4 single text line fields,
 - Header / Name of person
 - Lifespan
 - Link text
 - Link URL 
-Strip out erroneous code and tidy styling options based on defaults