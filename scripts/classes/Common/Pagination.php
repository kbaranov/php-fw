<?php

class Common_Pagination
{
    public function __construct()
    {
    }

    /**
     * Генерация HTML постраничной навигации для списка объявлений
     *
     * @param int $pageLinkCount - количество страниц всего
     * @param int $pageLinkCurrent - номер текущей страницы
     * @param string $baseUrl - базовый URL страницы для перехода (без параметров)
     * @param string $strParams - список параметров в виде строки с &-разделителем (без параметра с номером страницы)
     * @param string $pageParamName - имя параметра для передачи номера страницы
     * @param int $pageLinkMaxCount - максимальное количество отображаемых номеров страниц
     *
     * @return string
     */
    public function render(
        $pageLinkCount,
        $pageLinkCurrent,
        $baseUrl,
        $strParams = '',
        $pageParamName = 'page',
        $pageLinkMaxCount = 9
    ) {
        $pageNumberBegin = max(1, ($pageLinkCurrent - 4));
        $pageNumberEnd = min($pageLinkCount, ($pageNumberBegin + $pageLinkMaxCount - 1));

        $strParams = preg_replace('/^page=(\d)*$/', '', $strParams);

        $pageLinkPrev = 'предыдущая';
        if ($pageLinkCurrent > 1) {
            $pageNumberItem = $pageLinkCurrent - 1;
            $pageLinkPrevUrl = "{$baseUrl}?{$pageParamName}={$pageNumberItem}";
            if (strlen($strParams)) {
                $pageLinkPrevUrl .= "&{$strParams}";
            }
            $pageLinkPrev = '<a href="' . $pageLinkPrevUrl . '">' . $pageLinkPrev . '</a>';
        }
        $pageLinkNext = 'следующая';
        if ($pageLinkCurrent < $pageLinkCount) {
            $pageNumberItem = $pageLinkCurrent + 1;
            $pageLinkNextUrl = "{$baseUrl}?{$pageParamName}={$pageNumberItem}";
            if (strlen($strParams)) {
                $pageLinkNextUrl .= "&{$strParams}";
            }
            $pageLinkNext = '<a href="' . $pageLinkNextUrl . '">' . $pageLinkNext . '</a>';
        }

        $content = '
<div class="pagination">
    <div class="pagination_right">' . $pageLinkNext . ' <!--Alt -->&rarr;</div>
    <div class="pagination_left">&larr;<!-- Alt--> ' . $pageLinkPrev . '</div>
    <div class="pagination_center">Страницы:
        ';
        if ($pageNumberBegin > 1) {
            $pageNumberItem = max(1, ($pageNumberBegin - 5));
            $pageNumberItemUrl = "{$baseUrl}?{$pageParamName}={$pageNumberItem}";
            if (strlen($strParams)) {
                $pageNumberItemUrl .= "&{$strParams}";
            }
            $content .= '<span><a href="' . $pageNumberItemUrl . '">...</a></span>';
        }
        $pageNumberItem = $pageNumberBegin;
        while ($pageNumberItem <= $pageNumberEnd) {
            if ($pageNumberItem==$pageLinkCurrent) {
                $content .= '<span class="active">' . $pageNumberItem . '</span>';
            } else {
                $pageNumberItemUrl = "{$baseUrl}?{$pageParamName}={$pageNumberItem}";
                if (strlen($strParams)) {
                    $pageNumberItemUrl .= "&{$strParams}";
                }
                $content .= '<span><a href="' . $pageNumberItemUrl . '">' . $pageNumberItem . '</a></span>';
            }
            $pageNumberItem++;
        }
        if ($pageNumberEnd < $pageLinkCount) {
            $pageNumberItem = min($pageLinkCount, ($pageNumberEnd + 5));
            $pageNumberItemUrl = "{$baseUrl}?{$pageParamName}={$pageNumberItem}";
            if (strlen($strParams)) {
                $pageNumberItemUrl .= "&{$strParams}";
            }
            $content .= '<span><a href="' . $pageNumberItemUrl . '">...</a></span>';
        }
        $content .= '
    </div>
</div>
        ';

        return $content;
    }
}
