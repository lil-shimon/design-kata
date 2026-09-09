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

echo "\n";
echo "==================================================\n";
echo " Location.php : 操作をデータと同じクラスに置く\n";
echo "==================================================\n";

require_once __DIR__ . '/Location.php';

echo "\n== 1. 移動のルールが Location の中に1つだけある ==\n";
// bad では ActorManager と SpecialAttackManager が同じ計算を別々に持ち、
// しかも private に弾かれて呼ぶことすらできなかった。
// good では Manager を通さず、Location 自身に動き方を聞く。
$location = new Location(10, 20);
$moved = $location->shift(5, 5);
printf("移動前 : (%3d, %3d)\n", $location->x(), $location->y());
printf("移動後 : (%3d, %3d)  <- shift(5, 5)\n", $moved->x(), $moved->y());
echo "-> 移動先の計算が書かれているのは shift() の中だけ。Manager は要らなくなった\n";

echo "\n== 2. 引数を書き換えず、結果を戻り値で返す ==\n";
// bad は ActorManager::shift(Location $location, int, int): void だった。
// 第1引数の Location を書き換えて結果を返す、いわゆる出力引数。
// good の shift は、書き換える相手をそもそも引数に取らない。
$shift = new ReflectionMethod(Location::class, 'shift');
$signature = array_map(
    fn(ReflectionParameter $p): string => $p->getType() . ' $' . $p->getName(),
    $shift->getParameters()
);
printf("Location::shift            : (%s): %s\n", implode(', ', $signature), $shift->getReturnType());

$objectParams = array_filter(
    $shift->getParameters(),
    fn(ReflectionParameter $p): bool => !$p->getType()->isBuiltin()
);
printf("書き換え対象になりうる引数 : %d 個\n", count($objectParams));
printf("戻り値の型                 : %s  <- void ではない\n", $shift->getReturnType());
echo "                             ^- 結果は戻り値にしか現れない = 出力引数になっていない\n";

echo "\n";
echo "==================================================\n";
echo " MagicPoint.php : 状態をクラスに持たせて、引数を減らす\n";
echo "==================================================\n";

require_once __DIR__ . '/MagicPoint.php';

echo "\n== 1. 状態を自分で持つので、recover の引数が 4つ -> 1つ になった ==\n";
// bad の recover は (int, int, array, int) の4引数だった。
// MP に関する値を MagicPoint 自身が持つと、外から渡すのは「いくら回復するか」だけになる。
// 引数の順番を取り違える余地は、実行時に検証するまでもなく消えている。
$recover = new ReflectionMethod(MagicPoint::class, 'recover');
printf("プロパティ数     : %d\n", count((new ReflectionClass(MagicPoint::class))->getProperties()));
printf("recover() の引数 : %d\n", $recover->getNumberOfParameters());
printf(
    "引数の内訳       : %s\n",
    implode(', ', array_map(
        fn(ReflectionParameter $p): string => $p->getType() . ' $' . $p->getName(),
        $recover->getParameters()
    ))
);
echo "                   ^- bad は int が3つ並んでいた。並んでいなければ入れ替えようがない\n";

echo "\n== 2. 最大MPの計算が、クラスの中の1箇所にしかない ==\n";
// bad では増分の一覧を呼び出し箇所ごとに組み立てていて、回復魔法だけ +20 が漏れていた。
// good ではどちらも同じ MagicPoint に頼むので、上限がズレようがない。
function restAtInn(MagicPoint $magicPoint): void
{
    $magicPoint->recover(30);
}
function castHealSpell(MagicPoint $magicPoint): void
{
    $magicPoint->recover(30);
}
$atInn = new MagicPoint(130, 100, [10, 10, 20]); // 装備 +10 / +10、レベルアップ +20
$byMagic = new MagicPoint(130, 100, [10, 10, 20]);
restAtInn($atInn);
castHealSpell($byMagic);
printf("現在MP     : %4d (最大 %d)\n", 130, $atInn->max());
printf("宿屋で休む : %4d\n", $atInn->current());
printf("回復魔法   : %4d  <- bad は 140 / 120 とズレていた\n", $byMagic->current());
echo "             ^- 呼び出し側に増分の配列が1つも書かれていない\n";
