<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Migrate;
use App\Action\Actions\Cli\XRPL\AnalyzeNFTs as AnalyzeNFTsXRPL;
use App\Action\Actions\Cli\XRPL\CalcRichLists as CalcRichListsXRPL;
use App\Action\Actions\Cli\XRPL\UpdateDataNFT as UpdateDataNFTXRPL;
use App\Action\Actions\Cli\Ethereum\UpdateDataNFT as UpdateDataNFTEthereum;
use App\Action\Actions\Cli\Ethereum\CalcRichLists as CalcRichListsEthereum;
use App\Action\Actions\Cli\Base\UpdateDataNFT as UpdateDataNFTBase;
use App\Action\Actions\Cli\Base\CalcRichLists as CalcRichListsBase;
use App\Action\BaseAction;
use App\Models\Collection;
use App\Query\BlockchainTokenQuery;
use App\Query\CollectionQuery;

class Cli extends BaseAction
{

    private string $action;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->terminal = true;
        parent::__construct();

        if (!$_SERVER['argv']) {
            exit;
        }

        if (!isset($_SERVER['argv'][1])) {
            exit;
        }

        $this->action = $_SERVER['argv'][1];

        if ($this->action === 'update-data-nft-xrpl') {
            (new UpdateDataNFTXRPL())->run();
        }

        if ($this->action === 'update-data-nft-ethereum') {
            (new UpdateDataNFTEthereum())->run();
        }

        if ($this->action === 'update-data-nft-base') {
            (new UpdateDataNFTBase())->run();
        }

        if ($this->action === 'remove-ext') {


//            $files = glob('/var/www/metadata-bullrunpunks/*.json');
//            foreach ($files as $file) {
//                $filenameWithoutExtension = str_replace('.json', '', $file);
//                file_put_contents(str_replace('punks', 'punks-2', $filenameWithoutExtension), file_get_contents($file));
//            }



// Set the source and destination directories
            $sourceDir = '/var/www/kut copy/';
            $destDir = '/var/www/kut_no_json/';

// Check if the destination directory exists, if not, create it
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }

// Get all files in the source directory
            $files = array_diff(scandir($sourceDir), array('..', '.'));

// Loop through the files and rename + move them
            foreach ($files as $file) {
                $fileInfo = pathinfo($file);
                $filename = $fileInfo['filename'];
                $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : ''; // Get file extension

                // Check if the file name is a valid number (0-9999) and if it ends with .json
                if (is_numeric($filename) && strtolower($extension) === 'json') {
                    $newName = (intval($filename) + 1); // Increment the file name by 1

                    // Ensure the new file has no extension (as per your requirement)
                    $newFileName = $newName;

                    // Move and rename the file, remove the .json extension
                    $sourceFilePath = $sourceDir . $file;
                    $destFilePath = $destDir . $newFileName; // No .json extension here

                    if (rename($sourceFilePath, $destFilePath)) {
                        echo "Moved and renamed: $file -> $newFileName\n";
                    } else {
                        echo "Failed to move or rename: $file\n";
                    }
                }
            }

        }

        if ($this->action === 'calc-richlists-xrpl') {
            echo 'calculating richlists for XRPL' . PHP_EOL;
            (new CalcRichListsXRPL())->run();
            echo ' - finished' . PHP_EOL;
            exit;
        }

        if ($this->action === 'calc-richlists-ethereum') {
            echo 'calculating richlists for Ethereum' . PHP_EOL;
            (new CalcRichListsEthereum())->run();
            echo ' - finished' . PHP_EOL;
            exit;
        }

        if ($this->action === 'calc-richlists-base') {
            echo 'calculating richlists for Base' . PHP_EOL;
            (new CalcRichListsBase())->run();
            echo ' - finished' . PHP_EOL;
            exit;
        }

        if ($this->action === 'analyze-nfts-xrpl') {
            (new AnalyzeNFTsXRPL())->run();
        }

        if ($this->action === 'migrate') {
            (new Migrate())->run();
        }

        if ($this->action === 'create-wl-new-collections') {
            $this->create_wl_nogens();
            $this->create_paid_wl_nogens();
//            $this->create_free_wl_based_habibi();
//            $this->create_paid_wl_based_habibi();
        }

        if ($this->action === 'create-loadingpunks-metadata') {
            $this->createOpenSeaCryptoPunksMetadata();
        }

        if ($this->action === 'create-luke-metadata') {
            $this->createOpenSeaLooneyLukeMetadata();
        }

        if ($this->action === 'create-justan-metadata') {
            $this->createOpenSeaJustanAlienMetadata();
        }

        if ($this->action === 'create_wl_justan_alien') {
            $this->create_wl_justan_alien();
        }

        if ($this->action === 'download') {
            $this->download_full_metadata();
        }

        if ($this->action === 'wl-loadingpunks') {
            $endpoint = 'https://richlist.hasmints.com/json/hasmints/ethereum/Q7YGA3CAN11KZZ2SCIKUT0RY';

            $wallets = (array) json_decode(file_get_contents($endpoint), true);

            $count = 0;
            $csv = 'wallet,mints' . PHP_EOL;
            foreach ($wallets as $wallet => $collectionData) {
                $csv .= $wallet . ',' . $collectionData['collections']['LoadingPunks']['total'] .  PHP_EOL;
                $count = $count + $collectionData['collections']['LoadingPunks']['total'];
            }

            var_dump($count);
            file_put_contents(ROOT . '/wallets-free-wl-loadingpunks.csv', $csv);
        }

        if ($this->action === 'rename-images') {
            $newId = 1;
            foreach (range(160, 5360) as $oldId) {

                if ($oldId !== 5000) {

                    $oldImage = 'images-dickbuttsonape/' . $oldId . '.png';
                    $newImage = 'images-dickbuttsonape-new/' . $newId . '.png';

                    if (file_exists($oldImage)) {
                        if (copy($oldImage, $newImage)) {
                            echo "Copied $oldImage to $newImage\n";
                        } else {
                            echo "Failed to copy $oldImage to $newImage\n";
                        }
                    } else {
                        echo "File does not exist: $oldImage\n";
                    }

                    $newId++;
                }
            }
        }

        if ($this->action === 'fix-metadata') {
            $newId = 1;
            $test = range(160, 5360);
//            var_dump(count($test));exit;

//            foreach ($test as $id) {
//                if (!file_exists('images-dickbuttsonape/' . $id . '.png')) {
//                    var_dump($id);exit;
//                }
//            }
//            exit;

            foreach (range(160, 5360) as $imageId) {

                if ($imageId !== 5000) {

                    $originalMetadata = file_get_contents('metadata-crypto-dickbutts/' . $imageId . '.json');
                    $originalMetadata = (array) json_decode($originalMetadata, true);
                    $newMetadata = $originalMetadata;
                    $newMetadata['name'] = 'DickButt on Ape #' . $newId;
                    $newMetadata['tokenId'] = $newId;
                    $newMetadata['image'] = $newId . '.png';



                    file_put_contents('metadata-dickbuttsonape/' . $newId . '.json', json_encode($newMetadata));

                    $newId++;
                }


            }
        }
    }

    private function create_wl_nogens()
    {
        $whiteListSource = (array) json_decode(file_get_contents('https://richlist.hasmints.com/json/nopatty/base/943UO7HBU5W7BGI6I6R9MV7P'), true);
        $wallets = array_keys($whiteListSource);
        $wallets = array_unique($wallets);

        $csv = 'wallet,mints' . PHP_EOL;
        foreach ($wallets as $wallet) {
            $csv .= $wallet . ',1' . PHP_EOL;
        }
        file_put_contents(ROOT . '/wallets-free-wl-nogens.csv', $csv);
    }


    private function create_paid_wl_nogens()
    {
        $whiteListSourceFree = (array) json_decode(file_get_contents('https://richlist.hasmints.com/json/nopatty/base/943UO7HBU5W7BGI6I6R9MV7P'), true);
        $walletsFree = array_keys($whiteListSourceFree);

        $whiteListSource = (array) json_decode(file_get_contents('https://richlist.hasmints.com/json/nopattypaid/base/943UO7HBU5W7BGI6I6R9MV7Q'), true);
        $wallets = array_keys($whiteListSource);

        $wallets = array_merge($walletsFree, $wallets);

        $wallets[] = '0x4F6ef5051D280a8805aDF81b25F770a9da603CB9';
        $wallets[] = '0xA1E6152dDfd4A1788Dc329c052ac765F514aE911';
        $wallets[] = '0xc0b897cf39a4dC9d344E582c5C00B1db813B3494';
        $wallets[] = '0xb201FD3Ca3bE8e65778e358D004C9aD57ceB4bF6';
        $wallets[] = '0x0dE09a751a2DE29B36D880fe6f0983a8935760a5';
        $wallets[] = '0x462CF879Ed708C3e934f8e2b9DCf22E028B096Ba';
        $wallets[] = '0xa7E0F26f99cF22372c5c0c5a29438AD9DAaFbDD0';
        $wallets[] = '0xE6D6CB96b6A0e7ac992A2d4B42B83B2Ae386525a';
        $wallets[] = '0x82A5748673C8ca39A66f44F407dC00C937802eea';
        $wallets[] = '0x26Ca989d6d2f83942c5b0f783944c84f74868ffa';
        $wallets[] = '0x973707A7282D63aF3737CA27532E06B7F8f8d8b9';
        $wallets[] = '0xE3f530861065F621ff547fF3450aB5351031C7Fa';
        $wallets[] = '0xb0947DD081FDD77F87267D742975B51a91BD1bb2';
        $wallets[] = '0x364A6b5646EDD632371886aBE7a22D018703B554';
        $wallets[] = '0x8c69DC3035628af59F423C5c5C6C7dA8ADe2A71A';
        $wallets[] = '0x0504556B1734368d7075Ab704d06212a9a7Deae8';
        $wallets[] = '0x9ce8E07C6C7d1D657330E9DE080aEE5e37De4196';
        $wallets[] = '0xD10B504757C72A7207Fbe6d36Cd54a8c79e2E3bC';
        $wallets[] = '0x40ED529313137684245A1bE9396cD9fD9cec10E3';
        $wallets[] = '0x89A2d2255287070cEbbd6C09A1512a4CA4926Bcb';

        $wallets = array_unique($wallets);

        $csv = 'wallet,mints' . PHP_EOL;
        foreach ($wallets as $wallet) {
            $csv .= $wallet . ',100' . PHP_EOL;
        }
        file_put_contents(ROOT . '/wallets-paid-wl-nogens.csv', $csv);
    }

    private function create_free_wl_based_habibi()
    {

        $whiteListSource = (array) json_decode(file_get_contents('https://richlist.hasmints.com/json/basedhabibifreewl/base/Z57JYU9TMV2LKQ3G6A1BP4WR'), true);
        $wallets = array_keys($whiteListSource);

        // Habibi_DAO_NFT
        $wallets[] = '0xBDB969A121D3Bd526D90996D426e815C1e88652B';
        $wallets[] = '0x34379E29ae02055cB9558DB402f0762a786cBB44';

        $wallets = array_unique($wallets);

        $csv = 'wallet,mints' . PHP_EOL;
        foreach ($wallets as $wallet) {
            $csv .= $wallet . ',1' . PHP_EOL;
        }
        file_put_contents(ROOT . '/wallets-free-wl-habibi.csv', $csv);
    }


    private function create_wl_justan_alien()
    {

        $whiteListSource = (array) json_decode(file_get_contents('https://richlist.hasmints.com/json/nopatty/base/943UO7HBU5W7BGI6I6R9MV7P'), true);
        $wallets = array_keys($whiteListSource);

        $wallets[] = '0x0d91471d46a885e205b0cf5d447a91abfbf2c81e';
        $wallets[] = '0xa6f60f8063df779efbe6b85abd42cea993da4c8c';
        $wallets[] = '0x8f8d76b291ccda7659a8b95c359ef4d05fadb25b';
        $wallets[] = '0x6de8bdd19cd76b89ea2eb1ab6d9b245433652ef9';
        $wallets[] = '0x7f448f0435803744bcda76afed4f17b0a6e0fb23';
        $wallets[] = '0xf01fdecadaa7553150f913f2de6a6f2aafd9da29';
        $wallets[] = '0x731a115945d08dec53dfd0f87dbcbce40db8dfa7';
        $wallets[] = '0xb203faa6207ce9384d46fa5b9f397d304f17943c';
        $wallets[] = '0x02856fadf19fdd7049a981885a575d962135fa8b';
        $wallets[] = '0x08271518696dd55ce933b4f399000de19ab22e0f';
        $wallets[] = '0xe7d5233b655bd23615d6e7983680d27f92fa887d';
        $wallets[] = '0x9d995f45e0ac56d69b4305b116a62f2f2b36b944';
        $wallets[] = '0x0967cff245a0adc506652799aaa1cbfe667fffa2';
        $wallets[] = '0x548a83ca6a293271214044ae56ef8523c8415a4b';
        $wallets[] = '0x91a35d0d46792ec18d3ec72e7fddd85653c385d3';
        $wallets[] = '0xa0d43450060d37c26f4d9ca4b24f868012821f28';
        $wallets[] = '0xac2548e2fd81214fdce699fc0f198bd832409123';
        $wallets[] = '0xf5a11baf2643fa17b707c247f20959a11cc44f7d';
        $wallets[] = '0x7bc219a5315552a4dc6ee82c435ce0b592a3d109';
        $wallets[] = '0xe2a63da33a3d7953b9d83ca19e2a9eb2bdd53462';
        $wallets[] = '0x5829ecbcddf0fa6a9ed23fd3921e1b39e1fa1f2f';
        $wallets[] = '0xad2cc1b3750c40719c18d8145cd9d0285a1d1d7b';
        $wallets[] = '0x728a510d6611149f9fd6e5e62938bcf3303c0f90';
        $wallets[] = '0x98f720994c1ea2e5ba3a2c5dd9902b6558c70f4a';
        $wallets[] = '0x206d359900f3becfce0c0f51ca2234d19d367757';
        $wallets[] = '0x30f9e0d7b0357f4a4efe3b1cfb802739de067244';
        $wallets[] = '0x4962caedfdfcaedacf9db8be377e0191edd97266';
        $wallets[] = '0x67b4ab6aca056262159c560623186bfd334e34af';
        $wallets[] = '0x7efd5ba8c85c674016e2154e4739c20a9af81b44';
        $wallets[] = '0xadd6fe623b1b247c5e878bad51bb13a5c8775f8b';
        $wallets[] = '0xc4ba929015d42a58c4dc29c63847d7a81d6284d3';
        $wallets[] = '0x16ab446c8112812c2092e99215ba521c029526d3';
        $wallets[] = '0x3b81b1f0397458b20a1cb8f4a7467224d8002c59';
        $wallets[] = '0x4500cb374c0128f5860d34c86a8c353b6d8dd6cf';
        $wallets[] = '0x5b0c5a232827344ad6dd8c9ceb941145ea67fa99';
        $wallets[] = '0x6ea3b97e28f6d65034fb814cda9770a92bea4bf2';
        $wallets[] = '0x75521de871eea2f188a45e1a232bb6794600ebc6';
        $wallets[] = '0xbf26c1e998d1a2bd22ce847c58cb24c1d97af703';
        $wallets[] = '0xd621766e949b31cdc7f0b7f11c0e0524f9fb743d';
        $wallets[] = '0xec5f2310418a112fef190d30fc5f1ab2d58fd1b5';
        $wallets[] = '0xf5d9acd516869cf8d78652838b275db3ffa00892';
        $wallets[] = '0x06b1edc6eb90c1ce29bd426aa824cd87be555f94';
        $wallets[] = '0x239c8dac99ceb0d6e1f0cc612170e14ea0217410';
        $wallets[] = '0x4c21e42b6bc1bac767e7b895ee28e05d2347f8e2';
        $wallets[] = '0x64868a5cabd6badf00339752b98740b762b7a69b';
        $wallets[] = '0x78f410bacb028b2f81207389f39b755cf5d53230';
        $wallets[] = '0xa5bab6296ff112ebf9d6f88b9bf63e6af2b318ba';
        $wallets[] = '0xb283da3c2b9b5a0bb04b1b68d91032d89ee6a422';
        $wallets[] = '0xdc48cb1812f9b7f7ca52dd27bebd26dd8ff817df';
        $wallets[] = '0xef6b8fc64e1308a974faebd021ba7b6380fc3299';
        $wallets[] = '0x163d24bf5a87218761f1b004282030057ceea02e';
        $wallets[] = '0x184ba00be49d642f42e9d814a762d7b4ebdf50bf';
        $wallets[] = '0x22da9ba7534102c7816da93e60ad35a5e4c03061';
        $wallets[] = '0x2acaa4d2b7c54e60b1a22e52991d73ecf685ff8c';
        $wallets[] = '0x3552acb70f76087449740859048373664cdae99b';
        $wallets[] = '0x3be5b332203084cd11a3670e24f098fb0b711d02';
        $wallets[] = '0x45179c28321d3e5dc965faa39e70ea67f5b5ec3a';
        $wallets[] = '0x489dc75cca11d5697564a68e956dcad68bdaa1b8';
        $wallets[] = '0x49f4255bf5922959960d49c414231217a0e2f257';
        $wallets[] = '0x4ed357f91196c4477a2b9e3a2529f599ac23b31c';
        $wallets[] = '0x4ed98189870923040c1fd6c97c1dad38dc4d45bd';
        $wallets[] = '0x522565135a146209256211b01f2aceb69bba0d71';
        $wallets[] = '0x5b191ee9a180a85276f010126047b5ffe28e0191';
        $wallets[] = '0x63fa6db8ae8d072237ae6e39ddb695b9ae47221b';
        $wallets[] = '0x6cdde82c767644b1c20b5f4902d220deac3fe9c8';
        $wallets[] = '0x6e7f570ab2c02fe96c053e0ec8dcad619cc1e06b';
        $wallets[] = '0x765f37c194282e72258ca154286fc95749c1f981';
        $wallets[] = '0x76b1f3a3cc59838ee1049563daf25010e30f1acb';
        $wallets[] = '0x7aaef447c7e724ab17cad5d626a7721289ba5cc3';
        $wallets[] = '0x7f74c8c4a1f7b480b614e46cde69d7489630698f';
        $wallets[] = '0x80d529678af8fac77df2db810795f7e143bd89a6';
        $wallets[] = '0x8b956dd5bec2c40a77c77c843beca3df6d03aeb1';
        $wallets[] = '0xa386c804cfd788654fb7f3df343a6f7999049947';
        $wallets[] = '0xbb96f5443a60c7f8034ad4b8d6402f8e1babae6d';
        $wallets[] = '0xd43d36a36bdffc59d06782f671a123291ee7569f';
        $wallets[] = '0xe06c10eef93b17256a84e3d28bb6ae75e69f89c3';
        $wallets[] = '0xe3986cbb04ecc3aa728162fa85f8636da2706298';
        $wallets[] = '0xed6ed945077fe62a5cb23bb8995375da3d0281b4';
        $wallets[] = '0xedb8e1fac3e2d01dcd9a592a1b7b80904a0cd97e';
        $wallets[] = '0xf33b9684b37542cfe043e4ca9f1b3abece177976';
        $wallets[] = '0xf99a194a2a4ac2e34d9d3011b008979e8a2aee9d';
        $wallets[] = '0xfcec962d1b15121ffac681435faba8880e6caecd';
        $wallets[] = '0x0222beb26f80f07a34113919c2d8ff4f6e194b25';
        $wallets[] = '0x031eb7a5dad63990a5cd7ea9d116af8b5b7213f9';
        $wallets[] = '0x07bc35e1438719171c0f1e0540c5655c0fd17603';
        $wallets[] = '0x07fbd5f174d3b51b41f820a13995e929d9e74beb';
        $wallets[] = '0x084169a8869edce289215d312db5bd1b121262d3';
        $wallets[] = '0x0a98a5c6e90596fab6b647a77970801a280b1154';
        $wallets[] = '0x0b17024baa008c26f104ec4bdd0246e62d0fc1c1';
        $wallets[] = '0x0b839c6e4a5c6c4aa1a3281a7488246db3f3a7e8';
        $wallets[] = '0x0c09bf276a8447bfdbd6da30ee8965d4e3514caa';
        $wallets[] = '0x0c645ab91f2e0d49756de454d35814fa02be878f';
        $wallets[] = '0x0ec3c7e0e35afe4044a82dc864e5f00c2a548c53';
        $wallets[] = '0x0f4529682a8a4c8de966ef415b6e67dced82084d';
        $wallets[] = '0x0fda8a2e9288f2359811a7fc2253292ad3a22d4d';
        $wallets[] = '0x1105bbdd72b002880fbf514273a8c66b6b205306';
        $wallets[] = '0x15a325bbf3010c1ca850cf295b67ee43e993bb6d';
        $wallets[] = '0x15c926a95233ec566d49971455cae1f5a993cc8a';
        $wallets[] = '0x1ad1326ae5cad3c912d7899649286afbcf908925';
        $wallets[] = '0x1baf28445cfd8118e7618ca6c09a7c37661bebaf';
        $wallets[] = '0x1bb50c4e574b089a123b3b3dafb314b8691baee4';
        $wallets[] = '0x1e5eeec683c1772628e8619aedb0dc3b64423de7';
        $wallets[] = '0x1f67d9f9829f375cd43f19f65acda8483e4ea308';
        $wallets[] = '0x2aa691212bda688664d83107cd7bb0017dc1100a';
        $wallets[] = '0x2bbc124b13df24d2df0482f482b51eb05696e996';
        $wallets[] = '0x345897b4a8dafe697a4e89ef4e89b0ffa9c34e32';
        $wallets[] = '0x352c3f4dc9db25970aac862a5780f80a9f359023';
        $wallets[] = '0x38b8c82088b5267c6c87a609498f1be872e16546';
        $wallets[] = '0x39f8ca9c7f7227dc2479f1ff66de6c350f0c540b';
        $wallets[] = '0x3bad0c9993cef246ef526f816e76c9612f7172d1';
        $wallets[] = '0x3d725c33f217052c624fdd9fa374c29dbc5aa9e4';
        $wallets[] = '0x45384cfaa791c55051985b7b3d3d094bbf870aa2';
        $wallets[] = '0x46885d6e4657f2a6ef243d3f1525df78a1446545';
        $wallets[] = '0x4844e846900b31ffca52e0e8c44fc30cc52590fc';
        $wallets[] = '0x488840ed28846c895cfca02b038496574dfc5a37';
        $wallets[] = '0x493d546604457fddaf869b2b61ef7f8626f4e18a';
        $wallets[] = '0x4a51bbb98e93f0cceed56669f4a049fae0e3731a';
        $wallets[] = '0x4b54d304d38ba5e9d2ff0842ec8f96f16e7350b4';
        $wallets[] = '0x4e4f49220fd95a000a8019ca5fcc5e63509d343d';
        $wallets[] = '0x4e8e9d2c0252db25e7a288f53918226abf6473f9';
        $wallets[] = '0x5765e001d1c2175849b837c505d04e53aeefaeda';
        $wallets[] = '0x57727d5a058815655a5a9b312d8c0fd3491927e6';
        $wallets[] = '0x599cc2b0d7d8060a226883b834fab6b8707f4a7b';
        $wallets[] = '0x5a894294014ec7d4ae60ed79db0f3ac45d949e61';
        $wallets[] = '0x5bfd2b9afbeb4355b7d53ed5c8d7d3706734dbd8';
        $wallets[] = '0x5de1fbe8b909ab29c9d38ce188539fc5c72c84f7';
        $wallets[] = '0x632a2d924efbde7bbfea535a01849e93fb989507';
        $wallets[] = '0x66e00e8210d375abebe0a21bdad6b6f9419de3d8';
        $wallets[] = '0x6c7b8f499a3a0c310e4a1c9edb8406e764590950';
        $wallets[] = '0x6f278d4aead3f0f4e2b85a5fbe98b078d9df42de';
        $wallets[] = '0x6f4a8fd67f40bcee163a088546a1c964d7d05c52';
        $wallets[] = '0x70fee14b3259b1ceda749f1bf3b731e989d586d9';
        $wallets[] = '0x71d66dd89b3f790afb3d08f2ad17b5165fc2a0ac';
        $wallets[] = '0x7876424168f67b6bb9426a85dff971c76de8113c';
        $wallets[] = '0x7a5d06306a1881680fedfe2aa118c5af51ed5323';
        $wallets[] = '0x7ac30a38778ee7f2093a3ab81ba1eee412b15797';
        $wallets[] = '0x7b12524a85e065714af1acf69bf3c698a3c1d157';
        $wallets[] = '0x7db665cb14045fe9b749d4f2f168831099b4e69e';
        $wallets[] = '0x7e0d37b2dcf423ca78e8dbff37724bf39ed9f659';
        $wallets[] = '0x7efd660324a84881935756bba99526bfd9ec2292';
        $wallets[] = '0x85331418b7629fe3e785d3751d0ae2a3d997c946';
        $wallets[] = '0x8896b42460ecebb749b11cefb02532ed4d75f296';
        $wallets[] = '0x8ba3187ed532f373984b50ab07b554e0ec8fbb5c';
        $wallets[] = '0x8cf371fb1b2887138dccbf939d38624ee54bcce9';
        $wallets[] = '0x8e86f33ef400f6306ffd0e7ade68bb2e00b03b1b';
        $wallets[] = '0x8fbb3d193bd96a2729abb31f89a7cdfe5559bd7e';
        $wallets[] = '0x90c80115a0d90cff95237c14ffbf5fc3cb69b7ca';
        $wallets[] = '0x9689a82a8ccfa9c75122e74a9dfb74a76eac2be8';
        $wallets[] = '0x96b468b888a9123e0e64b0a411ca995e7f6d1699';
        $wallets[] = '0x9707a87992d52fae942be5a7b8d619f629921463';
        $wallets[] = '0x980224ce5a59a667408811fb9a1786caac35f3ef';
        $wallets[] = '0xa12890f31c513ea7a4a297caeafa647447278123';
        $wallets[] = '0xa1470cbe9872f58322e48840a001950825e1a09b';
        $wallets[] = '0xaa8abd8bd157b0f0c79330496be96b6e1f9fcb35';
        $wallets[] = '0xaaec910120384bfcfd8cd34ae38e068bb45a3162';
        $wallets[] = '0xac63d342f2a0a5542a88e832b1976c818107a087';
        $wallets[] = '0xacde04b98b0cd786d1ecbc0ab62f16c8c9743b12';
        $wallets[] = '0xad59805b42de2075d48bb7c73bd02087c3228cb2';
        $wallets[] = '0xaea36cb848ac70f1a33e7e375744e06f68ef53ce';
        $wallets[] = '0xaf2e5b5aa3542c1acdcc692bab3c578d7504c94c';
        $wallets[] = '0xb0ca0581dc497fb32b72be80e323abf9c2ff35f2';
        $wallets[] = '0xb67647f25492da0ee4967d971b8e3e8d89b1749f';
        $wallets[] = '0xb67e994c094aaa8375926b8d03350f83618fca07';
        $wallets[] = '0xbb65c0b47b8da688264f5e2f73aa359fdbee700c';
        $wallets[] = '0xbce298b759acff20271cb9143d58d6625480149a';
        $wallets[] = '0xbe3fa8b97a67b3981964dd0bdc3ba781e17bf37f';
        $wallets[] = '0xbea2de3503b81911a7fd59629fb674119f9e731e';
        $wallets[] = '0xc3696f7383668d430cd102b044fe7cb7c67ccf8c';
        $wallets[] = '0xc382b4f73e3b8123a5939958f4ddb0f66d3d1b57';
        $wallets[] = '0xc71aa7ddb6b3303cc381a27697a13e54438011d7';
        $wallets[] = '0xc7994dfb3b7a03bd88572fba584c0cc75374eb5a';
        $wallets[] = '0xc91185ffaf8bb088ff40250997adbfd41b374ce1';
        $wallets[] = '0xcafaa6bfd3f0ab798b773cd1afc4f8d66002e481';
        $wallets[] = '0xcc58454724ed3e8b41daa34ad617b1906c38cf3e';
        $wallets[] = '0xce427a74f1f1db736b08c6ac492092e92deb5b27';
        $wallets[] = '0xd01be1524ec9b1724e2beecc7047f562979fd3af';
        $wallets[] = '0xd393e2aaf5febf582d50fd8bf9ddc4469767a3db';
        $wallets[] = '0xd3f966c56fddc3e4199da14c6ebaca56fe3e9572';
        $wallets[] = '0xd5affd4f3b8b5661dfc587e3342500a8e276c4bb';
        $wallets[] = '0xd80700b680be2ddf3a824699607ab3fcbb2b558e';
        $wallets[] = '0xd878a99308a886411ce82db9ca751ab403f42022';
        $wallets[] = '0xd899691d90f1996baeb19850f615cc90ec98c784';
        $wallets[] = '0xdbf536e6c74e874ecb5ef74d3db7ab0e40eaeaa7';
        $wallets[] = '0xde00eff0fbfffaee90a57e3087c258d4b73eba19';
        $wallets[] = '0xdeb528d05340a8a0849202d3b164872be6d3233d';
        $wallets[] = '0xe0ac0afdf41faf2dab70341cfbdf8639a6b49ebc';
        $wallets[] = '0xe12e38237989732cbcf9c57146c904a04fce7648';
        $wallets[] = '0xe1f27743a4380a9562fdde7bb98614e3f0f8801f';
        $wallets[] = '0xe2b6af822185ed12f8bdb8382ba61b9fea23ebb9';
        $wallets[] = '0xe7549bdf937bac5fcc9954fef032695a51f4c47c';
        $wallets[] = '0xe966b57e024573025bb10ade07b68cd546f3fd2a';
        $wallets[] = '0xef1803411186b0aba8c7be9425b25063abc6a3ca';
        $wallets[] = '0xf6b42d4817355d420fb4207b19097d6175044553';
        $wallets[] = '0xf87a916b28beda9e90bf944be59879ea286740ab';
        $wallets[] = '0xfc3dc1dc527b582e278726bb4922d92d6cc27bc4';
        $wallets[] = '0xfe7d411c8220d0c4f38098d81b57618439c67763';




        $wallets = array_unique($wallets);

        $csv = 'wallet,mints' . PHP_EOL;
        foreach ($wallets as $wallet) {

            if ($this->isValidBaseAddress($wallet)) {
                $wallet = $this->toChecksumAddress($wallet);
                $csv .= $wallet . ',1' . PHP_EOL;
            }

        }
        file_put_contents(ROOT . '/wallets-justan.csv', $csv);
    }

    function toChecksumAddress(string $address): string
    {
        $address = strtolower(str_replace('0x', '', $address));
        $hash = hash('sha3-256', $address);
        $checksumAddress = '0x';

        for ($i = 0; $i < strlen($address); $i++) {
            $checksumAddress .= (intval($hash[$i], 16) > 7) ? strtoupper($address[$i]) : $address[$i];
        }

        return $checksumAddress;
    }

    function isValidChecksumAddress(string $address): bool
    {
        return $address === $this->toChecksumAddress($address);
    }

    function isValidBaseAddress(string $address): bool
    {
        // Check if the address is the correct length and starts with '0x'
        if (strlen($address) !== 42 || strpos($address, '0x') !== 0) {
            return false;
        }

        // Check if the remaining characters are valid hexadecimal characters
        return ctype_xdigit(substr($address, 2));
    }

    private function startMonitoring(string $cronjob): \DateTime
    {
        $slack = new \App\Slack();
        $slack->sendInfoMessage('Started with cronjob `' . $cronjob . '`');

        return date_create();
    }

    private function stopMonitoring(string $cronjob, \DateTime $start): void
    {
        $took = date_diff($start, date_create())->format('%H:%I:%S');

        $slack = new \App\Slack();
        $slack->sendInfoMessage('Done with `' . $cronjob . '`, took ' . $took);
    }

    function download_full_metadata()
    {
        foreach (range(1, 10000) as $id) {

            echo $id;

            $download = false;

            if (file_exists('deadfellazmetadata/' . $id . '.json')) {
                $fileSize = filesize('deadfellazmetadata/' . $id . '.json');
                if ($fileSize < 100) {
                    $download = true;
                }
            } else {
                $download = true;
            }

            var_dump($download);
            if ($download) {
                $url = "https://ipfs.io/ipfs/bafybeiaad7jp7bsk2fubp4wmks56yxevoz7ywst5fd4gqdschuqonpd2ee/" . $id;

                exec('curl --user-agent \'Chrome/' . rand(1,1000) . '\' -o "deadfellazmetadata/' . $id . '.json" "' . $url . '"');

                echo 'Downloaded metadata DeadFellaz #' . $id . PHP_EOL;
                echo ' --- ' . $url . PHP_EOL;
            }

            echo $id . PHP_EOL;
        }
    }

    public function createOpenSeaCryptoPunksMetadata()
    {

        $tokenIds = [];

        $csvData = 'tokenID,name,description,file_name,attributes[Type],attributes[Total Accessories]';
        $attributesCollected = [];
        foreach (range(0, 9999) as $id) {
            $metadata = file_get_contents('metadata-bullrunpunks-2/' . $id . '.json');
            $metadata = (array)json_decode($metadata, true);
            foreach ($metadata['attributes'] as $attributes) {
                if (
                    !in_array($attributes['trait_type'], ['Type']) &&
                    !in_array($attributes['value'], [
                        '0 Attributes',
                        '1 Attributes',
                        '2 Attributes',
                        '3 Attributes',
                        '4 Attributes',
                        '5 Attributes',
                        '6 Attributes',
                        '7 Attributes',
                    ])
                ) {
                    if (!in_array($attributes['value'], $attributesCollected)) {
                        $attributesCollected[] = $attributes['value'];
                    }
                }

            }
        }
        foreach ($attributesCollected as $attributeHeader) {
            $csvData .= ',attributes[' . $attributeHeader . ']';
        }

        $csvData .= PHP_EOL;

        foreach (range(0, 9999) as $id) {
            $metadata = file_get_contents('metadata-bullrunpunks-2/' . $id . '.json');
            $metadata = (array) json_decode($metadata, true);
            $nftID = $id;

            $attributesCsv = '';
            foreach ($metadata['attributes'] as $attributes) {
                if ($attributes['trait_type'] === 'Type') {
                    $attributesCsv .= ',' . $attributes['value'];
                }
            }

            $attributesCsv .= ',' . count($metadata['attributes']) - 1;

            $hasAttributes = [];
            foreach ($metadata['attributes'] as $attributes) {
                if (
                    !in_array($attributes['trait_type'], ['Type']) &&
                    !in_array($attributes['value'], [
                        '0 Attributes',
                        '1 Attributes',
                        '2 Attributes',
                        '3 Attributes',
                        '4 Attributes',
                        '5 Attributes',
                        '6 Attributes',
                        '7 Attributes',
                    ])
                ) {
                    $hasAttributes[] = $attributes['value'];
                }
            }

            foreach ($attributesCollected as $attributeCollected) {
                if (in_array($attributeCollected, $hasAttributes)) {
                    $attributesCsv .= ',Yes';
                } else {
                    $attributesCsv .= ',';
                }
            }

//        $tokenId = mt_rand(1000000000, 9999999999);
//        if (in_array($tokenId, $tokenIds)) {
//            echo 'token ID already found.';
//            exit;
//        }
//        $tokenIds[] = $tokenId;

            $csvData .= ($id + 1) . ',"Built with pixels and inspired by the original, this BullRun Punk showcases a bold upward trend—a perfect fusion of code art and market optimism.",BullRun Punk #' . $nftID . ',' . $nftID . '.png' . $attributesCsv . PHP_EOL;
        }

        file_put_contents('metadata-bullrun-base.csv', $csvData);

    }



    public function createOpenSeaLooneyLukeMetadata()
    {

        $tokenIds = [];

        $csvData = 'tokenID,name,description,file_name,attributes[Total Accessories]';
        $attributesCollected = [];
        foreach (range(1, 10000) as $id) {
            $metadata = file_get_contents('metadata-luke/' . $id . '.json');
            $metadata = (array)json_decode($metadata, true);
            foreach ($metadata['attributes'] as $attributes) {
                if (!in_array($attributes['trait_type'], $attributesCollected)) {
                    $attributesCollected[] = $attributes['trait_type'];
                }

            }
        }
        foreach ($attributesCollected as $attributeHeader) {
            $csvData .= ',attributes[' . $attributeHeader . ']';
        }

        $csvData .= PHP_EOL;

        foreach (range(1, 10000) as $id) {
            $metadata = file_get_contents('metadata-luke/' . $id . '.json');
            $metadata = (array) json_decode($metadata, true);

            $attributesCsv = '';
            $attributesCsv .= ',' . count($metadata['attributes']);

            $hasAttributes = [];
            foreach ($metadata['attributes'] as $attributes) {
                $hasAttributes[$attributes['trait_type']] = $attributes['value'];
            }

            foreach ($attributesCollected as $attributeCollected) {
                if (array_key_exists($attributeCollected, $hasAttributes)) {
                    $attributesCsv .= ',' . $hasAttributes[$attributeCollected];
                } else {
                    $attributesCsv .= ',';
                }
            }

//        $tokenId = mt_rand(1000000000, 9999999999);
//        if (in_array($tokenId, $tokenIds)) {
//            echo 'token ID already found.';
//            exit;
//        }
//        $tokenIds[] = $tokenId;

            $csvData .= $id . ',' . $metadata['name'] . ',' . $metadata['description'] . ','  . $id . '.png' . $attributesCsv .PHP_EOL;
        }

        file_put_contents('metadata-luke.csv', $csvData);

    }



    public function createOpenSeaJustanAlienMetadata()
    {

        $tokenIds = [];

        $csvData = 'tokenID,name,description,file_name';
        $attributesCollected = [];
        $metadata = file_get_contents('justanalien/NFTMetadataSummary.json');

        $metadata = (array)json_decode($metadata, true);

        foreach ($metadata as $metadataSingle) {
            foreach ($metadataSingle['attributes'] as $attributes) {
                if (!in_array($attributes['trait_type'], $attributesCollected)) {
                    $attributesCollected[] = $attributes['trait_type'];
                }

            }
        }
        foreach ($attributesCollected as $attributeHeader) {
            $csvData .= ',attributes[' . $attributeHeader . ']';
        }

        $csvData .= PHP_EOL;

        $index = 1;
        foreach ($metadata as $metadataSingle) {

            $attributesCsv = '';

            $hasAttributes = [];
            foreach ($metadataSingle['attributes'] as $attributes) {
                $hasAttributes[$attributes['trait_type']] = $attributes['value'];
            }

            foreach ($attributesCollected as $attributeCollected) {
                if (array_key_exists($attributeCollected, $hasAttributes)) {
                    $attributesCsv .= ',' . $hasAttributes[$attributeCollected];
                } else {
                    $attributesCsv .= ',';
                }
            }

//        $tokenId = mt_rand(1000000000, 9999999999);
//        if (in_array($tokenId, $tokenIds)) {
//            echo 'token ID already found.';
//            exit;
//        }
//        $tokenIds[] = $tokenId;

            $csvData .= $index . ',' . $metadataSingle['name'] . ',"' . $metadataSingle['description'] . '",'  . $index . '.png' . $attributesCsv .PHP_EOL;

            $index++;
        }

        file_put_contents('metadata-justan.csv', $csvData);

    }

    public function createOpenSeaMetadataCSV()
    {

        $tokenIds = [];

        $csvData = 'tokenID,name,description,file_name';

        $csvData .= ',attributes[Type]';
        $csvData .= ',attributes[Accessory]';
        $csvData .= ',attributes[Accessory]';
        $csvData .= ',attributes[Accessory]';
        $csvData .= ',attributes[Accessory]';
        $csvData .= ',attributes[Accessory]';
        $csvData .= ',attributes[Accessory]';
        $csvData .= ',attributes[Accessory]';

        $csvData .= PHP_EOL;

        foreach (range(1, 10000) as $fileId) {
            $metadata = file_get_contents('metadata-loadingpunks-base/' . $fileId);
            $metadata = (array) json_decode($metadata, true);
            $name = $metadata['name'];
            $id = (int) str_replace('LoadingPunk #', '', $name);

            $attributesCsv = '';


            foreach ($metadata['attributes'] as $attributes) {
                $attributesCsv .= ',' . $attributes['value'];
            }

            $csvData .= $fileId . ',' . 'LoadingPunk on Base #' . $id . ',Loading the famous Punks on Base.,'  . $id . '.gif' . $attributesCsv .PHP_EOL;
        }

        file_put_contents('metadata-loadingpunks-base.csv', $csvData);
    }

}
