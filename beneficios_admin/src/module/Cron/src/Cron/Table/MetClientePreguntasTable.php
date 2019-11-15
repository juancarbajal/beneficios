<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 07/02/16
 * Time: 06:24 PM
 */

namespace Cron\Table;

use Zend\Db\Adapter\Adapter;

class MetClientePreguntasTable
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function truncate()
    {
        $sql_str = "TRUNCATE TABLE `BNF_DM_Met_Cliente_Preguntas`";
        $statement = $this->adapter->createStatement($sql_str);
        $statement->execute();
    }

    public function preguntas()
    {
        $sql_str = "INSERT INTO BNF_DM_Met_Cliente_Preguntas
                        SELECT
                        null,
                        c.BNF_Cliente_id,
                        c.BNF_DM_Dim_Empresa_id,
                        CASE
                            WHEN trim(lower(p.Pregunta05)) = 'soltero' or trim(lower(p.Pregunta05)) = 'soltera' THEN 1
                            WHEN trim(lower(p.Pregunta05)) = 'casado' or trim(lower(p.Pregunta05)) = 'casada' THEN 2
                            WHEN trim(lower(p.Pregunta05)) = 'viudo' or trim(lower(p.Pregunta05)) = 'viuda' THEN 3
                            WHEN trim(lower(p.Pregunta05)) = 'divorciado' or trim(lower(p.Pregunta05)) = 'divorciada' THEN 5
                            ELSE 4
                        END EstadoCivil,
                        CASE
                            WHEN p.Pregunta08 IS NULL THEN 1
                            WHEN p.Pregunta08 = 0 THEN 2
                            WHEN p.Pregunta08 = 1 THEN 3
                            WHEN p.Pregunta08 = 2 THEN 4
                            WHEN p.Pregunta08 = 3 THEN 5
                            WHEN p.Pregunta08 = 4 THEN 6
                            WHEN p.Pregunta08 = 5 THEN 7
                            WHEN p.Pregunta08 = 6 THEN 8
                            WHEN p.Pregunta08 = 7 THEN 9
                            WHEN p.Pregunta08 = 8 THEN 10
                            WHEN p.Pregunta08 = 9 THEN 11
                            WHEN p.Pregunta08 = 10 THEN 12
                            WHEN p.Pregunta08 = 11 THEN 13
                            WHEN p.Pregunta08 = 12 THEN 14
                            WHEN p.Pregunta08 = 13 THEN 15
                            WHEN p.Pregunta08 = 14 THEN 16
                            WHEN p.Pregunta08 = 15 THEN 17
                            WHEN p.Pregunta08 = 16 THEN 18
                            WHEN p.Pregunta08 = 17 THEN 19
                            WHEN p.Pregunta08 = 18 THEN 20
                            WHEN p.Pregunta08 = 19 THEN 21
                            WHEN p.Pregunta08 = 20 THEN 22
                            WHEN p.Pregunta08 = 21 THEN 23
                            WHEN p.Pregunta08 = 22 THEN 24
                            WHEN p.Pregunta08 = 23 THEN 25
                            WHEN p.Pregunta08 = 24 THEN 26
                            WHEN p.Pregunta08 = 25 THEN 27
                            WHEN p.Pregunta08 = 26 THEN 28
                            WHEN p.Pregunta08 = 27 THEN 29
                            WHEN p.Pregunta08 = 28 THEN 30
                            WHEN p.Pregunta08 = 29 THEN 31
                            WHEN p.Pregunta08 = 30 THEN 32
                            WHEN p.Pregunta08 = 31 THEN 33
                            WHEN p.Pregunta08 = 32 THEN 34
                            WHEN p.Pregunta08 = 33 THEN 35
                            WHEN p.Pregunta08 = 34 THEN 36
                            WHEN p.Pregunta08 = 35 THEN 37
                            WHEN p.Pregunta08 = 36 THEN 38
                            WHEN p.Pregunta08 = 37 THEN 39
                            WHEN p.Pregunta08 = 38 THEN 40
                            WHEN p.Pregunta08 = 39 THEN 41
                            WHEN p.Pregunta08 = 40 THEN 42
                            WHEN p.Pregunta08 = 41 THEN 43
                            WHEN p.Pregunta08 = 42 THEN 44
                            WHEN p.Pregunta08 = 43 THEN 45
                            WHEN p.Pregunta08 = 44 THEN 46
                            WHEN p.Pregunta08 = 45 THEN 47
                            WHEN p.Pregunta08 = 46 THEN 48
                            WHEN p.Pregunta08 = 47 THEN 49
                            WHEN p.Pregunta08 = 48 THEN 50
                            WHEN p.Pregunta08 = 49 THEN 51
                            WHEN p.Pregunta08 = 50 THEN 52
                        END hijos,
                        CASE
                            WHEN ISNULL(p.Pregunta03) THEN 1
                            WHEN (YEAR(curdate()) - p.Pregunta03) <= 20 AND (YEAR(curdate()) - p.Pregunta03) >= 0  THEN 2
                            WHEN (YEAR(curdate()) - p.Pregunta03) <= 30 AND (YEAR(curdate()) - p.Pregunta03) > 20  THEN 3
                            WHEN (YEAR(curdate()) - p.Pregunta03) <= 40 AND (YEAR(curdate()) - p.Pregunta03) > 30  THEN 4
                            WHEN (YEAR(curdate()) - p.Pregunta03) > 40  THEN 5
                            ELSE 1
                        END Rango_Edad,
                        CASE
                            WHEN p.Pregunta04 = 'Masculino' THEN 'H'
                            WHEN p.Pregunta04 = 'Femenino' THEN 'M'
                            ELSE NULL
                        END Genero,
                        CASE
                            WHEN trim(p.Pregunta01) IS NULL OR trim(p.Pregunta01) = '' THEN NULL
                            WHEN trim(p.Pregunta01) IS NOT NULL THEN p.Pregunta01
                        END nombres,
                        CASE
                            WHEN trim(p.Pregunta02) IS NULL OR trim(p.Pregunta02) = '' THEN NULL
                            WHEN trim(p.Pregunta02) IS NOT NULL THEN p.Pregunta02
                        END apellidos,
                        CASE
                            WHEN trim(p.Pregunta06) IS NULL OR trim(p.Pregunta06) = '' THEN NULL
                            WHEN trim(p.Pregunta06) IS NOT NULL THEN lower(p.Pregunta06)
                        END distrito_vive,
                        CASE
                            WHEN trim(p.Pregunta07) IS NULL OR trim(p.Pregunta07) = '' THEN NULL
                            WHEN trim(p.Pregunta07) IS NOT NULL THEN lower(p.Pregunta07)
                        END distrito_trabaja,
                        CASE
                            WHEN trim(p.Pregunta09) IS NULL OR trim(p.Pregunta09) = '' THEN NULL
                            WHEN trim(p.Pregunta09) IS NOT NULL THEN lower(p.Pregunta09)
                        END celular,
                        CASE
                            WHEN trim(p.Pregunta10) IS NULL OR trim(p.Pregunta10) = '' THEN NULL
                            WHEN trim(p.Pregunta10) IS NOT NULL THEN lower(p.Pregunta10)
                        END nivel_estudios
                    FROM
                    BNF_DM_Met_Cliente c
                    LEFT JOIN BNF_Preguntas p ON p.BNF_Cliente_id = c.BNF_Cliente_id
                    WHERE  c.BNF_Cliente_id IS NOT NULL
                    GROUP BY c.BNF_Cliente_id";
        $statement = $this->adapter->createStatement($sql_str);
        $result = $statement->execute();
        return $result;
    }
}
