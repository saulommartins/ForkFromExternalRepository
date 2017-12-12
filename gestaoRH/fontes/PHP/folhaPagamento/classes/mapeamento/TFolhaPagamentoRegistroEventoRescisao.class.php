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
    * Classe de mapeamento da tabela folhapagamento.registro_evento_rescisao
    * Data de Criação: 16/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 10:54:36 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.registro_evento_rescisao
  * Data de Criação: 16/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEventoRescisao extends Persistente
{
/**
    * Mï¿½todo Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEventoRescisao()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.registro_evento_rescisao");

    $this->setCampoCod('cod_registro');
    $this->setComplementoChave('timestamp,desdobramento,cod_evento');

    $this->AddCampo('cod_registro'            ,'sequence'     ,true  ,''      ,true,false);
    $this->AddCampo('timestamp'               ,'timestamp_now',true  ,''      ,true,false);
    $this->AddCampo('desdobramento'           ,'char'         ,true  ,'1'     ,true,false);
    $this->AddCampo('cod_evento'              ,'integer'      ,true  ,''      ,true,'TFolhaPagamentoEvento');
    $this->AddCampo('cod_contrato'            ,'integer'      ,true  ,''      ,false,'TFolhaPagamentoContratoServidorPeriodo');
    $this->AddCampo('cod_periodo_movimentacao','integer'      ,true  ,''      ,false,'TFolhaPagamentoContratoServidorPeriodo');
    $this->AddCampo('valor'                   ,'numeric'      ,false ,'15,2'  ,false,false);
    $this->AddCampo('quantidade'              ,'numeric'      ,false ,'15,2'  ,false,false);
    $this->AddCampo('automatico'              ,'boolean'      ,true  ,''      ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT registro_evento_rescisao.cod_registro                                                           \n";
    $stSql .= "     , registro_evento_rescisao.timestamp                                                              \n";
    $stSql .= "     , registro_evento_rescisao.cod_evento                                                             \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                           \n";
    $stSql .= "     , registro_evento_rescisao.cod_periodo_movimentacao                                               \n";
    $stSql .= "     , registro_evento_rescisao.valor                                                                  \n";
    $stSql .= "     , registro_evento_rescisao.quantidade                                                             \n";
    $stSql .= "     , registro_evento_rescisao.desdobramento                                                          \n";
    $stSql .= "     , getDesdobramentoRescisao(registro_evento_rescisao.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto         \n";
    $stSql .= "     , CASE WHEN registro_evento_rescisao.automatico = 't' THEN 'Sim'                                  \n";
    $stSql .= "       ELSE 'Não' END as automatico                                                                    \n";
    $stSql .= "     , evento.codigo                                                                                   \n";
    $stSql .= "     , evento.evento_sistema                                                                           \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                             \n";
    $stSql .= "     , registro_evento_rescisao_parcela.parcela                                                        \n";
    $stSql .= "  FROM folhapagamento.registro_evento_rescisao                                                         \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_rescisao                                                  \n";
    $stSql .= "LEFT JOIN folhapagamento.registro_evento_rescisao_parcela                                              \n";
    $stSql .= "       ON registro_evento_rescisao_parcela.cod_evento = ultimo_registro_evento_rescisao.cod_evento       \n";
    $stSql .= "      AND registro_evento_rescisao_parcela.cod_registro = ultimo_registro_evento_rescisao.cod_registro   \n";
    $stSql .= "      AND registro_evento_rescisao_parcela.timestamp = ultimo_registro_evento_rescisao.timestamp         \n";
    $stSql .= "      AND registro_evento_rescisao_parcela.desdobramento = ultimo_registro_evento_rescisao.desdobramento \n";
    $stSql .= "     , folhapagamento.evento                                                                         \n";
    $stSql .= " WHERE registro_evento_rescisao.cod_evento = evento.cod_evento                                    \n";
    $stSql .= "   AND registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro              \n";
    $stSql .= "   AND registro_evento_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento                  \n";
    $stSql .= "   AND registro_evento_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp                    \n";
    $stSql .= "   AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento            \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no mï¿½todo montaRecuperaContratosDoFiltro
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
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
    $stSql .= "   SELECT contrato.*                                                                                                                                                 \n";
    $stSql .= "        , servidor.numcgm                                                                                                                                            \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                             \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                           \n";
    $stSql .= "        , vw_orgao_nivel.orgao as cod_estrutural                                                                                                                     \n";
    $stSql .= "        , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao                                                        \n";
    $stSql .= "        , contrato_servidor.cod_cargo                                                                                                                                \n";
    $stSql .= "        , contrato_servidor.cod_sub_divisao                                                                                                                          \n";
    $stSql .= "        , contrato_servidor_especialidade_cargo.cod_especialidade                                                                                                    \n";
    $stSql .= "        , causa_rescisao.descricao as descricao_causa                                                                                                                \n";
    $stSql .= "        , causa_rescisao.num_causa                                                                                                                                   \n";
    $stSql .= "        , to_char(contrato_servidor_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_rescisao                                                                              \n";
    $stSql .= "     FROM pessoal.contrato                                                                                                                                           \n";
    $stSql .= "        , pessoal.contrato_servidor                                                                                                                                  \n";
    $stSql .= "INNER JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                             \n";
    $stSql .= "                , contrato_servidor_padrao.cod_padrao                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                                                                   \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                             \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                              \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                                                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                                            \n";
    $stSql .= "            WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                                  \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao                                           \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_padrao.cod_contrato                                                                                     \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                                                                               \n";
    $stSql .= "                , contrato_servidor_local.cod_local                                                                                                                  \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                                                    \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                             \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                              \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                                          \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                             \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                                    \n";
    $stSql .= "              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local                                              \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                                      \n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_especialidade_cargo                                                                                                              \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                                        \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                                \n";
    $stSql .= "                , contrato_servidor_especialidade_funcao.cod_especialidade                                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                     \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                             \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                              \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                                                           \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                              \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                      \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                       \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                                                                                         \n";
    $stSql .= "        , pessoal.servidor                                                                                                                                           \n";
    $stSql .= "        , sw_cgm                                                                                                                                                     \n";
    $stSql .= "        , pessoal.contrato_servidor_orgao                                                                                                                            \n";
    $stSql .= "        , (  SELECT cod_contrato                                                                                                                                     \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                                                      \n";
    $stSql .= "               FROM pessoal.contrato_servidor_orgao                                                                                                                  \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                     \n";
    $stSql .= "        , organograma.orgao                                                                                                                                          \n";
    $stSql .= "        , organograma.orgao_nivel                                                                                                                                    \n";
    $stSql .= "        , organograma.organograma                                                                                                                                    \n";
    $stSql .= "        , organograma.nivel                                                                                                                                          \n";
    $stSql .= "        , organograma.vw_orgao_nivel                                                                                                                                 \n";
    $stSql .= "        , pessoal.contrato_servidor_funcao                                                                                                                           \n";
    $stSql .= "        , (  SELECT cod_contrato                                                                                                                                     \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                                                      \n";
    $stSql .= "               FROM pessoal.contrato_servidor_funcao                                                                                                                 \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                    \n";
    $stSql .= "        , pessoal.contrato_servidor_caso_causa                                                                                                                       \n";
    $stSql .= "        , pessoal.caso_causa                                                                                                                                         \n";
    $stSql .= "        , pessoal.causa_rescisao                                                                                                                                     \n";
    $stSql .= "    WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                                            \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                            \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm.numcgm                                                                                                                            \n";
    $stSql .= "      AND contrato.cod_contrato = contrato_servidor.cod_contrato                                                                                                     \n";
    $stSql .= "      AND contrato.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                               \n";
    $stSql .= "      AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                            \n";
    $stSql .= "      AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                                                  \n";
    $stSql .= "      AND contrato_servidor_orgao.cod_orgao = orgao.cod_orgao                                                                                                        \n";
    $stSql .= "      AND orgao.cod_orgao = orgao_nivel.cod_orgao                                                                                                                    \n";
    $stSql .= "      AND orgao_nivel.cod_nivel = nivel.cod_nivel                                                                                                                    \n";
    $stSql .= "      AND orgao_nivel.cod_organograma = nivel.cod_organograma                                                                                                        \n";
    $stSql .= "      AND nivel.cod_organograma = organograma.cod_organograma                                                                                                        \n";
    $stSql .= "      AND orgao_nivel.cod_nivel = vw_orgao_nivel.nivel                                                                                                               \n";
    $stSql .= "      AND organograma.cod_organograma = vw_orgao_nivel.cod_organograma                                                                                               \n";
    $stSql .= "      AND orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                                                                 \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                     \n";
    $stSql .= "      AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                          \n";
    $stSql .= "      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                                                                \n";
    $stSql .= "      AND contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato                                                                                          \n";
    $stSql .= "      AND contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa                                                                                    \n";
    $stSql .= "      AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao                                                                                          \n";

    return $stSql;
}

function recuperaContratosAutomaticos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAutomaticos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAutomaticos()
{
    $stSql .= "  SELECT registro_evento_rescisao.cod_contrato                                               \n";
    $stSql .= "       , registro                                                                            \n";
    $stSql .= "       , sw_cgm.numcgm                                                                       \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                      \n";
    $stSql .= "    FROM folhapagamento".Sessao::getEntidade().".registro_evento_rescisao                    \n";
    $stSql .= "       , folhapagamento".Sessao::getEntidade().".ultimo_registro_evento_rescisao             \n";
    $stSql .= "       , (SELECT servidor_contrato_servidor.cod_contrato                                     \n";
    $stSql .= "               , servidor.numcgm                                                             \n";
    $stSql .= "            FROM pessoal".Sessao::getEntidade().".servidor_contrato_servidor                 \n";
    $stSql .= "               , pessoal".Sessao::getEntidade().".servidor                                   \n";
    $stSql .= "           WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor             \n";
    $stSql .= "           UNION                                                                             \n";
    $stSql .= "          SELECT contrato_pensionista.cod_contrato                                           \n";
    $stSql .= "               , pensionista.numcgm                                                          \n";
    $stSql .= "            FROM pessoal".Sessao::getEntidade().".contrato_pensionista                       \n";
    $stSql .= "               , pessoal".Sessao::getEntidade().".pensionista                                \n";
    $stSql .= "           WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista          \n";
    $stSql .= "             AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_contrato_servidor\n";
    $stSql .= "       , pessoal".Sessao::getEntidade().".contrato                                           \n";
    $stSql .= "       , sw_cgm                                                                               \n";
    $stSql .= "   WHERE registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro \n";
    $stSql .= "     AND registro_evento_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento     \n";
    $stSql .= "     AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento \n";
    $stSql .= "     AND registro_evento_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp      \n";
    $stSql .= "     AND registro_evento_rescisao.cod_contrato  = servidor_contrato_servidor.cod_contrato    \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                     \n";
    $stSql .= "     AND servidor_contrato_servidor.numcgm = sw_cgm.numcgm                                   \n";
    $stSql .= "     AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."           \n";
    $stSql .= "     AND sw_cgm.numcgm IN (".$this->getDado("numcgm").")                                     \n";
    $stSql .= "     AND EXISTS (SELECT 1 FROM pessoal".Sessao::getEntidade().".contrato_servidor_caso_causa WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato \n";
    $stSql .= "                         UNION ALL                                                           \n";
    $stSql .= "                         SELECT 1 FROM pessoal".Sessao::getEntidade().".contrato_pensionista_caso_causa WHERE contrato_pensionista_caso_causa.cod_contrato = contrato.cod_contrato ) \n";
    $stSql .= "GROUP BY registro_evento_rescisao.cod_contrato                                               \n";
    $stSql .= "       , registro                                                                            \n";
    $stSql .= "       , sw_cgm.numcgm                                                                       \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                      \n";

    return $stSql;
}

function montaRecuperarRegistroContratoRescisao()
{
    $stSql .= "SELECT to_real(registro_evento_rescisao.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(registro_evento_rescisao.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_rescisao.cod_contrato) as matricula        \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                                                                            \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                  \n";
    $stSql .= "     , ( case when registro_evento_rescisao.desdobramento = 'S'  then 'Saldo Salário'                                                                   \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'A'  then 'Aviso Prêvio Indenizado'                                                         \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'V'  then 'Férias Vencidas'                                                                 \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'P'  then 'Férias Proporcionais'                                                            \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'D'  then '13º Salário'                                                                     \n";
    $stSql .= "        end ) as descricao                                                                                                                              \n";
    $stSql .= "     , registro_evento_rescisao.cod_periodo_movimentacao                                                                                                \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                           \n";
    $stSql .= " FROM folhapagamento.registro_evento_rescisao                                                                                 \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_rescisao                                                                         \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                     \n";
    $stSql .= "     , pessoal.servidor                                                                                                       \n";
    $stSql .= "     , folhapagamento.evento                                                                                                  \n";
    $stSql .= "WHERE registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro                                                              \n";
    $stSql .= "  AND registro_evento_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp                                                                    \n";
    $stSql .= "  AND registro_evento_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento                                                                  \n";
    $stSql .= "  AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento                                                            \n";
    $stSql .= "  AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                   \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                   \n";
    $stSql .= "  AND registro_evento_rescisao.cod_evento = evento.cod_evento                                                                                           \n";

    return $stSql;
}

function recuperaRegistroContratoRescisao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarRegistroContratoRescisao().$stFiltro.$stOrdem;
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
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                           \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_rescisao                                    \n";
    $stSql .= "     , sw_cgm                                                                                                   \n";
    $stSql .= " WHERE servidor_pensionista.cod_contrato = contrato.cod_contrato                                                \n";
    $stSql .= "   AND contrato.cod_contrato = registro_evento_rescisao.cod_contrato                                              \n";
    $stSql .= "   AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                              \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro                         \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento                             \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp                               \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.desdobramento = registro_evento_rescisao.desdobramento                       \n";
    $stSql .= "   AND contrato.cod_contrato     IN (SELECT cod_contrato                                                        \n";
    $stSql .= "                                       FROM pessoal.contrato_servidor_caso_causa )     \n";
    $stSql .= "   AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                \n";
    $stSql .= "   AND cod_orgao IN (".$this->getDado("cod_orgao").")                                                           \n";
    $stSql .= "GROUP BY contrato.registro                                                                                      \n";
    $stSql .= "       , contrato.cod_contrato                                                                                  \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                          \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                         \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaRescisaoContratoPensionista.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRescisaoContratoPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRescisaoContratoPensionista().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta Sql para recuperaRescisaoContratoPensionista - Utilizado para resgatar data e motivo de rescisao de uma pensionista.
    * @access public
    * @return String $stSql
*/
function montaRecuperaRescisaoContratoPensionista()
{
    $stSql .= " SELECT contrato.*                                                                                                                                                   \n";
    $stSql .= "        , pensionista.numcgm                                                                                                                                         \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                             \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                           \n";
    $stSql .= "        , vw_orgao_nivel.orgao as cod_estrutural                                                                                                                     \n";
    $stSql .= "        , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao                                                        \n";
    $stSql .= "        , contrato_servidor.cod_cargo                                                                                                                                \n";
    $stSql .= "        , contrato_servidor.cod_sub_divisao                                                                                                                          \n";
    $stSql .= "        , contrato_servidor_especialidade_cargo.cod_especialidade                                                                                                    \n";
    $stSql .= "        , causa_rescisao.descricao as descricao_causa                                                                                                                \n";
    $stSql .= "        , causa_rescisao.num_causa                                                                                                                                   \n";
    $stSql .= "        , to_char(contrato_pensionista_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_rescisao                                                                           \n";
    $stSql .= " FROM pessoal".Sessao::getEntidade().".contrato                                                                                                                      \n";
    $stSql .= "        , pessoal".Sessao::getEntidade().".contrato_servidor                                                                                                         \n";
    $stSql .= "        , pessoal".Sessao::getEntidade().".pensionista                                                                                                               \n";
    $stSql .= "        , pessoal".Sessao::getEntidade().".contrato_pensionista                                                                                                      \n";
    $stSql .= " INNER JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                            \n";
    $stSql .= "                 , contrato_servidor_padrao.cod_padrao                                                                                                               \n";
    $stSql .= "              FROM pessoal".Sessao::getEntidade().".contrato_servidor_padrao                                                                                         \n";
    $stSql .= "                 , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                           , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal".Sessao::getEntidade().".contrato_servidor_padrao                                                                                \n";
    $stSql .= "                      GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                                         \n";
    $stSql .= "             WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                                 \n";
    $stSql .= "               AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao                                          \n";
    $stSql .= "        ON pessoal".Sessao::getEntidade().".contrato_pensionista.cod_contrato_cedente = contrato_servidor_padrao.cod_contrato                                        \n";
    $stSql .= " LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                                                                              \n";
    $stSql .= "                 , contrato_servidor_local.cod_local                                                                                                                 \n";
    $stSql .= "              FROM pessoal".Sessao::getEntidade().".contrato_servidor_local                                                                                          \n";
    $stSql .= "                 , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                           , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal".Sessao::getEntidade().".contrato_servidor_local                                                                                 \n";
    $stSql .= "                      GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                          \n";
    $stSql .= "             WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                                   \n";
    $stSql .= "               AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local                                             \n";
    $stSql .= "        ON pessoal".Sessao::getEntidade().".contrato_pensionista.cod_contrato_cedente = contrato_servidor_local.cod_contrato                                         \n";
    $stSql .= " LEFT JOIN pessoal".Sessao::getEntidade().".contrato_servidor_especialidade_cargo                                                                                    \n";
    $stSql .= "        ON pessoal".Sessao::getEntidade().".contrato_pensionista.cod_contrato_cedente = contrato_servidor_especialidade_cargo.cod_contrato                           \n";
    $stSql .= " LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                               \n";
    $stSql .= "                 , contrato_servidor_especialidade_funcao.cod_especialidade                                                                                          \n";
    $stSql .= "            FROM pessoal".Sessao::getEntidade().".contrato_servidor_especialidade_funcao                                                                             \n";
    $stSql .= "                 , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                           , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal".Sessao::getEntidade().".contrato_servidor_especialidade_funcao                                                                  \n";
    $stSql .= "                      GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                           \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                      \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "        ON pessoal".Sessao::getEntidade().".contrato_pensionista.cod_contrato_cedente = contrato_servidor_especialidade_funcao.cod_contrato                          \n";
    $stSql .= "         , sw_cgm                                                                                                                                                    \n";
    $stSql .= "         , pessoal".Sessao::getEntidade().".contrato_pensionista_orgao                                                                                               \n";
    $stSql .= "         , (  SELECT cod_contrato                                                                                                                                    \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                                                                     \n";
    $stSql .= "               FROM pessoal".Sessao::getEntidade().".contrato_pensionista_orgao                                                                                      \n";
    $stSql .= "              GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                               \n";
    $stSql .= "         , organograma.orgao                                                                                                                                         \n";
    $stSql .= "         , organograma.orgao_nivel                                                                                                                                   \n";
    $stSql .= "         , organograma.organograma                                                                                                                                   \n";
    $stSql .= "         , organograma.nivel                                                                                                                                         \n";
    $stSql .= "         , organograma.vw_orgao_nivel                                                                                                                                \n";
    $stSql .= "         , pessoal".Sessao::getEntidade().".contrato_servidor_funcao                                                                                                 \n";
    $stSql .= "         , (  SELECT cod_contrato                                                                                                                                    \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                                                                     \n";
    $stSql .= "                FROM pessoal".Sessao::getEntidade().".contrato_servidor_funcao                                                                                       \n";
    $stSql .= "            GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                   \n";
    $stSql .= " 	, pessoal".Sessao::getEntidade().".contrato_pensionista_caso_causa                                                                                          \n";
    $stSql .= "         , pessoal".Sessao::getEntidade().".caso_causa                                                                                                               \n";
    $stSql .= "         , pessoal".Sessao::getEntidade().".causa_rescisao                                                                                                           \n";
    $stSql .= "     WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                                        \n";
    $stSql .= "       AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                              \n";
    $stSql .= "       AND pensionista.numcgm = sw_cgm.numcgm                                                                                                                        \n";
    $stSql .= "       AND contrato.cod_contrato = contrato_pensionista.cod_contrato                                                                                                 \n";
    $stSql .= "       AND contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                                                           \n";
    $stSql .= "       AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                     \n";
    $stSql .= "       AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp                                                                           \n";
    $stSql .= "       AND contrato_pensionista_orgao.cod_orgao = orgao.cod_orgao                                                                                                    \n";
    $stSql .= "       AND orgao.cod_orgao = orgao_nivel.cod_orgao                                                                                                                   \n";
    $stSql .= "       AND orgao_nivel.cod_nivel = nivel.cod_nivel                                                                                                                   \n";
    $stSql .= "       AND orgao_nivel.cod_organograma = nivel.cod_organograma                                                                                                       \n";
    $stSql .= "       AND nivel.cod_organograma = organograma.cod_organograma                                                                                                       \n";
    $stSql .= "       AND orgao_nivel.cod_nivel = vw_orgao_nivel.nivel                                                                                                              \n";
    $stSql .= "       AND organograma.cod_organograma = vw_orgao_nivel.cod_organograma                                                                                              \n";
    $stSql .= "       AND orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                                                                \n";
    $stSql .= "       AND contrato_pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato                                                                                \n";
    $stSql .= "       AND contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                    \n";
    $stSql .= "       AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                         \n";
    $stSql .= "       AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                                                               \n";
    $stSql .= "       AND contrato.cod_contrato = contrato_pensionista_caso_causa.cod_contrato                                                                                      \n";
    $stSql .= "       AND contrato_pensionista_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa                                                                                \n";
    $stSql .= "       AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao                                                                                         \n";

    return $stSql;
}

}
?>
