<?php
// Setup AutoLoader
include_once('AutoLoader.php');
new AutoLoader();

    function fillUsers($http)
    {
        $rows = array_map('str_getcsv', file('Users.csv', FILE_USE_INCLUDE_PATH));
        $header = array_shift($rows);
        $users = array();
        foreach ($rows as $row) {
            $users[] = array_combine($header, $row);
            $users['password'] = password_hash($users['password'], PASSWORD_DEFAULT);
        }
        var_dump($users);
        foreach ($users as $user) {
            $http->post('User/post_new_user.php', $user);
        }
    }

    function fillTowns($http)
    {
        $rows = array_map('str_getcsv', file('Towns.csv', FILE_USE_INCLUDE_PATH));
        $header = array_shift($rows);
        $towns = array();
        foreach ($rows as $row) {
            $towns[] = array_combine($header, $row);
        }
        var_dump($towns);
        foreach ($towns as $town) {
            $http->post('Towns/post_new_town.php', $town);
        }
    }

    function fillBuildings($http)
    {
        $rows = array_map('str_getcsv', file('Buildings.csv', FILE_USE_INCLUDE_PATH));
        $header = array_shift($rows);
        $buildings = array();
        foreach ($rows as $row) {
            $buildings[] = array_combine($header, $row);
        }
        var_dump($buildings);
        foreach ($buildings as $building) {
            $http->post('Buildings/post_new_building.php', $building);
        }
    }

    function fillArmies($http)
    {
        $rows = array_map('str_getcsv', file('Armies.csv', FILE_USE_INCLUDE_PATH));
        $header = array_shift($rows);
        $armies = array();
        foreach ($rows as $row) {
            $armies[] = array_combine($header, $row);
        }
        var_dump($armies);
        foreach ($armies as $army) {
            $http->post('Armies/post_new_army.php', $army);
        }
    }
