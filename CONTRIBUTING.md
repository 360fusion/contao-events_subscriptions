# Contributing

Bug reports and ideas are welcome as issues. Please say which Contao version you use and what you expected to happen.

To work on the code, install the extension in a local Contao 5.7 site with a Composer path repository
(`"options": {"symlink": true}`), so edits show straight away. Run the unit tests with `vendor/bin/phpunit`.

Code follows Contao's coding standard and uses only documented Contao APIs. Add a test for new logic, update the pages in
`docs/` and add a line to `CHANGELOG.md`. Add every label in both `contao/languages/en` and `contao/languages/de`.
