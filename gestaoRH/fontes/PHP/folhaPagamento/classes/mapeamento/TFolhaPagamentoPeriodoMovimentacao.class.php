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
* Classe de mapeamento da tabela FOLHAPAGAMENTO.PERIODO_MOVIMENTACAO
* Data de Criação: 24/10/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-02-13 13:27:10 -0200 (Qua, 13 Fev 2008) $

* Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.PERIODO_MOVIMENTACAO
  * Data de Criação: 24/10/2005

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoPeriodoMovimentacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoPeriodoMovimentacao()
{
    parent::Persistente();
    $this->setTabela('folhapagamento'.Sessao::getEntidade().'.periodo_movimentacao');

    $this->setCampoCod('cod_periodo_movimentacao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_periodo_movimentacao', 'integer', true, '',  true, false);
    $this->AddCampo('dt_inicial'              , 'date'   , true, '', false, false);
    $this->AddCampo('dt_final'                , 'date'   , true, '', false, false);
}

function recuperaPeriodoMovimentacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "", $schemaFolhaPagamento = "folhapagamento")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaPeriodoMovimentacao($schemaFolhaPagamento).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function recuperaPrimeiraMovimentacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaPrimeiraMovimentacao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function recuperaUltimaMovimentacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaUltimaMovimentacao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function recuperaUltimaMovimentacaoFechada(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaUltimaMovimentacao().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function recuperaAnosPeriodoMovimentacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;

    $stOrdem  = " GROUP BY ano                                                          \n ";
    $stOrdem .= " ORDER BY ano DESC                                                     \n ";

    $stSql = $this->montaRecuperaAnosPeriodoMovimentacao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAnosPeriodoMovimentacao()
{
    $stSql = "  SELECT to_char(periodo_movimentacao.dt_final, 'yyyy') as ano         \n ";
    $stSql.= "       , max(cod_periodo_movimentacao) as cod_periodo_movimentacao     \n ";
    $stSql.= "    FROM folhapagamento.periodo_movimentacao                           \n ";
    $stSql.= "   WHERE true                                                          \n ";

    return $stSql;
}

function montaRecuperaPeriodoMovimentacao($schemaFolhaPagamento = "folhapagamento")
{
    $stSql = "SELECT                                                                 \n ";
    $stSql.= "  FPM.cod_periodo_movimentacao,                                        \n ";
    $stSql.= "  to_char(FPM.dt_inicial, 'dd/mm/yyyy') as dt_inicial,                 \n ";
    $stSql.= "  to_char(FPM.dt_final, 'dd/mm/yyyy') as dt_final,                     \n ";
    $stSql.= "  FPMS.situacao,                                                       \n ";
    $stSql.= "  to_char(FPMS.timestamp, 'dd/mm/yyyy') as timestamp_situacao          \n ";
    $stSql.= "FROM                                                                   \n ";
    $stSql.= "    ".$schemaFolhaPagamento.".periodo_movimentacao FPM,                \n ";
    $stSql.= "    ".$schemaFolhaPagamento.".periodo_movimentacao_situacao FPMS,      \n ";
    $stSql.= "    (SELECT                                                            \n ";
    $stSql.= "        cod_periodo_movimentacao,                                      \n ";
    $stSql.= "        MAX(timestamp) as timestamp                                    \n ";
    $stSql.= "    FROM ".$schemaFolhaPagamento.".periodo_movimentacao_situacao       \n ";
    $stSql.= "    GROUP BY cod_periodo_movimentacao) as MAX_FPMS                     \n ";
    $stSql.= "WHERE FPM.cod_periodo_movimentacao = FPMS.cod_periodo_movimentacao     \n ";
    $stSql.= "AND   FPM.cod_periodo_movimentacao = MAX_FPMS.cod_periodo_movimentacao \n ";
    $stSql.= "AND   FPMS.timestamp               = MAX_FPMS.timestamp                \n ";

    return $stSql;
}

function montaRecuperaUltimaMovimentacao()
{
    $stSql = "SELECT                                                             \n ";
    $stSql.= "  FPM.cod_periodo_movimentacao,                                    \n ";
    $stSql.= "  to_char(FPM.dt_inicial, 'dd/mm/yyyy') as dt_inicial,             \n ";
    $stSql.= "  to_char(FPM.dt_final, 'dd/mm/yyyy') as dt_final,                 \n ";
    $stSql.= "  FPMS.situacao                                                    \n ";
    $stSql.= "FROM                                                               \n ";
    $stSql.= "    folhapagamento".Sessao::getEntidade().".periodo_movimentacao FPM,                       \n ";
    $stSql.= "    folhapagamento".Sessao::getEntidade().".periodo_movimentacao_situacao FPMS,             \n ";
    $stSql.= "    (SELECT                                                        \n ";
    $stSql.= "        MAX(timestamp) as timestamp                                \n ";
    $stSql.= "    FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao_situacao              \n ";
    $stSql.= "    WHERE situacao = 'a') as MAX_TIMESTAMP                         \n ";
    $stSql.= "WHERE FPM.cod_periodo_movimentacao = FPMS.cod_periodo_movimentacao \n ";
    $stSql.= "AND   FPMS.timestamp               = MAX_TIMESTAMP.timestamp       \n ";
    $stSql.= "AND   FPMS.situacao                = 'a'                           \n ";

    return $stSql;
}

function montaRecuperaPrimeiraMovimentacao()
{
    $stSql = "SELECT                                                             \n ";
    $stSql.= "  FPM.cod_periodo_movimentacao,                                    \n ";
    $stSql.= "  to_char(FPM.dt_inicial, 'dd/mm/yyyy') as dt_inicial,             \n ";
    $stSql.= "  to_char(FPM.dt_final, 'dd/mm/yyyy') as dt_final,                 \n ";
    $stSql.= "  FPMS.situacao                                                    \n ";
    $stSql.= "FROM                                                               \n ";
    $stSql.= "    folhapagamento.periodo_movimentacao FPM,                       \n ";
    $stSql.= "    folhapagamento.periodo_movimentacao_situacao FPMS,             \n ";
    $stSql.= "    (SELECT                                                        \n ";
    $stSql.= "        min(timestamp) as timestamp                                \n ";
    $stSql.= "    FROM folhapagamento.periodo_movimentacao_situacao              \n ";
    $stSql.= "    WHERE situacao = 'a') as min_TIMESTAMP                         \n ";
    $stSql.= "WHERE FPM.cod_periodo_movimentacao = FPMS.cod_periodo_movimentacao \n ";
    $stSql.= "AND   FPMS.timestamp               = min_TIMESTAMP.timestamp       \n ";
    $stSql.= "AND   FPMS.situacao                = 'a'                           \n ";

    return $stSql;
}

function montaRecuperaUltimaMovimentacaoFechada()
{
    $stSql = "SELECT                                                             \n ";
    $stSql.= "  FPM.cod_periodo_movimentacao,                                    \n ";
    $stSql.= "  to_char(FPM.dt_inicial, 'dd/mm/yyyy') as dt_inicial,             \n ";
    $stSql.= "  to_char(FPM.dt_final, 'dd/mm/yyyy') as dt_final,                 \n ";
    $stSql.= "  FPMS.situacao                                                    \n ";
    $stSql.= "FROM                                                               \n ";
    $stSql.= "    folhapagamento.periodo_movimentacao FPM,                       \n ";
    $stSql.= "    folhapagamento.periodo_movimentacao_situacao FPMS,             \n ";
    $stSql.= "    (SELECT                                                        \n ";
    $stSql.= "        MAX(timestamp) as timestamp                                \n ";
    $stSql.= "    FROM folhapagamento.periodo_movimentacao_situacao              \n ";
    $stSql.= "    WHERE situacao = 'f') as MAX_TIMESTAMP                         \n ";
    $stSql.= "WHERE FPM.cod_periodo_movimentacao = FPMS.cod_periodo_movimentacao \n ";
    $stSql.= "AND   FPMS.timestamp               = MAX_TIMESTAMP.timestamp       \n ";
    $stSql.= "AND   FPMS.situacao                = 'f'                           \n ";

    return $stSql;
}

function abrirPeriodoMovimentacao($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaAbrirPeriodoMovimentacao();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaAbrirPeriodoMovimentacao()
{
    $stSql = "SELECT abrirPeriodoMovimentacao('".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."','".$this->getDado('exercicio')."','".$this->getDado("cod_entidade")."')\n";

    return $stSql;
}

function cancelarPeriodoMovimentacao($boTransacao = "")
{
    return $this->executaRecupera("montaCancelarPeriodoMovimentacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaCancelarPeriodoMovimentacao()
{
    $stSql = "SELECT cancelarPeriodoMovimentacao('".$this->getDado("cod_entidade")."')\n";

    return $stSql;
}

function recuperaPeriodoMovimentacaoDaCompetencia(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $inMes = ($this->getDado("mes") < 10) ? "0".$this->getDado("mes") : $this->getDado("mes");
    $dtCompetencia = $inMes."/".$this->getDado("ano");
    $stFiltro = " AND to_char(dt_final, 'mm/yyyy') = '".$dtCompetencia."'";
    $stSql  = $this->montaRecuperaPeriodoMovimentacao().$stFiltro;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function recuperaIntervaloPeriodosMovimentacaoDaCompetencia(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $inMesInicial = $this->getDado("mesInicial") ;
    $dtCompetenciaInicial = $this->getDado("anoInicial").$inMesInicial;

    $inMesFinal = $this->getDado("mesFinal")+1 ;
    $dtCompetenciaFinal = $this->getDado("anoFinal").$inMesFinal;

    $stFiltro = " WHERE periodo_movimentacao.dt_final BETWEEN to_date('".$dtCompetenciaInicial."', 'YYYYMM') AND to_date('".$dtCompetenciaFinal."', 'YYYYMM')";
    $stSql  = $this->montaRecuperaIntervaloPeriodosDaCompetencia().$stFiltro.$stOrdem;
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaIntervaloPeriodosDaCompetencia()
{
    $stSql = " SELECT  * FROM ".$this->getTabela();

    return $stSql;
}

function recuperaIntervaloPeriodosMovimentacaoDaSituacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $inMesInicial = $this->getDado("mesInicial") ;
    $dtCompetenciaInicial = $this->getDado("anoInicial").$inMesInicial;

    $inMesFinal = $this->getDado("mesFinal")+1 ;
    $dtCompetenciaFinal = $this->getDado("anoFinal").$inMesFinal;

    $stFiltro = " WHERE periodo_movimentacao.dt_final BETWEEN to_date('".$dtCompetenciaInicial."', 'YYYYMM') AND to_date('".$dtCompetenciaFinal."', 'YYYYMM')";
    $stFiltro .= " AND periodo_movimentacao_situacao.situacao = 'f' ";
    $stSql = $this->montaRecuperaIntervaloPeriodosDaSituacao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaIntervaloPeriodosDaSituacao()
{
    $stSql = "
        SELECT periodo_movimentacao.cod_periodo_movimentacao
             , periodo_movimentacao.dt_inicial
             , periodo_movimentacao.dt_final
          FROM folhapagamento.periodo_movimentacao
    INNER JOIN folhapagamento.periodo_movimentacao_situacao
            ON periodo_movimentacao_situacao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
    INNER JOIN (SELECT periodo_movimentacao_situacao.cod_periodo_movimentacao
                     , max(periodo_movimentacao_situacao.timestamp) as timestamp
                  FROM folhapagamento.periodo_movimentacao_situacao
              GROUP BY periodo_movimentacao_situacao.cod_periodo_movimentacao
              ORDER BY 1
               ) AS max_periodo_movimentacao_situacao
            ON max_periodo_movimentacao_situacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao
           AND max_periodo_movimentacao_situacao.timestamp = periodo_movimentacao_situacao.timestamp
    ";
    return $stSql;
}

function recuperaPeriodoMovimentacaoTCEAL(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
{
    $stSql="SELECT MAX(cod_periodo_movimentacao ) as cod_periodo_movimentacao
                    FROM folhapagamento.periodo_movimentacao
                  WHERE periodo_movimentacao.dt_final BETWEEN to_date('".$this->getDado("dtInicial")."', 'dd/mm/yyyy') AND to_date('".$this->getDado("dtFinal")."', 'dd/mm/yyyy')";

     return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
}

}
