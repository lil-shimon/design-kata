<?php

require_once __DIR__ . '/Good.php';

echo "== 1. 生成できた時点で、必ず検証済みの値になっている ==\n";
// primitive の int / float ではなく、検証を通った型からしか組み立てられない。
// 「作れた」ことが「有効である」ことと同じ意味になる。
var_dump(new DiscountedPrice(new RegularPrice(1000), new DiscountRate(0.9)));
echo "-> 1000 x 0.9 = 900。未検証の値が入り込む隙間がない\n";

echo "\n== 2. 同じルールが1箇所にまとまった ==\n";
// bad では「regularPriceは0以上」を Common と Util が別々に持っていた。
// good ではそのルールは RegularPrice の中の1行だけ。片方だけ直す事故が起きない。
try {
    new RegularPrice(-1);
} catch (InvalidArgumentException $e) {
    printf("new RegularPrice(-1)   : %s\n", $e->getMessage());
}
try {
    new DiscountRate(-0.1);
} catch (InvalidArgumentException $e) {
    printf("new DiscountRate(-0.1) : %s\n", $e->getMessage());
}
echo "                         ^- ルールの置き場所が型ごとに1つに決まった\n";

echo "\n== 3. 意味の違う値を取り違えられない ==\n";
// bad では割引後価格も正規価格も同じ int だったので、
// Util::isFairPrice(int $regularPrice) に割引後価格を渡せてしまった。
$regularPrice = new RegularPrice(1000);
$discountedPrice = new DiscountedPrice($regularPrice, new DiscountRate(0.9));

try {
    new DiscountedPrice($discountedPrice, new DiscountRate(0.9));
} catch (TypeError $e) {
    echo "割引後価格を正規価格の位置に渡す:\n";
    printf("  %s\n", $e->getMessage());
}
try {
    new DiscountedPrice(1000, 0.9);
} catch (TypeError $e) {
    echo "生の int / float を渡す:\n";
    printf("  %s\n", $e->getMessage());
}

echo "\n== 4. ただし、書いていないルールは守られない(bad の観点2 と同じ) ==\n";
// 値オブジェクトを作って得られたのは「ルールの置き場所」であって、
// 置いていないルールまで自動で守られるわけではない。
// DiscountRate に上限が無いので、割引率 1.5 が今も生成できる。
$rate = new DiscountRate(1.5);
var_dump(new DiscountedPrice(new RegularPrice(1000), $rate));
echo "-> 1000 -> 1500。「割引」なのに値上がりする。DiscountRate に上限がまだ無い\n";
