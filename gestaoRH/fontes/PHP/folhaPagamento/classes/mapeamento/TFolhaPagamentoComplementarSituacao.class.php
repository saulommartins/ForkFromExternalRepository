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
    * Classe de mapeamento da tabela folhapagamento.complementar_situacao
    * Data de Criação: 13/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.complementar_situacao
  * Data de Criação: 13/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoComplementarSituacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoComplementarSituacao()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.complementar_situacao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_complementar,cod_periodo_movimentacao,timestamp');

    $this->AddCampo('cod_periodo_movimentacao','integer',true,'',true,true);
    $this->AddCampo('cod_complementar','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('situacao','char',true,'1',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT complementar_situacao.cod_complementar                                                \n";
    $stSql .= "     , CASE complementar_situacao.situacao                                                   \n";
    $stSql .= "       WHEN 'a' THEN 'Aberto'                                                                \n";
    $stSql .= "       WHEN 'f' THEN 'Fechado'                                                               \n";
    $stSql .= "        END AS situacao                                                                      \n";
    $stSql .= "     , CASE folha_situacao.situacao                                                          \n";
    $stSql .= "       WHEN 'a' THEN 'Aberto'                                                                \n";
    $stSql .= "       WHEN 'f' THEN 'Fechado'                                                               \n";
    $stSql .= "        END || ' em ' || to_char(complementar_situacao_fechada.timestamp_folha,'dd/mm/yyyy - HH24:MI:SS') AS situacao_folha                                                                \n";
    $stSql .= "     , to_char(complementar_situacao.timestamp,'dd/mm/yyyy') as data_fechamento              \n";
    $stSql .= "     , to_char(complementar_situacao_fechada.timestamp_folha,'dd/mm/yyyy - HH24:MI:SS') as timestamp_folha \n";
    $stSql .= "     , complementar_situacao_abertura.data_abertura                                          \n";
    $stSql .= "  FROM folhapagamento.complementar_situacao                                                  \n";
    $stSql .= "     , (  SELECT cod_complementar                                                            \n";
    $stSql .= "               , max(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM folhapagamento.complementar_situacao                                        \n";
    $stSql .= "        GROUP BY cod_complementar) as max_complementar_situacao                              \n";
    $stSql .= "     , (SELECT complementar_situacao.cod_periodo_movimentacao                                \n";
    $stSql .= "             , complementar_situacao.cod_complementar                                        \n";
    $stSql .= "             , to_char(complementar_situacao.timestamp,'dd/mm/yyyy') as data_abertura        \n";
    $stSql .= "          FROM folhapagamento.complementar_situacao                                          \n";
    $stSql .= "             , (  SELECT cod_complementar                                                    \n";
    $stSql .= "                       , max(timestamp) as timestamp                                         \n";
    $stSql .= "                    FROM folhapagamento.complementar_situacao                                \n";
    $stSql .= "                   WHERE situacao = 'a'                                                      \n";
    $stSql .= "                GROUP BY cod_complementar) as max_complementar_situacao                      \n";
    $stSql .= "         WHERE situacao = 'a'                                                                \n";
    $stSql .= "           AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar \n";
    $stSql .= "           AND complementar_situacao.timestamp = max_complementar_situacao.timestamp) as complementar_situacao_abertura  \n";
    $stSql .= "     , folhapagamento.complementar_situacao_fechada                                          \n";
    $stSql .= "     , folhapagamento.folha_situacao                                                         \n";
    $stSql .= " WHERE complementar_situacao.cod_complementar= max_complementar_situacao.cod_complementar    \n";
    $stSql .= "   AND complementar_situacao.timestamp       = max_complementar_situacao.timestamp           \n";
    $stSql .= "   AND complementar_situacao.cod_periodo_movimentacao= complementar_situacao_abertura.cod_periodo_movimentacao \n";
    $stSql .= "   AND complementar_situacao.cod_complementar        = complementar_situacao_abertura.cod_complementar         \n";
    $stSql .= "   AND complementar_situacao.cod_periodo_movimentacao= complementar_situacao_fechada.cod_periodo_movimentacao  \n";
    $stSql .= "   AND complementar_situacao.cod_complementar        = complementar_situacao_fechada.cod_complementar          \n";
    $stSql .= "   AND complementar_situacao.timestamp               = complementar_situacao_fechada.timestamp                 \n";
    $stSql .= "   AND complementar_situacao_fechada.cod_periodo_movimentacao_folha = folha_situacao.cod_periodo_movimentacao  \n";
    $stSql .= "   AND complementar_situacao_fechada.timestamp_folha                = folha_situacao.timestamp                 \n";

    return $stSql;
}

function recuperaUltimaFolhaComplementarSituacao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimaFolhaComplementarSituacao();
    $stOrder = ( $stOrder != "" ) ? " ORDER BY ".$stOrder : "";
    $stSql .= $stFiltro.$stOrder;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUltimaFolhaComplementarSituacao()
{
    $stSql .= "SELECT complementar_situacao.*                                                                          \n";
    $stSql .= "  FROM folhapagamento.complementar_situacao                                                             \n";
    $stSql .= "     , (  SELECT cod_complementar                                                                       \n";
    $stSql .= "               , max(timestamp) as timestamp                                                            \n";
    $stSql .= "            FROM folhapagamento.complementar_situacao                                                   \n";
    $stSql .= "        GROUP BY cod_complementar) as max_complementar_situacao                                         \n";
    $stSql .= "     , (SELECT max(cod_periodo_movimentacao) as cod_periodo_movimentacao                                \n";
    $stSql .= "          FROM folhapagamento.periodo_movimentacao) as periodo_movimentacao                             \n";
    $stSql .= " WHERE complementar_situacao.cod_complementar= max_complementar_situacao.cod_complementar               \n";
    $stSql .= "   AND complementar_situacao.timestamp       = max_complementar_situacao.timestamp                      \n";
    $stSql .= "   AND complementar_situacao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao   \n";
    $stSql .= "ORDER BY complementar_situacao.cod_complementar DESC                                                    \n";
    $stSql .= "LIMIT 1                                                                                                 \n";

    return $stSql;
}

function recuperaRelacionamentoFechadaPosteriorSalario(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoFechadaPosteriorSalario();
    $stOrder = ( $stOrder != "" ) ? " ORDER BY ".$stOrder : "";
    $stSql .= $stFiltro.$stOrder;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoFechadaPosteriorSalario()
{
    $stSql .= "SELECT complementar_situacao.cod_complementar                                                \n";
    $stSql .= "     , to_char(complementar_situacao.timestamp,'dd/mm/yyyy') as data_fechamento              \n";
    $stSql .= "     , complementar_situacao_abertura.data_abertura                                          \n";
    $stSql .= "  FROM folhapagamento.complementar_situacao                                                  \n";
    $stSql .= "     , (  SELECT cod_complementar                                                            \n";
    $stSql .= "               , max(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM folhapagamento.complementar_situacao                                        \n";
    $stSql .= "        GROUP BY cod_complementar) as max_complementar_situacao                              \n";
    $stSql .= "     , (SELECT complementar_situacao.cod_periodo_movimentacao                                \n";
    $stSql .= "             , complementar_situacao.cod_complementar                                        \n";
    $stSql .= "             , to_char(complementar_situacao.timestamp,'dd/mm/yyyy') as data_abertura        \n";
    $stSql .= "          FROM folhapagamento.complementar_situacao                                          \n";
    $stSql .= "             , (  SELECT cod_complementar                                                    \n";
    $stSql .= "                       , max(timestamp) as timestamp                                         \n";
    $stSql .= "                    FROM folhapagamento.complementar_situacao                                \n";
    $stSql .= "                   WHERE situacao = 'a'                                                      \n";
    $stSql .= "                GROUP BY cod_complementar) as max_complementar_situacao                      \n";
    $stSql .= "         WHERE situacao = 'a'                                                                \n";
    $stSql .= "           AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar \n";
    $stSql .= "           AND complementar_situacao.timestamp = max_complementar_situacao.timestamp) as complementar_situacao_abertura  \n";
    $stSql .= " WHERE complementar_situacao.cod_complementar= max_complementar_situacao.cod_complementar    \n";
    $stSql .= "   AND complementar_situacao.timestamp       = max_complementar_situacao.timestamp           \n";
    $stSql .= "   AND complementar_situacao.cod_periodo_movimentacao= complementar_situacao_abertura.cod_periodo_movimentacao \n";
    $stSql .= "   AND complementar_situacao.cod_complementar        = complementar_situacao_abertura.cod_complementar         \n";

    return $stSql;
}

function recuperaFolhaComplementarSituacaoFechadaNaoIncluida(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaFolhaComplementarSituacaoFechadaNaoIncluida();
    $stOrder = ( $stOrder != "" ) ? " ORDER BY ".$stOrder : "";
    $stSql .= $stFiltro.$stOrder;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFolhaComplementarSituacaoFechadaNaoIncluida()
{
    $stSql .= "   SELECT fcs.cod_periodo_movimentacao                                                  \n";
    $stSql .= "        , fcs.cod_complementar                                                          \n";
    $stSql .= "        , fcs.timestamp                                                                 \n";
    $stSql .= "        , fcs.situacao                                                                  \n";
    $stSql .= "     FROM folhapagamento.complementar_situacao fcs                                      \n";
    $stSql .= "     JOIN (   SELECT mfcs.cod_periodo_movimentacao                                      \n";
    $stSql .= "                   , mfcs.cod_complementar                                              \n";
    $stSql .= "                   , MAX(mfcs.timestamp) as timestamp                                   \n";
    $stSql .= "                FROM folhapagamento.complementar_situacao mfcs                          \n";
    $stSql .= "            GROUP BY mfcs.cod_periodo_movimentacao                                      \n";
    $stSql .= "                   , mfcs.cod_complementar                                              \n";
    $stSql .= "          ) as max_fcs                                                                  \n";
    $stSql .= "       ON max_fcs.cod_periodo_movimentacao = fcs.cod_periodo_movimentacao               \n";
    $stSql .= "      AND max_fcs.cod_complementar         = fcs.cod_complementar                       \n";
    $stSql .= "      AND max_fcs.timestamp                = fcs.timestamp                              \n";
    $stSql .= "    WHERE fcs.cod_periodo_movimentacao::varchar||fcs.cod_complementar||fcs.timestamp             \n";
    $stSql .= "   NOT IN ( SELECT fcsf.cod_periodo_movimentacao::varchar||fcsf.cod_complementar||fcsf.timestamp \n";
    $stSql .= "              FROM folhapagamento.complementar_situacao_fechada fcsf                    \n";
    $stSql .= "          )                                                                             \n";
    $stSql .= "      AND fcs.situacao = 'f'                                                            \n";

    return $stSql;
}

}
