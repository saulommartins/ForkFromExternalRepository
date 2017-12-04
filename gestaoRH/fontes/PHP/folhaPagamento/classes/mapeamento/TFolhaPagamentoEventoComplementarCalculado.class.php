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
    * Classe de mapeamento da tabela folhapagamento.evento_complementar_calculado
    * Data de Criação: 25/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32866 $
    $Name$
    $Author: alex $
    $Date: 2008-04-07 10:38:16 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-04.05.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.evento_complementar_calculado
  * Data de Criação: 25/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoComplementarCalculado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoComplementarCalculado()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.evento_complementar_calculado");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,timestamp_registro,cod_configuracao');

    $this->AddCampo('cod_evento','integer',true,'',true             ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('cod_registro','integer',true,'',true           ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('timestamp_registro','timestamp',false,'',true  ,"TFolhaPagamentoUltimoRegistroEventoComplementar","timestamp");
    $this->AddCampo('cod_configuracao','integer',true,'',true       ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('valor','numeric',true,'15,2',false,false);
    $this->AddCampo('quantidade','numeric',true,'15,2',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT * FROM (                                                                                                                                                         \n";
    $stSql .= "SELECT                                                                                                                                                                  \n";
    $stSql .= "       contrato.registro                                                                                                                                                \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                                  \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                                                                   \n";
    $stSql .= "     , servidor.funcao                                                                                                                                                  \n";
    $stSql .= "     , contrato_servidor_especialidade_funcao.descricao as especialidade                                                                                                \n";
    $stSql .= "     , servidor.lotacao                                                                                                                                                 \n";
    $stSql .= "     , registro_evento_complementar.cod_complementar                                                                                                                    \n";
    $stSql .= "  FROM folhapagamento.evento_complementar_calculado                                                                                            \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar                                                                                             \n";
    $stSql .= "     , folhapagamento.contrato_servidor_complementar                                                                                           \n";
    $stSql .= "     , (SELECT contrato_pensionista.cod_contrato                                                                                                                        \n";
    $stSql .= "             , pensionista.numcgm                                                                                                                                       \n";
    $stSql .= "             , '' as funcao                                                                                                                                             \n";
    $stSql .= "             , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao                                                                   \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                                                    \n";
    $stSql .= "             , pessoal.pensionista                                                                                                             \n";
    $stSql .= "             , pessoal.contrato_pensionista_orgao                                                                                              \n";
    $stSql .= "             , (SELECT cod_contrato                                                                                                                                     \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                                                      \n";
    $stSql .= "                  FROM pessoal.contrato_pensionista_orgao                                                                                      \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                                \n";
    $stSql .= "             , organograma.orgao                                                                                                                                        \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                                       \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                             \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                                              \n";
    $stSql .= "           AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                    \n";
    $stSql .= "           AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp                                                                          \n";
    $stSql .= "           AND contrato_pensionista_orgao.cod_orgao = orgao.cod_orgao                                                                                                   \n";
    $stSql .= "         UNION                                                                                                                                                          \n";
    $stSql .= "        SELECT servidor_contrato_servidor.cod_contrato                                                                                                                  \n";
    $stSql .= "             , servidor.numcgm                                                                                                                                          \n";
    $stSql .= "             , cargo.descricao as funcao                                                                                                                                \n";
    $stSql .= "             , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao                                                                   \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                                              \n";
    $stSql .= "             , pessoal.servidor                                                                                                                \n";
    $stSql .= "             , pessoal.contrato_servidor_funcao                                                                                                \n";
    $stSql .= "             , (  SELECT contrato_servidor_funcao.cod_contrato                                                                                                          \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                                                    \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_funcao                                                                                      \n";
    $stSql .= "                GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao                                                                         \n";
    $stSql .= "             , pessoal.cargo                                                                                                                   \n";
    $stSql .= "             , pessoal.contrato_servidor_orgao                                                                                                 \n";
    $stSql .= "             , (  SELECT contrato_servidor_orgao.cod_contrato                                                                                                           \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                                                    \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_orgao                                                                                       \n";
    $stSql .= "                GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao                                                                           \n";
    $stSql .= "             , organograma.orgao                                                                                                                                        \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                          \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                          \n";
    $stSql .= "           AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                        \n";
    $stSql .= "           AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp                                                                           \n";
    $stSql .= "           AND contrato_servidor_funcao.cod_cargo    = cargo.cod_cargo                                                                                                  \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_contrato                            = contrato_servidor_orgao.cod_contrato                                                \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_contrato                      = max_contrato_servidor_orgao.cod_contrato                                                     \n";
    $stSql .= "           AND contrato_servidor_orgao.timestamp                         = max_contrato_servidor_orgao.timestamp                                                        \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_orgao                         = orgao.cod_orgao         ) as servidor                                                        \n";
    $stSql .= "     LEFT JOIN (SELECT especialidade.cod_especialidade                                                                                                                  \n";
    $stSql .= "                     , especialidade.descricao                                                                                                                          \n";
    $stSql .= "                     , contrato_servidor_especialidade_funcao.cod_contrato                                                                                              \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                                          \n";
    $stSql .= "                     , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                    \n";
    $stSql .= "                               , max(timestamp) as timestamp                                                                                                            \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_especialidade_funcao                                                                \n";
    $stSql .= "                        GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao                                     \n";
    $stSql .= "                     , pessoal.especialidade                                                                                                   \n";
    $stSql .= "                 WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                    \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp                                       \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao            \n";
    $stSql .= "            ON servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                              \n";
    $stSql .= "     , pessoal.contrato                                                                                                                        \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                                                                                             \n";
    $stSql .= "     , sw_cgm                                                                                                                                                           \n";
    $stSql .= " WHERE evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro                                                                           \n";
    $stSql .= "   AND evento_complementar_calculado.cod_evento   = registro_evento_complementar.cod_evento                                                                             \n";
    $stSql .= "   AND evento_complementar_calculado.timestamp_registro    = registro_evento_complementar.timestamp                                                                     \n";
    $stSql .= "   AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao                                                                   \n";
    $stSql .= "   AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao                                                  \n";
    $stSql .= "   AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar                                                          \n";
    $stSql .= "   AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato                                                              \n";
    $stSql .= "   AND contrato_servidor_complementar.cod_contrato           = servidor.cod_contrato                                                                                    \n";
    $stSql .= "   AND servidor.cod_contrato                        = contrato.cod_contrato                                                                                             \n";
    $stSql .= "   AND servidor.numcgm                                       = sw_cgm_pessoa_fisica.numcgm                                                                              \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm                           = sw_cgm.numcgm                                                                                            \n";
    $stSql .= "GROUP BY contrato.registro                                                                                                                                              \n";
    $stSql .= "       , servidor.numcgm                                                                                                                                                \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                                                                                 \n";
    $stSql .= "       , servidor.funcao                                                                                                                                                \n";
    $stSql .= "       , contrato_servidor_especialidade_funcao.descricao                                                                                                               \n";
    $stSql .= "       , registro_evento_complementar.cod_complementar                                                                                                                  \n";
    $stSql .= "       , servidor.lotacao ) as evento_complementar_calculado                                                                                                            \n";

    return $stSql;
}

function recuperaEventoFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY evento.cod_evento ";
    $stSql  = $this->montaRecuperaEventoFichaFinanceira().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoFichaFinanceira()
{
    $stSql  = "   SELECT evento.codigo                                                                                                                                 \n";
    $stSql .= "        , evento.cod_evento                                                                                                                             \n";
    $stSql .= "        , trim(evento.descricao) as descricao                                                                                                           \n";
    $stSql .= "        , evento.natureza                                                                                                                               \n";
    $stSql .= "        , evento_complementar_calculado.quantidade                                                                                                      \n";
    $stSql .= "        , evento_complementar_calculado.valor                                                                                                           \n";
    $stSql .= "        , evento_complementar_calculado.cod_configuracao                                                                                                \n";
    $stSql .= "        , CASE evento_complementar_calculado.cod_configuracao                                                                                           \n";
    $stSql .= "          WHEN '1' THEN 'Salário'                                                                                                                       \n";
    $stSql .= "          WHEN '2' THEN  ( CASE WHEN evento_complementar_calculado.desdobramento != ''                                                                  \n";
    $stSql .= "                                     THEN getDesdobramentoFerias(evento_complementar_calculado.desdobramento,'".Sessao::getEntidade()."')               \n";
    $stSql .= "                                     ELSE 'Férias' END )                                                                                                \n";
    $stSql .= "          WHEN '3' THEN 'Décimo'                                                                                                                        \n";
    $stSql .= "          WHEN '4' THEN 'Rescisão'                                                                                                                      \n";
    $stSql .= "          END as desdobramento                                                                                                                          \n";
    $stSql .= "        , CASE evento.natureza                                                                                                                          \n";
    $stSql .= "          WHEN 'P' THEN 'proventos'                                                                                                                     \n";
    $stSql .= "          WHEN 'D' THEN 'descontos'                                                                                                                     \n";
    $stSql .= "          WHEN 'B' THEN 'base'                                                                                                                          \n";
    $stSql .= "          END as proventos_descontos                                                                                                                    \n";
    $stSql .= "        , contrato.cod_contrato                                                                                                                         \n";
    $stSql .= "     FROM folhapagamento.evento_complementar_calculado                                                                                                  \n";
    $stSql .= "        , folhapagamento.registro_evento_complementar                                                                                                   \n";
    $stSql .= "        , folhapagamento.evento                                                                                                                         \n";
    $stSql .= "        , folhapagamento.sequencia_calculo_evento                                                                                                       \n";
    $stSql .= "        , folhapagamento.sequencia_calculo                                                                                                              \n";
    $stSql .= "        , folhapagamento.contrato_servidor_complementar                                                                                                 \n";
    $stSql .= "        , (SELECT pensionista.numcgm                                                                                                                    \n";
    $stSql .= "                , contrato_pensionista.cod_contrato                                                                                                     \n";
    $stSql .= "                , 0 as cod_cargo                                                                                                                        \n";
    $stSql .= "                , contrato_pensionista_orgao.cod_orgao                                                                                                  \n";
    $stSql .= "             FROM pessoal.pensionista                                                                                                                   \n";
    $stSql .= "                , pessoal.contrato_pensionista                                                                                                          \n";
    $stSql .= "                , pessoal.contrato_pensionista_orgao                                                                                                    \n";
    $stSql .= "                , (  SELECT contrato_pensionista_orgao.cod_contrato                                                                                     \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_pensionista_orgao                                                                                          \n";
    $stSql .= "                   GROUP BY contrato_pensionista_orgao.cod_contrato) as max_contrato_pensionista_orgao                                                  \n";
    $stSql .= "            WHERE pensionista.cod_pensionista = contrato_pensionista.cod_pensionista                                                                    \n";
    $stSql .= "              AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente                                                          \n";
    $stSql .= "              AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                 \n";
    $stSql .= "              AND contrato_pensionista_orgao.timestamp    = max_contrato_pensionista_orgao.timestamp                                                    \n";
    $stSql .= "              AND contrato_pensionista_orgao.cod_contrato = contrato_pensionista.cod_contrato                                                           \n";
    $stSql .= "            UNION                                                                                                                                       \n";
    $stSql .= "           SELECT servidor.numcgm                                                                                                                       \n";
    $stSql .= "                , contrato_servidor.cod_contrato                                                                                                        \n";
    $stSql .= "                , contrato_servidor_funcao.cod_cargo                                                                                                    \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                     \n";
    $stSql .= "             FROM pessoal.contrato_servidor                                                                                                             \n";
    $stSql .= "                , pessoal.servidor_contrato_servidor                                                                                                    \n";
    $stSql .= "                , pessoal.servidor                                                                                                                      \n";
    $stSql .= "                , pessoal.contrato_servidor_funcao                                                                                                      \n";
    $stSql .= "                , (  SELECT contrato_servidor_funcao.cod_contrato                                                                                       \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_funcao                                                                                            \n";
    $stSql .= "                   GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao                                                      \n";
    $stSql .= "                , pessoal.contrato_servidor_orgao                                                                                                       \n";
    $stSql .= "                , (  SELECT contrato_servidor_orgao.cod_contrato                                                                                        \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                                             \n";
    $stSql .= "                   GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao                                                        \n";
    $stSql .= "            WHERE contrato_servidor.cod_contrato          = servidor_contrato_servidor.cod_contrato                                                     \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                       \n";
    $stSql .= "              AND contrato_servidor.cod_contrato          = contrato_servidor_funcao.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_contrato   = max_contrato_servidor_funcao.cod_contrato                                                   \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp      = max_contrato_servidor_funcao.timestamp                                                      \n";
    $stSql .= "              AND contrato_servidor.cod_contrato          = contrato_servidor_orgao.cod_contrato                                                        \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_contrato    = max_contrato_servidor_orgao.cod_contrato                                                    \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp       = max_contrato_servidor_orgao.timestamp) as contrato_servidor                                 \n";
    $stSql .= "LEFT JOIN (SELECT especialidade.cod_especialidade                                                                                                       \n";
    $stSql .= "                , especialidade.descricao                                                                                                               \n";
    $stSql .= "                , contrato_servidor_especialidade_funcao.cod_contrato                                                                                   \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                        \n";
    $stSql .= "                , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                         \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                                              \n";
    $stSql .= "                   GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao                          \n";
    $stSql .= "                , pessoal.especialidade                                                                                                                 \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                         \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp                            \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                          \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.*                                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                                       \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                             \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp) as contrato_servidor_local                              \n";
    $stSql .= "              ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                  \n";
    $stSql .= "        , pessoal.contrato                                                                                                                              \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                          \n";
    $stSql .= "        , sw_cgm                                                                                                                                        \n";
    $stSql .= "        , folhapagamento.complementar                                                                                                                   \n";
    $stSql .= "        , folhapagamento.periodo_movimentacao                                                                                                           \n";
    $stSql .= "    WHERE evento_complementar_calculado.cod_registro            = registro_evento_complementar.cod_registro                                             \n";
    $stSql .= "      AND evento_complementar_calculado.timestamp_registro      = registro_evento_complementar.timestamp                                                \n";
    $stSql .= "      AND evento_complementar_calculado.cod_evento              = registro_evento_complementar.cod_evento                                               \n";
    $stSql .= "      AND evento_complementar_calculado.cod_configuracao        = registro_evento_complementar.cod_configuracao                                         \n";
    $stSql .= "      AND registro_evento_complementar.cod_evento               = evento.cod_evento                                                                     \n";
    $stSql .= "      AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao                               \n";
    $stSql .= "      AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar                                       \n";
    $stSql .= "      AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato                                           \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar       = complementar.cod_complementar                                                         \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_periodo_movimentacao = complementar.cod_periodo_movimentacao                                               \n";
    $stSql .= "      AND complementar.cod_periodo_movimentacao                 = periodo_movimentacao.cod_periodo_movimentacao                                         \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato           = contrato_servidor.cod_contrato                                                        \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato           = contrato.cod_contrato                                                        \n";
    $stSql .= "      AND contrato_servidor.numcgm                                       = sw_cgm_pessoa_fisica.numcgm                                                  \n";
    $stSql .= "      AND sw_cgm_pessoa_fisica.numcgm                           = sw_cgm.numcgm                                                                         \n";
    $stSql .= "      AND evento.cod_evento = sequencia_calculo_evento.cod_evento                                                                                       \n";
    $stSql .= "      AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                                                                      \n";

    return $stSql;
}

function recuperaRelatorioFolhaAnaliticaSintetica(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.cod_contrato ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnaliticaSintetica().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnaliticaSintetica()
{
    $stSql  = "SELECT evento.cod_evento                                                             \n";
    $stSql .= "     , evento.natureza                                                               \n";
    $stSql .= "     , evento.codigo                                                                 \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                           \n";
    $stSql .= "     , evento_complementar_calculado.quantidade                                      \n";
    $stSql .= "     , evento_complementar_calculado.valor                                           \n";
    $stSql .= "     , contrato.cod_contrato                                                         \n";
    $stSql .= "     , contrato.registro                                                             \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                \n";
    $stSql .= "     , sw_cgm.numcgm                                                                 \n";
    $stSql .= "     , cod_orgao                                                                     \n";
    $stSql .= "  FROM folhapagamento.evento_complementar_calculado                                  \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar                                   \n";
    $stSql .= "     , folhapagamento.evento                                                         \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                       \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                              \n";
    $stSql .= "     , folhapagamento.contrato_servidor_complementar                                 \n";
    $stSql .= "     , pessoal.contrato_servidor                                                     \n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                         \n";
    $stSql .= "                     , contrato_servidor_padrao.cod_padrao                           \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_padrao                              \n";
    $stSql .= "                     , (  SELECT cod_contrato                                        \n";
    $stSql .= "                               , max(timestamp) as timestamp                         \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_padrao                    \n";
    $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_padrao       \n";
    $stSql .= "                 WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao    \n";
    $stSql .= "            ON contrato_servidor.cod_contrato = contrato_servidor_padrao.cod_contrato\n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                           \n";
    $stSql .= "                     , especialidade.cod_cargo as cod_funcao                                                                         \n";
    $stSql .= "                     , especialidade.cod_especialidade as cod_especialidade_funcao                                                   \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                                \n";
    $stSql .= "                     , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                 \n";
    $stSql .= "                               , max(timestamp) as timestamp                                                                         \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_especialidade_funcao                                                      \n";
    $stSql .= "                        GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao  \n";
    $stSql .= "                     , pessoal.especialidade                                                                                         \n";
    $stSql .= "                 WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp    \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                  \n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_especialidade_cargo.cod_contrato                                                            \n";
    $stSql .= "                     , especialidade.cod_cargo                                                                                       \n";
    $stSql .= "                     , especialidade.cod_especialidade                                                                               \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_cargo                                                                 \n";
    $stSql .= "                     , pessoal.especialidade                                                                                         \n";
    $stSql .= "                 WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_cargo \n";
    $stSql .= "            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                  \n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                             \n";
    $stSql .= "                     , contrato_servidor_orgao.cod_orgao                                \n";
    $stSql .= "                     , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01')  as descricao_lotacao  \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_orgao                                   \n";
    $stSql .= "                     , (  SELECT cod_contrato                                            \n";
    $stSql .= "                               , max(timestamp) as timestamp                             \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_orgao                         \n";
    $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_orgao            \n";
    $stSql .= "                     , organograma.orgao                                                 \n";
    $stSql .= "                 WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp \n";
    $stSql .= "                   AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao) as contrato_servidor_orgao    \n";
    $stSql .= "            ON contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato     \n";

    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_local                                      \n";
    $stSql .= "                , contrato_servidor_local.cod_contrato                                   \n";
    $stSql .= "                , local.descricao as descricao_local                                     \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                        \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                              \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                 \n";
    $stSql .= "                , organograma.local                                                      \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                    \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp \n";
    $stSql .= "              AND contrato_servidor_local.cod_local    = local.cod_local) as contrato_servidor_local           \n";
    $stSql .= "              ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato   \n";

    $stSql .= "     , pessoal.contrato                                                                  \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                \n";
    $stSql .= "     , pessoal.servidor                                                                  \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                              \n";
    $stSql .= "     , sw_cgm                                                                            \n";
    $stSql .= "     , folhapagamento.complementar                                                       \n";
    $stSql .= " WHERE evento_complementar_calculado.cod_registro            = registro_evento_complementar.cod_registro \n";
    $stSql .= "   AND evento_complementar_calculado.timestamp_registro      = registro_evento_complementar.timestamp    \n";
    $stSql .= "   AND evento_complementar_calculado.cod_evento              = registro_evento_complementar.cod_evento   \n";
    $stSql .= "   AND evento_complementar_calculado.cod_configuracao        = registro_evento_complementar.cod_configuracao \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento               = evento.cod_evento                \n";
    $stSql .= "   AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao \n";
    $stSql .= "   AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar \n";
    $stSql .= "   AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato \n";
    $stSql .= "   AND contrato_servidor_complementar.cod_complementar       = complementar.cod_complementar \n";
    $stSql .= "   AND contrato_servidor_complementar.cod_periodo_movimentacao = complementar.cod_periodo_movimentacao \n";
    $stSql .= "   AND contrato_servidor_complementar.cod_contrato           = contrato_servidor.cod_contrato \n";
    $stSql .= "   AND contrato_servidor.cod_contrato                        = contrato.cod_contrato \n";
    $stSql .= "   AND contrato_servidor.cod_contrato                        = servidor_contrato_servidor.cod_contrato                   \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor               = servidor.cod_servidor                                     \n";
    $stSql .= "   AND servidor.numcgm                                       = sw_cgm_pessoa_fisica.numcgm                               \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm                           = sw_cgm.numcgm                                             \n";
    $stSql .= "   AND evento.cod_evento = sequencia_calculo_evento.cod_evento                          \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia         \n";

    return $stSql;
}

function recuperaRelatorioFolhaAnaliticaOutrasFolhas(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_complementar ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnaliticaOutrasFolhas().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnaliticaOutrasFolhas()
{
    $stSql  = "   SELECT contrato_servidor_complementar.cod_complementar                                                                           \n";
    $stSql .= "        , base_previdencia.valor as valor_base_previdencia                                                                          \n";
    $stSql .= "        , desconto_previdencia.valor as valor_desconto_previdencia                                                                  \n";
    $stSql .= "        , base_irrf.valor as valor_base_irrf                                                                                        \n";
    $stSql .= "        , desconto_irrf.valor as valor_desconto_irrf                                                                                \n";
    $stSql .= "        , base_fgts.valor as valor_base_fgts                                                                                        \n";
    $stSql .= "        , informativo_fgts.valor as valor_recolhido_fgts                                                                            \n";
    $stSql .= "        , informativo2_fgts.valor as valor_contribuicao_social                                                                      \n";
    $stSql .= "     FROM folhapagamento.complementar                                                                                               \n";
    $stSql .= "        , folhapagamento.complementar_situacao                                                                                      \n";
    $stSql .= "        , (  SELECT cod_complementar                                                                                                \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                     \n";
    $stSql .= "               FROM folhapagamento.complementar_situacao                                                                            \n";
    $stSql .= "           GROUP BY cod_complementar) as max_complementar_situacao                                                                  \n";
    $stSql .= "        , folhapagamento.contrato_servidor_complementar                                                                             \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "             FROM folhapagamento.registro_evento_complementar                                                                       \n";
    $stSql .= "                , folhapagamento.ultimo_registro_evento_complementar                                                                \n";
    $stSql .= "                , folhapagamento.evento_complementar_calculado                                                                      \n";
    $stSql .= "                , folhapagamento.fgts_evento                                                                                        \n";
    $stSql .= "                , (SELECT cod_fgts                                                                                                  \n";
    $stSql .= "                        ,  max(timestamp) as timestamp                                                                              \n";
    $stSql .= "                     FROM folhapagamento.fgts_evento                                                                                \n";
    $stSql .= "                   GROUP BY cod_fgts) as max_fgts_evento                                                                            \n";
    $stSql .= "            WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                      \n";
    $stSql .= "              AND registro_evento_complementar.timestamp    = ultimo_registro_evento_complementar.timestamp                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento   = ultimo_registro_evento_complementar.cod_evento                        \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao              \n";
    $stSql .= "              AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                            \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                    \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = fgts_evento.cod_evento                                                  \n";
    $stSql .= "              AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                 \n";
    $stSql .= "              AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                \n";
    $stSql .= "              AND fgts_evento.cod_tipo = 1) informativo2_fgts                                                                       \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = informativo2_fgts.cod_periodo_movimentacao                      \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = informativo2_fgts.cod_complementar                              \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = informativo2_fgts.cod_contrato                                  \n";
    $stSql .= "                                                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "             FROM folhapagamento.registro_evento_complementar                                                                       \n";
    $stSql .= "                , folhapagamento.ultimo_registro_evento_complementar                                                                \n";
    $stSql .= "                , folhapagamento.evento_complementar_calculado                                                                      \n";
    $stSql .= "                , folhapagamento.fgts_evento                                                                                        \n";
    $stSql .= "                , (SELECT cod_fgts                                                                                                  \n";
    $stSql .= "                        ,  max(timestamp) as timestamp                                                                              \n";
    $stSql .= "                     FROM folhapagamento.fgts_evento                                                                                \n";
    $stSql .= "                   GROUP BY cod_fgts) as max_fgts_evento                                                                            \n";
    $stSql .= "            WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                      \n";
    $stSql .= "              AND registro_evento_complementar.timestamp    = ultimo_registro_evento_complementar.timestamp                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento   = ultimo_registro_evento_complementar.cod_evento                        \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao              \n";
    $stSql .= "              AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                            \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                    \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = fgts_evento.cod_evento                                                  \n";
    $stSql .= "              AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                 \n";
    $stSql .= "              AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                \n";
    $stSql .= "              AND fgts_evento.cod_tipo = 2) informativo_fgts                                                                        \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = informativo_fgts.cod_periodo_movimentacao                       \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = informativo_fgts.cod_complementar                               \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = informativo_fgts.cod_contrato                                   \n";
    $stSql .= "                                                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "             FROM folhapagamento.registro_evento_complementar                                                                       \n";
    $stSql .= "                , folhapagamento.ultimo_registro_evento_complementar                                                                \n";
    $stSql .= "                , folhapagamento.evento_complementar_calculado                                                                      \n";
    $stSql .= "                , folhapagamento.fgts_evento                                                                                        \n";
    $stSql .= "                , (SELECT cod_fgts                                                                                                  \n";
    $stSql .= "                        ,  max(timestamp) as timestamp                                                                              \n";
    $stSql .= "                     FROM folhapagamento.fgts_evento                                                                                \n";
    $stSql .= "                   GROUP BY cod_fgts) as max_fgts_evento                                                                            \n";
    $stSql .= "            WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                      \n";
    $stSql .= "              AND registro_evento_complementar.timestamp    = ultimo_registro_evento_complementar.timestamp                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento   = ultimo_registro_evento_complementar.cod_evento                        \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao              \n";
    $stSql .= "              AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                            \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                    \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = fgts_evento.cod_evento                                                  \n";
    $stSql .= "              AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                 \n";
    $stSql .= "              AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                \n";
    $stSql .= "              AND fgts_evento.cod_tipo = 3) base_fgts                                                                               \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = base_fgts.cod_periodo_movimentacao                              \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = base_fgts.cod_complementar                                      \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = base_fgts.cod_contrato                                          \n";
    $stSql .= "                                                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "             FROM folhapagamento.registro_evento_complementar                                                                       \n";
    $stSql .= "                , folhapagamento.ultimo_registro_evento_complementar                                                                \n";
    $stSql .= "                , folhapagamento.evento_complementar_calculado                                                                      \n";
    $stSql .= "                , folhapagamento.tabela_irrf_evento                                                                                 \n";
    $stSql .= "                , (SELECT cod_tabela                                                                                                \n";
    $stSql .= "                        ,  max(timestamp) as timestamp                                                                              \n";
    $stSql .= "                     FROM folhapagamento.tabela_irrf_evento                                                                         \n";
    $stSql .= "                   GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                   \n";
    $stSql .= "            WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                      \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                            \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                          \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao              \n";
    $stSql .= "              AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                            \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                    \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = tabela_irrf_evento.cod_evento                                           \n";
    $stSql .= "              AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                 \n";
    $stSql .= "              AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                   \n";
    $stSql .= "              AND tabela_irrf_evento.cod_tipo = 3) as desconto_irrf                                                                 \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = desconto_irrf.cod_periodo_movimentacao                          \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = desconto_irrf.cod_complementar                                  \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = desconto_irrf.cod_contrato                                      \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "             FROM folhapagamento.registro_evento_complementar                                                                       \n";
    $stSql .= "                , folhapagamento.ultimo_registro_evento_complementar                                                                \n";
    $stSql .= "                , folhapagamento.evento_complementar_calculado                                                                      \n";
    $stSql .= "                , folhapagamento.tabela_irrf_evento                                                                                 \n";
    $stSql .= "                , (SELECT cod_tabela                                                                                                \n";
    $stSql .= "                        ,  max(timestamp) as timestamp                                                                              \n";
    $stSql .= "                     FROM folhapagamento.tabela_irrf_evento                                                                         \n";
    $stSql .= "                   GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                   \n";
    $stSql .= "            WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                      \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                            \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                          \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao              \n";
    $stSql .= "              AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                            \n";
    $stSql .= "              AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                         \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                \n";
    $stSql .= "              AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                    \n";
    $stSql .= "              AND registro_evento_complementar.cod_evento = tabela_irrf_evento.cod_evento                                           \n";
    $stSql .= "              AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                 \n";
    $stSql .= "              AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                   \n";
    $stSql .= "              AND tabela_irrf_evento.cod_tipo = 7) as base_irrf                                                                     \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = base_irrf.cod_periodo_movimentacao                              \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = base_irrf.cod_complementar                                      \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = base_irrf.cod_contrato                                          \n";
    $stSql .= "                                                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "            FROM folhapagamento.registro_evento_complementar                                                                        \n";
    $stSql .= "               , folhapagamento.ultimo_registro_evento_complementar                                                                 \n";
    $stSql .= "               , folhapagamento.evento_complementar_calculado                                                                       \n";
    $stSql .= "               , folhapagamento.previdencia_evento                                                                                  \n";
    $stSql .= "               , folhapagamento.previdencia_previdencia                                                                             \n";
    $stSql .= "               , (SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                    FROM folhapagamento.previdencia_previdencia                                                                     \n";
    $stSql .= "                  GROUP BY cod_previdencia) as max_previdencia_previdencia                                                          \n";
    $stSql .= "               , folhapagamento.previdencia                                                                                         \n";
    $stSql .= "               , pessoal.contrato_servidor_previdencia                                                                              \n";
    $stSql .= "               , (SELECT cod_contrato                                                                                               \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_previdencia                                                                      \n";
    $stSql .= "                  GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                       \n";
    $stSql .= "           WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                       \n";
    $stSql .= "             AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                             \n";
    $stSql .= "             AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                           \n";
    $stSql .= "             AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao               \n";
    $stSql .= "             AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                             \n";
    $stSql .= "             AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                          \n";
    $stSql .= "             AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                 \n";
    $stSql .= "             AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                     \n";
    $stSql .= "             AND registro_evento_complementar.cod_evento   = previdencia_evento.cod_evento                                          \n";
    $stSql .= "             AND previdencia_evento.cod_previdencia        = previdencia_previdencia.cod_previdencia                                \n";
    $stSql .= "             AND previdencia_evento.timestamp              = previdencia_previdencia.timestamp                                      \n";
    $stSql .= "             AND previdencia_previdencia.cod_previdencia   = max_previdencia_previdencia.cod_previdencia                            \n";
    $stSql .= "             AND previdencia_previdencia.timestamp         = max_previdencia_previdencia.timestamp                                  \n";
    $stSql .= "             AND previdencia_previdencia.cod_previdencia   = previdencia.cod_previdencia                                            \n";
    $stSql .= "             AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                                        \n";
    $stSql .= "             AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                        \n";
    $stSql .= "             AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                              \n";
    $stSql .= "             AND registro_evento_complementar.cod_contrato = contrato_servidor_previdencia.cod_contrato                             \n";
    $stSql .= "             AND previdencia_evento.cod_tipo               = 2) as base_previdencia                                                 \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = base_previdencia.cod_periodo_movimentacao                       \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = base_previdencia.cod_complementar                               \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = base_previdencia.cod_contrato                                   \n";
    $stSql .= "                                                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT evento_complementar_calculado.*                                                                                   \n";
    $stSql .= "                , registro_evento_complementar.cod_periodo_movimentacao                                                             \n";
    $stSql .= "                , registro_evento_complementar.cod_complementar                                                                     \n";
    $stSql .= "                , registro_evento_complementar.cod_contrato                                                                         \n";
    $stSql .= "            FROM folhapagamento.registro_evento_complementar                                                                        \n";
    $stSql .= "               , folhapagamento.ultimo_registro_evento_complementar                                                                 \n";
    $stSql .= "               , folhapagamento.evento_complementar_calculado                                                                       \n";
    $stSql .= "               , folhapagamento.previdencia_evento                                                                                  \n";
    $stSql .= "               , folhapagamento.previdencia_previdencia                                                                             \n";
    $stSql .= "               , (SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                    FROM folhapagamento.previdencia_previdencia                                                                     \n";
    $stSql .= "                  GROUP BY cod_previdencia) as max_previdencia_previdencia                                                          \n";
    $stSql .= "               , folhapagamento.previdencia                                                                                         \n";
    $stSql .= "               , pessoal.contrato_servidor_previdencia                                                                              \n";
    $stSql .= "               , (SELECT cod_contrato                                                                                               \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_previdencia                                                                      \n";
    $stSql .= "                  GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                       \n";
    $stSql .= "           WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                       \n";
    $stSql .= "             AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                             \n";
    $stSql .= "             AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                           \n";
    $stSql .= "             AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao               \n";
    $stSql .= "             AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                             \n";
    $stSql .= "             AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                          \n";
    $stSql .= "             AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                 \n";
    $stSql .= "             AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                     \n";
    $stSql .= "             AND registro_evento_complementar.cod_evento   = previdencia_evento.cod_evento                                          \n";
    $stSql .= "             AND previdencia_evento.cod_previdencia        = previdencia_previdencia.cod_previdencia                                \n";
    $stSql .= "             AND previdencia_evento.timestamp              = previdencia_previdencia.timestamp                                      \n";
    $stSql .= "             AND previdencia_previdencia.cod_previdencia   = max_previdencia_previdencia.cod_previdencia                            \n";
    $stSql .= "             AND previdencia_previdencia.timestamp         = max_previdencia_previdencia.timestamp                                  \n";
    $stSql .= "             AND previdencia_previdencia.cod_previdencia   = previdencia.cod_previdencia                                            \n";
    $stSql .= "             AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                                        \n";
    $stSql .= "             AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                        \n";
    $stSql .= "             AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                              \n";
    $stSql .= "             AND registro_evento_complementar.cod_contrato = contrato_servidor_previdencia.cod_contrato                             \n";
    $stSql .= "             AND previdencia_evento.cod_tipo               = 1) as desconto_previdencia                                             \n";
    $stSql .= "       ON contrato_servidor_complementar.cod_periodo_movimentacao = desconto_previdencia.cod_periodo_movimentacao                   \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar         = desconto_previdencia.cod_complementar                           \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato             = desconto_previdencia.cod_contrato                               \n";
    $stSql .= "    WHERE contrato_servidor_complementar.cod_periodo_movimentacao = complementar.cod_periodo_movimentacao                           \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_complementar = complementar.cod_complementar                                           \n";
    $stSql .= "      AND complementar.cod_periodo_movimentacao = complementar_situacao.cod_periodo_movimentacao                                    \n";
    $stSql .= "      AND complementar.cod_complementar = complementar_situacao.cod_complementar                                                    \n";
    $stSql .= "      AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar                                       \n";
    $stSql .= "      AND complementar_situacao.timestamp = max_complementar_situacao.timestamp                                                     \n";
    $stSql .= "      AND complementar_situacao.situacao = 'f'                                                                                      \n";

    return $stSql;
}

function recuperaRelatorioCustomizavelEventos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioCustomizavelEventos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioCustomizavelEventos()
{
    $stSql  = "   SELECT contrato.registro                                                                                                                                \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                   \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao                                                        \n";
    $stSql .= "        , local.descricao                as desc_local                                                                                                     \n";
    $stSql .= "        , cargo.descricao                as desc_cargo                                                                                                     \n";
    $stSql .= "        , especialidade.descricao        as desc_especialidade                                                                                             \n";
    $stSql .= "        , funcao.descricao               as desc_funcao                                                                                                    \n";
    $stSql .= "        , especialidade_funcao.descricao as desc_especialidade_funcao                                                                                      \n";
    $stSql .= "        , evento.codigo                                                                                                                                    \n";
    $stSql .= "        , evento_complementar_calculado.quantidade                                                                                                         \n";
    $stSql .= "        , evento_complementar_calculado.valor                                                                                                              \n";
    $stSql .= "     FROM folhapagamento.evento                                                                                                   \n";
    $stSql .= "        , folhapagamento.registro_evento_complementar                                                                             \n";
    $stSql .= "        , folhapagamento.ultimo_registro_evento_complementar                                                                      \n";
    $stSql .= "        , folhapagamento.evento_complementar_calculado                                                                            \n";
    $stSql .= "        , folhapagamento.contrato_servidor_complementar                                                                           \n";
    $stSql .= "        , pessoal.contrato_servidor                                                                                               \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                                                                                                     \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                        \n";
    $stSql .= "                , descricao                                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                                                          \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                   \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                    \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                                                \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                   \n";
    $stSql .= "                , organograma.orgao                                                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_orgao = orgao.cod_orgao                                                                                      \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                          \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as orgao                                                      \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = orgao.cod_contrato                                                                                              \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                                                                     \n";
    $stSql .= "                , contrato_servidor_local.cod_local                                                                                                        \n";
    $stSql .= "                , descricao                                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                                          \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                   \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                    \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                                \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                   \n";
    $stSql .= "                , organograma.local                                                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_local = local.cod_local                                                                                      \n";
    $stSql .= "              AND contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                          \n";
    $stSql .= "              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as local                                                      \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = local.cod_contrato                                                                                              \n";
    $stSql .= "LEFT JOIN pessoal.cargo                                                                                                                                    \n";
    $stSql .= "       ON contrato_servidor.cod_cargo = cargo.cod_cargo                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT cod_contrato                                                                                                                             \n";
    $stSql .= "                , especialidade.cod_especialidade                                                                                                          \n";
    $stSql .= "                , descricao                                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_cargo                                                                                            \n";
    $stSql .= "                , pessoal.especialidade                                                                                                                    \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as especialidade                              \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = especialidade.cod_contrato                                                                                      \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_funcao.cod_contrato                                                                                                    \n";
    $stSql .= "                , contrato_servidor_funcao.cod_cargo as cod_funcao                                                                                         \n";
    $stSql .= "                , descricao                                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_funcao                                                                                                         \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                   \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                    \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_funcao                                                                                               \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                  \n";
    $stSql .= "                , pessoal.cargo                                                                                                                            \n";
    $stSql .= "            WHERE contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                                                                     \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                        \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp) as funcao                                                \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = funcao.cod_contrato                                                                                             \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                      \n";
    $stSql .= "                , contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao                                                     \n";
    $stSql .= "                , descricao                                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                           \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                   \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                    \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                                                 \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                    \n";
    $stSql .= "                , pessoal.especialidade                                                                                                                    \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade                                               \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                            \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp) as especialidade_funcao      \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = especialidade_funcao.cod_contrato                                                                               \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                    \n";
    $stSql .= "                , contrato_servidor_padrao.cod_padrao                                                                                                      \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                                                         \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                   \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                    \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                                               \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                                  \n";
    $stSql .= "            WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                        \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp) as padrao                                                \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = padrao.cod_contrato                                                                                             \n";
    $stSql .= "                                                                                                                                                           \n";
    $stSql .= "        , pessoal.contrato                                                                                                                                 \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                                                                               \n";
    $stSql .= "        , pessoal.servidor                                                                                                                                 \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                             \n";
    $stSql .= "        , sw_cgm                                                                                                                                           \n";
    $stSql .= "    WHERE evento.cod_evento = registro_evento_complementar.cod_evento                                                                                      \n";
    $stSql .= "      AND registro_evento_complementar.cod_evento =  ultimo_registro_evento_complementar.cod_evento                                                        \n";
    $stSql .= "      AND registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                                                     \n";
    $stSql .= "      AND registro_evento_complementar.timestamp   = ultimo_registro_evento_complementar.timestamp                                                         \n";
    $stSql .= "      AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao                                             \n";
    $stSql .= "      AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                                               \n";
    $stSql .= "      AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                                                           \n";
    $stSql .= "      AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                                                        \n";
    $stSql .= "      AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                                                   \n";
    $stSql .= "      AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao                                  \n";
    $stSql .= "      AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar                                          \n";
    $stSql .= "      AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato                                              \n";
    $stSql .= "      AND contrato_servidor_complementar.cod_contrato = contrato_servidor.cod_contrato                                                                     \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = contrato.cod_contrato                                                                                           \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                         \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                    \n";
    $stSql .= "      AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                                      \n";

    return $stSql;
}

function recuperaEventoComplementarCalculadoParaRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY registro_evento_complementar.cod_evento ";
    $stSql  = $this->montaRecuperaEventoComplementarCalculadoParaRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoComplementarCalculadoParaRelatorio()
{
    $stSql  = "SELECT evento_complementar_calculado.*                                                                       \n";
    $stSql .= "     , evento.descricao                                                                                      \n";
    $stSql .= "     , evento.codigo                                                                                         \n";
    $stSql .= "     , evento.natureza                                                                                       \n";
    $stSql .= "     , sequencia_calculo.sequencia                                                                           \n";
    $stSql .= "     , registro_evento_complementar.cod_complementar                                                         \n";
    $stSql .= "  FROM folhapagamento.evento_complementar_calculado                                                          \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar                                                           \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_complementar                                                    \n";
    $stSql .= "     , folhapagamento.evento                                                                                 \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                                               \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                                                      \n";
    $stSql .= " WHERE registro_evento_complementar.cod_registro     = ultimo_registro_evento_complementar.cod_registro      \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento       = ultimo_registro_evento_complementar.cod_evento        \n";
    $stSql .= "   AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao  \n";
    $stSql .= "   AND registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp         \n";
    $stSql .= "   AND registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro            \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento              \n";
    $stSql .= "   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao        \n";
    $stSql .= "   AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro      \n";
    $stSql .= "   AND evento_complementar_calculado.cod_evento = evento.cod_evento                                          \n";
    $stSql .= "   AND evento_complementar_calculado.cod_evento = sequencia_calculo_evento.cod_evento                        \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                              \n";

    return $stSql;
}

function recuperaEventosBaseDescontoRelatorioFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql  = $this->montaRecuperaEventosBaseDescontoRelatorioFichaFinanceira().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosBaseDescontoRelatorioFichaFinanceira()
{
    $stSql  = "SELECT registro                                                                                                                                     \n";
    $stSql .= "     , codigo                                                                                                                                       \n";
    $stSql .= "     , cod_complementar                                                                                                                             \n";
    $stSql .= "     , descricao                                                                                                                                    \n";
    $stSql .= "     , valor                                                                                                                                        \n";
    $stSql .= "  FROM (SELECT *                                                                                                                                    \n";
    $stSql .= "          FROM (SELECT contrato.registro                                                                                                            \n";
    $stSql .= "                     , evento.codigo                                                                                                                \n";
    $stSql .= "                     , registro_evento_complementar.cod_complementar                                                                                \n";
    $stSql .= "                     , trim(evento.descricao) as descricao                                                                                          \n";
    $stSql .= "                     , evento_complementar_calculado.valor as valor                                                                                 \n";
    $stSql .= "                     , numcgm                                                                                                                       \n";
    $stSql .= "                     , natureza                                                                                                                     \n";
    $stSql .= "                     , cod_periodo_movimentacao                                                                                                     \n";
    $stSql .= "                  FROM folhapagamento.evento_complementar_calculado                                                                                 \n";
    $stSql .= "                     , folhapagamento.registro_evento_complementar                                                                                  \n";
    $stSql .= "                     , folhapagamento.ultimo_registro_evento_complementar                                                                           \n";
    $stSql .= "                     , folhapagamento.evento                                                                                                        \n";
    $stSql .= "                     , (SELECT servidor_contrato_servidor.cod_contrato                                                                              \n";
    $stSql .= "                             , servidor.numcgm                                                                                                      \n";
    $stSql .= "                          FROM pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "                             , pessoal.servidor                                                                                                     \n";
    $stSql .= "                         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                      \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT contrato_pensionista.cod_contrato                                                                                    \n";
    $stSql .= "                             , pensionista.numcgm                                                                                                   \n";
    $stSql .= "                          FROM pessoal.contrato_pensionista                                                                                         \n";
    $stSql .= "                             , pessoal.pensionista                                                                                                  \n";
    $stSql .= "                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                   \n";
    $stSql .= "                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                            \n";
    $stSql .= "                     , pessoal.contrato                                                                                                             \n";
    $stSql .= "                     , (SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 7                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 4                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 5                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 6                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 3                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                 \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                               \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                       \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                          \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                     \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 2                                                                                 \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                 \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                               \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                       \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                          \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                     \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 1) as eventos_base                                                                \n";
    $stSql .= "                 WHERE registro_evento_complementar.cod_evento     = ultimo_registro_evento_complementar.cod_evento                                 \n";
    $stSql .= "                   AND registro_evento_complementar.cod_registro   = ultimo_registro_evento_complementar.cod_registro                               \n";
    $stSql .= "                   AND registro_evento_complementar.timestamp      = ultimo_registro_evento_complementar.timestamp                                  \n";
    $stSql .= "                   AND registro_evento_complementar.cod_evento     = evento_complementar_calculado.cod_evento                                       \n";
    $stSql .= "                   AND registro_evento_complementar.cod_registro   = evento_complementar_calculado.cod_registro                                     \n";
    $stSql .= "                   AND registro_evento_complementar.timestamp      = evento_complementar_calculado.timestamp_registro                               \n";
    $stSql .= "                   AND registro_evento_complementar.cod_evento     = eventos_base.cod_evento                                                        \n";
    $stSql .= "                   AND registro_evento_complementar.cod_evento     = evento.cod_evento                                                              \n";
    $stSql .= "                   AND registro_evento_complementar.cod_contrato = servidor.cod_contrato                                                            \n";
    $stSql .= "                   AND servidor.cod_contrato = contrato.cod_contrato) as complementar                                                               \n";
    $stSql .= "        UNION                                                                                                                                       \n";
    $stSql .= "        SELECT *                                                                                                                                    \n";
    $stSql .= "          FROM (SELECT contrato.registro                                                                                                            \n";
    $stSql .= "                     , evento.codigo                                                                                                                \n";
    $stSql .= "                     , -1 as cod_complementar                                                                                                       \n";
    $stSql .= "                     , trim(evento.descricao) as descricao                                                                                          \n";
    $stSql .= "                     , evento_ferias_calculado.valor as valor                                                                                       \n";
    $stSql .= "                     , numcgm                                                                                                                       \n";
    $stSql .= "                     , natureza                                                                                                                     \n";
    $stSql .= "                     , cod_periodo_movimentacao                                                                                                     \n";
    $stSql .= "                  FROM folhapagamento.evento_ferias_calculado                                                                                       \n";
    $stSql .= "                     , folhapagamento.registro_evento_ferias                                                                                        \n";
    $stSql .= "                     , folhapagamento.ultimo_registro_evento_ferias                                                                                 \n";
    $stSql .= "                     , folhapagamento.evento                                                                                                        \n";
    $stSql .= "                     , (SELECT servidor_contrato_servidor.cod_contrato                                                                              \n";
    $stSql .= "                             , servidor.numcgm                                                                                                      \n";
    $stSql .= "                          FROM pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "                             , pessoal.servidor                                                                                                     \n";
    $stSql .= "                         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                      \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT contrato_pensionista.cod_contrato                                                                                    \n";
    $stSql .= "                             , pensionista.numcgm                                                                                                   \n";
    $stSql .= "                          FROM pessoal.contrato_pensionista                                                                                         \n";
    $stSql .= "                             , pessoal.pensionista                                                                                                  \n";
    $stSql .= "                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                   \n";
    $stSql .= "                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                            \n";
    $stSql .= "                     , pessoal.contrato                                                                                                             \n";
    $stSql .= "                     , (SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 7                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 4                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 5                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 6                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 3                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                 \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                               \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                       \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                          \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                     \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 2                                                                                 \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                 \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                               \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                       \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                          \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                     \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 1) as eventos_base                                                                \n";
    $stSql .= "                 WHERE registro_evento_ferias.cod_evento     = ultimo_registro_evento_ferias.cod_evento                                             \n";
    $stSql .= "                   AND registro_evento_ferias.cod_registro   = ultimo_registro_evento_ferias.cod_registro                                           \n";
    $stSql .= "                   AND registro_evento_ferias.timestamp      = ultimo_registro_evento_ferias.timestamp                                              \n";
    $stSql .= "                   AND registro_evento_ferias.desdobramento      = ultimo_registro_evento_ferias.desdobramento                                      \n";
    $stSql .= "                   AND registro_evento_ferias.cod_evento     = evento_ferias_calculado.cod_evento                                                   \n";
    $stSql .= "                   AND registro_evento_ferias.cod_registro   = evento_ferias_calculado.cod_registro                                                 \n";
    $stSql .= "                   AND registro_evento_ferias.timestamp      = evento_ferias_calculado.timestamp_registro                                           \n";
    $stSql .= "                   AND registro_evento_ferias.desdobramento      = evento_ferias_calculado.desdobramento                                            \n";
    $stSql .= "                   AND registro_evento_ferias.cod_evento     = eventos_base.cod_evento                                                              \n";
    $stSql .= "                   AND registro_evento_ferias.cod_evento     = evento.cod_evento                                                                    \n";
    $stSql .= "                   AND registro_evento_ferias.cod_contrato = servidor.cod_contrato                                                                  \n";
    $stSql .= "                   AND servidor.cod_contrato = contrato.cod_contrato) as ferias                                                                     \n";
    $stSql .= "        UNION                                                                                                                                       \n";
    $stSql .= "        SELECT *                                                                                                                                    \n";
    $stSql .= "          FROM (SELECT contrato.registro                                                                                                            \n";
    $stSql .= "                     , evento.codigo                                                                                                                \n";
    $stSql .= "                     , 0 as cod_complementar                                                                                                        \n";
    $stSql .= "                     , trim(evento.descricao) as descricao                                                                                          \n";
    $stSql .= "                     , evento_calculado.valor as valor                                                                                              \n";
    $stSql .= "                     , numcgm                                                                                                                       \n";
    $stSql .= "                     , natureza                                                                                                                     \n";
    $stSql .= "                     , cod_periodo_movimentacao                                                                                                     \n";
    $stSql .= "                  FROM folhapagamento.evento_calculado                                                                                              \n";
    $stSql .= "                     , folhapagamento.registro_evento                                                                                               \n";
    $stSql .= "                     , folhapagamento.ultimo_registro_evento                                                                                        \n";
    $stSql .= "                     , folhapagamento.registro_evento_periodo                                                                                       \n";
    $stSql .= "                     , folhapagamento.evento                                                                                                        \n";
    $stSql .= "                     , (SELECT servidor_contrato_servidor.cod_contrato                                                                              \n";
    $stSql .= "                             , servidor.numcgm                                                                                                      \n";
    $stSql .= "                          FROM pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "                             , pessoal.servidor                                                                                                     \n";
    $stSql .= "                         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                      \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT contrato_pensionista.cod_contrato                                                                                    \n";
    $stSql .= "                             , pensionista.numcgm                                                                                                   \n";
    $stSql .= "                          FROM pessoal.contrato_pensionista                                                                                         \n";
    $stSql .= "                             , pessoal.pensionista                                                                                                  \n";
    $stSql .= "                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                   \n";
    $stSql .= "                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                            \n";
    $stSql .= "                     , pessoal.contrato                                                                                                             \n";
    $stSql .= "                     , (SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 7                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 4                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 5                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 6                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                                 \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                      \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                      \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                              \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                    \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                     \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 3                                                                                        \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                 \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                               \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                       \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                          \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                     \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 2                                                                                 \n";
    $stSql .= "                         UNION                                                                                                                      \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                        \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                    \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                            \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                                \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                          \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                 \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                               \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                       \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                          \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                     \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 1) as eventos_base                                                                \n";
    $stSql .= "                 WHERE registro_evento.cod_evento     = ultimo_registro_evento.cod_evento                                                           \n";
    $stSql .= "                   AND registro_evento.cod_registro   = ultimo_registro_evento.cod_registro                                                         \n";
    $stSql .= "                   AND registro_evento.timestamp      = ultimo_registro_evento.timestamp                                                            \n";
    $stSql .= "                   AND registro_evento.cod_evento     = evento_calculado.cod_evento                                                                 \n";
    $stSql .= "                   AND registro_evento.cod_registro   = evento_calculado.cod_registro                                                               \n";
    $stSql .= "                   AND registro_evento.timestamp      = evento_calculado.timestamp_registro                                                         \n";
    $stSql .= "                   AND registro_evento.cod_registro   = registro_evento_periodo.cod_registro                                                        \n";
    $stSql .= "                   AND registro_evento.cod_evento     = eventos_base.cod_evento                                                                     \n";
    $stSql .= "                   AND registro_evento.cod_evento     = evento.cod_evento                                                                           \n";
    $stSql .= "                   AND registro_evento_periodo.cod_contrato = servidor.cod_contrato                                                                 \n";
    $stSql .= "                    AND EXISTS (SELECT *                                                                                                            \n";
    $stSql .= "                                  FROM folhapagamento.folha_situacao                                                                                \n";
    $stSql .= "                                    , (SELECT cod_periodo_movimentacao                                                                              \n";
    $stSql .= "                                            , max(timestamp) as timestamp                                                                           \n";
    $stSql .= "                                         FROM folhapagamento.folha_situacao                                                                         \n";
    $stSql .= "                                       GROUP BY cod_periodo_movimentacao) as max_folha_situacao                                                     \n";
    $stSql .= "                                WHERE folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao                         \n";
    $stSql .= "                                  AND folha_situacao.timestamp = max_folha_situacao.timestamp                                                       \n";
    $stSql .= "                                  AND folha_situacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao                    \n";
    $stSql .= "                                  AND folha_situacao.situacao = 'f')                                                                                \n";
    $stSql .= "                   AND servidor.cod_contrato = contrato.cod_contrato) as salario) as eventos                                                        \n";

    return $stSql;
}

function recuperaEventosCalculados(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaEventosCalculados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosCalculados()
{
    $stSql .= "SELECT evento_complementar_calculado.*                                                                          \n";
    $stSql .= "     , ( CASE WHEN evento_complementar_calculado.desdobramento IS NOT NULL                                      \n";
    $stSql .= "                 THEN evento.descricao||' '||getDesdobramentoFolha(evento_complementar_calculado.cod_configuracao,evento_complementar_calculado.desdobramento,'".Sessao::getEntidade()."') \n";
    $stSql .= "                 ELSE evento.descricao                                                                          \n";
    $stSql .= "         END ) AS descricao                                                                                     \n";
    $stSql .= "     , evento.descricao as nom_evento                                                                           \n";
    $stSql .= "     , evento.codigo                                                                                            \n";
    $stSql .= "     , evento.natureza                                                                                          \n";
    $stSql .= "     , getDesdobramentoFolha(evento_complementar_calculado.cod_configuracao,evento_complementar_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto \n";
    $stSql .= "  FROM folhapagamento.registro_evento_complementar                                                              \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_complementar                                                       \n";
    $stSql .= "     , folhapagamento.evento_complementar_calculado                                                             \n";
    $stSql .= "     , folhapagamento.evento                                                                                    \n";
    $stSql .= " WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro             \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento   = ultimo_registro_evento_complementar.cod_evento               \n";
    $stSql .= "   AND registro_evento_complementar.timestamp    = ultimo_registro_evento_complementar.timestamp                \n";
    $stSql .= "   AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao     \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro            \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.cod_evento   = evento_complementar_calculado.cod_evento              \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.timestamp    = evento_complementar_calculado.timestamp_registro      \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao    \n";
    $stSql .= "   AND evento_complementar_calculado.cod_evento = evento.cod_evento                                             \n";

    return $stSql;
}

function recuperaEventosCalculadosRais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEventosCalculadosRais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEventosCalculadosRais()
{
    $stSql  = "SELECT sum(evento_complementar_calculado.valor) as valor                                                     \n";
    $stSql .= "  FROM folhapagamento.registro_evento_complementar                          \n";
    $stSql .= "     , folhapagamento.evento_complementar_calculado                         \n";
    $stSql .= "     , folhapagamento.periodo_movimentacao                            \n";
    $stSql .= " WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro               \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                   \n";
    $stSql .= "   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao             \n";
    $stSql .= "   AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro            \n";
    $stSql .= "   AND registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao  \n";

    return $stSql;
}

function recuperaValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaValoresAcumuladosCalculo()
{
    $stSql = "select * from recuperaValoresAcumuladosCalculoComplementar(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    )";

    return $stSql;
}

function recuperaRotuloValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaRotuloValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaRotuloValoresAcumuladosCalculo()
{
    $stSql = "select recuperaRotuloValoresAcumuladosCalculoComplementar(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    ) as rotulo";

    return $stSql;
}

function recuperaContratosCalculados(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaContratosCalculados", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaContratosCalculados()
{
    $stSql  = "    SELECT contrato.*\n";
    $stSql .= "         , servidor.numcgm\n";
    $stSql .= "         , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm\n";
    $stSql .= "      FROM pessoal.contrato\n";
    $stSql .= "INNER JOIN (    SELECT servidor_contrato_servidor.cod_contrato\n";
    $stSql .= "                     , servidor.numcgm\n";
    $stSql .= "                  FROM pessoal.servidor_contrato_servidor\n";
    $stSql .= "            INNER JOIN pessoal.servidor\n";
    $stSql .= "                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor\n";
    $stSql .= "                 UNION \n";
    $stSql .= "                SELECT contrato_pensionista.cod_contrato\n";
    $stSql .= "                     , pensionista.numcgm\n";
    $stSql .= "                 FROM pessoal.contrato_pensionista\n";
    $stSql .= "           INNER JOIN pessoal.pensionista\n";
    $stSql .= "                   ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista\n";
    $stSql .= "                  AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor\n";
    $stSql .= "        ON servidor.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "INNER JOIN folhapagamento.registro_evento_complementar\n";
    $stSql .= "        ON registro_evento_complementar.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "       AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
    $stSql .= "       AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."\n";
    if (trim($this->getDado("stCodContratos")) != "") {
        $stSql .= "       AND registro_evento_complementar.cod_contrato IN (".$this->getDado("stCodContratos").")\n";
    }
    $stSql .= "INNER JOIN folhapagamento.evento_complementar_calculado\n";
    $stSql .= "        ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro\n";
    $stSql .= "       AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento\n";
    $stSql .= "       AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao\n";
    $stSql .= "       AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp\n";
    $stSql .= "  GROUP BY contrato.registro\n";
    $stSql .= "         , contrato.cod_contrato\n";
    $stSql .= "         , servidor.numcgm\n";

    return $stSql;
}

}
