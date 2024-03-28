<?php


final class Project extends dbConnection
{
    private $conn;

    public function __construct()
    {
        $this->conn = parent::connect();
    }
    
    public function __destruct(){
        mysqli_close($this->conn);
    }
    
    public function add($title, $description)
    {
        $sql = "INSERT INTO projects(title, description) values('$title', '$description');";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function showAllProjects()
    {
        $sql = "SELECT id, title, description, created_at from projects where deleted_at is null";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }
    
    public function showProjectMembers($desiredProjectId)
    {
        $sql = "SELECT u.id as id, u.name as name, ep.created_at as addedOn
        from users u
        inner join employeesProjects ep
        on u.id = ep.user_id
        where u.role = 'employee' and ep.project_id = '$desiredProjectId' and ep.deleted_at is null and u.deleted_at is null 
        order by addedOn desc";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function updateProjectDetails($desiredProjectId, $title, $description)
    {
        $sql = "UPDATE projects set title = '$title', description = '$description', updated_at = now() where id = '$desiredProjectId' and deleted_at is null;";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function showProjectDetails($desiredProjectId)
    {
        $sql = "SELECT title, description, created_at from projects where id = '$desiredProjectId' and deleted_at is null";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function addProjectMembers($desiredProjectId, $memberId)
    {
        $sql = "INSERT INTO employeesProjects(project_id, user_id) values('$desiredProjectId', '$memberId');";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function showUnAddedProjectMembers($desiredProjectId)
    {
        $sql = "SELECT id, name
        FROM users
        WHERE role = 'employee' AND deleted_at IS NULL
        AND NOT EXISTS (
            SELECT 1
            FROM employeesProjects
            WHERE users.id = employeesProjects.user_id
            AND employeesProjects.project_id = '$desiredProjectId'
            AND employeesProjects.deleted_at is null
        )
        ORDER BY name;";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function delete($desiredProjectId)
    {
        $sql  = "UPDATE projects SET deleted_at = now() WHERE id = '$desiredProjectId';";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }


    // for users
    public function showProjectsByUserId($desiredUserId)
    {
        $sql = "SELECT p.id as id, p.title as title, p.description as description, ep.created_at as assigned_on
        from projects as p
        inner join employeesProjects as ep
        on p.id = ep.project_id
        where ep.deleted_at is null and p.deleted_at is null and ep.user_id = '$desiredUserId'
        order by assigned_on desc;";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function addProjectDailyTask($desiredUserId, $projectId, $description)
    {
        $sql = "INSERT INTO UserProjectDailyTask(user_id, project_id, activity_log) values('$desiredUserId', '$projectId', '$description');";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function showPMSbyUserId($desiredUserId)
    {
        $sql = "SELECT updt.project_id as projectId, updt.activity_log as summary, updt.created_at as created_dateTime, p.title as projectTitle
        from UserProjectDailyTask updt
        inner join projects p
        on p.id = updt.project_id
        where user_id = $desiredUserId and p.deleted_at is null order by created_dateTime desc limit 10";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function totalProjectsCount(){
        $sql = "SELECT count(id) as totalProjectsCount from projects where deleted_at is null";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function deleteMember($desiredProjectId, $desiredUserId){
        $sql = "UPDATE employeesProjects set deleted_at = now() where user_id = '$desiredUserId' and project_id ='$desiredProjectId';";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function employeesWithNoPMSYesterday($date){
        $sql = "SELECT DISTINCT u.id, u.name
        from users u
        left JOIN userprojectdailytask updt
        on u.id = updt.user_id and DATE(updt.created_at) = SUBDATE('$date',1)
        where u.role = 'employee' and u.deleted_at is null and updt.deleted_at is null and updt.id is null";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }
}
