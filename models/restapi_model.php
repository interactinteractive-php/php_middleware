<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');
    
class Restapi_Model extends Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function setUserSessionBySessionIdModel($sessionId) {
        
        $userRow = self::getUserSessionInfoBySessionIdModel($sessionId);
        
        if ($userRow) {
            return self::setUserSessionModel($userRow);
        }
        
        return null;
    }
    
    public function setUserSessionByUserPassModel($username, $password) {
        
        $userRow = self::getUserSessionInfoByUserPassModel($username, $password);
        
        if ($userRow) {
            return self::setUserSessionModel($userRow);
        }
        
        return null;
    }
    
    public function setUserSessionModel($userRow) {
        
        $sessionValues = ['sessioncompanydepartmentid' => $userRow['COMPANY_DEPARTMENT_ID']];

        Session::init();

        Session::set(SESSION_PREFIX . 'LoggedIn', true);
        Session::set(SESSION_PREFIX . 'userkeyid', $userRow['USER_ID']);
        Session::set(SESSION_PREFIX . 'userid', $userRow['SYSTEM_USER_ID']);
        Session::set(SESSION_PREFIX . 'username', $userRow['USERNAME']);
        Session::set(SESSION_PREFIX . 'firstname', $userRow['FIRST_NAME']);
        Session::set(SESSION_PREFIX . 'personname', $userRow['FIRST_NAME']);
        Session::set(SESSION_PREFIX . 'employeeid', $userRow['EMPLOYEE_ID']);
        Session::set(SESSION_PREFIX . 'employeekeyid', $userRow['EMPLOYEE_KEY_ID']);
        Session::set(SESSION_PREFIX . 'departmentid', $userRow['DEPARTMENT_ID']);
        Session::set(SESSION_PREFIX . 'customerid', $userRow['CUSTOMER_ID']);
        Session::set(SESSION_PREFIX . 'sessionValues', $sessionValues);
        Session::set(SESSION_PREFIX . 'periodStartDate', Date::currentDate('Y-m') . '-01');

        return true;
    }
    
    public function getUserSessionInfoBySessionIdModel($sessionId) {
        
        try {
            
            $row = $this->db->GetRow("
                SELECT 
                    US.USER_ID, 
                    US.SYSTEM_USER_ID, 
                    US.EMPLOYEE_ID, 
                    US.EMPLOYEE_KEY_ID, 
                    US.DEPARTMENT_ID, 
                    US.COMPANY_DEPARTMENT_ID, 
                    US.CUSTOMER_ID, 
                    USU.USERNAME, 
                    BP.FIRST_NAME 
                FROM UM_USER_SESSION US 
                    INNER JOIN UM_SYSTEM_USER USU ON USU.USER_ID = US.SYSTEM_USER_ID 
                    LEFT JOIN BASE_PERSON BP ON BP.PERSON_ID = USU.PERSON_ID 
                WHERE US.SESSION_ID = ".$this->db->Param(0), 
                [$sessionId]
            );

            return $row;
        
        } catch (Exception $ex) { 
            return [];
        }
    }
    
    public function getUserSessionInfoByUserPassModel($username, $password) {
        
        try {
            
            $username = Str::lower($username);
            $oldHash  = Hash::createMD5reverse($password);
            $newHash  = Hash::create('sha256', $password);

            $usernamePh = $this->db->Param(0);
            $oldpassPh  = $this->db->Param(1);
            $newpassPh  = $this->db->Param(2);

            $row = $this->db->GetRow("
                SELECT 
                    UM.USER_ID, 
                    USU.USER_ID AS SYSTEM_USER_ID, 
                    VU.EMPLOYEE_ID, 
                    VU.EMPLOYEE_KEY_ID, 
                    VU.DEPARTMENT_ID, 
                    UM.COMPANY_DEPARTMENT_ID, 
                    NULL AS CUSTOMER_ID, 
                    USU.USERNAME, 
                    VU.FIRST_NAME 
                FROM UM_SYSTEM_USER USU 
                    LEFT JOIN UM_USER UM ON UM.SYSTEM_USER_ID = USU.USER_ID 
                    LEFT JOIN VW_USER VU ON VU.USER_ID = USU.USER_ID 
                WHERE LOWER(USU.USERNAME) = $usernamePh 
                    AND (USU.PASSWORD_HASH = $oldpassPh OR USU.PASSWORD_HASH = $newpassPh)", 
                [$username, $oldHash, $newHash]
            );

            return $row;
        
        } catch (Exception $ex) {
            return [];
        }
    }
    
    public function runIndicatorFromMetaProcessDataModel($param, $jsonParam) {
        
        $bpId         = $param['_metadataid'];
        $indicatorIds = $param['_indicatorid'];
        
        $indicators = $this->db->GetAll("
            SELECT 
                T0.SRC_INDICATOR_ID AS INDICATOR_ID, 
                T1.QUERY_STRING 
            FROM KPI_INDICATOR_INDICATOR_MAP T0 
                INNER JOIN KPI_INDICATOR T1 ON T1.ID = T0.SRC_INDICATOR_ID 
            WHERE T0.SEMANTIC_TYPE_ID = 78 
                AND T0.LOOKUP_META_DATA_ID = $bpId 
                AND T0.SRC_INDICATOR_ID IN ($indicatorIds) 
                AND T1.QUERY_STRING IS NOT NULL 
            ORDER BY T0.ORDER_NUMBER ASC");
        
        if ($indicators) {
            
            $this->load->model('mdform', 'middleware/models/');
            
            $runIndicators = [];
            
            foreach ($indicators as $indicator) {
                
                $indicatorId = $indicator['INDICATOR_ID'];
                
                if (!isset($runIndicators[$indicatorId])) {
                    
                    $queryString = $indicator['QUERY_STRING'];
                    
                    if (mb_strlen($queryString) > 30) {
                        
                        includeLib('Compress/Compression');
                        
                        $queryString = Compression::decompress($queryString);
                        $queryString = trim($queryString);
                        $queryString = $this->model->replaceKpiDbSchemaName($queryString);
                        $first7Char  = strtolower(substr($queryString, 0, 7));
                        $matches     = DBSql::getQueryNamedParams($queryString);
                        
                        $params = $this->db->GetAll("
                            SELECT 
                                LOWER(PM.SRC_INDICATOR_PATH) AS SRC_PATH, 
                                LOWER(PM.TRG_META_DATA_PATH) AS TRG_PATH 
                            FROM KPI_INDICATOR_INDICATOR_MAP M 
                                LEFT JOIN KPI_INDICATOR_INDICATOR_MAP PM ON M.ID = PM.SRC_INDICATOR_MAP_ID 
                            WHERE M.SEMANTIC_TYPE_ID = 78 
                                AND PM.TRG_META_DATA_PATH IS NOT NULL 
                                AND M.SRC_INDICATOR_ID = ".$this->db->Param(0)." 
                                AND M.LOOKUP_META_DATA_ID = ".$this->db->Param(1)." 
                            GROUP BY  
                                PM.SRC_INDICATOR_PATH, 
                                PM.TRG_META_DATA_PATH", 
                            [$indicatorId, $bpId]
                        );
                        
                        $tmpParams = $bindParams = [];
                        
                        foreach ($params as $row) {
                            $bpPath = $row['TRG_PATH'];
                            $qryPath = $row['SRC_PATH'];
                            
                            $tmpParams[$qryPath] = isset($param[$bpPath]) ? $param[$bpPath] : null;
                        }
                        
                        foreach ($matches as $matchParam) {
                            $matchParam = str_replace(':', '', strtolower($matchParam));
                            
                            if (isset($tmpParams[$matchParam])) {
                                $bindParams[$matchParam] = $tmpParams[$matchParam];
                            } else {
                                $bindParams[$matchParam] = null;
                            }
                        }
                        
                        $logParam = array(
                            'indicatorId' => $indicatorId, 
                            'affectedRows' => null, 
                            'executedQuery' => $queryString."\n\n".$jsonParam."\n\n".json_encode($bindParams), 
                            'errorMsg' => null
                        );
                        
                        try {
                            
                            /*if ($first7Char == 'declare') {
                                
                                $keys = array_map('strlen', array_keys($bindParams));
                                array_multisort($keys, SORT_DESC, $bindParams);
                                
                                foreach ($bindParams as $bindParam => $bindVal) {
                                    $queryString = str_ireplace(':'.$bindParam, $bindVal, $queryString);
                                }
                                
                                $this->db->Execute($queryString);
                                
                            } else {*/
                            
                                $this->db->Execute($queryString, $bindParams);
                                $affectedRows = $this->db->affected_rows();
                                
                                $logParam['affectedRows'] = $affectedRows;
                                
                                self::insertCheckQueryLogModel($logParam);
                            //}
                            
                        } catch (Exception $ex) { 
                            
                            $logParam['errorMsg'] = $ex->getMessage();
                            self::insertCheckQueryLogModel($logParam);
                        }
                    }
                    
                    $runIndicators[$indicatorId] = 1;
                }
            }
            
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'error', 'message' => 'No indicators!');
        }
        
        return $response;
    }
    
    public function insertCheckQueryLogModel($param) {
        
        try {
            
            $errorMsg = $param['errorMsg'];
            $errorMsgLength = mb_strlen($errorMsg);
            
            $data = [
                'LOG_ID'       => getUID(), 
                'INDICATOR_ID' => $param['indicatorId'], 
                'ERROR_QTY'    => $param['affectedRows'], 
                'CREATED_DATE' => Date::currentDate()
            ];
            
            if ($errorMsgLength < 4000) {
                $data['LOG_MESSAGE'] = $errorMsg;
            }
            
            $rs = $this->db->AutoExecute('V_CHECK_QUERY_EXECUTED_LOG', $data);
            
            if ($errorMsgLength > 4000) {
                $this->db->UpdateClob('V_CHECK_QUERY_EXECUTED_LOG', 'LOG_MESSAGE', $errorMsg, 'LOG_ID = '.$data['LOG_ID']);
            }
            
            if ($rs) {
                $this->db->UpdateClob('V_CHECK_QUERY_EXECUTED_LOG', 'EXECUTED_QUERY', $param['executedQuery'], 'LOG_ID = '.$data['LOG_ID']);
                return true;
            }
            
        } catch (Exception $ex) { }
        
        return false;
    }
    
}