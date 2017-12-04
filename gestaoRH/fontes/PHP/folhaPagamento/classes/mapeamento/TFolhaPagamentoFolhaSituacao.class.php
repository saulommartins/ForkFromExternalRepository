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
  * Classe de mapeamento da tabela FOLHAPAGAMENTO.FOLHA_SITUACAO
  * Data de Criação: 11/01/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

      Caso de uso: uc-04.05.12

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TFolhaPagamentoFolhaSituacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoFolhaSituacao()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.folha_situacao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_periodo_movimentacao, timestamp');

    $this->AddCampo('cod_periodo_movimentacao', 'integer'  , true, '' ,  true,  true);
    $this->AddCampo('timestamp'               , 'timestamp',false, '' ,  true, false);
    $this->AddCampo('situacao'                , 'char'     , true, '1', false, false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT folha_situacao.*                                                  \n";
    $stSql .= "     , max_folha_situacao.count                                          \n";
    $stSql .= "     , max_folha_situacao_fechada.timestamp as timestamp_fechado         \n";
    $stSql .= "  FROM folhapagamento.folha_situacao                                     \n";
    $stSql .= "     , (  SELECT cod_periodo_movimentacao                                \n";
    $stSql .= "               , count(cod_periodo_movimentacao)                         \n";
    $stSql .= "               , max(timestamp) as timestamp                             \n";
    $stSql .= "            FROM folhapagamento.folha_situacao                           \n";
    $stSql .= "        GROUP BY cod_periodo_movimentacao) AS max_folha_situacao         \n";
    $stSql .= "     , (  SELECT cod_periodo_movimentacao                                \n";
    $stSql .= "               , max(timestamp) as timestamp                             \n";
    $stSql .= "            FROM folhapagamento.folha_situacao                           \n";
    $stSql .= "           WHERE situacao = 'f'                                          \n";
    $stSql .= "        GROUP BY cod_periodo_movimentacao) AS max_folha_situacao_fechada \n";
    $stSql .= " WHERE folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao \n";
    $stSql .= "   AND folha_situacao.timestamp                = max_folha_situacao.timestamp                \n";
    $stSql .= "   AND folha_situacao.cod_periodo_movimentacao = max_folha_situacao_fechada.cod_periodo_movimentacao \n";

    return $stSql;
}

function recuperaFolhaSituacaoPeriodo(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFolhaSituacaoPeriodo().$stFiltro.$stOrder;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFolhaSituacaoPeriodo()
{
    $stSql .= " SELECT  max(fl.cod_periodo_movimentacao) as cod_periodo_movimentacao,
                        /*max(fl.timestamp) as timestamp,*/
                        to_char(fl.timestamp, 'MM/YYYY') as mes_ano

                    FROM    folhapagamento".$this->getDado('entidade').".folha_situacao AS fl,
                            folhapagamento".$this->getDado('entidade').".periodo_movimentacao AS pm

                WHERE pm.cod_periodo_movimentacao = fl.cod_periodo_movimentacao
                  AND fl.situacao = 'f'
                  AND pm.dt_final BETWEEN to_date('".$this->getDado("dt_inicial")."', 'MM/YYYY') AND to_date('".$this->getDado("dt_final")."', 'MM/YYYY')

                GROUP BY to_char(fl.timestamp, 'MM/YYYY')

                ORDER BY mes_ano";

    return $stSql;
}

function recuperaUltimaFolhaSituacao(&$rsRecordSet, $boTransacao = "", $stFiltro = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montarecuperaUltimaFolhaSituacao().$stFiltro;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montarecuperaUltimaFolhaSituacao()
{
    $stSql  = "SELECT ffs.cod_periodo_movimentacao                                     \n";
    $stSql .= "     , ffs.situacao                                                     \n";
    $stSql .= "     , to_char(ffs.timestamp, 'dd/mm/yyyy - HH24:MI:SS ') as data_hora  \n";
    $stSql .= "     , timestamp                                                        \n";
    $stSql .= "  FROM folhapagamento.folha_situacao ffs                                \n";
    $stSql .= "  JOIN ( SELECT MAX(ffs2.timestamp) as max_timestamp                        \n";
    $stSql .= "           FROM folhapagamento.folha_situacao ffs2                      \n";
    $stSql .= "       ) max_ffs                                                        \n";
    $stSql .= "    ON max_ffs.max_timestamp = ffs.timestamp                                \n";

    return $stSql;
}

function recuperaVezesFecharAbrirFolhaPagamento(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVezesFecharAbrirFolhaPagamento().$stFiltro;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVezesFecharAbrirFolhaPagamento()
{
    $stSql .= "SELECT count(folha_situacao.cod_periodo_movimentacao) as contador                             \n";
    $stSql .= " FROM folhapagamento.folha_situacao                                                           \n";
    $stSql .= "    , (  SELECT cod_periodo_movimentacao                                                      \n";
    $stSql .= "              , max(timestamp) as timestamp                                                   \n";
    $stSql .= "           FROM folhapagamento.folha_situacao                                                 \n";
    $stSql .= "       GROUP BY cod_periodo_movimentacao) as max_folha_situacao                               \n";
    $stSql .= "WHERE folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao   \n";
    $stSql .= "  AND folha_situacao.timestamp                = max_folha_situacao.timestamp                  \n";

    return $stSql;
}

}
