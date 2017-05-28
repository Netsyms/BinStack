<?php

require_once __DIR__ . '/../required.php';

dieifnotloggedin();

require_once __DIR__ . '/userinfo.php';

header("Content-Type: application/json");

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('items');
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 2:
        $order = ["name" => $sortby];
        break;
    case 3:
        $order = ["catname" => $sortby];
        break;
    case 4:
        $order = ["locname" => $sortby];
        break;
    case 5:
        $order = ["code1" => $sortby];
        break;
    case 6:
        $order = ["code2" => $sortby];
        break;
    case 7:
        $order = ["qty" => $sortby];
        break;
    // Note: We're not going to sort by assigned user.  It's too hard.  Maybe later.
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "OR" => [
            "name[~]" => $VARS['search']['value'],
            "catname[~]" => $VARS['search']['value'],
            "locname[~]" => $VARS['search']['value'],
            "code1[~]" => $VARS['search']['value'],
            "code2[~]" => $VARS['search']['value']
        ]
    ];
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["LIMIT" => [$VARS['start'], $VARS['length']]];
}
if (!is_null($order)) {
    $where["ORDER"] = $order;
}


$items = $database->select('items', [
    '[>]categories' => ['catid' => 'catid'],
    '[>]locations' => ['locid' => 'locid']
        ], [
    'itemid',
    'name',
    'catname',
    'locname',
    'loccode',
    'code1',
    'code2',
    'qty',
    'userid'
        ], $where);


$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('items', [
        '[>]categories' => ['catid' => 'catid'],
        '[>]locations' => ['locid' => 'locid']
            ], 'itemid', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;

$usercache = [];
for ($i = 0; $i < count($items); $i++) {
    $items[$i]["editbtn"] = '<a class="btn btn-blue btn-xs" href="app.php?page=edititem&id=' . $items[$i]['itemid'] . '"><i class="fa fa-pencil-square-o"></i> ' . lang("edit", false) . '</a>';
    if (is_null($items[$i]['userid'])) {
        $items[$i]["username"] = "";
    } else {
        if (!isset($usercache[$items[$i]['userid']])) {
            $usercache[$items[$i]['userid']] = getUserByID($items[$i]['userid']);
        }
        $items[$i]["username"] = $usercache[$items[$i]['userid']]['name'];
    }
}
$out['items'] = $items;

echo json_encode($out);
