<?php

$stmt = db_select('slave_table', ['*'], ['master'=>$table]);

while ($row = $stmt->fetch()) {
    $t = $row['slave'];
    $slave_field = $row['field'];
    $table_field = $row['table_field'];
    $t_header = "<th></th>";
    $t_list_html = "";
    $list_list_html = '';
    $t_footer = "";
    if ($table_field == null || $table_field == "") {
        $where = [$slave_field=>$id];
    } else {
        $where = [$slave_field=>$id, $table_field=>$table];
    }
    if (isset($_GET['cols'])) {
        $cols = json_decode($_GET['cols'], true);
    } else {
        $cols = ['*'];
    }

    $schema = db_schema($t);
    $stmt2 = db_select($t, $cols, $where, 'ORDER BY id DESC');
    $i = 0;
    $first = true;
    $is_warn_for_id = false;
    $list_html = '';
    while ($row2 = $stmt2->fetch()) {
        $i++;
        $t_row2 = "";
        foreach ($row2 as $key => $value) {
            $fn = get_type_function('decode_list', $schema[$key]['type']);
            $value = $fn($schema[$key], $value, '', $row2);
            $t_row2 .= "<td>$value</td>";
        }
        if (!isset($row2['id'])) {
            $row2['id'] = 0;
            // Show a warning about not having an id
            $is_warn_for_id = true;
        }
        $t_list_html .= <<<HTML

        <tr class='list_table_data_row'>
            <td align="left" valign="center">
                <a href="?p=display&t=$t&id={$row2['id']}" title="View"><span class="ti-eye"></span></a>
                &nbsp;
                <a href="?p=edit&t=$t&id={$row2['id']}" title="Edit"><span class="ti-pencil"></a>
                &nbsp;
                <a href="?p=delete&t=$t&id={$row2['id']}" title="Delete"><span class="ti-close"></a>
            </td>
            $t_row2
        </tr>

HTML;

        if ($first) {
            foreach ($row2 as $key => $value) {
                $key = column_pretty_print($key);
                $t_header .= "<th>$key</th>";
            }
            $first = false;
        }
        if ($i % 20 == 0) {
            $list_list_html .= <<<HTML
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr class="list_table_header_row">
                            $t_header
                        </tr>
                        $t_list_html
                        <tr class="list_table_totals_row2">
                            $t_footer
                        </tr>
                    </tbody>
                </table>
            </div>
HTML;
            $t_list_html = '';
        }
    }

    $list_list_html .= <<<HTML
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <tbody>
                <tr class="list_table_header_row">
                    $t_header
                </tr>
                $t_list_html
                <tr class="list_table_totals_row">
                    $t_footer
                </tr>
            </tbody>
        </table>
    </div>
HTML;

    if ($is_warn_for_id) {
        debug_warn('This table does not have an ID field');
    }

    if (!$first) {
        $list_html .= <<<HTML

        <p><a href='?p=add&t=$t&d={"$slave_field":"$id"}'><span class="ti-plus"></span></a> &nbsp; <a href="?p=list&t=$t"><span class="ti-view-list"></span></a></p>

        $list_list_html

HTML;
    } else {
        $list_html .= <<<HTML
        <h1>Empty Set</h1>
        <p><a href='?p=add&t=$t&d={"$slave_field":"$id"}'>Create the First One</a></p>
HTML;
    }
    $tabs[] = [
        'name' => table_pretty_print($t),
        'html' => $list_html
    ];
}
