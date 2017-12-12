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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.EVENTO
    * Data de Criação: 26/08/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32866 $
    $Name$
    $Author: alex $
    $Date: 2007-10-19 19:01:51 -0200 (Sex, 19 Out 2007) $

    $Id: TFolhaPagamentoEvento.class.php 65564 2016-05-31 20:49:28Z carlos.silva $

    * Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.EVENTO
  * Data de Criação: 26/08/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEvento()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.evento');

    $this->setCampoCod('cod_evento');

    $this->AddCampo('cod_evento','integer',true,'',true,false);
    $this->AddCampo('codigo','char',true,'5',false,false);
    $this->AddCampo('descricao','varchar',true,'80',false,false);
    $this->AddCampo('sigla','varchar',false,'5',false,false);
    $this->AddCampo('natureza','char',true,'1',false,false);
    $this->AddCampo('tipo','char',true,'1',false,false);
    $this->AddCampo('fixado','char',true,'1',false,false);
    $this->AddCampo('limite_calculo','boolean',false,'',false,false);
    $this->AddCampo('apresenta_parcela','boolean',false,'',false,false);
    $this->AddCampo('evento_sistema','boolean',false,'',false,false);
    $this->AddCampo('apresentar_contracheque','boolean',false,'',false,false);
    $this->AddCampo('cod_verba','char',false,'10',false,false);
}

function listar(&$rsLista)
{
    $obErro      = new Erro;
    $rsLista     = new RecordSet;

    if ( $this->getDado('cod_evento') ) {
        $stFiltro  = " AND cod_evento=".$this->getDado('cod_evento');
    }
    if ( $this->getDado('codigo') ) {
        $stFiltro .= " AND codigo like '".$this->getDado('codigo')."'";
    }
    if ( $this->getDado('descricao') ) {
        $stFiltro .= " AND descricao like '".$this->getDado('descricao')."%' ";
    }
    if ( $this->getDado('natureza') ) {
        $stFiltro .= " AND natureza='".$this->getDado('natureza')."'";
    }
    if ( $this->getDado('tipo') ) {
        $stFiltro .= " AND tipo=".$this->getDado('tipo');
    }
    if ( $this->getDado('fixado') ) {
        $stFiltro .= " AND fixado=".$this->getDado('fixado');
    }
    if ( $this->getDado('limite_calculo') ) {
        $stFiltro .= " AND limite_calculo=".$this->getDado('limite_calculo');
    }
    if ( $this->getDado('apresenta_parcela') ) {
        $stFiltro .= " AND apresenta_parcela=".$this->getDado('apresenta_parcela');
    }
    if ( $this->getDado('apresentar_contracheque') ) {
        $stFiltro .= " AND apresentar_contracheque=".$this->getDado('apresentar_contracheque');
    }

    $stFiltro = ( $stFiltro != "" ) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";

    $obErro = $this->recuperaTodos( $rsLista, $stFiltro );

    return $obErro;
}

function recuperaEventos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY FPE.descricao ";
    $stSql  = $this->montaRecuperaEventos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventos()
{
    $stSql  = "   SELECT FPE.cod_evento                                         \n";
    $stSql .= "        , FPE.codigo                                             \n";
    $stSql .= "        , TRIM(FPE.descricao) as descricao                       \n";
    $stSql .= "        , FPE.natureza                                           \n";
    $stSql .= "        , FPE.sigla                                              \n";
    $stSql .= "        , CASE WHEN FPE.natureza = 'P' THEN 'Proventos'          \n";
    $stSql .= "               WHEN FPE.natureza = 'I' THEN 'Informativos'       \n";
    $stSql .= "               WHEN FPE.natureza = 'B' THEN 'Base'               \n";
    $stSql .= "              ELSE 'Descontos'                                   \n";
    $stSql .= "          END AS proventos_descontos                             \n";
    $stSql .= "        , FPE.tipo                                               \n";
    $stSql .= "        , FPE.fixado                                             \n";
    $stSql .= "        , FPE.limite_calculo                                     \n";
    $stSql .= "        , FPE.apresenta_parcela                                  \n";
    $stSql .= "        , FPE.evento_sistema                                     \n";
    $stSql .= "        , FPEE.timestamp                                         \n";
    $stSql .= "        , FPEE.valor_quantidade                                  \n";
    $stSql .= "        , FPEE.unidade_quantitativa                              \n";
    $stSql .= "        , FPEE.observacao                                        \n";
    $stSql .= "        , FSCE.cod_sequencia                                     \n";
    $stSql .= "        , FPE.cod_verba                                          \n";
    $stSql .= "        , FPE.apresentar_contracheque                            \n";
    $stSql .= "     FROM folhapagamento".Sessao::getEntidade().".evento AS FPE                           \n";
    $stSql .= "        , folhapagamento".Sessao::getEntidade().".evento_evento AS FPEE                   \n";
    $stSql .= "LEFT JOIN folhapagamento".Sessao::getEntidade().".sequencia_calculo_evento AS FSCE        \n";
    $stSql .= "       ON FSCE.cod_evento = FPEE.cod_evento                      \n";
    $stSql .= "        , (   SELECT FPEE.cod_evento                             \n";
    $stSql .= "                   , MAX (FPEE.timestamp) AS timestamp           \n";
    $stSql .= "                FROM folhapagamento".Sessao::getEntidade().".evento_evento FPEE           \n";
    $stSql .= "            GROUP BY FPEE.cod_evento                             \n";
    $stSql .= "          ) AS MAX_FPEE                                          \n";
    $stSql .= "    WHERE FPE.cod_evento  = MAX_FPEE.cod_evento                  \n";
    $stSql .= "      AND FPEE.timestamp  = MAX_FPEE.timestamp                   \n";
    $stSql .= "      AND FPE.cod_evento  = FPEE.cod_evento                      \n";

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

function montaRecuperaEventosFormatado()
{
    $stSql  = "SELECT  evento.cod_evento                                               \n";
    $stSql .= "     , evento.codigo                                                   \n";
    $stSql .= "     , TRIM(evento.descricao) as descricao                             \n";
    $stSql .= "     , sequencia_calculo.sequencia                                     \n";
    $stSql .= "     , evento.fixado                                                   \n";
    $stSql .= "     , ( CASE when evento.tipo = 'F' then 'Fixo'                       \n";
    $stSql .= "              when evento.tipo = 'V' then 'Variável'                   \n";
    $stSql .= "              when evento.tipo = 'B' then 'Base'                       \n";
    $stSql .= "         END ) as tipo                                                 \n";
    $stSql .= "     , ( CASE when evento.natureza = 'P' THEN 'Provento'               \n";
    $stSql .= "              when evento.natureza = 'D' THEN 'Desconto'               \n";
    $stSql .= "              when evento.natureza = 'B' THEN 'Base'                   \n";
    $stSql .= "              when evento.natureza = 'I' THEN 'Informativo'            \n";
    $stSql .= "         END ) as natureza                                             \n";
    $stSql .= " FROM folhapagamento.evento                                            \n";
    $stSql .= "    , folhapagamento.evento_evento                                     \n";
    $stSql .= "    , folhapagamento.sequencia_calculo_evento                          \n";
    $stSql .= "    , folhapagamento.sequencia_calculo                                 \n";
    $stSql .= "    , ( SELECT cod_evento                                              \n";
    $stSql .= "             , MAX(timestamp) AS timestamp                             \n";
    $stSql .= "         FROM folhapagamento.evento_evento                             \n";
    $stSql .= "        GROUP BY cod_evento                                            \n";
    $stSql .= "       ) AS max_evento_evento                                          \n";
    $stSql .= "WHERE evento.cod_evento = evento_evento.cod_evento                     \n";
    $stSql .= "  AND evento_evento.cod_evento = max_evento_evento.cod_evento          \n";
    $stSql .= "  AND evento_evento.timestamp = max_evento_evento.timestamp            \n";
    $stSql .= "  AND evento_evento.cod_evento = sequencia_calculo_evento.cod_evento   \n";
    $stSql .= "  AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia \n";

    return $stSql;
}

function recuperaEventosFormatado(&$rsRecordSet, $stFiltro="", $stOrdem="")
{
    $obErro = $this->executaRecupera("montaRecuperaEventosFormatado",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaRelatorioCustomizavelEventos()
{
    $stSql = "   SELECT contrato.registro                                                                                                                             \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao                                                     \n";
    $stSql .= "        , local.descricao                as desc_local                                                                                                  \n";
    $stSql .= "        , cargo.descricao                as desc_cargo                                                                                                  \n";
    $stSql .= "        , especialidade.descricao        as desc_especialidade                                                                                          \n";
    $stSql .= "        , funcao.descricao               as desc_funcao                                                                                                 \n";
    $stSql .= "        , especialidade_funcao.descricao as desc_especialidade_funcao                                                                                   \n";
    $stSql .= "        , evento.codigo                                                                                                                                 \n";
    $stSql .= "        , evento_calculado.quantidade                                                                                                                   \n";
    $stSql .= "        , evento_calculado.valor                                                                                                                        \n";
    $stSql .= "     FROM folhapagamento.evento                                                                                                \n";
    $stSql .= "        , folhapagamento.registro_evento                                                                                       \n";
    $stSql .= "        , folhapagamento.ultimo_registro_evento                                                                                \n";
    $stSql .= "        , folhapagamento.evento_calculado                                                                                      \n";
    $stSql .= "        , folhapagamento.registro_evento_periodo                                                                               \n";
    $stSql .= "        , pessoal.contrato_servidor                                                                                            \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                                                                                                  \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                     \n";
    $stSql .= "                --, descricao                                                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                    \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                \n";
    $stSql .= "                , organograma.orgao                                                                                                                     \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_orgao = orgao.cod_orgao                                                                                   \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as orgao                                                   \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = orgao.cod_contrato                                                                                           \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                                                                  \n";
    $stSql .= "                , contrato_servidor_local.cod_local                                                                                                     \n";
    $stSql .= "                , descricao                                                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                    \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                \n";
    $stSql .= "                , organograma.local                                                                                                                     \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_local = local.cod_local                                                                                   \n";
    $stSql .= "              AND contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as local                                                   \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = local.cod_contrato                                                                                           \n";
    $stSql .= "LEFT JOIN pessoal.cargo                                                                                                        \n";
    $stSql .= "       ON contrato_servidor.cod_cargo = cargo.cod_cargo                                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT cod_contrato                                                                                                                          \n";
    $stSql .= "                , especialidade.cod_especialidade                                                                                                       \n";
    $stSql .= "                , descricao                                                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_cargo                                                                \n";
    $stSql .= "                , pessoal.especialidade                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as especialidade                           \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = especialidade.cod_contrato                                                                                   \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_funcao.cod_contrato                                                                                                 \n";
    $stSql .= "                , contrato_servidor_funcao.cod_cargo as cod_funcao                                                                                      \n";
    $stSql .= "                , descricao                                                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_funcao                                                                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_funcao                                                                   \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                               \n";
    $stSql .= "                , pessoal.cargo                                                                                                \n";
    $stSql .= "            WHERE contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                                                                  \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                     \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp) as funcao                                             \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = funcao.cod_contrato                                                                                          \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                   \n";
    $stSql .= "                , contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao                                                  \n";
    $stSql .= "                , descricao                                                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                               \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                     \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                 \n";
    $stSql .= "                , pessoal.especialidade                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade                                            \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                         \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp) as especialidade_funcao   \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = especialidade_funcao.cod_contrato                                                                            \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                 \n";
    $stSql .= "                , contrato_servidor_padrao.cod_padrao                                                                                                   \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                   \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                               \n";
    $stSql .= "            WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                     \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp) as padrao                                             \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = padrao.cod_contrato                                                                                          \n";

    $stSql .= "        , pessoal.contrato                                                                                                     \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "        , pessoal.servidor                                                                                                     \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                          \n";
    $stSql .= "        , sw_cgm                                                                                                                                        \n";
    $stSql .= "    WHERE evento.cod_evento = registro_evento.cod_evento                                                                                                \n";
    $stSql .= "      AND registro_evento.cod_evento =  ultimo_registro_evento.cod_evento                                                                               \n";
    $stSql .= "      AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                            \n";
    $stSql .= "      AND registro_evento.timestamp   = ultimo_registro_evento.timestamp                                                                                \n";
    $stSql .= "      AND registro_evento.cod_evento = evento_calculado.cod_evento                                                                                      \n";
    $stSql .= "      AND registro_evento.cod_registro = evento_calculado.cod_registro                                                                                  \n";
    $stSql .= "      AND registro_evento.timestamp = evento_calculado.timestamp_registro                                                                               \n";
    $stSql .= "      AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                                                           \n";
    $stSql .= "      AND registro_evento_periodo.cod_contrato = contrato_servidor.cod_contrato                                                                         \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = contrato.cod_contrato                                                                                        \n";
    $stSql .= "      AND contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                      \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                               \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                 \n";
    $stSql .= "      AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                                   \n";

    return $stSql;
}

/*
função...: RecuperaEventoCodigoNatureza;
objetivo.: Recebe codigo e natureza de um evento e retorna um um recordset
           por referencia preenchido com o evento se ele for encontrado
Data.....: 29/05/2006
Autor....: Bruce Sena
*/
function recuperaEventoCodigoNatureza(&$recordset, $stCodigo, $stNatureza, $boEventoSistema = false)
{
    $recordset = new RecordSet;
    $stFiltro = '';

    if ($boEventoSistema) {
        $stEventoSistema = "true";
    } else {
        $stEventoSistema = "false";
    }

    $stFiltro =  " and  FPE.codigo = '$stCodigo' and FPE.natureza = '$stNatureza' AND FPE.evento_sistema = '".$stEventoSistema."'";

    $obErro = $this->recuperaEventos( $recordset, $stFiltro );

    return $obErro;

}

function recuperaCreditosBanco(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaCreditosBanco().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCreditosBanco()
{
    $stSql  = "   SELECT contrato.registro                                                                                                     \n";
    $stSql .= "        , servidor.numcgm                                                                                                       \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                        \n";
    $stSql .= "        , substr(cpf,1,3)||'.'||substr(cpf,4,3)||'.'||substr(cpf,7,3)||'-'||substr(cpf,10,2) as cpf                             \n";
    $stSql .= "        , contrato_servidor_conta_salario.nr_conta                                                                              \n";
    $stSql .= "        , agencia.num_agencia                                                                                                   \n";
    $stSql .= "        , agencia.nom_agencia                                                                                                   \n";
    $stSql .= "        , agencia.cod_agencia                                                                                                   \n";
    $stSql .= "        , banco.num_banco                                                                                                       \n";
    $stSql .= "        , banco.nom_banco                                                                                                       \n";
    $stSql .= "        , banco.cod_banco                                                                                                       \n";
    $stSql .= "        , valores.valor                                                                                                         \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao                                \n";
    $stSql .= "        , vw_orgao_nivel.orgao as cod_estrutural                                                                                \n";
    $stSql .= "        , orgao.cod_orgao                                                                                                       \n";
    $stSql .= "        , descricao_local as local                                                                                              \n";
    $stSql .= "        , cod_local                                                                                                             \n";
    $stSql .= "    FROM (SELECT cod_contrato                                                                                                   \n";
    $stSql .= "               , cod_agencia                                                                                                    \n";
    $stSql .= "               , cod_banco                                                                                                      \n";
    $stSql .= "               , nr_conta                                                                                                       \n";
    $stSql .= "            FROM pessoal.contrato_servidor_conta_salario                                               \n";
    $stSql .= "           UNION                                                                                                                \n";
    $stSql .= "          SELECT contrato_pensionista_conta_salario.cod_contrato                                                                \n";
    $stSql .= "               , cod_agencia                                                                                                    \n";
    $stSql .= "               , cod_banco                                                                                                      \n";
    $stSql .= "               , nr_conta                                                                                                       \n";
    $stSql .= "            FROM pessoal.contrato_pensionista_conta_salario                                            \n";
    $stSql .= "               , (SELECT cod_contrato                                                                                           \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_pensionista_conta_salario                                    \n";
    $stSql .= "                  GROUP BY cod_contrato) as max_contrato_pensionista_conta_salario                                              \n";
    $stSql .= "           WHERE contrato_pensionista_conta_salario.cod_contrato = max_contrato_pensionista_conta_salario.cod_contrato          \n";
    $stSql .= "             AND contrato_pensionista_conta_salario.timestamp = max_contrato_pensionista_conta_salario.timestamp) as contrato_servidor_conta_salario\n";

    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                                             \n";
    $stSql .= "                , contrato_servidor_local.cod_contrato                                                                          \n";
    $stSql .= "                , local.descricao as descricao_local                                                                            \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                               \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                        \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                         \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                     \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                        \n";
    $stSql .= "                , organograma.local                                                                                             \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                               \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp                                  \n";
    $stSql .= "              AND contrato_servidor_local.cod_local    = local.cod_local) as contrato_servidor_local                            \n";
    $stSql .= "              ON contrato_servidor_conta_salario.cod_contrato = contrato_servidor_local.cod_contrato                            \n";
    $stSql .= "LEFT JOIN monetario.agencia                                                                                                     \n";
    $stSql .= "       ON contrato_servidor_conta_salario.cod_banco = agencia.cod_banco                                                         \n";
    $stSql .= "      AND contrato_servidor_conta_salario.cod_agencia = agencia.cod_agencia                                                     \n";
    $stSql .= "LEFT JOIN monetario.banco                                                                                                       \n";
    $stSql .= "       ON contrato_servidor_conta_salario.cod_banco = banco.cod_banco                                                           \n";
    $stSql .= "        , pessoal.contrato                                                                                                      \n";
    $stSql .= "        , (SELECT contrato.cod_contrato                                                                                         \n";
    $stSql .= "                , (                                                                                                             \n";
    if ( $this->getDado("boSalario") == true ) {
        $stSql .= "(coalesce(proventos_salario.proventos,0.00)      - coalesce(descontos_salario.descontos,0.00)) +                        \n";
    }
    if ( $this->getDado("boComplementar") == true ) {
        $stSql .= "(coalesce(proventos_complementar.proventos,0.00) - coalesce(descontos_complementar.descontos,0.00)) +                   \n";
    }
    if ( $this->getDado("boFerias") == true ) {
        $stSql .= "(coalesce(proventos_ferias.proventos,0.00)       - coalesce(descontos_ferias.descontos,0.00)) +                         \n";
    }
    if ( $this->getDado("boDecimo") == true ) {
        $stSql .= "(coalesce(proventos_decimo.proventos,0.00)       - coalesce(descontos_decimo.descontos,0.00)) +                         \n";
    }
    if ( $this->getDado("boRescisao") == true ) {
        $stSql .= "(coalesce(proventos_rescisao.proventos,0.00)       - coalesce(descontos_rescisao.descontos,0.00)) +                         \n";
    }
    $stSql .= "                                                                                                          0.00) as valor        \n";
    $stSql .= "             FROM pessoal.contrato                                                                                              \n";
    $stSql .= "        LEFT JOIN ( SELECT sum(evento_calculado.valor) as proventos                                                             \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_periodo                                                               \n";
    $stSql .= "                         , folhapagamento.registro_evento                                                                       \n";
    $stSql .= "                         , folhapagamento.evento_calculado                                                                      \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."    \n";
    $stSql .= "                       AND registro_evento_periodo.cod_registro = registro_evento.cod_registro                                  \n";
    $stSql .= "                       AND registro_evento.cod_registro = evento_calculado.cod_registro                                         \n";
    $stSql .= "                       AND registro_evento.cod_evento = evento_calculado.cod_evento                                             \n";
    $stSql .= "                       AND registro_evento.timestamp = evento_calculado.timestamp_registro                                      \n";
    $stSql .= "                       AND evento_calculado.cod_evento = evento.cod_evento                                                      \n";
    $stSql .= "                       AND evento.natureza = 'P'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as proventos_salario                                                                   \n";
    $stSql .= "               ON contrato.cod_contrato = proventos_salario.cod_contrato                                                        \n";
    $stSql .= "        LEFT JOIN ( SELECT sum(evento_complementar_calculado.valor) as proventos                                                \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_complementar                                                          \n";
    $stSql .= "                         , folhapagamento.evento_complementar_calculado                                                         \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_complementar.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
    $stSql .= "                       AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro               \n";
    $stSql .= "                       AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                   \n";
    $stSql .= "                       AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao       \n";
    $stSql .= "                       AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro            \n";
    $stSql .= "                       AND evento_complementar_calculado.cod_evento = evento.cod_evento                                         \n";
    $stSql .= "                       AND evento.natureza = 'P'                                                                                \n";
    if ( $this->getDado("cod_complementar") ) {
        $stSql .= "                   AND cod_complementar in (".$this->getDado("cod_complementar").")                                         \n";
    }
    $stSql .= "                  GROUP BY cod_contrato) as proventos_complementar                                                              \n";
    $stSql .= "               ON contrato.cod_contrato = proventos_complementar.cod_contrato                                                   \n";
    $stSql .= "        LEFT JOIN ( SELECT sum(evento_ferias_calculado.valor) as proventos                                                      \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_ferias                                                                \n";
    $stSql .= "                         , folhapagamento.evento_ferias_calculado                                                               \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_ferias.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."     \n";
    $stSql .= "                       AND registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                           \n";
    $stSql .= "                       AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                               \n";
    $stSql .= "                       AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento                         \n";
    $stSql .= "                       AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro                        \n";
    $stSql .= "                       AND evento_ferias_calculado.cod_evento = evento.cod_evento                                               \n";
    $stSql .= "                       AND evento.natureza = 'P'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as proventos_ferias                                                                    \n";
    $stSql .= "               ON contrato.cod_contrato = proventos_ferias.cod_contrato                                                         \n";

    $stSql .= "        LEFT JOIN ( SELECT sum(evento_rescisao_calculado.valor) as proventos                                                	   \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_rescisao                                    \n";
    $stSql .= "                         , folhapagamento.evento_rescisao_calculado                                   \n";
    $stSql .= "                         , folhapagamento.evento                                                      \n";
    $stSql .= "                     WHERE registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."   \n";
    $stSql .= "                       AND registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro               	 	 \n";
    $stSql .= "                       AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                   	 	 \n";
    $stSql .= "                       AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro            	 	 \n";
    $stSql .= "                       AND evento_rescisao_calculado.cod_evento = evento.cod_evento                                         	 \n";
    $stSql .= "                       AND evento.natureza = 'P'                                                                                \n";

    $stSql .= "                  GROUP BY cod_contrato) as proventos_rescisao                                                                  \n";
    $stSql .= "               ON contrato.cod_contrato = proventos_rescisao.cod_contrato    \n";

    $stSql .= "        LEFT JOIN ( SELECT sum(evento_rescisao_calculado.valor) as descontos                                                             \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_rescisao                                                               \n";
    $stSql .= "                         , folhapagamento.evento_rescisao_calculado                                                                      \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."    \n";
    $stSql .= "                       AND registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                         \n";
    $stSql .= "                       AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                            \n";
    $stSql .= "                       AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro                     \n";
    $stSql .= "                       AND evento_rescisao_calculado.cod_evento = evento.cod_evento                                              \n";
    $stSql .= "                       AND evento.natureza = 'D'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as descontos_rescisao                                                                  \n";
    $stSql .= "               ON contrato.cod_contrato = descontos_rescisao.cod_contrato                                                       \n";

    $stSql .= "        LEFT JOIN ( SELECT sum(evento_decimo_calculado.valor) as proventos                                                      \n";

    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_decimo                                                                \n";
    $stSql .= "                         , folhapagamento.evento_decimo_calculado                                                               \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_decimo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."     \n";
    $stSql .= "                       AND registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                           \n";
    $stSql .= "                       AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                               \n";
    $stSql .= "                       AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento                         \n";
    $stSql .= "                       AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro                        \n";
    $stSql .= "                       AND evento_decimo_calculado.cod_evento = evento.cod_evento                                               \n";
    $stSql .= "                       AND evento.natureza = 'P'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as proventos_decimo                                                                    \n";
    $stSql .= "               ON contrato.cod_contrato = proventos_decimo.cod_contrato                                                         \n";

    $stSql .= "        LEFT JOIN ( SELECT sum(evento_calculado.valor) as descontos                                                             \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_periodo                                                               \n";
    $stSql .= "                         , folhapagamento.registro_evento                                                                       \n";
    $stSql .= "                         , folhapagamento.evento_calculado                                                                      \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_periodo.cod_periodo_movimentacao = '".$this->getDado("cod_periodo_movimentacao")."'  \n";
    $stSql .= "                       AND registro_evento_periodo.cod_registro = registro_evento.cod_registro                                  \n";
    $stSql .= "                       AND registro_evento.cod_registro = evento_calculado.cod_registro                                         \n";
    $stSql .= "                       AND registro_evento.cod_evento = evento_calculado.cod_evento                                             \n";
    $stSql .= "                       AND registro_evento.timestamp = evento_calculado.timestamp_registro                                      \n";
    $stSql .= "                       AND evento_calculado.cod_evento = evento.cod_evento                                                      \n";
    $stSql .= "                       AND evento.natureza = 'D'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as descontos_salario                                                                   \n";
    $stSql .= "               ON contrato.cod_contrato = descontos_salario.cod_contrato                                                        \n";
    $stSql .= "        LEFT JOIN ( SELECT sum(evento_complementar_calculado.valor) as descontos                                                \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_complementar                                                          \n";
    $stSql .= "                         , folhapagamento.evento_complementar_calculado                                                         \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_complementar.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
    $stSql .= "                       AND registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro               \n";
    $stSql .= "                       AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                   \n";
    $stSql .= "                       AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao       \n";

    $stSql .= "                       AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro            \n";
    $stSql .= "                       AND evento_complementar_calculado.cod_evento = evento.cod_evento                                         \n";
    $stSql .= "                       AND evento.natureza = 'D'                                                                                \n";
    if ( $this->getDado("cod_complementar") ) {
        $stSql .= "                   AND cod_complementar in (".$this->getDado("cod_complementar").")                                         \n";
    }
    $stSql .= "                  GROUP BY cod_contrato) as descontos_complementar                                                              \n";
    $stSql .= "               ON contrato.cod_contrato = descontos_complementar.cod_contrato                                                   \n";
    $stSql .= "        LEFT JOIN ( SELECT sum(evento_ferias_calculado.valor) as descontos                                                      \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_ferias                                                                \n";
    $stSql .= "                         , folhapagamento.evento_ferias_calculado                                                               \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_ferias.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."     \n";
    $stSql .= "                       AND registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                           \n";
    $stSql .= "                       AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                               \n";
    $stSql .= "                       AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento                         \n";
    $stSql .= "                       AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro                        \n";
    $stSql .= "                       AND evento_ferias_calculado.cod_evento = evento.cod_evento                                               \n";
    $stSql .= "                       AND evento.natureza = 'D'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as descontos_ferias                                                                    \n";
    $stSql .= "               ON contrato.cod_contrato = descontos_ferias.cod_contrato                                                         \n";

    $stSql .= "        LEFT JOIN ( SELECT sum(evento_decimo_calculado.valor) as descontos                                                      \n";
    $stSql .= "                         , cod_contrato                                                                                         \n";
    $stSql .= "                      FROM folhapagamento.registro_evento_decimo                                                                \n";
    $stSql .= "                         , folhapagamento.evento_decimo_calculado                                                               \n";
    $stSql .= "                         , folhapagamento.evento                                                                                \n";
    $stSql .= "                     WHERE registro_evento_decimo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."     \n";
    $stSql .= "                       AND registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                           \n";
    $stSql .= "                       AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                               \n";
    $stSql .= "                       AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento                         \n";
    $stSql .= "                       AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro                        \n";
    $stSql .= "                       AND evento_decimo_calculado.cod_evento = evento.cod_evento                                               \n";
    $stSql .= "                       AND evento.natureza = 'D'                                                                                \n";
    $stSql .= "                  GROUP BY cod_contrato) as descontos_decimo                                                                    \n";
    $stSql .= "               ON contrato.cod_contrato = descontos_decimo.cod_contrato) as valores                                             \n";
    $stSql .= "        , (SELECT servidor_contrato_servidor.cod_contrato                                                                        \n";
    $stSql .= "                , servidor.numcgm                                                                                                \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                              \n";
    $stSql .= "             FROM pessoal.servidor_contrato_servidor                                                                             \n";
    $stSql .= "                , pessoal.servidor                                                                                               \n";
    $stSql .= "                , pessoal.contrato_servidor_orgao                                                                                \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                         \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                      \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                         \n";
    $stSql .= "            WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                 \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                      \n";
    $stSql .= "            UNION                                                                                                                \n";
    $stSql .= "           SELECT contrato_pensionista.cod_contrato                                                                              \n";
    $stSql .= "                , pensionista.numcgm                                                                                             \n";
    $stSql .= "                , contrato_pensionista_orgao.cod_orgao                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_pensionista                                                                                   \n";
    $stSql .= "                , pessoal.pensionista                                                                                            \n";
    $stSql .= "                , pessoal.contrato_pensionista_orgao                                                                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                         \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                          \n";
    $stSql .= "                       FROM pessoal.contrato_pensionista_orgao                                                                   \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                      \n";
    $stSql .= "            WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                             \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                   \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato                                    \n";
    $stSql .= "              AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                          \n";
    $stSql .= "              AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as servidor                   \n";
    $stSql .= "        , sw_cgm                                                                                                                 \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                   \n";
    $stSql .= "        , organograma.orgao                                                                                                      \n";
    $stSql .= "        , organograma.vw_orgao_nivel                                                                                             \n";
    $stSql .= "    WHERE contrato_servidor_conta_salario.cod_contrato = contrato.cod_contrato                                                   \n";
    $stSql .= "      AND servidor.cod_contrato = contrato.cod_contrato                                                                          \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm.numcgm                                                                                        \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                          \n";
    $stSql .= "      AND contrato.cod_contrato = valores.cod_contrato                                                                           \n";
    $stSql .= "      AND (valores.valor > 0.00 or valores.valor < 0.00)                                                                         \n";
    $stSql .= "      AND servidor.cod_orgao = orgao.cod_orgao                                                                                   \n";
    $stSql .= "      AND orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                             \n";

    return $stSql;
}

function recuperaInformacoesParaRelatorioRegistroEvento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaInformacoesParaRelatorioRegistroEvento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInformacoesParaRelatorioRegistroEvento()
{
    $stSql  = "SELECT sw_cgm.numcgm                                                                                                                                                       \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                                                                      \n";
    $stSql .= "     , contrato.*                                                                                                                                                          \n";
    $stSql .= "     , (SELECT descricao FROM pessoal.cargo WHERE cargo.cod_cargo = contrato_servidor.cod_cargo) as desc_cargo                                    \n";
    $stSql .= "     , contrato_servidor_especialidade_cargo.desc_especialidade_cargo                                                                                                      \n";
    $stSql .= "     , contrato_servidor_funcao.desc_funcao                                                                                                                                \n";
    $stSql .= "     , contrato_servidor_especialidade_funcao.desc_especialidade_funcao                                                                                                    \n";
    $stSql .= "     , recuperaDescricaoOrgao(contrato_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao                                                                  \n";
    $stSql .= "     , (SELECT orgao FROM organograma.vw_orgao_nivel WHERE vw_orgao_nivel.cod_orgao = contrato_orgao.cod_orgao) as cod_estrutural                    \n";
    $stSql .= "  FROM pessoal.contrato                                                                                                                           \n";
    $stSql .= "     , (SELECT servidor_contrato_servidor.cod_contrato                                                                                                                     \n";
    $stSql .= "             , servidor.numcgm                                                                                                                                             \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                                                 \n";
    $stSql .= "             , pessoal.servidor                                                                                                                   \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                             \n";
    $stSql .= "         UNION                                                                                                                                                             \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                                                                           \n";
    $stSql .= "             , pensionista.numcgm                                                                                                                                          \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                                                       \n";
    $stSql .= "             , pessoal.pensionista                                                                                                                \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                                          \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                                                                   \n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor                                                                                                               \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor.cod_contrato                                                                                                           \n";
    $stSql .= "LEFT JOIN (SELECT cargo.descricao as desc_funcao                                                                                                                           \n";
    $stSql .= "                , contrato_servidor_funcao.cod_contrato                                                                                                                    \n";
    $stSql .= "             FROM pessoal.contrato_servidor_funcao                                                                                                \n";
    $stSql .= "                , (SELECT cod_contrato                                                                                                                                     \n";
    $stSql .= "                        , max(timestamp) as timestamp                                                                                                                      \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_funcao                                                                                        \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                  \n";
    $stSql .= "                , pessoal.cargo                                                                                                                   \n";
    $stSql .= "            WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                        \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                                                              \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo) as contrato_servidor_funcao                                                                        \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_cargo.cod_contrato                                                                                                       \n";
    $stSql .= "                , especialidade.descricao as desc_especialidade_cargo                                                                                                      \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_cargo                                                                                   \n";
    $stSql .= "                , pessoal.especialidade                                                                                                           \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_cargo                      \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                                                       \n";
    $stSql .= "LEFT JOIN (SELECT especialidade.descricao as desc_especialidade_funcao                                                                                                     \n";
    $stSql .= "             , contrato_servidor_especialidade_funcao.cod_contrato                                                                                                         \n";
    $stSql .= "          FROM pessoal.contrato_servidor_especialidade_funcao                                                                                     \n";
    $stSql .= "             , (SELECT cod_contrato                                                                                                                                        \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                                                                         \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                                             \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                                       \n";
    $stSql .= "             , pessoal.especialidade                                                                                                              \n";
    $stSql .= "         WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                               \n";
    $stSql .= "           AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp                                                  \n";
    $stSql .= "           AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao                       \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                                      \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_orgao                                                                                                                        \n";
    $stSql .= "                , contrato_servidor_orgao.cod_contrato                                                                                                                     \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                                                 \n";
    $stSql .= "                , (SELECT cod_contrato                                                                                                                                     \n";
    $stSql .= "                        , max(timestamp) as timestamp                                                                                                                      \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_orgao                                                                                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                   \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                          \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp                                                                             \n";
    $stSql .= "            UNION                                                                                                                                                          \n";
    $stSql .= "           SELECT contrato_pensionista_orgao.cod_orgao                                                                                                                     \n";
    $stSql .= "                , contrato_pensionista_orgao.cod_contrato                                                                                                                  \n";
    $stSql .= "             FROM pessoal.contrato_pensionista_orgao                                                                                              \n";
    $stSql .= "                , (SELECT cod_contrato                                                                                                                                     \n";
    $stSql .= "                        , max(timestamp) as timestamp                                                                                                                      \n";
    $stSql .= "                     FROM pessoal.contrato_pensionista_orgao                                                                                      \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                                \n";
    $stSql .= "            WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                    \n";
    $stSql .= "              AND contrato_pensionista_orgao.timestamp    = max_contrato_pensionista_orgao.timestamp) as contrato_orgao                                                    \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_orgao.cod_contrato                                                                                                              \n";
    $stSql .= "     , sw_cgm                                                                                                                                                              \n";
    $stSql .= " WHERE contrato.cod_contrato = servidor.cod_contrato                                                                                                                       \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                                                                                                     \n";

    return $stSql;
}

}
