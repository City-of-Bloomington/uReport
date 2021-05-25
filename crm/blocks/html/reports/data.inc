<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Blossom\Classes\Url;
?>
<section>
    <header>
        <h1><?= $this->template->title; ?></h1>
        <div class="tools">
        <?php
            if (!empty($_GET['categories'])) {
                $current_url = new Url(Url::current_url(BASE_HOST));
                $current_url->format = 'csv';
                $h = $this->template->getHelper('buttonLink');
                echo $h->buttonLink($current_url, 'CSV', 'download');
                unset($current_url->format);
            }
        ?>
        </div>
    </header>
    <p>You must choose at least a catgegory, in order to download data.</p>
</section>
