<?php

require_once __DIR__ . '/Bad.php';

echo "== 1. 同じルールが2つのクラスに散っている ==\n";
// 「regularPriceは0以上」というルールを Common も Util も独自に持っている。
// 後続の検証まで到達させたいのでここは catch する。
try {
    Common::discountedPrice(-1, 0.9);
} catch (InvalidArgumentException $e) {
    printf("Common::discountedPrice(-1, 0.9) : %s\n", $e->getMessage());
}
try {
    Util::isFairPrice(-1);
} catch (InvalidArgumentException $e) {
    printf("Util::isFairPrice(-1)            : %s\n", $e->getMessage());
}
echo "                                   ^- 同じルールが2箇所。片方だけ直すと不整合になる\n";

echo "\n== 2. どこにも無いルールは、誰にも気づかれないまま素通りする ==\n";
// discountRate の下限は Common が持っているが、上限はどのクラスも持っていない。
// 例外にならず「もっともらしい値」が返るので、落ちるより気づきにくい。
foreach ([0.9, 0.0, 1.5] as $rate) {
    $note = $rate > 1.0 ? '  <- 「割引」なのに値上がり。例外は出ない' : '';
    printf("割引率 %4.1f : %4d -> %4d%s\n", $rate, 1000, Common::discountedPrice(1000, $rate), $note);
}

echo "\n== 3. 戻り値が int なので、意味の違う値が混ざる ==\n";
// 割引後価格も正規価格も同じ int。型が意味を区別してくれない。
$regularPrice = 1000;
$discounted = Common::discountedPrice($regularPrice, 0.9);
printf("正規価格               : %4d\n", $regularPrice);
printf("割引後価格             : %4d\n", $discounted);
printf(
    "isFairPrice(割引後価格): %s  <- 割引後価格を「正規価格」として検査できてしまう\n",
    Util::isFairPrice($discounted) ? 'true' : 'false'
);

echo "\n== 4. static なので、ルールを持つ場所がそもそも無い ==\n";
// Common も Util もプロパティを1つも持たない。static メソッドは状態を扱えないので、
// 「割引率の上限は1.0」のようなルールをクラス側に保持できない。
// 観点2 で上限がどこにも無かったのは、書き忘れではなく置く場所が無いから。
printf("Common のプロパティ数 : %d\n", count((new ReflectionClass('Common'))->getProperties()));
printf("Util   のプロパティ数 : %d\n", count((new ReflectionClass('Util'))->getProperties()));
echo "                        ^- 状態が無いので、ルールは呼び出し側から毎回渡すしかない\n";

// インスタンス化しても結果は変わらない。クラスが名前空間としてしか機能していない。
$common = new Common();
printf("\nCommon::discountedPrice(1000, 0.9)  : %4d\n", Common::discountedPrice(1000, 0.9));
printf("\$common->discountedPrice(1000, 0.9) : %4d  <- 無警告で通り、結果も同じ\n", $common->discountedPrice(1000, 0.9));
echo "                                       ^- new する意味が無い = データを持たない手続きの置き場\n";
