<?php

namespace AppBundle\Services;

use Exporter\Writer\SitemapWriter as BaseSitemapWriter;

/**
 * Class SitemapWriter
 */
class SitemapWriter extends BaseSitemapWriter
{
    public const NAMESPACE_VIDEO = 'video';

    /** @var array<string, string> */
    public $namespaces = [
        self::NAMESPACE_VIDEO => 'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"',
    ];

    private bool $isVideo = false;

    public function setVideoSitemap(bool $value): void
    {
        $this->isVideo = $value;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $alternateUrls
     */
    public function write(array $data, array $alternateUrls = []): void
    {
        $line = match ($data['type']) {
            'video' => $this->generateVideoLine($data),
            default => $this->generateDefaultLine($data, $alternateUrls),
        };

        $this->addSitemapLine($line);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function generateDefaultLine(array $data): string
    {
        $loc = sprintf("\n\t\t<loc>%s</loc>", $data['url']);
        $lastmod = isset($data['lastmod']) ? sprintf("\n\t\t<lastmod>%s</lastmod>", date('Y-m-d', strtotime($data['lastmod']))) : '';
        $changefreq = sprintf("\n\t\t<changefreq>%s</changefreq>", $data['changefreq']);
        $priority = sprintf("\n\t\t<priority>%s</priority>", $data['priority']);

        $images = '';
        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                $images .= sprintf("\n\t\t<image:image><image:loc>%s</image:loc></image:image>", $image);
            }
        }

        return sprintf("\t<url>%s%s%s%s%s\n\t</url>", $loc, $lastmod, $changefreq, $priority, $images) . "\n";
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function generateVideoLine(array $data): string
    {
        $videos = '';
        foreach ($data['video'] as $key => $video) {
            $videos .= sprintf("\n\t\t" . '<video:%1$s>%2$s</video:%1$s>', $key, $video);
        }

        return sprintf("<url>\n\t\t<loc>%s</loc>\n\t\t<video:video>%s</video:video>\n\t\t</url>\n", $data['url'], $videos);
    }

    /**
     * @throws \RuntimeException
     */
    protected function generateNewPart(): void
    {
        if ($this->buffer) {
            $this->closeSitemap();
        }

        $this->bufferUrlCount = 0;
        $this->bufferSize = 0;
        ++$this->bufferPart;

        if (!is_writable($this->folder)) {
            throw new \RuntimeException(sprintf('Unable to write to folder: %s', $this->folder));
        }

        $filename = sprintf($this->pattern, $this->bufferPart);

        $this->buffer = fopen($this->folder . '/' . $filename, 'w');

        if ($this->isVideo) {
            $this->bufferSize += fwrite($this->buffer, '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"' . $this->getHeaderByFlag() . '>' . "\n");
        } else {
            $this->bufferSize += fwrite($this->buffer, '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $this->getHeaderByFlag() . '>' . "\n");
        }
    }

    protected function getHeaderByFlag(): string
    {
        $result = '';
        foreach ($this->headers as $flag) {
            $result .= ' ' . $this->namespaces[$flag];
        }

        return $result;
    }
}
