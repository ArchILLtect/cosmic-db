<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
/** 
 * Purpose:     Parameterizes a database query.
 * 
 * Description: Parameterizes an SQL query given a database connection, a query string, a data
 *              types string, and a variable number of parameters to be used in the query. If
 *              the query is successful, the database result object will be returned (or TRUE
 *              if no results set and the query was successful). otherwise FALSE will be
 *              returned and the connection will have to be queried for the last error
 * 
 * @param       $dbc database connection
 * @param       $sql_query SQL statement
 * @param       $data_types string containing one character for each parameter's type
 * @param       $query_parameters a variable list of parameters representing each query param
 * 
 * @return string   DB result set, otherwise: false if there is a DB error or true if successful
*/
function parameterizedQuery($dbc, $sql_query, $data_types, ...$query_parameters)
{
    $result = false; // Assume failure

    if ($stmt = mysqli_prepare($dbc, $sql_query))
    {
        if (mysqli_stmt_bind_param($stmt, $data_types, ...$query_parameters)
                && mysqli_stmt_execute($stmt))
        {
            $result = mysqli_stmt_get_result($stmt);

            if (!mysqli_errno($dbc) && !$result)
            {
                $result = true;
            }
        }
    }
    return $result;
}