<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Traits\UpdateDataNFTTrait;
use App\Action\BaseAction;
use App\Variable;
use App\Models\User as UserModel;

class User extends BaseAction
{

    use UpdateDataNFTTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/user');

        $project = $this->getRequest()->getParam('project');
        if (!$project) {
            abort();
        }

        $user = $this->getUserQuery()->getUserByProject($project);
        $projectName = $user->projectName;

        $this->setVariable(new Variable('projectName', $projectName));
        $this->setVariable(new Variable('user', $user));
        $this->setVariable(new Variable('exampleCodeHtml', $this->getExampleCodeHtmlApi($user)));
        $this->setVariable(new Variable('exampleCodeJson', $this->getExampleCodeJsonApi($user)));

    }

    private function getExampleCodeHtmlApi(UserModel $user): string
    {
        $exampleUrl = env('URL') . '/html/' . $user->projectSlug . '/ethereum/' . $user->token;
        $exampleCode = <<<HTML
<iframe src="{$exampleUrl}/
        table_tr_td::color:white;font-weight:bold;background-color:#333333;font-size:26px;padding:10px_20px;
        table::width:100%25;
        a.link-richlist-details::background-color:purple;color:white;text-decoration:none;padding:20px;"
    width="100%" 
    height="100%"
></iframe>
HTML;

        return htmlentities($exampleCode);
    }

    private function getExampleCodeJsonApi(UserModel $user): string
    {
        $exampleUrl = env('URL') . '/json/' . $user->projectSlug . '/ethereum/' . $user->token;
        $exampleCode = <<<JS
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{$exampleUrl}')
    .then(response => response.json())
    .then(data => renderData(data))
    .catch(error => console.error('Error fetching data:', error));

    function renderData(data) {
        var tableHTML = '<table class="table table-striped"><thead><tr><th>User ID</th><th>Total</th></tr></thead><tbody>';

        for (var userId in data) {
            if (data.hasOwnProperty(userId)) {
                var total = data[userId].total;
                tableHTML += '<tr><td>' + userId + '</td><td>' + total + '</td></tr>';
            }
        }

        tableHTML += '</tbody></table>';

        document.getElementById('tableContainer').innerHTML = tableHTML;
    }
});
</script>

<div id="tableContainer"></div>
JS;

        return htmlentities($exampleCode);
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
