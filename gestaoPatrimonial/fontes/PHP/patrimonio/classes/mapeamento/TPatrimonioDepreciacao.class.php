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
  * Página de
  * Data de criação : 18/11/2008

  * @copyright CCA Consultoria de Gestão Pública S/S Ltda.
  * @link http://www.ccanet.com.br CCA Consultoria de Gestão Pública S/S Ltda.

  * @author Analista: Gelson
  * @author Programador: Vitor Hugo

  $Id: TPatrimonioDepreciacao.class.php 42696 2009-10-16 19:38:32Z diogo.zarpelon $

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioDepreciacao extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela('patrimonio.depreciacao');
        $this->setCampoCod('cod_depreciacao');
        $this->setComplementoChave('cod_bem,timestamp');
        
        $this->AddCampo('cod_depreciacao','integer',true,'',true,false);
        $this->AddCampo('cod_bem','integer',true,'',true,false);
        $this->AddCampo('timestamp','timestamp',true,'',true,false);
        $this->AddCampo('vl_depreciado','numeric',true,'14.2',false,false);
        $this->AddCampo('dt_depreciacao','date',true,'',false,false);
        $this->AddCampo('competencia','varchar',true,'6',false,false);
        $this->AddCampo('motivo','varchar',true,'100',false,false);
        $this->AddCampo('acelerada','boolean',true,'',false,false);
        $this->AddCampo('quota_utilizada','numeric',true,'5.2',false,false);
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT
                  depreciacao.cod_depreciacao
                , depreciacao.cod_bem
                , depreciacao.timestamp
                , depreciacao.vl_depreciado
                , depreciacao.dt_depreciacao
                , depreciacao.competencia
                , depreciacao.motivo
                , depreciacao.acelerada
                , depreciacao.quota_utilizada
                , bem_plano_depreciacao.timestamp
                , bem_plano_depreciacao.exercicio
                , bem_plano_depreciacao.cod_plano
                , (SELECT valor FROM administracao.configuracao WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro = 'competencia_depreciacao') as tipoCompetencia
             FROM patrimonio.depreciacao

        INNER JOIN  (  SELECT cod_bem
                            , MAX(timestamp) AS timestamp
                            , exercicio
                        FROM patrimonio.bem_plano_depreciacao
                       WHERE bem_plano_depreciacao.exercicio = '".Sessao::getExercicio()."'
                    GROUP BY cod_bem
                           , exercicio 
                    ) AS ultimo_plano
                   ON patrimonio.depreciacao.cod_bem = ultimo_plano.cod_bem

           INNER JOIN patrimonio.bem_plano_depreciacao
                   ON ultimo_plano.cod_bem   = bem_plano_depreciacao.cod_bem
                  AND ultimo_plano.timestamp = bem_plano_depreciacao.timestamp
                  AND ultimo_plano.exercicio = bem_plano_depreciacao.exercicio

            WHERE 1=1 ";

        if ($this->getDado('cod_bem')) {
            $stSql .= " AND depreciacao.cod_bem = ".$this->getDado('cod_bem');
        }

        if ($this->getDado('cod_plano')) {
            $stSql .= " AND patrimonio.bem_plano_depreciacao.cod_plano = ".$this->getDado('cod_plano');
        }

        if ($this->getDado('timestamp')) {
            $stSql .= " AND patrimonio.bem_plano_depreciacao.timestamp = '".$this->getDado('timestamp')."'";
        }

        $stSql .= " ORDER BY depreciacao.competencia ";
            
        return $stSql;
    }

    public function recuperaDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDepreciacao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDepreciacao()
    {
      $stSql = "
            SELECT
                  depreciacao.cod_depreciacao
                , depreciacao.cod_bem
                , depreciacao.timestamp
                , depreciacao.vl_depreciado
                , depreciacao.dt_depreciacao
                , depreciacao.competencia
                , depreciacao.motivo
                , depreciacao.acelerada
                , depreciacao.quota_utilizada
                , CASE WHEN bem_plano_depreciacao.exercicio IS NOT NULL
                       THEN bem_plano_depreciacao.exercicio
                       ELSE grupo_plano_depreciacao.exercicio
                  END AS exercicio
                , CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                       THEN bem_plano_depreciacao.cod_plano
                       ELSE grupo_plano_depreciacao.cod_plano
                  END AS cod_plano
                , (SELECT valor
                     FROM administracao.configuracao
                    WHERE exercicio  = '".Sessao::getExercicio()."'
                      AND cod_modulo = 6
                      AND parametro  = 'competencia_depreciacao'
                   ) AS tipoCompetencia
                   
             FROM patrimonio.depreciacao          
        
        LEFT JOIN (
                    SELECT bem_plano_depreciacao.cod_bem
                         , bem_plano_depreciacao.cod_plano 
                         , bem_plano_depreciacao.exercicio
                         , MAX(bem_plano_depreciacao.timestamp::timestamp) AS timestamp
			 , plano_conta.cod_estrutural
                         , plano_conta.nom_conta AS nom_conta_depreciacao
                         
                      FROM patrimonio.bem_plano_depreciacao 

                 LEFT JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                       AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio

                 LEFT JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                         
                     WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
									    FROM patrimonio.bem_plano_depreciacao AS bem_plano
									   
                                                                           WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
									     AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                             /*AND bem_plano_depreciacao.exercicio '".$this->getDado('exercicio')."'*/
								        
                                                                        GROUP BY bem_plano.cod_bem
                                                                               , bem_plano.exercicio )
                  GROUP BY bem_plano_depreciacao.cod_bem
                         , bem_plano_depreciacao.cod_plano
                         , bem_plano_depreciacao.exercicio
                         , plano_conta.cod_estrutural
                         , plano_conta.nom_conta 
                  
                  ORDER BY timestamp DESC
                  
                )AS bem_plano_depreciacao
                 ON bem_plano_depreciacao.cod_bem = depreciacao.cod_bem
        
         LEFT JOIN ( SELECT cod_plano
	                  , cod_bem
                          , exercicio
	             
		     FROM patrimonio.grupo_plano_depreciacao
        
               INNER JOIN patrimonio.grupo
                       ON grupo.cod_natureza = grupo_plano_depreciacao.cod_natureza
                      AND grupo.cod_grupo    = grupo_plano_depreciacao.cod_grupo
               
               INNER JOIN patrimonio.especie
                       ON especie.cod_grupo    = grupo.cod_grupo
                      AND especie.cod_natureza = grupo.cod_natureza
               
               INNER JOIN patrimonio.bem
                       ON bem.cod_especie  = especie.cod_especie
                      AND bem.cod_grupo    = especie.cod_grupo
                      AND bem.cod_natureza = especie.cod_natureza
                      
                 ) AS grupo_plano_depreciacao
                   ON grupo_plano_depreciacao.cod_bem = depreciacao.cod_bem

            WHERE 1 = 1
              AND NOT EXISTS ( SELECT 1 
                                 FROM patrimonio.depreciacao_anulada
                                WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                  AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                  AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                             ) \n ";

        if ($this->getDado('competencia')) {
            $stSql .= " AND competencia = '".$this->getDado('competencia')."'";
        }
        
        if ($this->getDado('cod_bem')) {
            $stSql .= " AND depreciacao.cod_bem = ".$this->getDado('cod_bem');
        }
        
        if ($this->getDado('cod_plano_bem')) {
            $stSql .= " AND bem_plano_depreciacao.cod_plano = ".$this->getDado('cod_plano_bem');
        }
        
        if ($this->getDado('cod_plano_grupo')) {
            $stSql .= " AND grupo_plano_depreciacao.cod_plano = ".$this->getDado('cod_plano_grupo');
        }
        
        return $stSql;
    }
    
    public function recuperaCodPlanoDebitoCredito(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaCodPlanoDebitoCredito().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCodPlanoDebitoCredito()
    {
        $stSql = " SELECT cod_plano
                        , cod_estrutural
                        , nom_conta 
                    
                    FROM contabilidade.plano_conta 
               
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.exercicio = plano_conta.exercicio 
                     AND plano_analitica.cod_conta = plano_conta.cod_conta 
                 
                WHERE plano_conta.cod_estrutural = '".$this->getDado('stCodEstrutural')."'
                  AND plano_analitica.exercicio  = '".$this->getDado('exercicio')."'
               ";
        
        return $stSql;
    }
        
    public function recuperaMaxCodDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaMaxCodDepreciacao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMaxCodDepreciacao()
    {
        $stSql  = " SELECT MAX(cod_depreciacao) AS max_cod_depreciacao
	              FROM patrimonio.depreciacao \n ";
        return $stSql;
    }
        
    public function recuperaMinCompetenciaDepreciada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaMinCompetenciaDepreciada().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMinCompetenciaDepreciada()
    {
        $stSql  = " SELECT MIN(depreciacao.competencia) AS min_competencia
                         , TO_CHAR(TO_DATE(MIN(depreciacao.competencia), 'YYYYMM'), 'MM/YYYY') AS min_competencia_formatada
                     FROM patrimonio.depreciacao
                    WHERE NOT EXISTS ( SELECT 1
                                         FROM patrimonio.depreciacao_anulada
                                        WHERE depreciacao.cod_bem         = depreciacao_anulada.cod_bem
                                          AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
                                          AND depreciacao.timestamp       = depreciacao_anulada.timestamp
                                     ) \n ";
        return $stSql;
    }
        
    public function recuperaMaxCompetenciaDepreciada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaMaxCompetenciaDepreciada().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMaxCompetenciaDepreciada()
    {
        $stSql  = " SELECT MAX(depreciacao.competencia) AS max_competencia
                         , TO_CHAR(TO_DATE(MAX(depreciacao.competencia), 'YYYYMM'), 'MM/YYYY') AS max_competencia_formatada
                     FROM patrimonio.depreciacao
                    WHERE NOT EXISTS ( SELECT 1
                                         FROM patrimonio.depreciacao_anulada
                                        WHERE depreciacao.cod_bem         = depreciacao_anulada.cod_bem
                                          AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
                                          AND depreciacao.timestamp       = depreciacao_anulada.timestamp
                                     ) \n ";
        return $stSql;
    } 

}

?>