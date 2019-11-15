<?php
class SQL {
  public static function main() {
    $con = mysqli_connect(
      getenv('OPENSHIFT_MYSQL_DB_HOST'),
      getenv('OPENSHIFT_MYSQL_DB_USERNAME'),
      getenv('OPENSHIFT_MYSQL_DB_PASSWORD'),
      getenv('OPENSHIFT_APP_NAME')
    );

    $q = "DROP TABLE IF EXISTS things";
    mysqli_query($con, $q);
    $q = "CREATE TABLE things (name varchar(20))";
    mysqli_query($con, $q);
    $q = "INSERT INTO things(name) VALUES('John')";
    mysqli_query($con, $q);
    $q = "INSERT INTO things(name) VALUES('Paul')";
    mysqli_query($con, $q);
    $q = "SELECT * FROM things";
    return mysqli_query($con, $q);
  }
}
?>
