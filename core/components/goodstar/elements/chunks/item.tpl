<div itemscope itemtype="http://schema.org/Product">
    <p itemprop="name" class="rating-hide">[[+name]]</p> <!-- Название Продукта -->

    <select class="example" data-thread="[[+id]]" data-current-rating="[[+current_rating]]" data-group="[[+group]]" data-readonly="[[+readonly]]">
        <option value=""></option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>
    <span>[[+user_rating:notempty=`Ваш голос: [[+user_rating]]`]]</span>
    <span>Число голосов: [[+count_voite]]</span>

    <div itemscope itemtype="http://schema.org/AggregateRating" itemprop="aggregateRating" class="rating-hide"> <!-- Начало РЕЙТИНГА -->
        <div itemprop="ratingValue">[[+current_rating]]</div> <!-- Значение рейтинга -->
        <div itemprop="bestRating">5</div> <!-- Максимальное Значение рейтинга -->
        <div itemprop="worstRating">1</div> <!-- Минимальное Значение рейтинга -->
        <div itemprop="ratingCount">[[+count_voite]]</div> <!-- Число голосов -->
    </div><!-- Конец РЕЙТИНГА -->
</div>
