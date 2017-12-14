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
    * Classe de mapeamento da funcao pessoalOrganogramaVigentePorTimestamp
    * Data de Criação: 28/07/2009

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alex

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

    * Casos de uso: uc-00.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FPessoalOrganogramaVigentePorTimestamp extends Persistente
{
function FPessoalOrganogramaVigentePorTimestamp()
{
    parent::Persistente();
    $this->setTabela('pessoalOrganogramaVigentePorTimestamp');
}

function recuperaOrganogramaVigentePorTimestamp(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaOrganogramaVigentePorTimestamp().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaOrganogramaVigentePorTimestamp()
{
    $stTimestamp              = "now()::varchar";

    $stCodPeriodoMovimentacao = "coalesce( (SELECT max(cod_periodo_movimentacao)
                                              FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao_situacao
                                             WHERE timestamp <= now())
                                         , 0)";

    $stDataFinalPeriodoMovimentacao = "coalesce((    SELECT periodo_movimentacao.dt_final::varchar
                                                       FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao
                                                 INNER JOIN folhapagamento".Sessao::getEntidade().".periodo_movimentacao_situacao
                                                         ON periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao
                                                      WHERE periodo_movimentacao_situacao.timestamp <= now()
                                                   ORDER BY periodo_movimentacao.cod_periodo_movimentacao DESC
                                                      LIMIT 1)
                                                ,'1900-01-01'::varchar)";

    if ($this->getDado('timestamp') != "") {
        $stTimestamp = $this->getDado('timestamp');

        $stCodPeriodoMovimentacao = "coalesce( (SELECT max(cod_periodo_movimentacao)
                                                  FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao_situacao
                                                 WHERE timestamp = '".$this->getDado('timestamp')."'
                                                 LIMIT 1)
                                             , 0)";

        $stDataFinalPeriodoMovimentacao = "coalesce((    SELECT periodo_movimentacao.dt_final::varchar
                                                           FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao
                                                     INNER JOIN folhapagamento".Sessao::getEntidade().".periodo_movimentacao_situacao
                                                             ON periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao
                                                          WHERE periodo_movimentacao_situacao.timestamp = '".$this->getDado('timestamp')."'
                                                       ORDER BY periodo_movimentacao.cod_periodo_movimentacao DESC
                                                          LIMIT 1)
                                                    ,'1900-01-01'::varchar)";
    } else {

        if ($this->getDado('cod_periodo_movimentacao') != "") {
            $stTimestamp = 'ultimoTimestampPeriodoMovimentacao('.$this->getDado('cod_periodo_movimentacao').', \''.Sessao::getEntidade().'\')';

            $stCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');

            $stDataFinalPeriodoMovimentacao = "(SELECT dt_final::varchar
                                                  FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao
                                                 WHERE cod_periodo_movimentacao = ".$this->getDado('cod_periodo_movimentacao').")";
        }
    }

    $stSql = "SELECT ".$this->getTabela()."('".Sessao::getEntidade()."',".$stTimestamp.") AS cod_organograma
                    , ($stCodPeriodoMovimentacao) AS cod_periodo_movimentacao
                    , ($stDataFinalPeriodoMovimentacao) AS dt_final\n";

    return $stSql;
}

}
