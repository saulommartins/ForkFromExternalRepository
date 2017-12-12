<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Extensão da Classe de mapeamento
    * Data de Criação: 15/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTBAAltOrc.class.php 63819 2015-10-19 20:52:10Z michel $

    * Casos de uso: uc-06.03.00
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAAltOrc extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct ()
{
  parent::Persistente();
  $this->setEstrutura( array() );
  $this->setEstruturaAuxiliar( array() );
  $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT 1 AS tipo_registro
                      ,".$this->getDado('unidade_gestora')." AS unidade_gestora
                      ,despesa.exercicio           
                      ,despesa.num_orgao          
                      ,despesa.num_unidade          
                      ,despesa.cod_funcao          
                      ,despesa.cod_subfuncao          
                      ,despesa.cod_programa          
                      ,REPLACE(conta_despesa.cod_estrutural,'.','') AS estrutural          
                      ,despesa.num_pao
                      ,orcamento.fn_consulta_tipo_pao(despesa.exercicio,despesa.num_pao) AS tipo_pao          
                      ,despesa.cod_recurso          
                      ,despesa.vl_original          
                      ,su.cod_tipo          
                      ,su.cod_norma          
                      ,TO_CHAR(su.dt_suplementacao,'dd/mm/yyyy') AS data_suplementacao          
                      ,COALESCE(su.vl_suplementado,0.00) + COALESCE(su.vl_reducao,0.00) AS vl_suplementacao          
                      ,vinculo_tipo_norma.cod_tipo AS tipo_fundamento        
                      ,norma.num_norma          
                      ,norma.nom_norma
                      ,despesa.exercicio::VARCHAR || '".$this->getDado('mes')."' AS competencia    
                      ,TO_CHAR(norma.dt_publicacao,'dd/mm/yyyy') AS data_publicacao          
                      ,CASE WHEN (vl_reducao > 0.00)  THEN '19'
                            ELSE
                                  CASE WHEN (su.cod_tipo = 6  )  THEN '4'
                                       WHEN (su.cod_tipo = 8  )  THEN '16'
                                       WHEN (su.cod_tipo = 3  )  THEN '18'
                                       WHEN (su.cod_tipo = 1  )  THEN '6'
                                       WHEN (su.cod_tipo = 9  )  THEN '7'
                                       WHEN (su.cod_tipo = 10 )  THEN '4'
                                       WHEN (su.cod_tipo = 5  )  THEN '12'
                                       WHEN (su.cod_tipo = 7  )  THEN '13'
                                       WHEN (su.cod_tipo = 2  )  THEN '15'
                                       WHEN (su.cod_tipo = 11 )  THEN '17'
                                       WHEN (su.cod_tipo = 12 )  THEN '28'
                                       WHEN (su.cod_tipo = 13 )  THEN '28'
                                       WHEN (su.cod_tipo = 14 )  THEN '28'
                                  END
                        END AS tipo_alteracao

                FROM  (          
                        SELECT exercicio
                              ,cod_norma
                              ,cod_tipo
                              ,dt_suplementacao
                              ,cod_despesa
                              ,SUM(vl_suplementado) AS vl_suplementado
                              ,SUM(vl_reducao) AS vl_reducao
                        FROM (
                                SELECT OS.exercicio          
                                      ,OS.cod_suplementacao          
                                      ,OS.cod_norma          
                                      ,OS.cod_tipo          
                                      ,OS.dt_suplementacao          
                                      ,OSS.cod_despesa          
                                      ,OSS.valor as vl_suplementado          
                                      ,0.00 as vl_reducao          
                                FROM orcamento.suplementacao AS OS          
                          INNER JOIN orcamento.suplementacao_suplementada AS OSS          
                                  ON OSS.exercicio = OS.exercicio          
                                 AND OSS.cod_suplementacao = OS.cod_suplementacao         
                                 AND OS.exercicio='".$this->getDado('exercicio')."' 
                           LEFT JOIN orcamento.suplementacao_anulada
                                  ON suplementacao_anulada.exercicio = OS.exercicio
                                 AND suplementacao_anulada.cod_suplementacao = OS.cod_suplementacao
                               WHERE suplementacao_anulada.cod_suplementacao_anulacao IS NULL

                               UNION          

                               SELECT OS.exercicio          
                                     ,OS.cod_suplementacao          
                                     ,OS.cod_norma          
                                     ,OS.cod_tipo          
                                     ,OS.dt_suplementacao          
                                     ,OSR.cod_despesa          
                                     ,0.00 as vl_suplementado          
                                     ,OSR.valor as vl_reducao          
                                FROM orcamento.suplementacao AS OS          
                          INNER JOIN orcamento.suplementacao_reducao AS OSR          
                                  ON OSR.exercicio = OS.exercicio          
                                 AND OSR.cod_suplementacao = OS.cod_suplementacao
                                 AND OS.exercicio='".$this->getDado('exercicio')."'
                           LEFT JOIN orcamento.suplementacao_anulada
                                  ON suplementacao_anulada.exercicio = OS.exercicio
                                 AND suplementacao_anulada.cod_suplementacao = OS.cod_suplementacao
                               WHERE suplementacao_anulada.cod_suplementacao_anulacao IS NULL
                            ) as tbl          
                        GROUP BY exercicio,cod_despesa,cod_norma,cod_tipo,dt_suplementacao          
                    ) as su

                INNER JOIN orcamento.despesa
                        ON despesa.exercicio = su.exercicio
                       AND despesa.cod_despesa = su.cod_despesa

                INNER JOIN orcamento.conta_despesa
                        ON conta_despesa.exercicio = despesa.exercicio
                       AND conta_despesa.cod_conta = despesa.cod_conta

                INNER JOIN normas.norma
                        ON norma.cod_norma = su.cod_norma

                 LEFT JOIN tcmba.vinculo_tipo_norma
                        ON vinculo_tipo_norma.cod_tipo_norma = norma.cod_tipo_norma

                WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                  AND despesa.cod_entidade IN ( ".$this->getDado('entidades')." )
                  AND su.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                  AND su.cod_tipo <> 16

                ORDER BY  despesa.exercicio
                         ,despesa.num_orgao
                         ,despesa.num_unidade
                         ,despesa.cod_funcao
                         ,despesa.cod_subfuncao
                         ,despesa.cod_programa
                         ,estrutural
                         ,despesa.cod_recurso
            ";
            return $stSql;
}

function recuperaLogErro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaLogErro().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLogErro()
{
    $stSql = " SELECT count(despesa.exercicio) as registros
                    , count(vinculo_tipo_norma.cod_tipo) AS obrigatorio
                 FROM (
                        SELECT exercicio
                              ,cod_norma
                              ,cod_tipo
                              ,dt_suplementacao
                              ,cod_despesa
                              ,SUM(vl_suplementado) AS vl_suplementado
                              ,SUM(vl_reducao) AS vl_reducao
                        FROM (
                                SELECT OS.exercicio
                                      ,OS.cod_suplementacao
                                      ,OS.cod_norma
                                      ,OS.cod_tipo
                                      ,OS.dt_suplementacao
                                      ,OSS.cod_despesa
                                      ,OSS.valor as vl_suplementado
                                      ,0.00 as vl_reducao
                                FROM orcamento.suplementacao AS OS
                          INNER JOIN orcamento.suplementacao_suplementada AS OSS
                                  ON OSS.exercicio = OS.exercicio
                                 AND OSS.cod_suplementacao = OS.cod_suplementacao
                                 AND OS.exercicio='".$this->getDado('exercicio')."' 
                           LEFT JOIN orcamento.suplementacao_anulada
                                  ON suplementacao_anulada.exercicio = OS.exercicio
                                 AND suplementacao_anulada.cod_suplementacao = OS.cod_suplementacao
                               WHERE suplementacao_anulada.cod_suplementacao_anulacao IS NULL

                               UNION

                               SELECT OS.exercicio
                                     ,OS.cod_suplementacao
                                     ,OS.cod_norma
                                     ,OS.cod_tipo
                                     ,OS.dt_suplementacao
                                     ,OSR.cod_despesa
                                     ,0.00 as vl_suplementado
                                     ,OSR.valor as vl_reducao
                                FROM orcamento.suplementacao AS OS
                          INNER JOIN orcamento.suplementacao_reducao AS OSR
                                  ON OSR.exercicio = OS.exercicio
                                 AND OSR.cod_suplementacao = OS.cod_suplementacao
                                 AND OS.exercicio='".$this->getDado('exercicio')."'
                           LEFT JOIN orcamento.suplementacao_anulada
                                  ON suplementacao_anulada.exercicio = OS.exercicio
                                 AND suplementacao_anulada.cod_suplementacao = OS.cod_suplementacao
                               WHERE suplementacao_anulada.cod_suplementacao_anulacao IS NULL
                            ) as tbl          
                        GROUP BY exercicio,cod_despesa,cod_norma,cod_tipo,dt_suplementacao
                      ) as su

           INNER JOIN orcamento.despesa
                   ON despesa.exercicio = su.exercicio
                  AND despesa.cod_despesa = su.cod_despesa

           INNER JOIN orcamento.conta_despesa
                   ON conta_despesa.exercicio = despesa.exercicio
                  AND conta_despesa.cod_conta = despesa.cod_conta

           INNER JOIN normas.norma
                   ON norma.cod_norma = su.cod_norma

            LEFT JOIN tcmba.vinculo_tipo_norma
                   ON vinculo_tipo_norma.cod_tipo_norma = norma.cod_tipo_norma

                WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                  AND despesa.cod_entidade IN ( ".$this->getDado('entidades')." )
                  AND su.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                  AND su.cod_tipo <> 16
                  ";
    return $stSql;
}

}
