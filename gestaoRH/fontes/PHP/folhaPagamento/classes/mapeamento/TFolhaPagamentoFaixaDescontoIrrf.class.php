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
    * Classe de mapeamento da tabela folhapagamento.faixa_desconto_irrf
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-11 10:42:54 -0300 (Qui, 11 Out 2007) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.faixa_desconto_irrf
  * Data de Criação: 05/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoFaixaDescontoIrrf extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoFaixaDescontoIrrf()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.faixa_desconto_irrf");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_faixa,cod_tabela,timestamp');

    $this->AddCampo('cod_faixa','integer',true,'',true,false);
    $this->AddCampo('cod_tabela','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('vl_inicial','numeric',true,'14,2',false,false);
    $this->AddCampo('vl_final','numeric',true,'14,2',false,false);
    $this->AddCampo('aliquota','numeric',true,'5,2',false,false);
    $this->AddCampo('parcela_deduzir','numeric',true,'14,2',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT faixa_desconto_irrf.*                                                 \n";
    $stSql .= "  FROM folhapagamento.faixa_desconto_irrf                                    \n";
    $stSql .= "     , (  SELECT cod_tabela                                                  \n";
    $stSql .= "               , max(timestamp) as timestamp                                 \n";
    $stSql .= "            FROM folhapagamento.faixa_desconto_irrf                          \n";
    $stSql .= "        GROUP BY cod_tabela) as max_faixa_desconto_irrf                      \n";
    $stSql .= " WHERE faixa_desconto_irrf.cod_tabela = max_faixa_desconto_irrf.cod_tabela   \n";
    $stSql .= "   AND faixa_desconto_irrf.timestamp  = max_faixa_desconto_irrf.timestamp    \n";

    return $stSql;
}

function recuperaFaixaDescontoIrrf(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaFaixaDescontoIrrf().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFaixaDescontoIrrf()
{
    $stSql .= "SELECT faixa_desconto_irrf.*                                                                                                                    \n";
    $stSql .= "  FROM folhapagamento.faixa_desconto_irrf                                                                             \n";
    $stSql .= "     , folhapagamento.tabela_irrf                                                                                     \n";
    $stSql .= "     , (  SELECT cod_tabela                                                                                                                     \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                    \n";
    $stSql .= "            FROM folhapagamento.tabela_irrf                                                                           \n";
    $stSql .= "           WHERE tabela_irrf.vigencia = (SELECT vigencia                                                                                        \n";
    $stSql .= "                                           FROM folhapagamento.tabela_irrf                                            \n";
    $stSql .= "                                              , (SELECT cod_tabela                                                                              \n";
    $stSql .= "                                                      , max(timestamp) as timestamp                                                             \n";
    $stSql .= "                                                   FROM folhapagamento.tabela_irrf                                    \n";
    $stSql .= "                                                  WHERE vigencia <= (  SELECT dt_final                                                          \n";
    $stSql .= "                                                                         FROM folhapagamento.periodo_movimentacao     \n";
    $stSql .= "                                                                     ORDER BY cod_periodo_movimentacao DESC                                     \n";
    $stSql .= "                                                                        LIMIT 1)                                                                \n";
    $stSql .= "                                                  GROUP BY cod_tabela) as max_tabela_irrf                                                       \n";
    $stSql .= "                                          WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela                                             \n";
    $stSql .= "                                            AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp)                                             \n";
    $stSql .= "        GROUP BY cod_tabela) as max_tabela_irrf                                                                                                 \n";
    $stSql .= " WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela                                                                                      \n";
    $stSql .= "   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp                                                                                       \n";
    $stSql .= "   AND tabela_irrf.cod_tabela = faixa_desconto_irrf.cod_tabela                                                                                  \n";
    $stSql .= "   AND tabela_irrf.timestamp  = faixa_desconto_irrf.timestamp                                                                                   \n";
    $stSql .= "   AND (SELECT valor                                                                                                                            \n";
    $stSql .= "          FROM (SELECT sum(evento_calculado.valor) as valor                                                                                     \n";
    $stSql .= "             , contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                                                                 \n";
    $stSql .= "             , 1 AS inFolha                                                                                                                     \n";
    $stSql .= "          FROM folhapagamento.evento_calculado                                                                        \n";
    $stSql .= "             , folhapagamento.registro_evento                                                                         \n";
    $stSql .= "             , folhapagamento.registro_evento_periodo                                                                 \n";
    $stSql .= "             , pessoal.contrato                                                                                       \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                                               \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                              \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                                              \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                  \n";
    $stSql .= "         WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                                         \n";
    $stSql .= "           AND evento_calculado.cod_registro = registro_evento.cod_registro                                                                     \n";
    $stSql .= "           AND evento_calculado.timestamp_registro = registro_evento.timestamp                                                                  \n";
    $stSql .= "           AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                                              \n";
    $stSql .= "           AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                                                     \n";
    $stSql .= "           AND registro_evento.cod_evento = tabela_irrf_evento.cod_evento                                                                       \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                  \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                     \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                                                                 \n";
    $stSql .= "         UNION                                                                                                                                  \n";
    $stSql .= "        SELECT evento_ferias_calculado.valor                                                                                                    \n";
    $stSql .= "             , contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_ferias.cod_periodo_movimentacao                                                                                  \n";
    $stSql .= "             , 2 AS inFolha                                                                                                                     \n";
    $stSql .= "          FROM folhapagamento.evento_ferias_calculado                                                                 \n";
    $stSql .= "             , folhapagamento.registro_evento_ferias                                                                  \n";
    $stSql .= "             , pessoal.contrato                                                                                       \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                                               \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                              \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                                              \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                  \n";
    $stSql .= "         WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                                                   \n";
    $stSql .= "           AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                                                 \n";
    $stSql .= "           AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                                                \n";
    $stSql .= "           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                                                    \n";
    $stSql .= "           AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                                                      \n";
    $stSql .= "           AND registro_evento_ferias.cod_evento = tabela_irrf_evento.cod_evento                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                  \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                     \n";
    $stSql .= "           AND evento_ferias_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                                    \n";
    $stSql .= "         UNION                                                                                                                                  \n";
    $stSql .= "        SELECT evento_decimo_calculado.valor                                                                                                    \n";
    $stSql .= "             , contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_decimo.cod_periodo_movimentacao                                                                                  \n";
    $stSql .= "             , 3 AS inFolha                                                                                                                     \n";
    $stSql .= "          FROM folhapagamento.evento_decimo_calculado                                                                 \n";
    $stSql .= "             , folhapagamento.registro_evento_decimo                                                                  \n";
    $stSql .= "             , pessoal.contrato                                                                                       \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                                               \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                              \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                                              \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                  \n";
    $stSql .= "         WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                                                   \n";
    $stSql .= "           AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                                                 \n";
    $stSql .= "           AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                                                \n";
    $stSql .= "           AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                                                    \n";
    $stSql .= "           AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                                                      \n";
    $stSql .= "           AND registro_evento_decimo.cod_evento = tabela_irrf_evento.cod_evento                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                  \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                     \n";
    $stSql .= "           AND evento_decimo_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                                    \n";
    $stSql .= "        UNION                                                                                                                                   \n";
    $stSql .= "        SELECT evento_rescisao_calculado.valor                                                                                                  \n";
    $stSql .= "             , contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_rescisao.cod_periodo_movimentacao                                                                                \n";
    $stSql .= "             , 4 AS inFolha                                                                                                                     \n";
    $stSql .= "          FROM folhapagamento.evento_rescisao_calculado                                                               \n";
    $stSql .= "             , folhapagamento.registro_evento_rescisao                                                                \n";
    $stSql .= "             , pessoal.contrato                                                                                       \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                                               \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                              \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                                              \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                  \n";
    $stSql .= "         WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                                               \n";
    $stSql .= "           AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                                             \n";
    $stSql .= "           AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                                            \n";
    $stSql .= "           AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                                                \n";
    $stSql .= "           AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                                                    \n";
    $stSql .= "           AND registro_evento_rescisao.cod_evento = tabela_irrf_evento.cod_evento                                                              \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                  \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                     \n";
    $stSql .= "           AND evento_rescisao_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                                  \n";
    $stSql .= "        UNION                                                                                                                                   \n";
    $stSql .= "        SELECT sum(evento_complementar_calculado.valor) as valor                                                                                \n";
    $stSql .= "             , contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao                                                                            \n";
    $stSql .= "             , 0 AS inFolha                                                                                                                     \n";
    $stSql .= "          FROM folhapagamento.evento_complementar_calculado                                                           \n";
    $stSql .= "             , folhapagamento.registro_evento_complementar                                                            \n";
    $stSql .= "             , pessoal.contrato                                                                                       \n";
    $stSql .= "             , folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "             , (SELECT cod_tabela                                                                                                               \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                              \n";
    $stSql .= "                  FROM folhapagamento.tabela_irrf_evento                                                              \n";
    $stSql .= "                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                  \n";
    $stSql .= "         WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento                                       \n";
    $stSql .= "           AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro                                     \n";
    $stSql .= "           AND evento_complementar_calculado.cod_configuracao      = registro_evento_complementar.cod_configuracao                              \n";
    $stSql .= "           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                                        \n";
    $stSql .= "           AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                                                \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento = tabela_irrf_evento.cod_evento                                                          \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                \n";
    $stSql .= "           AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                  \n";
    $stSql .= "           AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."                                           \n";
    $stSql .= "           AND tabela_irrf_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                     \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                                            \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao ) as irrf                                                                       \n";
    $stSql .= " WHERE cod_contrato = ".$this->getDado("cod_contrato")."                                                                                        \n";
    $stSql .= "   AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                                                \n";
    $stSql .= "   AND inFolha = ".$this->getDado("inFolha").") BETWEEN faixa_desconto_irrf.vl_inicial                                                          \n";
    $stSql .= "                        AND faixa_desconto_irrf.vl_final;                                                                                       \n";

    return $stSql;
}

}
