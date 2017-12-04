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
    * Classe de mapeamento da tabela folhapagamento.complementar
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
  * Efetua conexão com a tabela  folhapagamento.complementar
  * Data de Criação: 13/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoComplementar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoComplementar()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.complementar");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_complementar,cod_periodo_movimentacao');

    $this->AddCampo('cod_complementar','integer',true,'',true,false);
    $this->AddCampo('cod_periodo_movimentacao','integer',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT *                                                                                      \n";
    $stSql .= "   FROM folhapagamento.complementar complementar                                               \n";
    $stSql .= "   JOIN ( SELECT fcs.*                                                                         \n";
    $stSql .= "            FROM folhapagamento.complementar_situacao fcs                                      \n";
    $stSql .= "            JOIN (   SELECT cod_periodo_movimentacao                                           \n";
    $stSql .= "                          , cod_complementar                                                   \n";
    $stSql .= "                          , max(timestamp) as timestamp                                        \n";
    $stSql .= "                       FROM folhapagamento.complementar_situacao                               \n";
    $stSql .= "                   GROUP BY cod_periodo_movimentacao, cod_complementar                         \n";
    $stSql .= "                 ) as max_fcs                                                                  \n";
    $stSql .= "              ON max_fcs.cod_periodo_movimentacao = fcs.cod_periodo_movimentacao               \n";
    $stSql .= "             AND max_fcs.cod_complementar         = fcs.cod_complementar                       \n";
    $stSql .= "             AND max_fcs.timestamp                = fcs.timestamp                              \n";
    $stSql .= "        ) as complementar_situacao                                                             \n";
    $stSql .= "     ON complementar_situacao.cod_periodo_movimentacao = complementar.cod_periodo_movimentacao \n";
    $stSql .= "    AND complementar_situacao.cod_complementar         = complementar.cod_complementar         \n";

    return $stSql;
}

function recuperaFolhaComplementarCalculadaPorContrato(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_complementar ";
    $stSql  = $this->montaRecuperaFolhaComplementarCalculadaPorContrato().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFolhaComplementarCalculadaPorContrato()
{
    $stSql .= " SELECT complementar.*                                                                         \n";
    $stSql .= "   FROM (SELECT complementar.*                                                                         \n";
    $stSql .= "              , contrato_servidor_complementar.cod_contrato                                            \n";
    $stSql .= "           FROM folhapagamento.complementar complementar                                               \n";
    $stSql .= "           JOIN ( SELECT fcs.*                                                                         \n";
    $stSql .= "                    FROM folhapagamento.complementar_situacao fcs                                      \n";
    $stSql .= "                    JOIN (   SELECT cod_periodo_movimentacao                                           \n";
    $stSql .= "                                  , cod_complementar                                                   \n";
    $stSql .= "                                  , max(timestamp) as timestamp                                        \n";
    $stSql .= "                               FROM folhapagamento.complementar_situacao                               \n";
    $stSql .= "                           GROUP BY cod_periodo_movimentacao, cod_complementar                         \n";
    $stSql .= "                         ) as max_fcs                                                                  \n";
    $stSql .= "                      ON max_fcs.cod_periodo_movimentacao = fcs.cod_periodo_movimentacao               \n";
    $stSql .= "                     AND max_fcs.cod_complementar         = fcs.cod_complementar                       \n";
    $stSql .= "                     AND max_fcs.timestamp                = fcs.timestamp                              \n";
    $stSql .= "                ) as complementar_situacao                                                             \n";
    $stSql .= "             ON complementar_situacao.cod_periodo_movimentacao = complementar.cod_periodo_movimentacao \n";
    $stSql .= "            AND complementar_situacao.cod_complementar         = complementar.cod_complementar         \n";
    $stSql .= "              , folhapagamento.contrato_servidor_complementar                                          \n";
    $stSql .= "              , folhapagamento.registro_evento_complementar                                            \n";
    $stSql .= "              , folhapagamento.evento_complementar_calculado                                           \n";
    $stSql .= "          WHERE complementar.cod_complementar = contrato_servidor_complementar.cod_complementar        \n";
    $stSql .= "            AND complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao \n";
    $stSql .= "            AND contrato_servidor_complementar.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao \n";
    $stSql .= "            AND contrato_servidor_complementar.cod_complementar = registro_evento_complementar.cod_complementar \n";
    $stSql .= "            AND contrato_servidor_complementar.cod_contrato = registro_evento_complementar.cod_contrato \n";
    $stSql .= "            AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro \n";
    $stSql .= "            AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro \n";
    $stSql .= "            AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento \n";
    $stSql .= "            AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao \n";
    $stSql .= "        GROUP BY complementar.cod_complementar,complementar.cod_periodo_movimentacao,contrato_servidor_complementar.cod_contrato) as complementar                                                     \n";

    return $stSql;
}

}
