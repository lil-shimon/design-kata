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

echo "\n";
echo "==================================================\n";
echo " GiftPoint.php : 初期化ロジックが色々な箇所に散る\n";
echo "==================================================\n";

require_once __DIR__ . '/GiftPoint.php';

// --- 生成箇所その1: 会員登録処理 ---
function signUpStandardMembership(): GiftPoint
{
    return new GiftPoint(3000); // 標準会員の入会ポイント
}

// --- 生成箇所その2: キャンペーン申込処理(別の時期に別の人が書いた) ---
function applyStandardCampaign(): GiftPoint
{
    return new GiftPoint(3000); // ここにも同じ 3000 が書かれている
}

echo "\n== 1. 同じ意味の初期値が、複数の箇所で new される ==\n";
// コンストラクタが public なので、GiftPoint を作りたい場所はどこでも自由に new できる。
// 「標準会員の入会ポイントは3000」という知識が、生成箇所の数だけ複製される。
printf("会員登録    の標準会員入会ポイント : %5d\n", signUpStandardMembership()->value());
printf("キャンペーンの標準会員入会ポイント : %5d\n", applyStandardCampaign()->value());

// 仕様変更「標準会員の入会ポイントを 3000 -> 5000 に」。
// 会員登録処理は直したが、キャンペーン申込処理の 3000 は残ったまま。
function signUpStandardMembershipV2(): GiftPoint
{
    return new GiftPoint(5000);
}

echo "\n仕様変更「3000 -> 5000」を会員登録にだけ反映した後:\n";
printf("会員登録    の標準会員入会ポイント : %5d\n", signUpStandardMembershipV2()->value());
printf("キャンペーンの標準会員入会ポイント : %5d  <- 直し忘れ。例外も型エラーも出ない\n", applyStandardCampaign()->value());

echo "\n== 2. 生成の式に「プレミアムは標準の2倍」というルールが漏れる ==\n";
// GiftPoint 側に「プレミアム会員の入会ポイント」という概念が無いので、
// 呼び出し側が 3000 * 2 と書くしかない。ルールがクラスの外に出ている。
function signUpPremiumMembership(): GiftPoint
{
    return new GiftPoint(3000 * 2); // 「標準の2倍」というルールが呼び出し側にある
}
printf("標準会員   : %5d\n", signUpStandardMembership()->value());
printf("プレミアム : %5d  <- 呼び出し側に 3000 * 2 と書かれている\n", signUpPremiumMembership()->value());
echo "             ^- 標準が5000になった時にここが10000になるべきか、GiftPoint は知らない\n";

echo "\n== 3. new GiftPoint(3000) からは、何の 3000 か読めない ==\n";
// 生成が自由ということは、生成された値に名前が付かないということでもある。
$standard = signUpStandardMembership(); // 標準会員の入会ポイント
$campaign = applyStandardCampaign();    // 夏キャンペーンの配布ポイント
printf("標準会員入会ポイント   : %5d\n", $standard->value());
printf("夏キャンペーンポイント : %5d\n", $campaign->value());
printf("2つは等価か (==)       : %s  <- 意味が違うのに区別できない\n", $standard == $campaign ? 'yes' : 'no');
printf("足せてしまう           : %5d  <- add の結果も、何のポイントか名前を持たない\n", $standard->add($campaign)->value());

echo "\n== 4. 「足りているか確認してから消費する」手順が呼び出し側に散る ==\n";
// isEnough と consume が別のメソッドなので、「先に isEnough で確認する」という
// 順序ルールは GiftPoint の中ではなく、consume を呼ぶ箇所すべてに複製される。
$balance = signUpStandardMembership();
$small = new ConsumptionPoint(1000);
$large = new ConsumptionPoint(5000);

printf("残高                       : %5d\n", $balance->value());
printf("1000 は足りるか (isEnough) : %s\n", $balance->isEnough($small) ? 'yes' : 'no');
printf("1000 を消費した後の残高    : %5d\n", $balance->consume($small)->value());
printf("5000 は足りるか (isEnough) : %s  <- ここで止めるのは呼び出し側の責務\n", $balance->isEnough($large) ? 'yes' : 'no');

// isEnough を呼ばずに consume した場合。例外自体は出るが、
// メッセージは生成時のルールのままで「残高不足」という意味を持たない。
try {
    $balance->consume($large);
} catch (InvalidArgumentException $e) {
    printf("確認せずに 5000 を消費     : %s\n", $e->getMessage());
}
echo "                             ^- 「残高不足」ではなく「0以上にしろ」。原因が読み取れない\n";

echo "\n";
echo "==================================================\n";
echo " Location.php : データを持つクラスと操作するクラスを分ける\n";
echo "==================================================\n";

require_once __DIR__ . '/Location.php';

$location = new Location(10, 20);

echo "\n== 1. 操作クラスは、データクラスの中身に触れない ==\n";
// 状態を Location に、操作を Manager に分けた結果、
// Manager から $location->x を書き換えようとして private に弾かれる。
// 「操作を外に出す」設計と、カプセル化は両立しない。
try {
    (new ActorManager())->shift($location, 5, 5);
} catch (Error $e) {
    printf("ActorManager::shift()         : %s\n", $e->getMessage());
}
try {
    (new SpecialAttackManager())->shift($location, 5, 5);
} catch (Error $e) {
    printf("SpecialAttackManager::shift() : %s\n", $e->getMessage());
}
echo "                                ^- どちらの Manager も、そもそも呼べない\n";

echo "\n== 2. private を突破しても、readonly が代入を止める ==\n";
// Manager が触れないのは private のせいだけではない。
// Reflection で private を迂回しても、readonly が生成後の代入を禁止している。
$x = new ReflectionProperty(Location::class, 'x');
printf("Reflection で読む : %d\n", $x->getValue($location));
try {
    $x->setValue($location, 999);
} catch (Error $e) {
    printf("Reflection で書く : %s\n", $e->getMessage());
}
echo "                    ^- shift() が void = 書き換える前提。だが型は不変を宣言している\n";
echo "                       設計の意図と型宣言が矛盾したまま、誰にも気づかれずに存在できる\n";

echo "\n== 3. 「移動する」というルールが Location の外に複製されている ==\n";
// 移動先の計算が2つの Manager に分かれて書かれている。
// 等倍と2倍で中身が違うだけで、「x と y をずらす」という知識は同じもの。
foreach (['ActorManager', 'SpecialAttackManager'] as $class) {
    $method = new ReflectionMethod($class, 'shift');
    printf(
        "%-27s : %s の %d 行目\n",
        $class . '::shift',
        basename($method->getFileName()),
        $method->getStartLine()
    );
}
$locationMethods = array_map(
    fn(ReflectionMethod $m): string => $m->getName(),
    (new ReflectionClass('Location'))->getMethods()
);
printf("Location が持つメソッド     : %s\n", implode(', ', $locationMethods));
echo "                              ^- Location は自分の動かし方を知らない。値を読む手段すら無い\n";

echo "\n";
echo "==================================================\n";
echo " MagicPoint.php : 引数が多い = 状態を呼び出し側に持たせている\n";
echo "==================================================\n";

require_once __DIR__ . '/MagicPoint.php';

$magicPoint = new MagicPoint();

echo "\n== 1. MagicPoint は自分の状態を1つも持たない ==\n";
// MP を扱うクラスなのに、現在MP も 最大MP も持っていない。
// 状態が無いので、回復に必要な値は毎回 呼び出し側 が全部揃えて渡すしかない。
// 「引数が多い」のは書き方の問題ではなく、状態の置き場所が無いことの結果。
$recover = new ReflectionMethod('MagicPoint', 'recover');
printf("プロパティ数     : %d\n", count((new ReflectionClass('MagicPoint'))->getProperties()));
printf("recover() の引数 : %d\n", $recover->getNumberOfParameters());
printf(
    "引数の内訳       : %s\n",
    implode(', ', array_map(
        fn(ReflectionParameter $p): string => $p->getType() . ' $' . $p->getName(),
        $recover->getParameters()
    ))
);
echo "                   ^- MP に関する知識が全部 呼び出し側 にある\n";

echo "\n== 2. int が3つ並ぶので、順番を取り違えても型は何も言わない ==\n";
// 現在MP / 元の最大MP / 回復量 はどれも int。取り違えても TypeError にならず、
// もっともらしい値が返る。落ちてくれないぶん、気づく手掛かりが無い。
$increments = [10, 10, 20]; // 最大MPの増加量(装備 +10 / +10、レベルアップ +20)
printf("正しい順序                     : %4d\n", $magicPoint->recover(50, 100, $increments, 30));
printf("current と original を入れ替え : %4d  <- 例外は出ない。静かに違う値になる\n", $magicPoint->recover(100, 50, $increments, 30));
printf("current と recovery を入れ替え : %4d  <- 正解と同じ値。テストしても気づけない\n", $magicPoint->recover(30, 100, $increments, 50));

echo "\n== 3. 引数が多いほど、呼び出し側で揃え損ねる ==\n";
// 最大MPの増加量は装備やレベルアップで増える。その一覧を、recover を呼ぶ箇所すべてが
// 自前で正しく組み立てる必要がある。1箇所でも組み立てを間違えると挙動が食い違う。
function restAtInn(MagicPoint $magicPoint, int $current): int
{
    return $magicPoint->recover($current, 100, [10, 10, 20], 30);
}
function castHealSpell(MagicPoint $magicPoint, int $current): int
{
    // 別の時期に書かれた呼び出し。レベルアップ分の +20 が漏れている
    return $magicPoint->recover($current, 100, [10, 10], 30);
}
printf("現在MP     : %4d\n", 130);
printf("宿屋で休む : %4d\n", restAtInn($magicPoint, 130));
printf("回復魔法   : %4d  <- 同じ状態のはずが上限が違う。回復したのに減っている\n", castHealSpell($magicPoint, 130));
echo "             ^- 増分の組み立てを MagicPoint が知らないので、呼び出し箇所の数だけ複製される\n";

echo "\n== 4. 引数が4つあるのに、そのどれにも検証が無い ==\n";
// 状態を持たない = 不変条件を守る場所が無い、ということでもある。
// 観点1 で見た通り MagicPoint には守るべき自分の値が無いので、検証を置きようがない。
printf("回復量に -30       : %4d  <- 「回復」なのに減る\n", $magicPoint->recover(50, 100, [], -30));
printf("現在MPに -999      : %4d  <- 負のMP がそのまま返る\n", $magicPoint->recover(-999, 100, [], 10));
printf("増分に -50         : %4d  <- 最大MPが元の 100 より小さくなる\n", $magicPoint->recover(50, 100, [-50], 30));
echo "                     ^- 4つとも呼び出し側から来る値なのに、1つも検査していない\n";
