<?php
final class User extends dbConnection
{
    private $conn;
    private $desiredUserId;

    public function __construct($userId)
    {
        try {
            //code...
            $this->conn = parent::connect();
            $this->desiredUserId = $userId;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }

    public function __destruct()
    {
        try {
            //code...
            mysqli_close($this->conn);
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }

    public function showDetails()
    {
        try {
            //code...
            $sql = "SELECT 
        COALESCE(SUM(TIMESTAMPDIFF(SECOND, e.check_in_time, e.check_out_time)), 0) AS total_seconds,
        u.name, 
        u.mobile, 
        u.date_of_birth, 
        u.gender, 
        u.city, 
        u.state, 
        u.email, 
        u.password,
        u.profile_url,
        ed.salary, 
        ed.technology_assigned, 
        ed.joining_date, 
        ed.bond_period, 
        ed.notice_period 
    FROM 
        users u
    LEFT JOIN
        employeeTrackingDetails e ON u.id = e.user_id AND MONTH(e.check_in_time) = MONTH(CURDATE()) AND YEAR(e.check_in_time) = YEAR(CURDATE())
    LEFT JOIN 
        employeeDetails ed ON u.id = ed.user_id
    WHERE 
        u.id = '$this->desiredUserId' 
        AND u.deleted_at IS NULL
    GROUP BY 
        u.id, ed.user_id;";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }
    public function checkInCounts()
    {
        try {
            //code...
            $sql = "SELECT count(date(check_in_time)) as count from employeeTrackingDetails where user_id = '$this->desiredUserId' and date(check_in_time) = date(now()) and deleted_at is null;";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }

    public function checkIn()
    {
        try {
            //code...
            $sql = "INSERT into employeeTrackingDetails(user_id, check_in_time) values ('$this->desiredUserId',now())";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }

    public function isCheckedIn()
    {
        try {
            //code...
            $sql = "SELECT date(check_in_time) as date from employeeTrackingDetails where user_id = '$this->desiredUserId' and deleted_at is null order by check_in_time desc limit 1;";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }
    public function checkOut()
    {
        try {
            //code...
            $sql = "UPDATE employeeTrackingDetails set check_out_time = now(), updated_at = now() where user_id = '$this->desiredUserId' order by check_in_time desc limit 1";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }
    public function showEmployeeCheckOutTime()
    {
        try {
            //code...
            $sql = "SELECT check_out_time from employeeTrackingDetails where user_id = '$this->desiredUserId' and deleted_at is null order by check_in_time desc limit 1";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }
    public function showTrackDetails($withDateGroupBy)
    {
        try {
            //code...
            if ($withDateGroupBy) {
                $sql =  "SELECT
                id,
                check_in_time,
                check_out_time,
                DATE(check_in_time) AS date,
                SUM(TIMESTAMPDIFF(SECOND, check_in_time, check_out_time)) AS total_seconds
                FROM employeeTrackingDetails
                WHERE user_id = '$this->desiredUserId' and deleted_at is null
                GROUP BY DATE(check_in_time)
                ORDER BY DATE(check_in_time) DESC;";
            } else
                $sql =  "SELECT
                id,
                check_in_time,
                check_out_time,
                DATE(check_in_time) AS date,
                SUM(TIMESTAMPDIFF(SECOND, check_in_time, check_out_time)) AS total_seconds
                FROM employeeTrackingDetails
                WHERE user_id = '$this->desiredUserId' and deleted_at is null
                GROUP BY check_in_time
                ORDER BY check_in_time DESC;";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }

    public function changePassword($newHashedPassword)
    {
        try {
            //code...
            $sql = "UPDATE users set password = '$newHashedPassword', updated_at = now() where id = '$this->desiredUserId'";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }

    public function daysInCurrentMonth()
    {
        try {
            //code...
            $sql = "SELECT DAY(LAST_DAY(NOW())) AS days_in_current_month;";
            $result = mysqli_query($this->conn, $sql);
            return $result;
        } catch (\Throwable $th) {
            echo "Something went wrong";
            exit();
        }
    }
}
