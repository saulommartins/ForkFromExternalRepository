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
    * Classe de mapeamento da tabela folhapagamento.registro_evento_decimo
    * Data de Criação: 06/09/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.21

    $Id: TFolhaPagamentoRegistroEventoDecimo.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.registro_evento_decimo
  * Data de Criação: 06/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEventoDecimo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEventoDecimo()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.registro_evento_decimo");

    $this->setCampoCod('cod_registro');
    $this->setComplementoChave('');

    $this->AddCampo('cod_registro'              ,'sequence',true,'',true,false);
    $this->AddCampo('timestamp'                 ,'timestamp_now',true,'',true,false);
    $this->AddCampo('desdobramento'             ,'char',true,'1',true,false);
    $this->AddCampo('cod_evento'                ,'integer',true,'',true,'TFolhaPagamentoEvento');
    $this->AddCampo('cod_contrato'              ,'integer',true,'',false,'TFolhaPagamentoContratoServidorPeriodo');
    $this->AddCampo('cod_periodo_movimentacao'  ,'integer',true,'',false,'TFolhaPagamentoContratoServidorPeriodo');
    $this->AddCampo('valor'                     ,'numeric',false,'15,2',false,false);
    $this->AddCampo('quantidade'                ,'numeric',false,'15,2',false,false);
    $this->AddCampo('automatico'                ,'boolean',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT registro_evento_Decimo.cod_registro                                                           \n";
    $stSql .= "     , registro_evento_Decimo.timestamp                                                              \n";
    $stSql .= "     , registro_evento_Decimo.cod_evento                                                             \n";
    $stSql .= "     , registro_evento_Decimo.cod_contrato                                                           \n";
    $stSql .= "     , registro_evento_Decimo.cod_periodo_movimentacao                                               \n";
    $stSql .= "     , registro_evento_Decimo.valor                                                                  \n";
    $stSql .= "     , registro_evento_Decimo.quantidade                                                             \n";
    $stSql .= "     , registro_evento_Decimo.desdobramento                                                          \n";
    $stSql .= "     , getDesdobramentoDecimo(registro_evento_Decimo.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto           \n";
    $stSql .= "     , CASE WHEN registro_evento_Decimo.automatico = 't' THEN 'Sim'                                  \n";
    $stSql .= "       ELSE 'Não' END as automatico                                                                  \n";
    $stSql .= "     , evento.codigo                                                                                 \n";
    $stSql .= "     , evento.evento_sistema                                                                         \n";
    $stSql .= "     , evento.natureza                                                                               \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                           \n";
    $stSql .= "     , registro_evento_Decimo_parcela.parcela                                                        \n";
    $stSql .= "  FROM folhapagamento.registro_evento_decimo                                                         \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_decimo                                                  \n";
    $stSql .= "LEFT JOIN folhapagamento.registro_evento_Decimo_parcela                                              \n";
    $stSql .= "       ON registro_evento_decimo_parcela.cod_evento = ultimo_registro_evento_decimo.cod_evento       \n";
    $stSql .= "      AND registro_evento_decimo_parcela.cod_registro = ultimo_registro_evento_decimo.cod_registro   \n";
    $stSql .= "      AND registro_evento_decimo_parcela.timestamp = ultimo_registro_evento_decimo.timestamp         \n";
    $stSql .= "      AND registro_evento_decimo_parcela.desdobramento = ultimo_registro_evento_decimo.desdobramento \n";
    $stSql .= "     , folhapagamento.evento                                                                         \n";
    $stSql .= " WHERE registro_evento_decimo.cod_evento = evento.cod_evento                                         \n";
    $stSql .= "   AND registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro              \n";
    $stSql .= "   AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                  \n";
    $stSql .= "   AND registro_evento_decimo.timestamp = ultimo_registro_evento_decimo.timestamp                    \n";
    $stSql .= "   AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento            \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContratosDoFiltro
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContratosDoFiltro(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaContratosDoFiltro().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosDoFiltro()
{
    $stSql .= "   SELECT contrato.*                                                                                                                                                                        \n";
    $stSql .= "        , servidor.numcgm                                                                                                                                                                   \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                                                    \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                                                  \n";
    $stSql .= "        , vw_orgao_nivel.orgao as cod_estrutural                                                                                                                                            \n";
    $stSql .= "        , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "        , servidor.cod_cargo                                                                                                                                                                \n";
    $stSql .= "        , servidor.cod_sub_divisao                                                                                                                                                          \n";
    $stSql .= "        , servidor.cod_especialidade_cargo as cod_especialidade                                                                                                                             \n";
    $stSql .= "     FROM pessoal.contrato                                                                                                                                         \n";
    $stSql .= "        , (SELECT servidor_contrato_servidor.cod_contrato                                                                                                                                   \n";
    $stSql .= "                , servidor.numcgm                                                                                                                                                           \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                                                         \n";
    $stSql .= "                , contrato_servidor.ativo                                                                                                                                                   \n";
    $stSql .= "                , contrato_servidor.cod_cargo                                                                                                                                               \n";
    $stSql .= "                , contrato_servidor.cod_sub_divisao                                                                                                                                         \n";
    $stSql .= "                , contrato_servidor_especialidade_cargo.cod_especialidade as cod_especialidade_cargo                                                                                        \n";
    $stSql .= "                , contrato_servidor_funcao.cod_cargo as cod_funcao                                                                                                                          \n";
    $stSql .= "                , contrato_servidor_especialidade_funcao.cod_especialidade_funcao                                                                                                           \n";
    $stSql .= "                , contrato_servidor_local.cod_local                                                                                                                                         \n";
    $stSql .= "                , contrato_servidor_padrao.cod_padrao                                                                                                                                       \n";
    $stSql .= "             FROM pessoal.servidor_contrato_servidor                                                                                                               \n";
    $stSql .= "                , pessoal.servidor                                                                                                                                 \n";
    $stSql .= "                , pessoal.contrato_servidor_orgao                                                                                                                  \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                                                                                 \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                                    \n";
    $stSql .= "                , pessoal.contrato_servidor_funcao                                                                                                                                          \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_funcao                                                                                                                                \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                                   \n";
    $stSql .= "                , pessoal.contrato_servidor_padrao                                                                                                                                          \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                                                                                \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                                                                   \n";
    $stSql .= "                , pessoal.contrato_servidor                                                                                                                                                 \n";
    $stSql .= "        LEFT JOIN pessoal.contrato_servidor_especialidade_cargo                                                                                                                             \n";
    $stSql .= "               ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                                                       \n";
    $stSql .= "        LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao                                                                              \n";
    $stSql .= "                        , contrato_servidor_especialidade_funcao.cod_contrato                                                                                                               \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                                    \n";
    $stSql .= "                        , (SELECT cod_contrato                                                                                                                                              \n";
    $stSql .= "                                , max(timestamp) as timestamp                                                                                                                               \n";
    $stSql .= "                             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                            \n";
    $stSql .= "                           GROUP BY cod_contrato) max_contrato_servidor_especialidade_funcao                                                                                                \n";
    $stSql .= "                    WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                                     \n";
    $stSql .= "                      AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp) as contrato_servidor_especialidade_funcao                \n";
    $stSql .= "               ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                                      \n";
    $stSql .= "        LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                                                                                                 \n";
    $stSql .= "                        , contrato_servidor_local.cod_contrato                                                                                                                              \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_local                                                                                                                                   \n";
    $stSql .= "                        , (SELECT cod_contrato                                                                                                                                              \n";
    $stSql .= "                                , max(timestamp) as timestamp                                                                                                                               \n";
    $stSql .= "                             FROM pessoal.contrato_servidor_local                                                                                                                           \n";
    $stSql .= "                           GROUP BY cod_contrato) max_contrato_servidor_local                                                                                                               \n";
    $stSql .= "                    WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                                                   \n";
    $stSql .= "                      AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local                                                             \n";
    $stSql .= "               ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                                                     \n";
    $stSql .= "            WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                                           \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                            \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                                           \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                                                                 \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                           \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                                         \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                                                                               \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato                                                                                                  \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_contrato = contrato_servidor_padrao.cod_contrato                                                                                           \n";
    $stSql .= "              AND contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                                                         \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp                                                                                               \n";
    $stSql .= "            UNION                                                                                                                                                                           \n";
    $stSql .= "           SELECT contrato_pensionista.cod_contrato                                                                                                                                         \n";
    $stSql .= "                , pensionista.numcgm                                                                                                                                                        \n";
    $stSql .= "                , contrato_pensionista_orgao.cod_orgao                                                                                                                                      \n";
    $stSql .= "                , false as ativo                                                                                                                                                            \n";
    $stSql .= "                , contrato_servidor.cod_cargo                                                                                                                                               \n";
    $stSql .= "                , contrato_servidor.cod_sub_divisao                                                                                                                                         \n";
    $stSql .= "                , contrato_servidor_especialidade_cargo.cod_especialidade as cod_especialidade_cargo                                                                                        \n";
    $stSql .= "                , 0 as cod_funcao                                                                                                                                                           \n";
    $stSql .= "                , 0 as cod_especialidade_funcao                                                                                                                                             \n";
    $stSql .= "                , 0 as cod_local                                                                                                                                                            \n";
    $stSql .= "                , 0 as cod_padrao                                                                                                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_pensionista                                                                                                                                              \n";
    $stSql .= "                , pessoal.pensionista                                                                                                                                                       \n";
    $stSql .= "                , pessoal.contrato_pensionista_orgao                                                                                                                                        \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                    \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                     \n";
    $stSql .= "                       FROM pessoal.contrato_pensionista_orgao                                                                                                                              \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                                                 \n";
    $stSql .= "                , pessoal.contrato_servidor                                                                                                                                                 \n";
    $stSql .= "        LEFT JOIN pessoal.contrato_servidor_especialidade_cargo                                                                                                                             \n";
    $stSql .= "               ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                                                       \n";
    $stSql .= "            WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                                                        \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                                              \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                                                               \n";
    $stSql .= "              AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                                     \n";
    $stSql .= "              AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp                                                                                           \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato) as servidor                                                                                   \n";
    $stSql .= "        , sw_cgm                                                                                                                                                                            \n";
    $stSql .= "        , organograma.orgao                                                                                                                                                                 \n";
    $stSql .= "        , organograma.orgao_nivel                                                                                                                                                           \n";
    $stSql .= "        , organograma.organograma                                                                                                                                                           \n";
    $stSql .= "        , organograma.nivel                                                                                                                                                                 \n";
    $stSql .= "        , organograma.vw_orgao_nivel                                                                                                                                                        \n";
    $stSql .= "    WHERE contrato.cod_contrato = servidor.cod_contrato                                                                                                                                     \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm.numcgm                                                                                                                                                   \n";
    $stSql .= "      AND servidor.cod_orgao = orgao.cod_orgao                                                                                                                                              \n";
    $stSql .= "      AND orgao.cod_orgao = orgao_nivel.cod_orgao                                                                                                                                           \n";
    $stSql .= "      AND orgao_nivel.cod_nivel = nivel.cod_nivel                                                                                                                                           \n";
    $stSql .= "      AND orgao_nivel.cod_organograma = nivel.cod_organograma                                                                                                                               \n";
    $stSql .= "      AND nivel.cod_organograma = organograma.cod_organograma                                                                                                                               \n";
    $stSql .= "      AND orgao_nivel.cod_nivel = vw_orgao_nivel.nivel                                                                                                                                      \n";
    $stSql .= "      AND organograma.cod_organograma = vw_orgao_nivel.cod_organograma                                                                                                                      \n";
    $stSql .= "      AND orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                                                                                        \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContratosDoFiltro
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContratosComRegistroDeEvento(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperaContratosComRegistroDeEvento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEvento()
{
    $stSql .= "SELECT servidor.*                                                                                                                               \n";
    $stSql .= "  FROM (SELECT servidor_contrato_servidor.cod_contrato                                                                                          \n";
    $stSql .= "             , servidor.numcgm                                                                                                                  \n";
    $stSql .= "             , contrato_servidor_orgao.cod_orgao                                                                                                \n";
    $stSql .= "             , contrato_servidor_local.cod_local                                                                                                \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                                               \n";
    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                                                     \n";
    $stSql .= "                     , contrato_servidor_local.cod_local                                                                                        \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_local                                                                                          \n";
    $stSql .= "                     , (SELECT cod_contrato                                                                                                     \n";
    $stSql .= "                             , max(timestamp) as timestamp                                                                                      \n";
    $stSql .= "                          FROM pessoal.contrato_servidor_local                                                                                  \n";
    $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_local                                                                   \n";
    $stSql .= "                 WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                          \n";
    $stSql .= "                   AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local                    \n";
    $stSql .= "            ON servidor_contrato_servidor.cod_contrato = contrato_servidor_local.cod_local                                                      \n";
    $stSql .= "             , pessoal.servidor                                                                                                                 \n";
    $stSql .= "             , pessoal.contrato_servidor_orgao                                                                                                  \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                                                           \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_orgao                                                                                        \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                           \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                  \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                   \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                  \n";
    $stSql .= "           AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                        \n";
    $stSql .= "         UNION                                                                                                                                  \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                                                \n";
    $stSql .= "             , pensionista.numcgm                                                                                                               \n";
    $stSql .= "             , contrato_pensionista_orgao.cod_orgao                                                                                             \n";
    $stSql .= "             , 0 as cod_local                                                                                                                   \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                                                     \n";
    $stSql .= "             , pessoal.pensionista                                                                                                              \n";
    $stSql .= "             , pessoal.contrato_pensionista_orgao                                                                                               \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                                                           \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_pensionista_orgao                                                                                     \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                        \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                               \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                     \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                      \n";
    $stSql .= "           AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                            \n";
    $stSql .= "           AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as servidor                                     \n";
    $stSql .= "     , pessoal.contrato                                                                                                                         \n";
    $stSql .= " WHERE servidor.cod_contrato IN (SELECT cod_contrato                                                                                            \n";
    $stSql .= "                                   FROM folhapagamento.registro_evento_decimo                                                                   \n";
    $stSql .= "                                  WHERE cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao").")                              \n";
    $stSql .= "   AND servidor.cod_contrato = contrato.cod_contrato                                                                                            \n";

    return $stSql;
}

function recuperaContratosAutomaticos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAutomaticos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAutomaticos()
{
    $stSql .= "  SELECT registro_evento_decimo.cod_contrato \n";
    $stSql .= "       , registro \n";
    $stSql .= "       , sw_cgm.numcgm \n";
    $stSql .= "       , sw_cgm.nom_cgm \n";
    $stSql .= "    FROM folhapagamento.registro_evento_decimo \n";
    $stSql .= "       , folhapagamento.ultimo_registro_evento_decimo \n";
    $stSql .= "           , (SELECT servidor_contrato_servidor.cod_contrato                             \n";
    $stSql .= "                   , servidor.numcgm                                                     \n";
    $stSql .= "                FROM pessoal.servidor_contrato_servidor                                  \n";
    $stSql .= "                   , pessoal.servidor                                                    \n";
    $stSql .= "               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor     \n";
    $stSql .= "               UNION                                                                     \n";
    $stSql .= "              SELECT contrato_pensionista.cod_contrato                                   \n";
    $stSql .= "                   , pensionista.numcgm                                                  \n";
    $stSql .= "                FROM pessoal.contrato_pensionista                                        \n";
    $stSql .= "                   , pessoal.pensionista                                                 \n";
    $stSql .= "               WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista  \n";
    $stSql .= "                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_contrato_servidor\n";
    $stSql .= "       , pessoal.contrato \n";
    $stSql .= "       , sw_cgm \n";
    $stSql .= "   WHERE registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro \n";
    $stSql .= "     AND registro_evento_decimo.cod_evento  = ultimo_registro_evento_decimo.cod_evento \n";
    $stSql .= "     AND registro_evento_decimo.desdobramento  = ultimo_registro_evento_decimo.desdobramento \n";
    $stSql .= "     AND registro_evento_decimo.timestamp  = ultimo_registro_evento_decimo.timestamp \n";
    $stSql .= "     AND registro_evento_decimo.cod_contrato  = servidor_contrato_servidor.cod_contrato \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato \n";
    $stSql .= "     AND servidor_contrato_servidor.numcgm = sw_cgm.numcgm \n";
    $stSql .= "     AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")." \n";
    $stSql .= "     AND sw_cgm.numcgm IN (".$this->getDado("numcgm").") \n";
    $stSql .= "     AND contrato.cod_contrato NOT IN (SELECT cod_contrato FROM pessoal.contrato_servidor_caso_causa) \n";
    $stSql .= "     AND EXISTS (SELECT 1                                                                                            \n";
    $stSql .= "                   FROM folhapagamento.concessao_decimo                                                              \n";
    $stSql .= "                  WHERE concessao_decimo.cod_periodo_movimentacao = registro_evento_decimo.cod_periodo_movimentacao  \n";
    $stSql .= "                    AND concessao_decimo.cod_contrato = registro_evento_decimo.cod_contrato                          \n";
    $stSql .= "                    AND concessao_decimo.folha_salario IS FALSE)                                                     \n";
    $stSql .= "GROUP BY registro_evento_decimo.cod_contrato \n";
    $stSql .= "       , registro \n";
    $stSql .= "       , sw_cgm.numcgm \n";
    $stSql .= "       , sw_cgm.nom_cgm \n";

    return $stSql;
}

function montaRecuperarRegistroContratoDecimo()
{
    $stSql .= "SELECT to_real(registro_evento_decimo.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(registro_evento_decimo.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_decimo.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_decimo.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                 \n";
    $stSql .= "     , ( case when registro_evento_decimo.desdobramento = 'A'  then 'Adiantamento'                                                                     \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'D'  then '13º Salário'                                                                      \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'C'  then 'Complemento 13º Salário'                                                          \n";
    $stSql .= "        end ) as descricao                                                                                                                             \n";
    $stSql .= "     , registro_evento_decimo.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                          \n";
    $stSql .= " FROM folhapagamento.registro_evento_decimo                                                                                  \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_decimo                                                                          \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "     , pessoal.servidor                                                                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                                 \n";
    $stSql .= "WHERE registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro                                                                 \n";
    $stSql .= "  AND registro_evento_decimo.timestamp = ultimo_registro_evento_decimo.timestamp                                                                       \n";
    $stSql .= "  AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                                                                     \n";
    $stSql .= "  AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento                                                               \n";
    $stSql .= "  AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "  AND ultimo_registro_evento_decimo.cod_evento = evento.cod_evento                                                                                     \n";

    return $stSql;
}

function montaRecuperarRegistroContratoDecimoComPensionista($stFiltro,$stOrdem)
{
    $stSql .= "SELECT to_real(registro_evento_decimo.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(registro_evento_decimo.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_decimo.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_decimo.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                 \n";
    $stSql .= "     , ( case when registro_evento_decimo.desdobramento = 'A'  then 'Adiantamento'                                                                     \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'D'  then '13º Salário'                                                                      \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'C'  then 'Complemento 13º Salário'                                                          \n";
    $stSql .= "        end ) as descricao                                                                                                                             \n";
    $stSql .= "     , registro_evento_decimo.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                          \n";
    $stSql .= " FROM folhapagamento.registro_evento_decimo                                                                                  \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_decimo                                                                          \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "     , pessoal.servidor                                                                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                                 \n";
    $stSql .= "WHERE registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro                                                                 \n";
    $stSql .= "  AND registro_evento_decimo.timestamp = ultimo_registro_evento_decimo.timestamp                                                                       \n";
    $stSql .= "  AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                                                                     \n";
    $stSql .= "  AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento                                                               \n";
    $stSql .= "  AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "  AND ultimo_registro_evento_decimo.cod_evento = evento.cod_evento                                                                                     \n";
    $stSql .= $stFiltro." \n";
    $stSql .= " UNION ";
    $stSql .= " SELECT to_real(registro_evento_decimo.valor) as valor                                                                                                 \n";
    $stSql .= "      , to_real(registro_evento_decimo.quantidade) as quantidade                                                                                       \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_decimo.cod_contrato) as matricula                                  \n";
    $stSql .= "      , registro_evento_decimo.cod_contrato                                                                                                            \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                             \n";
    $stSql .= "      , ( case when registro_evento_decimo.desdobramento = 'A'  then 'Adiantamento'                                                                    \n";
    $stSql .= "               when registro_evento_decimo.desdobramento = 'D'  then '13º Salário'                                                                     \n";
    $stSql .= "               when registro_evento_decimo.desdobramento = 'C'  then 'Complemento 13º Salário'                                                         \n";
    $stSql .= "         end ) as descricao                                                                                                                            \n";
    $stSql .= "      , registro_evento_decimo.cod_periodo_movimentacao                                                                                                \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                      \n";
    $stSql .= "  FROM folhapagamento.registro_evento_decimo                                                                                                           \n";
    $stSql .= " INNER JOIN folhapagamento.ultimo_registro_evento_decimo                                                                                               \n";
    $stSql .= "    ON registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro                                                                \n";
    $stSql .= "   AND registro_evento_decimo.timestamp = ultimo_registro_evento_decimo.timestamp                                                                      \n";
    $stSql .= "   AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                                                                    \n";
    $stSql .= "   AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento                                                              \n";
    $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                               \n";
    $stSql .= "    ON registro_evento_decimo.cod_contrato = contrato_pensionista.cod_contrato                                                                         \n";
    $stSql .= " INNER JOIN pessoal.pensionista                                                                                                                        \n";
    $stSql .= "    ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                              \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                    \n";
    $stSql .= " INNER JOIN folhapagamento.evento                                                                                                                      \n";
    $stSql .= "    ON ultimo_registro_evento_decimo.cod_evento = evento.cod_evento                                                                                    \n";
    $stSql .= $stFiltro." \n";
    $stSql .= $stOrdem." \n";

    return $stSql;
}

function recuperaRegistroContratoDecimo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarRegistroContratoDecimo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRegistroContratoDecimoComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarRegistroContratoDecimoComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosDeLotacao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro  = $this->executaRecupera("montaRecuperaContratosDeLotacao", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function montaRecuperaContratosDeLotacao()
{
    $stSql .= "SELECT contrato.*                                                                                               \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                            \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                           \n";
    $stSql .= "  FROM (SELECT contrato_servidor_orgao.cod_contrato                                                             \n";
    $stSql .= "             , contrato_servidor_orgao.cod_orgao                                                                \n";
    $stSql .= "             , numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.contrato_servidor_orgao                                         \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "                       , MAX(timestamp) as timestamp                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_orgao                               \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_orgao                                           \n";
    $stSql .= "             , pessoal.servidor_contrato_servidor                                      \n";
    $stSql .= "             , pessoal.servidor                                                        \n";
    $stSql .= "         WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                  \n";
    $stSql .= "           AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                        \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_contrato = servidor_contrato_servidor.cod_contrato                   \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                  \n";
    $stSql .= "         UNION                                                                                                  \n";
    $stSql .= "        SELECT contrato_pensionista_orgao.cod_contrato                                                          \n";
    $stSql .= "             , contrato_pensionista_orgao.cod_orgao                                                             \n";
    $stSql .= "             , numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.contrato_pensionista_orgao                                      \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "                       , MAX(timestamp) as timestamp                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_pensionista_orgao                            \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                        \n";
    $stSql .= "             , pessoal.contrato_pensionista                                            \n";
    $stSql .= "             , pessoal.pensionista                                                     \n";
    $stSql .= "         WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato            \n";
    $stSql .= "           AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp                  \n";
    $stSql .= "           AND contrato_pensionista_orgao.cod_contrato = contrato_pensionista.cod_contrato                      \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                     \n";
    $stSql .= "           AND contrato_pensionista.cod_pensionista = pensionista.cod_pensionista) as servidor_pensionista      \n";
    $stSql .= "     , pessoal.contrato                                                                \n";
    $stSql .= "     , folhapagamento.registro_evento_decimo                                           \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_decimo                                    \n";
    $stSql .= "     , sw_cgm                                                                                                   \n";
    $stSql .= " WHERE servidor_pensionista.cod_contrato = contrato.cod_contrato                                                \n";
    $stSql .= "   AND contrato.cod_contrato = registro_evento_decimo.cod_contrato                                              \n";
    $stSql .= "   AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                              \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo.cod_registro                         \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento = registro_evento_decimo.cod_evento                             \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.timestamp = registro_evento_decimo.timestamp                               \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.desdobramento = registro_evento_decimo.desdobramento                       \n";
    $stSql .= "   AND contrato.cod_contrato NOT IN (SELECT cod_contrato                                                        \n";
    $stSql .= "                                       FROM pessoal.contrato_servidor_caso_causa )     \n";
    $stSql .= "   AND EXISTS (SELECT 1                                                                                            \n";
    $stSql .= "                 FROM folhapagamento.concessao_decimo                                                              \n";
    $stSql .= "                WHERE concessao_decimo.cod_periodo_movimentacao = registro_evento_decimo.cod_periodo_movimentacao  \n";
    $stSql .= "                  AND concessao_decimo.cod_contrato = registro_evento_decimo.cod_contrato                          \n";
    $stSql .= "                  AND concessao_decimo.folha_salario IS FALSE)                                                     \n";
    $stSql .= "   AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                \n";
    $stSql .= "   AND cod_orgao IN (".$this->getDado("cod_orgao").")                                                           \n";
    $stSql .= "GROUP BY contrato.registro                                                                                      \n";
    $stSql .= "       , contrato.cod_contrato                                                                                  \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                          \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                         \n";

    return $stSql;
}

}
