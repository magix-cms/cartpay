{foreach $list as $attr}
    <option value="{$attr.idattribute}"{if isset($selected) && $selected == $attr.idattribute} selected{/if}>{$attr.title_attribute}</option>
{/foreach}