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

  * @author Analista: Gelson
  * @author Programador: Vitor Hugo

  $Id: TPatrimonioReavaliacao.class.php 66412 2016-08-25 13:18:12Z michel $

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioReavaliacao extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.reavaliacao');
        $this->setCampoCod('cod_reavaliacao');
        $this->setComplementoChave('cod_bem');
        $this->AddCampo('cod_reavaliacao','integer',true,'',true,false);
        $this->AddCampo('cod_bem','integer',true,'',true,false);
        $this->AddCampo('dt_reavaliacao','date',false,'',false,false);
        $this->AddCampo('vida_util','integer',false,'',false,false);
        $this->AddCampo('vl_reavaliacao','numeric',true,'14.2',false,false);
        $this->AddCampo('motivo','varchar',true,'100',false,false);
    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrder = ( $stOrder != "" ) ? " ORDER BY ".$stOrder : " \n ORDER BY reavaliacao.dt_reavaliacao ";

        $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stGroup.$stOrder;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT reavaliacao.cod_reavaliacao
                 , reavaliacao.cod_bem
                 , bem.descricao
                 , TO_CHAR(reavaliacao.dt_reavaliacao,'dd/mm/yyyy') AS dt_reavaliacao
                 , reavaliacao.vida_util
                 , reavaliacao.vl_reavaliacao
                 , reavaliacao.motivo
                 , CASE WHEN lancamento_reavaliacao.estorno = TRUE
                        THEN 'Estornado'
                        WHEN lancamento_reavaliacao.estorno = FALSE
                        THEN 'Lançado'
                        ELSE 'Pendente'
                   END AS situacao

              FROM patrimonio.bem

        INNER JOIN patrimonio.reavaliacao
                ON patrimonio.reavaliacao.cod_bem = patrimonio.bem.cod_bem

         LEFT JOIN ( SELECT lancamento_reavaliacao.cod_reavaliacao
                          , lancamento_reavaliacao.cod_bem
                          , MAX(lancamento_reavaliacao.timestamp) AS timestamp
                       FROM contabilidade.lancamento_reavaliacao
                   GROUP BY lancamento_reavaliacao.cod_reavaliacao
                          , lancamento_reavaliacao.cod_bem
                 ) AS max_lancamento_reavaliacao
                ON max_lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
               AND max_lancamento_reavaliacao.cod_bem = reavaliacao.cod_bem

         LEFT JOIN contabilidade.lancamento_reavaliacao
                ON lancamento_reavaliacao.cod_reavaliacao = max_lancamento_reavaliacao.cod_reavaliacao
               AND lancamento_reavaliacao.cod_bem = max_lancamento_reavaliacao.cod_bem
               AND lancamento_reavaliacao.timestamp = max_lancamento_reavaliacao.timestamp

             WHERE ";

        if ($this->getDado('cod_bem')) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."   AND ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaUltimaReavaliacao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrder = ( $stOrder != "" ) ? " ORDER BY ".$stOrder : " \n ORDER BY dt_reavaliacao DESC LIMIT 1 ";

        $stSql = $this->montaRecuperaUltimaReavaliacao().$stFiltro.$stGroup.$stOrder;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaUltimaReavaliacao()
    {
        $stSql = "
          SELECT reavaliacao.cod_reavaliacao
               , reavaliacao.cod_bem
               , TO_CHAR(reavaliacao.dt_reavaliacao,'dd/mm/yyyy') AS dt_reavaliacao
               , reavaliacao.vida_util
               , reavaliacao.vl_reavaliacao
               , reavaliacao.motivo

            FROM patrimonio.reavaliacao

      INNER JOIN patrimonio.bem
              ON bem.cod_bem = reavaliacao.cod_bem

      INNER JOIN ( SELECT lancamento_reavaliacao.*
                     FROM ( SELECT lancamento_reavaliacao.cod_bem
                                 , MAX(lancamento_reavaliacao.timestamp) AS timestamp
                              FROM contabilidade.lancamento_reavaliacao
                         LEFT JOIN contabilidade.lancamento_reavaliacao_estorno
                                ON lancamento_reavaliacao_estorno.id = lancamento_reavaliacao.id
                             WHERE lancamento_reavaliacao.estorno IS FALSE
                              AND lancamento_reavaliacao_estorno.id IS NULL
                         GROUP BY lancamento_reavaliacao.cod_bem
                          ) AS max_lancamento_reavaliacao
               INNER JOIN contabilidade.lancamento_reavaliacao
                       ON lancamento_reavaliacao.cod_reavaliacao = ( SELECT MAX(LR.cod_reavaliacao)
                                                                       FROM contabilidade.lancamento_reavaliacao AS LR
                                                                      WHERE LR.timestamp = max_lancamento_reavaliacao.timestamp
                                                                        AND LR.cod_bem   = max_lancamento_reavaliacao.cod_bem
                                                                   )
                      AND lancamento_reavaliacao.cod_bem         = max_lancamento_reavaliacao.cod_bem
                      AND lancamento_reavaliacao.timestamp       = max_lancamento_reavaliacao.timestamp
                      AND lancamento_reavaliacao.estorno IS FALSE
                 ) AS lancamento_reavaliacao
              ON lancamento_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
             AND lancamento_reavaliacao.cod_bem         = reavaliacao.cod_bem

           WHERE 1 = 1 ";

        if ($this->getDado('cod_bem')) {
            $stSql .= " AND bem.cod_bem = ".$this->getDado('cod_bem');
        }

        return $stSql;
    }
    
    public function recuperaMinDataReavaliacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = $this->montaRecuperaMinDataReavaliacao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMinDataReavaliacao()
    {
        $stSql  = " SELECT TO_CHAR(MIN(reavaliacao.dt_reavaliacao),'YYYYMM') AS min_competencia
                         , TO_CHAR(MIN(reavaliacao.dt_reavaliacao), 'MM/YYYY') AS min_competencia_formatada
                     FROM patrimonio.reavaliacao
                    WHERE NOT EXISTS ( SELECT 1
                                         FROM patrimonio.bem_baixado
                                        WHERE bem_baixado.cod_bem = reavaliacao.cod_bem
                                     ) \n ";
        return $stSql;
    }

}

?>