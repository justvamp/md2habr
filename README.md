This class allows you to convert an article in markdown syntax right to correct habrahabr article that is ready to publish.

Usage example:
```php
    $mdFilePath   = __DIR__ . '/article.md';
    $habrFilePath = __DIR__ . '/habr_article.html';

    $mdText = file_get_contents($mdFilePath);
    $conv = new MarkdownToHabrahabrConverter;
    $habrText = $conv->convert($mdText);
    file_put_contents($habrFilePath, $habrText);
```