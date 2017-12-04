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
    * Classe de mapeamento da tabela folhapagamento.evento_calculado_decimo
    * Data de Criação: 06/09/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.21

    $Id: TFolhaPagamentoEventoDecimoCalculado.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.evento_calculado_decimo
  * Data de Criação: 06/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoDecimoCalculado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoDecimoCalculado()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.evento_decimo_calculado");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,desdobramento,timestamp_registro');

    $this->AddCampo('cod_evento','integer',true,'',true             ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('cod_registro','integer',true,'',true           ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('desdobramento','char',true,'1',true            ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('timestamp_registro','timestamp',false,'',true  ,'TFolhaPagamentoUltimoRegistroEventoDecimo','timestamp');
    $this->AddCampo('valor','numeric',true,'15,2',false,false);
    $this->AddCampo('quantidade','numeric',true,'15,2',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaContratosCalculados(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql = $this->montaRecuperaContratosCalculados().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosCalculados()
{
    $stSql  = "SELECT *                                                                                             \n";
    $stSql .= "  FROM (SELECT registro_evento_decimo.cod_contrato                                                   \n";
    $stSql .= "             , registro_evento_decimo.cod_periodo_movimentacao                                       \n";
    $stSql .= "             , contrato.registro                                                                     \n";
    $stSql .= "             , sw_cgm.numcgm                                                                         \n";
    $stSql .= "             , sw_cgm.nom_cgm                                                                        \n";
    $stSql .= "          FROM folhapagamento.ultimo_registro_evento_decimo                                          \n";
    $stSql .= "             , folhapagamento.registro_evento_decimo                                                 \n";
    $stSql .= "             , folhapagamento.evento_decimo_calculado                                                \n";
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
    $stSql .= "                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor\n";
    $stSql .= "             , pessoal.contrato                                                                      \n";
    $stSql .= "             , sw_cgm                                                                                \n";
    $stSql .= "         WHERE ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo.cod_registro      \n";
    $stSql .= "           AND ultimo_registro_evento_decimo.cod_evento = registro_evento_decimo.cod_evento          \n";
    $stSql .= "           AND ultimo_registro_evento_decimo.timestamp = registro_evento_decimo.timestamp            \n";
    $stSql .= "           AND ultimo_registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro     \n";
    $stSql .= "           AND ultimo_registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento         \n";
    $stSql .= "           AND ultimo_registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro  \n";
    $stSql .= "           AND registro_evento_decimo.cod_contrato = servidor.cod_contrato         \n";
    $stSql .= "           AND servidor.numcgm = sw_cgm.numcgm                                                       \n";
    $stSql .= "           AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                           \n";
    $stSql .= "      GROUP BY registro_evento_decimo.cod_contrato                                                   \n";
    $stSql .= "             , registro_evento_decimo.cod_periodo_movimentacao                                       \n";
    $stSql .= "             , contrato.registro                                                                     \n";
    $stSql .= "             , sw_cgm.numcgm                                                                         \n";
    $stSql .= "             , sw_cgm.nom_cgm) as contratos                                                          \n";

    return $stSql;
}

function recuperaConsultaFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY evento_decimo_calculado.cod_evento ";
    $stSql = $this->montaRecuperaConsultaFichaFinanceira().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsultaFichaFinanceira()
{
    $stSql  = "SELECT evento_decimo_calculado.*                                                                 \n";
    $stSql .= "     , getDesdobramentoDecimo(evento_decimo_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto      \n";
    $stSql .= "     , evento.natureza                                                                           \n";
    $stSql .= "     , evento.codigo                                                                             \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                       \n";
    $stSql .= "     , CASE evento.natureza                                                                      \n";
    $stSql .= "       WHEN 'P' THEN 'proventos'                                                                 \n";
    $stSql .= "       WHEN 'D' THEN 'descontos'                                                                 \n";
    $stSql .= "       WHEN 'B' THEN 'base'                                                                      \n";
    $stSql .= "       END as proventos_descontos                                                                \n";
    $stSql .= "     , registro_evento_decimo.cod_contrato                                                       \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_decimo                                              \n";
    $stSql .= "     , folhapagamento.registro_evento_decimo                                                     \n";
    $stSql .= "     , folhapagamento.evento_decimo_calculado                                                    \n";
    $stSql .= "     , folhapagamento.evento                                                                     \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                                       \n";        $stSql .= "     , folhapagamento.sequencia_calculo                                                              \n";
    $stSql .= " WHERE ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo.cod_registro          \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento   = registro_evento_decimo.cod_evento            \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.timestamp    = registro_evento_decimo.timestamp             \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.desdobramento= registro_evento_decimo.desdobramento         \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro         \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento   = evento_decimo_calculado.cod_evento           \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.timestamp    = evento_decimo_calculado.timestamp_registro   \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.desdobramento= evento_decimo_calculado.desdobramento        \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento   = evento.cod_evento                            \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento   = sequencia_calculo_evento.cod_evento            \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                      \n";

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
    $stSql  = "SELECT evento.cod_evento||evento_decimo_calculado.desdobramento as cod_evento                                                                           \n";
    $stSql .= "     , evento.codigo                                                                                                                                    \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                                                                              \n";
    $stSql .= "     , evento.natureza                                                                                                                                  \n";
    $stSql .= "     , evento_decimo_calculado.quantidade                                                                                                               \n";
    $stSql .= "     , evento_decimo_calculado.valor                                                                                                                    \n";
    $stSql .= "     , evento_decimo_calculado.desdobramento                                                                                                            \n";
    $stSql .= "     , contrato.cod_contrato                                                                                                                            \n";
    $stSql .= "     , contrato.registro                                                                                                                                \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                                                   \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                                                                    \n";
    $stSql .= "     , cod_orgao                                                                                                                                        \n";
    $stSql .= "  FROM folhapagamento.evento_decimo_calculado                                                                                                           \n";
    $stSql .= "     , folhapagamento.registro_evento_decimo                                                                                                            \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_decimo                                                                                                     \n";
    $stSql .= "     , folhapagamento.contrato_servidor_periodo                                                                                                         \n";
    $stSql .= "     , (SELECT servidor_contrato_servidor.cod_contrato                                                                                                  \n";
    $stSql .= "             , servidor.numcgm                                                                                                                          \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                                                       \n";
    $stSql .= "             , pessoal.servidor                                                                                                                         \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                          \n";
    $stSql .= "         UNION                                                                                                                                          \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                                                        \n";
    $stSql .= "             , pensionista.numcgm                                                                                                                       \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                                                             \n";
    $stSql .= "             , pessoal.pensionista                                                                                                                      \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                       \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                                                \n";
    $stSql .= "     , pessoal.contrato                                                                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                 \n";
    $stSql .= "                , contrato_servidor_padrao.cod_padrao                                                                                                   \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                                                      \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                                            \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                               \n";
    $stSql .= "            WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                     \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao                           \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_padrao.cod_contrato                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                   \n";
    $stSql .= "                , especialidade.cod_cargo as cod_funcao                                                                                                 \n";
    $stSql .= "                , especialidade.cod_especialidade as cod_especialidade_funcao                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                        \n";
    $stSql .= "                , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                         \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                     \n";
    $stSql .= "                   GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao                          \n";
    $stSql .= "                , pessoal.especialidade                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                         \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp                            \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                   \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_cargo.cod_contrato                                                                                    \n";
    $stSql .= "                , especialidade.cod_cargo                                                                                                               \n";
    $stSql .= "                , especialidade.cod_especialidade                                                                                                       \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_cargo                                                                \n";
    $stSql .= "                , pessoal.especialidade                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_cargo   \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                                    \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                                                                                                  \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                     \n";
    $stSql .= "                , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                      \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                    \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                \n";
    $stSql .= "                , organograma.orgao                                                                                                                     \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp                                                          \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao ) as contrato_servidor_orgao                                                   \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                  \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                                                                     \n";
    $stSql .= "                , contrato_servidor_local.cod_contrato                                                                                                  \n";
    $stSql .= "                , local.descricao as descricao_local                                                                                                    \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                             \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                \n";
    $stSql .= "                , organograma.local                                                                                                                     \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp                                                          \n";
    $stSql .= "              AND contrato_servidor_local.cod_local    = local.cod_local) as contrato_servidor_local                                                    \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                                                                  \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                                                                             \n";
    $stSql .= "     , sw_cgm                                                                                                                                           \n";
    $stSql .= "     , folhapagamento.evento                                                                                                                            \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                                                                                          \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                                                                                                 \n";
    $stSql .= " WHERE evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento                                                                           \n";
    $stSql .= "   AND evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                                                                       \n";
    $stSql .= "   AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                                                                    \n";
    $stSql .= "   AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento                                                                     \n";
    $stSql .= "   AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                                                                     \n";
    $stSql .= "   AND registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro                                                                 \n";
    $stSql .= "   AND registro_evento_decimo.timestamp  = ultimo_registro_evento_decimo.timestamp                                                                      \n";
    $stSql .= "   AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento                                                               \n";
    $stSql .= "   AND registro_evento_decimo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                             \n";
    $stSql .= "   AND registro_evento_decimo.cod_contrato = contrato_servidor_periodo.cod_contrato                                                                     \n";
    $stSql .= "   AND contrato_servidor_periodo.cod_contrato = servidor.cod_contrato                                                                                   \n";
    $stSql .= "   AND servidor.cod_contrato = contrato.cod_contrato                                                                                                    \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                    \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                                      \n";
    $stSql .= "   AND registro_evento_decimo.cod_evento = evento.cod_evento                                                                                            \n";
    $stSql .= "   AND evento.cod_evento = sequencia_calculo_evento.cod_evento                                                                                          \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                                                                         \n";

    return $stSql;
}

function recuperaEventosDecimoCalculado(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY evento_decimo_calculado.cod_evento ";
    $stSql = $this->montaRecuperaEventosDecimoCalculado().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosDecimoCalculado()
{
    $stSql  = "SELECT evento_decimo_calculado.*                                                            \n";
    $stSql .= "     , evento.descricao                                                                     \n";
    $stSql .= "     , evento.codigo                                                                        \n";
    $stSql .= "     , evento.natureza                                                                      \n";
    $stSql .= "     , getDesdobramentoDecimo(evento_decimo_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto \n";
    $stSql .= "     , evento.descricao as nom_evento                                                       \n";
    $stSql .= "  FROM folhapagamento.evento_decimo_calculado                                               \n";
    $stSql .= "     , folhapagamento.registro_evento_decimo                                                \n";
    $stSql .= "     , folhapagamento.evento                                                                \n";
    $stSql .= " WHERE evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro           \n";
    $stSql .= "   AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento               \n";
    $stSql .= "   AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp        \n";
    $stSql .= "   AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento         \n";
    $stSql .= "   AND evento_decimo_calculado.cod_evento = evento.cod_evento                               \n";

    return $stSql;
}

function recuperaEventosDecimoCalculadoFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY evento_decimo_calculado.cod_evento ";
    $stSql = $this->montaRecuperaEventosDecimoCalculadoFichaFinanceira().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosDecimoCalculadoFichaFinanceira()
{
    $stSql  = "SELECT evento_decimo_calculado.*                                                             \n";
    $stSql .= "     , evento.descricao                                                                      \n";
    $stSql .= "     , evento.codigo                                                                         \n";
    $stSql .= "     , evento.natureza                                                                       \n";
    $stSql .= "     , sequencia_calculo.sequencia                                                    \n";
    $stSql .= "  FROM folhapagamento.evento_decimo_calculado                                                \n";
    $stSql .= "     , folhapagamento.registro_evento_decimo                                                 \n";
    $stSql .= "     , folhapagamento.evento                                                                 \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                               \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                                      \n";
    $stSql .= " WHERE evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro            \n";
    $stSql .= "   AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento                \n";
    $stSql .= "   AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp         \n";
    $stSql .= "   AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento          \n";
    $stSql .= "   AND evento_decimo_calculado.cod_evento = evento.cod_evento                                \n";
    $stSql .= "   AND evento_decimo_calculado.cod_evento = sequencia_calculo_evento.cod_evento              \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia              \n";

    return $stSql;
}

function recuperaEventosCalculadosRais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEventosCalculadosRais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEventosCalculadosRais()
{
    $stSql  = "SELECT sum(evento_decimo_calculado.valor) as valor                                                     \n";
    $stSql .= "  FROM folhapagamento.registro_evento_decimo                          \n";
    $stSql .= "     , folhapagamento.evento_decimo_calculado                         \n";
    $stSql .= "     , folhapagamento.periodo_movimentacao                            \n";
    $stSql .= " WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro               \n";
    $stSql .= "   AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                   \n";
    $stSql .= "   AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento             \n";
    $stSql .= "   AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro            \n";
    $stSql .= "   AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao  \n";

    return $stSql;
}

function recuperaValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaValoresAcumuladosCalculo()
{
    $stSql = "select * from recuperaValoresAcumuladosCalculoDecimo(
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
    $stSql = "select recuperaRotuloValoresAcumuladosCalculoDecimo(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    ) as rotulo";

    return $stSql;
}

function recuperaEventosCalculadosAutorizacaoEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $stOrdem = ($stOrdem != "") ? $stOrdem : " ORDER BY descricao";
    $obErro = $this->executaRecupera("montaRecuperaEventosCalculadosAutorizacaoEmpenho",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function montaRecuperaEventosCalculadosAutorizacaoEmpenho()
{
    $stSql  = "    SELECT evento.codigo                                                                                             \n";
    $stSql .= "         , evento.descricao                                                                                          \n";
    $stSql .= "         , evento.cod_evento                                                                                         \n";
    $stSql .= "      FROM folhapagamento.evento                                                            \n";
    $stSql .= "     WHERE EXISTS (    SELECT 1                                                                                      \n";
    $stSql .= "                         FROM folhapagamento.evento_decimo_calculado                        \n";
    $stSql .= "                   INNER JOIN folhapagamento.registro_evento_decimo                         \n";
    $stSql .= "                           ON registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro             \n";
    $stSql .= "                          AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                 \n";
    $stSql .= "                          AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento           \n";
    $stSql .= "                          AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro          \n";
    $stSql .= "                          AND registro_evento_decimo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
    if (trim($this->getDado("cod_orgao")) != "") {
        $stSql .= "                          AND EXISTS (     SELECT contrato_servidor_orgao.*                                                              \n";
        $stSql .= "                                             FROM pessoal.contrato_servidor_orgao                               \n";
        $stSql .= "                                       INNER JOIN (  SELECT cod_contrato                                                                 \n";
        $stSql .= "                                                          , max(timestamp) as timestamp                                                  \n";
        $stSql .= "                                                       FROM pessoal.contrato_servidor_orgao                     \n";
        $stSql .= "                                                      WHERE contrato_servidor_orgao.timestamp::date <= '".$this->getDado("vigencia")."'  \n";
        $stSql .= "                                                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                 \n";
        $stSql .= "                                               ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato        \n";
        $stSql .= "                                              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp              \n";
        $stSql .= "                                              AND contrato_servidor_orgao.cod_orgao = ".$this->getDado("cod_orgao")."                    \n";
        $stSql .= "                                            WHERE contrato_servidor_orgao.cod_contrato = registro_evento_decimo.cod_contrato)           \n";
    }
    if (trim($this->getDado("cod_local")) != "") {
        $stSql .= "                          AND EXISTS (     SELECT contrato_servidor_local.*                                                              \n";
        $stSql .= "                                             FROM pessoal.contrato_servidor_local                               \n";
        $stSql .= "                                       INNER JOIN (  SELECT cod_contrato                                                                 \n";
        $stSql .= "                                                          , max(timestamp) as timestamp                                                  \n";
        $stSql .= "                                                       FROM pessoal.contrato_servidor_local                     \n";
        $stSql .= "                                                   GROUP BY cod_contrato) as max_contrato_servidor_local                                 \n";
        $stSql .= "                                               ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato        \n";
        $stSql .= "                                              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp              \n";
        $stSql .= "                                              AND contrato_servidor_local.cod_local = ".$this->getDado("cod_local")."                    \n";
        $stSql .= "                                            WHERE contrato_servidor_local.cod_contrato = registro_evento_decimo.cod_contrato)           \n";
    }
    $stSql .= "                        WHERE evento_decimo_calculado.cod_evento = evento.cod_evento                                        \n";
    $stSql .= "                        LIMIT 1)                                                                                     \n";

    return $stSql;
}

}
