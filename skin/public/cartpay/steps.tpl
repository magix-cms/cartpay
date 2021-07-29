<nav class="form-steps{if isset($done)} {$done.status}-state{/if}">
    {foreach $steps as $i => $step}
        <div class="form-steps__item{if $current_step.pos >= $i} form-steps__item{if $current_step.pos > $i || ($current_step.pos === $i && $step@last)}--completed{elseif $current_step.pos === $i}--active{/if}{/if}">
            <div class="form-steps__item-content">
                <span class="form-steps__item-text"><span>{#$step.step#}</span><span>&nbsp;</span></span>
                <span class="form-steps__item-icon"><span class="sr-only">{$step.pos}</span></span>
            </div>
            {if $i > 1}
                <span class="form-steps__item-line"></span>
            {/if}
        </div>
    {/foreach}
</nav>