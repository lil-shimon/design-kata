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

echo "\n";
echo "==================================================\n";
echo " GiftPoint.php : 初期化ロジックがクラスの中に集まる\n";
echo "==================================================\n";

require_once __DIR__ . '/GiftPoint.php';

// --- 生成箇所その1: 会員登録処理 ---
function signUpStandardMembership(): GiftPoint
{
    return GiftPoint::forStandardMembership();
}

// --- 生成箇所その2: キャンペーン申込処理(別の時期に別の人が書いた) ---
function applyStandardCampaign(): GiftPoint
{
    return GiftPoint::forStandardMembership();
}

echo "\n== 1. コンストラクタが private なので、外から自由に new できない ==\n";
// bad は public コンストラクタだったので、GiftPoint を作りたい場所はどこでも
// new GiftPoint(3000) と書けた。good では生成の入口がファクトリだけになる。
try {
    new GiftPoint(3000);
} catch (Error $e) {
    printf("new GiftPoint(3000) : %s\n", $e->getMessage());
}
echo "                      ^- 「勝手に作れる場所」がクラスの外に存在しない\n";

echo "\n== 2. 初期値の知識が、クラスの中の1箇所に集まる ==\n";
// bad では生成箇所の数だけ 3000 が複製され、片方だけ直して不整合になった。
// good ではどちらの生成箇所も同じファクトリを呼ぶので、リテラルを1つも持たない。
printf("会員登録    の標準会員入会ポイント : %5d\n", signUpStandardMembership()->value());
printf("キャンペーンの標準会員入会ポイント : %5d\n", applyStandardCampaign()->value());
echo "                                      ^- 呼び出し側に数値リテラルが1つも無い\n";
printf(
    "\n値が書かれている場所 : GiftPoint::STANDARD_MEMBERSHIP_POINT = %d\n",
    GiftPoint::STANDARD_MEMBERSHIP_POINT
);
echo "-> 仕様変更「3000 -> 5000」で直すのはこの1行だけ。直し忘れる2箇所目が無い\n";

echo "\n== 3. 「プレミアムは標準の2倍」が呼び出し側から消える ==\n";
// bad では GiftPoint 側にプレミアムの概念が無く、呼び出し側が 3000 * 2 と書いていた。
printf("標準会員   : %5d\n", GiftPoint::forStandardMembership()->value());
printf("プレミアム : %5d  <- 呼び出し側に計算式が無い\n", GiftPoint::forPremiumMembership()->value());
// ただし「2倍」というルール自体はどこにも残っていない。定数2つが独立しているので、
// 標準を5000にしてもプレミアムは10000のまま。連動させたいなら、それはクラスの中で書く。
echo "             ^- 2つの定数は独立。連動させるかどうかもクラスの中で決められるようになった\n";

echo "\n== 4. 生成の式そのものが、何のポイントかを語る ==\n";
// bad では new GiftPoint(3000) を読んでも、入会ポイントなのか
// キャンペーンポイントなのか、呼び出し側のコメントを読むまで分からなかった。
printf("GiftPoint::forStandardMembership() : %5d\n", GiftPoint::forStandardMembership()->value());
printf("GiftPoint::forPremiumMembership()  : %5d\n", GiftPoint::forPremiumMembership()->value());
echo "                                     ^- 値ではなくメソッド名が意味を運ぶ\n";
