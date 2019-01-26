<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET["branch"]) && isset($_GET["gitLocation"]))
{
    //EXAMPLE OF GIT REPO IN REPOS FOLDER : /var/www/html/repos/examplerepo/ (WHICH IS LINKED YOUR DOCKER HOST REPOS FOLDER)
    $logFile        = getcwd() . "/log.txt";
    $branch         = $_GET["branch"];
    $dockerLocation = "";
    $gitLocation    = getcwd() . "/repos/" . $_GET["gitLocation"];
    if (isset($_GET["dockerLocation"]))
    {
        $dockerLocation = $gitLocation . "/" . $_GET["dockerLocation"];
    }
    else
    {
        $dockerLocation = $gitLocation;
    }
    $starting = "\n".$dockerLocation." is starting to integrate new changes\n\n";
    shell_exec("sudo echo -n $starting >> $logFile");
    //CHECK GIT REPO EXISTS
    if (!file_exists($gitLocation) && isset($_GET["user"]))
    {
        $git_name = basename($gitLocation);
        $user     = $_GET["user"];
        $location = str_replace($git_name, "", $gitLocation);
        shell_exec("cd $location && sudo git clone git@bitbucket.org:$user/$git_name.git");
        //YOU CAN  CHANGE ACCORDING TO YOUR GITHUB REPOS
    }
    //IF PROJECT HAS WRIITEN IN PHP LANGUAGE YOU MUST SET CHOWN AS WWW_DATA
    if (strpos($gitLocation, "/backend") !== false)
    {
        shell_exec("sudo chown -R www-data:www-data $dockerLocation 2>&1 >> $logFile");
        $log_chown = "\n".$dockerLocation . " is chowned as www-data\n";
        shell_exec("sudo echo -n $log_chown >> $logFile");
    }

    $log_git             = shell_exec("cd $gitLocation && sudo git pull origin $branch 2>&1 >> $logFile");
    $log_docker          = shell_exec("cd $dockerLocation && sudo docker-compose build --no-cache 2>&1 >> $logFile");
    $log_docker_recreate="";
   //REBUILD DOCKER CONTAINERS. EXCEPTION FOR NGINX WITHOUT ZERO DOWN TIME
    if(strpos($dockerLocation, "nginx")!==false){
        $log_docker_recreate = shell_exec("cd $dockerLocation && sudo docker exec nginx nginx -s reload 2>&1 >> $logFile");
    }else{
    	$log_docker_recreate = shell_exec("cd $dockerLocation && sudo docker-compose up -d --force-recreate 2>&1 >> $logFile");
    }
    $ending              = "\n".$dockerLocation." has been finished to integrate new changes\n";
    shell_exec("sudo echo -n $ending >> $logFile");
}
