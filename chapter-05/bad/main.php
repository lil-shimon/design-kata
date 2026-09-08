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
