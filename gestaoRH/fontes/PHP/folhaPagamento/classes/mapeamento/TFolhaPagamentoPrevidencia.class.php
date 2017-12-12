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
  * Classe de mapeamento da tabela FOLHAPAGAMENTO.PREVIDENCIA
  * Data de Criação: 26/11/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

      Caso de uso: uc-04.05.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.PREVIDENCIA
  * Data de Criação: 26/11/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoPrevidencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoPrevidencia()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.previdencia');

    $this->setCampoCod('cod_previdencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_previdencia'   ,'integer',true,'',true,false);
    $this->AddCampo('cod_vinculo'       ,'integer',true,'',false,true);
    $this->AddCampo('cod_regime_previdencia','integer',true,'',false,true);

}

function recuperaLista(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrder = " ORDER BY lower(previdencia_previdencia.descricao)";
    $stSql = $this->montaRecuperaLista().$stFiltro.$stOrder;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLista()
{
    $stSql  = "    SELECT previdencia.*\n";
    $stSql .= "         , previdencia_previdencia.*\n";
    $stSql .= "         , CASE WHEN contrato_servidor_previdencia.cod_contrato IS NULL THEN ''\n";
    $stSql .= "           ELSE                           'true'\n";
    $stSql .= "           END as booleano\n";
    $stSql .= "         , CASE previdencia_previdencia.tipo_previdencia\n";
    $stSql .= "           WHEN 'o' THEN 'Oficial'\n";
    $stSql .= "           WHEN 'p' THEN 'Privada'\n";
    $stSql .= "           END as tipo_previdencia\n";
    $stSql .= "      FROM folhapagamento.previdencia_previdencia\n";
    $stSql .= "         , (  SELECT cod_previdencia\n";
    $stSql .= "                   , max(timestamp) as timestamp\n";
    $stSql .= "                FROM folhapagamento.previdencia_previdencia\n";
    $stSql .= "            GROUP BY cod_previdencia) max_previdencia_previdencia\n";
    $stSql .= "         , folhapagamento.previdencia\n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_previdencia.cod_contrato\n";
    $stSql .= "                , contrato_servidor_previdencia.cod_previdencia\n";
    $stSql .= "                , contrato_servidor_previdencia.bo_excluido\n";
    $stSql .= "             FROM pessoal.contrato_servidor_previdencia\n";
    $stSql .= "                , (  SELECT cod_contrato\n";
    $stSql .= "                          , max(timestamp) as timestamp\n";
    $stSql .= "                       FROM pessoal.contrato_servidor_previdencia\n";
    $stSql .= "                   GROUP BY cod_contrato) max_contrato_servidor_previdencia\n";
    $stSql .= "            WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato\n";
    $stSql .= "              AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp\n";
    $stSql .= "              AND contrato_servidor_previdencia.bo_excluido IS FALSE \n";
    if ($this->getDado('cod_contrato') != '') {
        $stSql .= "              AND contrato_servidor_previdencia.cod_contrato = ".$this->getDado('cod_contrato')."\n";
    } else {
        $stSql .= "              AND contrato_servidor_previdencia.cod_contrato is null \n";
    }
    $stSql .= ") as contrato_servidor_previdencia\n";
    $stSql .= "      ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia\n";
    $stSql .= "   WHERE previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia\n";
    $stSql .= "     AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia\n";
    $stSql .= "     AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp\n";
    if ( $this->getDado("cod_vinculo") != "" ) {
        $stSql .= "	AND previdencia.cod_vinculo = ".$this->getDado("cod_vinculo")."					   \n";
    }

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql = "    SELECT fp.cod_previdencia                                          \n";
    $stSql.= "         , fpp.timestamp                                               \n";
    $stSql.= "         , fpp.descricao                                               \n";
    $stSql.= "         , fpp.aliquota                                                \n";
    $stSql.= "         , fpp.aliquota_rat                                            \n";
    $stSql.= "         , fpp.aliquota_fap                                            \n";
    $stSql.= "         , fpp.tipo_previdencia                                        \n";
    $stSql.= "         , fp.cod_vinculo                                              \n";
    $stSql.= "         , fv.descricao       as descricao_vinculo                     \n";
    $stSql.= "         , to_char(fpp.vigencia,'dd/mm/yyyy') as vigencia              \n";
    $stSql.= "         , fp.cod_regime_previdencia                                   \n";
    $stSql.= "      FROM folhapagamento.previdencia fp                               \n";
    $stSql.= "      JOIN ( SELECT fpp2.*                                             \n";
    $stSql.= "                  , previdencia_regime_rat.aliquota_rat                \n";
    $stSql.= "                  , previdencia_regime_rat.aliquota_fap                \n";
    $stSql.= "               FROM folhapagamento.previdencia_previdencia fpp2        \n";
    $stSql.= "             LEFT JOIN folhapagamento.previdencia_regime_rat           \n";
    $stSql.= "                    ON fpp2.cod_previdencia = previdencia_regime_rat.cod_previdencia \n";
    $stSql.= "                   AND fpp2.timestamp       = previdencia_regime_rat.timestamp       \n";
    $stSql.= "               JOIN (    SELECT cod_previdencia                        \n";
    $stSql.= "                              , MAX(timestamp) as timestamp            \n";
    $stSql.= "                           FROM folhapagamento.previdencia_previdencia \n";
    $stSql.= "                       GROUP BY cod_previdencia                        \n";
    $stSql.= "                    ) max_fpp                                          \n";
    $stSql.= "                 ON max_fpp.cod_previdencia = fpp2.cod_previdencia     \n";
    $stSql.= "                AND max_fpp.timestamp       = fpp2.timestamp           \n";
    $stSql.= "           ) fpp                                                       \n";
    $stSql.= "        ON fpp.cod_previdencia = fp.cod_previdencia                    \n";
    $stSql.= "      JOIN folhapagamento.vinculo fv                                   \n";
    $stSql.= "        ON fv.cod_vinculo = fp.cod_vinculo                             \n";
    $stSql.= "     WHERE true                                                        \n";

    return $stSql;
}

function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaValidaExclusao().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Esta previdência está sendo utilizada no cadastro de um servidor e por isso não pode ser excluída.');
        }
    }

    return $obErro;
}

function montaValidaExclusao()
{
    $stSQL  = " SELECT pcsp.cod_previdencia                                       \n";
    $stSQL .= "   FROM pessoal.contrato_servidor_previdencia pcsp                 \n";
    $stSQL .= "   JOIN folhapagamento.previdencia fp                              \n";
    $stSQL .= "     ON fp.cod_previdencia = pcsp.cod_previdencia                  \n";
    $stSQL .= "  WHERE fp.cod_previdencia = ".$this->getDado('cod_previdencia')." \n";

    return $stSQL;
}

function recuperaRelatorioContribuicaoPrevidenciaria(&$rsRecordSet, $stFiltro= "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = " ORDER BY ".$stOrdem;
    $stSql = $this->montaRecuperaRelatorioContribuicaoPrevidenciaria().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioContribuicaoPrevidenciaria()
{
    $stSql  = "    SELECT contrato.registro                                                                                                                \n";
    $stSql .= "         , contratos_calculados.cod_periodo_movimentacao                                                                                    \n";
    $stSql .= "         , contrato.cod_contrato                                                                                                            \n";
    $stSql .= "         , sw_cgm.nom_cgm                                                                                                                   \n";
    $stSql .= "         , descricao_lotacao                                                                                                                \n";
    $stSql .= "         , cod_categoria                                                                                                                    \n";
    $stSql .= "         , cod_ocorrencia                                                                                                                   \n";
    $stSql .= "         , servidor_dependente.num_dependentes                                                                                              \n";
    $stSql .= "         , evento_desconto.cod_evento as cod_evento_desconto                                                                                \n";
    $stSql .= "         , evento_base.cod_evento as cod_evento_base                                                                                        \n";
    $stSql .= "         , evento_maternidade.cod_evento as cod_evento_maternidade                                                                          \n";
    $stSql .= "         , evento_familia.cod_evento as cod_evento_familia                                                                                  \n";
    $stSql .= "      FROM (  SELECT registro_evento_periodo.cod_contrato                                                                                   \n";
    $stSql .= "                   , registro_evento_periodo.cod_periodo_movimentacao                                                                       \n";
    $stSql .= "                FROM folhapagamento.registro_evento_periodo                                                                                 \n";
    $stSql .= "                   , folhapagamento.registro_evento                                                                                         \n";
    $stSql .= "                   , folhapagamento.ultimo_registro_evento                                                                                  \n";
    $stSql .= "                   , folhapagamento.evento_calculado                                                                                        \n";
    $stSql .= "               WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                    \n";
    $stSql .= "                 AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                     \n";
    $stSql .= "                 AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento                                                       \n";
    $stSql .= "                 AND registro_evento.timestamp    = ultimo_registro_evento.timestamp                                                        \n";
    $stSql .= "                 AND registro_evento.cod_registro = evento_calculado.cod_registro                                                           \n";
    $stSql .= "                 AND registro_evento.cod_evento   = evento_calculado.cod_evento                                                             \n";
    $stSql .= "                 AND registro_evento.timestamp    = evento_calculado.timestamp_registro                                                     \n";
    $stSql .= "            GROUP BY registro_evento_periodo.cod_contrato                                                                                   \n";
    $stSql .= "                   , registro_evento_periodo.cod_periodo_movimentacao                                                                       \n";
    $stSql .= "            UNION                                                                                                                           \n";
    $stSql .= "              SELECT registro_evento_complementar.cod_contrato                                                                              \n";
    $stSql .= "                   , registro_evento_complementar.cod_periodo_movimentacao                                                                  \n";
    $stSql .= "                FROM folhapagamento.registro_evento_complementar                                                                            \n";
    $stSql .= "                   , folhapagamento.ultimo_registro_evento_complementar                                                                     \n";
    $stSql .= "                   , folhapagamento.evento_complementar_calculado                                                                           \n";
    $stSql .= "               WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                           \n";
    $stSql .= "                 AND registro_evento_complementar.cod_evento   = ultimo_registro_evento_complementar.cod_evento                             \n";
    $stSql .= "                 AND registro_evento_complementar.timestamp    = ultimo_registro_evento_complementar.timestamp                              \n";
    $stSql .= "                 AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao                   \n";
    $stSql .= "                 AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                                 \n";
    $stSql .= "                 AND registro_evento_complementar.cod_evento   = evento_complementar_calculado.cod_evento                                   \n";
    $stSql .= "                 AND registro_evento_complementar.timestamp    = evento_complementar_calculado.timestamp_registro                           \n";
    $stSql .= "                 AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                         \n";
    $stSql .= "            GROUP BY registro_evento_complementar.cod_contrato                                                                              \n";
    $stSql .= "                   , registro_evento_complementar.cod_periodo_movimentacao) as contratos_calculados                                         \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                                                         \n";
    $stSql .= "                , contrato_servidor_local.cod_contrato                                                                                      \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                           \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                 \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                    \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                           \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp) as contrato_servidor_local                  \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = contrato_servidor_local.cod_contrato                                                          \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_regime_funcao.cod_regime as cod_regime_funcao                                                           \n";
    $stSql .= "                , contrato_servidor_regime_funcao.cod_contrato                                                                              \n";
    $stSql .= "             FROM pessoal.contrato_servidor_regime_funcao                                                                                   \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_regime_funcao                                                                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                            \n";
    $stSql .= "            WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                           \n";
    $stSql .= "              AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp) as regime_funcao            \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = regime_funcao.cod_contrato                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao as cod_sub_divisao_funcao                                              \n";
    $stSql .= "                , contrato_servidor_sub_divisao_funcao.cod_contrato                                                                         \n";
    $stSql .= "             FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                    \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_subdivisao_funcao                                                        \n";
    $stSql .= "            WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_subdivisao_funcao.cod_contrato                  \n";
    $stSql .= "              AND contrato_servidor_sub_divisao_funcao.timestamp    = max_contrato_servidor_subdivisao_funcao.timestamp) as subdivisao_funcao\n";
    $stSql .= "       ON contratos_calculados.cod_contrato = subdivisao_funcao.cod_contrato                                                                \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_ocorrencia.cod_contrato                                                                                 \n";
    $stSql .= "                , contrato_servidor_ocorrencia.cod_ocorrencia                                                                               \n";
    $stSql .= "             FROM pessoal.contrato_servidor_ocorrencia                                                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_ocorrencia                                                   \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia                                                               \n";
    $stSql .= "            WHERE contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato                                 \n";
    $stSql .= "              AND contrato_servidor_ocorrencia.timestamp = max_contrato_servidor_ocorrencia.timestamp) as contrato_servidor_ocorrencia      \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = contrato_servidor_ocorrencia.cod_contrato                                                     \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                                                                                      \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                         \n";
    $stSql .= "                , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                          \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                  \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                        \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                    \n";
    $stSql .= "                , organograma.orgao                                                                                                         \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                           \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp                                              \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao) as contrato_servidor_orgao                                        \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = contrato_servidor_orgao.cod_contrato                                                          \n";
    $stSql .= "LEFT JOIN (SELECT previdencia_previdencia.cod_previdencia                                                                                   \n";
    $stSql .= "                , contrato_servidor_previdencia.cod_contrato                                                                                \n";
    $stSql .= "                , previdencia_evento.cod_evento                                                                                             \n";
    $stSql .= "             FROM folhapagamento.previdencia_previdencia                                                           \n";
    $stSql .= "                , (  SELECT cod_previdencia                                                                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM folhapagamento.previdencia_previdencia                                                 \n";
    $stSql .= "                   GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                 \n";
    $stSql .= "                , folhapagamento.previdencia                                                                       \n";
    $stSql .= "                , pessoal.contrato_servidor_previdencia                                                            \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_previdencia                                                  \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                              \n";
    $stSql .= "                , folhapagamento.previdencia_evento                                                                \n";
    $stSql .= "            WHERE previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                                     \n";
    $stSql .= "              AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                                               \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                     \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                 \n";
    $stSql .= "              AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                               \n";
    $stSql .= "              AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                                     \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia                                              \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = previdencia_evento.timestamp                                                          \n";
    $stSql .= "              AND previdencia_evento.cod_tipo = 1) as evento_desconto                                                                       \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = evento_desconto.cod_contrato                                                                  \n";
    $stSql .= "LEFT JOIN (SELECT previdencia_previdencia.cod_previdencia                                                                                   \n";
    $stSql .= "                , contrato_servidor_previdencia.cod_contrato                                                                                \n";
    $stSql .= "                , previdencia_evento.cod_evento                                                                                             \n";
    $stSql .= "             FROM folhapagamento.previdencia_previdencia                                                           \n";
    $stSql .= "                , (  SELECT cod_previdencia                                                                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM folhapagamento.previdencia_previdencia                                                 \n";
    $stSql .= "                   GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                 \n";
    $stSql .= "                , folhapagamento.previdencia                                                                       \n";
    $stSql .= "                , pessoal.contrato_servidor_previdencia                                                            \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_previdencia                                                  \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                              \n";
    $stSql .= "                , folhapagamento.previdencia_evento                                                                \n";
    $stSql .= "            WHERE previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                                     \n";
    $stSql .= "              AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                                               \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                     \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                 \n";
    $stSql .= "              AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                               \n";
    $stSql .= "              AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                                     \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia                                              \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = previdencia_evento.timestamp                                                          \n";
    $stSql .= "              AND previdencia_evento.cod_tipo = 2) as evento_base                                                                           \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = evento_base.cod_contrato                                                                      \n";
    $stSql .= "LEFT JOIN (SELECT previdencia_previdencia.cod_previdencia                                                                                   \n";
    $stSql .= "                , contrato_servidor_previdencia.cod_contrato                                                                                \n";
    $stSql .= "                , previdencia_evento.cod_evento                                                                                             \n";
    $stSql .= "             FROM folhapagamento.previdencia_previdencia                                                           \n";
    $stSql .= "                , (  SELECT cod_previdencia                                                                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM folhapagamento.previdencia_previdencia                                                 \n";
    $stSql .= "                   GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                 \n";
    $stSql .= "                , folhapagamento.previdencia                                                                       \n";
    $stSql .= "                , pessoal.contrato_servidor_previdencia                                                            \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_previdencia                                                  \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                              \n";
    $stSql .= "                , folhapagamento.previdencia_evento                                                                \n";
    $stSql .= "            WHERE previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                                     \n";
    $stSql .= "              AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                                               \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                     \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                 \n";
    $stSql .= "              AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                               \n";
    $stSql .= "              AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                                     \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia                                              \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = previdencia_evento.timestamp                                                          \n";
    $stSql .= "              AND previdencia_evento.cod_tipo = 3) as evento_maternidade                                                                    \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = evento_maternidade.cod_contrato                                                               \n";
    $stSql .= "LEFT JOIN ( SELECT previdencia_previdencia.cod_previdencia                                                                                  \n";
    $stSql .= "                 , contrato_servidor_previdencia.cod_contrato                                                                               \n";
    $stSql .= "                 , salario_familia_evento.cod_evento                                                                                        \n";
    $stSql .= "              FROM folhapagamento.salario_familia_evento                                                           \n";
    $stSql .= "                 , (  SELECT cod_regime_previdencia                                                                                         \n";
    $stSql .= "                           , max(timestamp) as timestamp                                                                                    \n";
    $stSql .= "                        FROM folhapagamento.salario_familia                                                        \n";
    $stSql .= "                    GROUP BY cod_regime_previdencia) as max_salario_familia                                                                 \n";
    $stSql .= "                 , folhapagamento.regime_previdencia                                                               \n";
    $stSql .= "                 , folhapagamento.previdencia_previdencia                                                          \n";
    $stSql .= "                 , (  SELECT cod_previdencia                                                                                                \n";
    $stSql .= "                           , max(timestamp) as timestamp                                                                                    \n";
    $stSql .= "                        FROM folhapagamento.previdencia_previdencia                                                \n";
    $stSql .= "                    GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                \n";
    $stSql .= "                 , folhapagamento.previdencia                                                                      \n";
    $stSql .= "                 , pessoal.contrato_servidor_previdencia                                                           \n";
    $stSql .= "                 , (  SELECT cod_contrato                                                                                                   \n";
    $stSql .= "                           , max(timestamp) as timestamp                                                                                    \n";
    $stSql .= "                        FROM pessoal.contrato_servidor_previdencia                                                 \n";
    $stSql .= "                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                             \n";
    $stSql .= "             WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia.cod_regime_previdencia                               \n";
    $stSql .= "               AND salario_familia_evento.timestamp = max_salario_familia.timestamp                                                         \n";
    $stSql .= "               AND salario_familia_evento.cod_regime_previdencia = regime_previdencia.cod_regime_previdencia                                \n";
    $stSql .= "               AND regime_previdencia.cod_regime_previdencia = previdencia.cod_regime_previdencia                                           \n";
    $stSql .= "               AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                                    \n";
    $stSql .= "               AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                                              \n";
    $stSql .= "               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                    \n";
    $stSql .= "               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                \n";
    $stSql .= "               AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                              \n";
    $stSql .= "               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                                    \n";
    $stSql .= "               AND salario_familia_evento.cod_tipo = 1) as evento_familia                                                                   \n";
    $stSql .= "       ON contratos_calculados.cod_contrato = evento_familia.cod_contrato                                                                   \n";
    $stSql .= "                                                                                                                                            \n";
    $stSql .= "        , pessoal.contrato_servidor                                                                                \n";
    $stSql .= "        , pessoal.contrato                                                                                         \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                                       \n";
    $stSql .= "        , pessoal.servidor                                                                                         \n";
    $stSql .= "LEFT JOIN (  SELECT COUNT(cod_dependente) as num_dependentes                                                                                \n";
    $stSql .= "                  , cod_servidor                                                                                                            \n";
    $stSql .= "               FROM pessoal.servidor_dependente                                                                    \n";
    $stSql .= "           GROUP BY cod_servidor) as servidor_dependente                                                                                    \n";
    $stSql .= "       ON servidor.cod_servidor = servidor_dependente.cod_servidor                                                                          \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                              \n";
    $stSql .= "        , sw_cgm                                                                                                                            \n";
    $stSql .= "    WHERE contratos_calculados.cod_contrato = contrato_servidor.cod_contrato                                                                \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = contrato.cod_contrato                                                                            \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                          \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                   \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                     \n";
    $stSql .= "      AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                       \n";

    return $stSql;
}

function recuperaFaixasDescontosPrevidencias(&$rsRecordSet, $stFiltro= "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperarFaixasDescontosPrevidencias().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperarFaixasDescontosPrevidencias()
{
    $stSql  = "SELECT contrato_servidor_previdencia.cod_contrato                                                       \n";
    $stSql .= "     , faixa_desconto.*                                                                                 \n";
    $stSql .= "  FROM folhapagamento.faixa_desconto                                                                    \n";
    $stSql .= "     , (  SELECT cod_previdencia                                                                        \n";
    $stSql .= "               , max(timestamp_previdencia) as timestamp_previdencia                                    \n";
    $stSql .= "            FROM folhapagamento.faixa_desconto                                                          \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_faixa_desconto                                                 \n";
    $stSql .= "     , pessoal.contrato_servidor_previdencia                                                            \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "               , max(timestamp) as timestamp                                                            \n";
    $stSql .= "            FROM pessoal.contrato_servidor_previdencia                                                  \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                     \n";
    $stSql .= "     , folhapagamento.previdencia_previdencia                                                           \n";
    $stSql .= " WHERE faixa_desconto.cod_previdencia = max_faixa_desconto.cod_previdencia                              \n";
    $stSql .= "   AND faixa_desconto.timestamp_previdencia = max_faixa_desconto.timestamp_previdencia                  \n";
    $stSql .= "   AND faixa_desconto.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                   \n";
    $stSql .= "   AND faixa_desconto.cod_previdencia = previdencia_previdencia.cod_previdencia                         \n";
    $stSql .= "   AND faixa_desconto.timestamp_previdencia = previdencia_previdencia.timestamp                         \n";
    $stSql .= "   AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato      \n";
    $stSql .= "   AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp            \n";

    return $stSql;
}

}
