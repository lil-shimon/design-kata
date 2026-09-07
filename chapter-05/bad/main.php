<?php

require_once __DIR__ . '/Bad.php';

$common = new Common();
$util = new Util();

echo "== 1. 同じルールが2つのクラスに散っている ==\n";
// 「regularPriceは0以上」というルールを Common も Util も独自に持っている。
// 後続の検証まで到達させたいのでここは catch する。
try {
    $common->discountedPrice(-1, 0.9);
} catch (InvalidArgumentException $e) {
    printf("Common::discountedPrice(-1, 0.9) : %s\n", $e->getMessage());
}
try {
    $util->isFairPrice(-1);
} catch (InvalidArgumentException $e) {
    printf("Util::isFairPrice(-1)            : %s\n", $e->getMessage());
}
echo "                                   ^- 同じルールが2箇所。片方だけ直すと不整合になる\n";

echo "\n== 2. どこにも無いルールは、誰にも気づかれないまま素通りする ==\n";
// discountRate の下限は Common が持っているが、上限はどのクラスも持っていない。
// 例外にならず「もっともらしい値」が返るので、落ちるより気づきにくい。
foreach ([0.9, 0.0, 1.5] as $rate) {
    $note = $rate > 1.0 ? '  <- 「割引」なのに値上がり。例外は出ない' : '';
    printf("割引率 %4.1f : %4d -> %4d%s\n", $rate, 1000, $common->discountedPrice(1000, $rate), $note);
}

echo "\n== 3. 戻り値が int なので、意味の違う値が混ざる ==\n";
// 割引後価格も正規価格も同じ int。型が意味を区別してくれない。
$regularPrice = 1000;
$discounted = $common->discountedPrice($regularPrice, 0.9);
printf("正規価格               : %4d\n", $regularPrice);
printf("割引後価格             : %4d\n", $discounted);
printf(
    "isFairPrice(割引後価格): %s  <- 割引後価格を「正規価格」として検査できてしまう\n",
    $util->isFairPrice($discounted) ? 'true' : 'false'
);
