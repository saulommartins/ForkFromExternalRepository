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
    * Classe de mapeamento da tabela folhapagamento.evento_ferias_calculado
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32866 $
    $Name$
    $Author: alex $
    $Date: 2008-04-07 10:38:16 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-04.05.53
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.evento_ferias_calculado
  * Data de Criação: 19/06/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoFeriasCalculado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoFeriasCalculado()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.evento_ferias_calculado");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_registro,timestamp_registro,cod_evento,desdobramento');

    $this->AddCampo('cod_registro'          ,'integer'      ,true   ,''     ,true   ,'TFolhaPagamentoUltimoRegistroEventoFerias');
    $this->AddCampo('timestamp_registro'    ,'timestamp'    ,false  ,''     ,false  ,'TFolhaPagamentoUltimoRegistroEventoFerias','timestamp');
    $this->AddCampo('cod_evento'            ,'integer'      ,true   ,''     ,true   ,'TFolhaPagamentoUltimoRegistroEventoFerias');
    $this->AddCampo('desdobramento'         ,'char',true,'1',true                   ,'TFolhaPagamentoUltimoRegistroEventoFerias');
    $this->AddCampo('valor'                 ,'numeric'      ,true   ,'15,2' ,false  ,false                                      );
    $this->AddCampo('quantidade'            ,'numeric'      ,true   ,'15,2' ,false  ,false                                      );
    $this->AddCampo('timestamp'             ,'timestamp'    ,false  ,''     ,true   ,false                                      );

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
    $stSql  = " SELECT evento_ferias_calculado.*                                                                \n";
    $stSql .= "     , evento.descricao                                                                          \n";
    $stSql .= "     , evento.codigo                                                                             \n";
    $stSql .= "     , evento.natureza                                                                           \n";
    $stSql .= "     , getDesdobramentoFerias(evento_ferias_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto      \n";
    $stSql .= "     , evento.descricao as nom_evento                                                            \n";
    $stSql .= "   FROM folhapagamento.registro_evento_ferias                                                    \n";
    $stSql .= "       , folhapagamento.ultimo_registro_evento_ferias                                            \n";
    $stSql .= "       , folhapagamento.evento_ferias_calculado                                                  \n";
    $stSql .= "       , folhapagamento.evento                                                                   \n";
    $stSql .= "   WHERE registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro        \n";
    $stSql .= "     AND registro_evento_ferias.cod_evento   = ultimo_registro_evento_ferias.cod_evento          \n";
    $stSql .= "     AND registro_evento_ferias.timestamp    = ultimo_registro_evento_ferias.timestamp           \n";
    $stSql .= "     AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento      \n";
    $stSql .= "     AND ultimo_registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro       \n";
    $stSql .= "     AND ultimo_registro_evento_ferias.cod_evento   = evento_ferias_calculado.cod_evento         \n";
    $stSql .= "     AND ultimo_registro_evento_ferias.timestamp    = evento_ferias_calculado.timestamp_registro \n";
    $stSql .= "     AND ultimo_registro_evento_ferias.desdobramento= evento_ferias_calculado.desdobramento      \n";
    $stSql .= "     AND evento_ferias_calculado.cod_evento = evento.cod_evento                                  \n";

    return $stSql;
}

function recuperaCalculoDoContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY ultimo_registro_evento_ferias.cod_registro ";
    $stSql = $this->montaRecuperaCalculoDoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculoDoContrato()
{
    $stSql  = "SELECT ultimo_registro_evento_ferias.*                                                      \n";
    $stSql .= "     , registro_evento_ferias.cod_contrato                                                  \n";
    $stSql .= "     , evento_ferias_calculado.*                                                            \n";
    $stSql .= "     , sw_cgm.numcgm                                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                       \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_ferias                                         \n";
    $stSql .= "     , folhapagamento.registro_evento_ferias                                                \n";
    $stSql .= "     , folhapagamento.evento_ferias_calculado                                               \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                   \n";
    $stSql .= "     , pessoal.servidor                                                                     \n";
    $stSql .= "     , sw_cgm                                                                               \n";
    $stSql .= " WHERE ultimo_registro_evento_ferias.cod_registro = registro_evento_ferias.cod_registro     \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_evento = registro_evento_ferias.cod_evento         \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.timestamp = registro_evento_ferias.timestamp           \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro    \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento        \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro \n";
    $stSql .= "   AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato        \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                      \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                      \n";

    return $stSql;
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
    $stSql .= "  FROM (SELECT registro_evento_ferias.cod_contrato                                                   \n";
    $stSql .= "             , registro_evento_ferias.cod_periodo_movimentacao                                       \n";
    $stSql .= "             , contrato.registro                                                                     \n";
    $stSql .= "             , sw_cgm.numcgm                                                                         \n";
    $stSql .= "             , sw_cgm.nom_cgm                                                                        \n";
    $stSql .= "          FROM folhapagamento.ultimo_registro_evento_ferias                                          \n";
    $stSql .= "             , folhapagamento.registro_evento_ferias                                                 \n";
    $stSql .= "             , folhapagamento.evento_ferias_calculado                                                \n";
    $stSql .= "             , pessoal.servidor_contrato_servidor                                                    \n";
    $stSql .= "             , pessoal.servidor                                                                      \n";
    $stSql .= "             , pessoal.contrato                                                                      \n";
    $stSql .= "             , sw_cgm                                                                                \n";
    $stSql .= "         WHERE ultimo_registro_evento_ferias.cod_registro = registro_evento_ferias.cod_registro      \n";
    $stSql .= "           AND ultimo_registro_evento_ferias.cod_evento = registro_evento_ferias.cod_evento          \n";
    $stSql .= "           AND ultimo_registro_evento_ferias.timestamp = registro_evento_ferias.timestamp            \n";
    $stSql .= "           AND ultimo_registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro     \n";
    $stSql .= "           AND ultimo_registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento         \n";
    $stSql .= "           AND ultimo_registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro  \n";
    $stSql .= "           AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato         \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                       \n";
    $stSql .= "           AND servidor.numcgm = sw_cgm.numcgm                                                       \n";
    $stSql .= "           AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                           \n";
    $stSql .= "      GROUP BY registro_evento_ferias.cod_contrato                                                   \n";
    $stSql .= "             , registro_evento_ferias.cod_periodo_movimentacao                                       \n";
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
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY evento_ferias_calculado.cod_evento ";
    $stSql = $this->montaRecuperaConsultaFichaFinanceira().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsultaFichaFinanceira()
{
    $stSql  = "SELECT evento_ferias_calculado.*                                                                 \n";
    $stSql .= "     , getDesdobramentoFerias(evento_ferias_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto      \n";
    $stSql .= "     , evento.natureza                                                                           \n";
    $stSql .= "     , evento.codigo                                                                             \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                       \n";
    $stSql .= "     , CASE evento.natureza                                                                      \n";
    $stSql .= "       WHEN 'P' THEN 'proventos'                                                                 \n";
    $stSql .= "       WHEN 'D' THEN 'descontos'                                                                 \n";
    $stSql .= "       WHEN 'B' THEN 'base'                                                                      \n";
    $stSql .= "       END as proventos_descontos                                                                \n";
    $stSql .= "     , registro_evento_ferias.cod_contrato                                                       \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_ferias                                              \n";
    $stSql .= "     , folhapagamento.registro_evento_ferias                                                     \n";
    $stSql .= "     , folhapagamento.evento_ferias_calculado                                                    \n";
    $stSql .= "     , folhapagamento.evento                                                                     \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                                   \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                                          \n";
    $stSql .= " WHERE ultimo_registro_evento_ferias.cod_registro = registro_evento_ferias.cod_registro          \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_evento   = registro_evento_ferias.cod_evento            \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.timestamp    = registro_evento_ferias.timestamp             \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.desdobramento= registro_evento_ferias.desdobramento         \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro         \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_evento   = evento_ferias_calculado.cod_evento           \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.timestamp    = evento_ferias_calculado.timestamp_registro   \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.desdobramento= evento_ferias_calculado.desdobramento        \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_evento   = evento.cod_evento                            \n";
    $stSql .= "   AND ultimo_registro_evento_ferias.cod_evento   = sequencia_calculo_evento.cod_evento            \n";
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
    $stSql  = "SELECT evento.cod_evento||'_'||evento_ferias_calculado.desdobramento as cod_evento                                                                      \n";
    $stSql .= "     , evento.codigo                                                                                                                                    \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                                                                              \n";
    $stSql .= "     , evento.natureza                                                                                                                                  \n";
    $stSql .= "     , evento_ferias_calculado.quantidade                                                                                                               \n";
    $stSql .= "     , evento_ferias_calculado.valor                                                                                                                    \n";
    $stSql .= "     , evento_ferias_calculado.desdobramento                                                                                                            \n";
    $stSql .= "     , contrato.cod_contrato                                                                                                                            \n";
    $stSql .= "     , contrato.registro                                                                                                                                \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                                                   \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                                                                    \n";
    $stSql .= "     , cod_orgao                                                                                                                                        \n";
    $stSql .= "  FROM folhapagamento.evento_ferias_calculado                                                                                  \n";
    $stSql .= "     , folhapagamento.registro_evento_ferias                                                                                   \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_ferias                                                                            \n";
    $stSql .= "     , folhapagamento.contrato_servidor_periodo                                                                                \n";
    $stSql .= "     , pessoal.contrato_servidor                                                                                               \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                 \n";
    $stSql .= "                , contrato_servidor_padrao.cod_padrao                                                                                                   \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                   \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                               \n";
    $stSql .= "            WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                     \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao                           \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_padrao.cod_contrato                                                                        \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                   \n";
    $stSql .= "                , especialidade.cod_cargo as cod_funcao                                                                                                 \n";
    $stSql .= "                , especialidade.cod_especialidade as cod_especialidade_funcao                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                               \n";
    $stSql .= "                , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                         \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                     \n";
    $stSql .= "                   GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao                          \n";
    $stSql .= "                , pessoal.especialidade                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                         \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp                            \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                          \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_cargo.cod_contrato                                                                                    \n";
    $stSql .= "                , especialidade.cod_cargo                                                                                                               \n";
    $stSql .= "                , especialidade.cod_especialidade                                                                                                       \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_cargo                                                                \n";
    $stSql .= "                , pessoal.especialidade                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_cargo   \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                           \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                                                                                                  \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                     \n";
    $stSql .= "                , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                    \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                 \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                    \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                \n";
    $stSql .= "                , organograma.orgao                                                                                                                     \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp                                                          \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao ) as contrato_servidor_orgao                                                   \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                         \n";
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
    $stSql .= "              ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                  \n";
    $stSql .= "     , pessoal.contrato                                                                                                                                 \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                                               \n";
    $stSql .= "     , pessoal.servidor                                                                                                                                 \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                                                                             \n";
    $stSql .= "     , sw_cgm                                                                                                                                           \n";
    $stSql .= "     , folhapagamento.evento                                                                                                                            \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                                                                                          \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                                                                                                 \n";
    $stSql .= " WHERE evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento                                                                           \n";
    $stSql .= "   AND evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                                                                       \n";
    $stSql .= "   AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                                                                    \n";
    $stSql .= "   AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento                                                                     \n";
    $stSql .= "   AND registro_evento_ferias.cod_evento = ultimo_registro_evento_ferias.cod_evento                                                                     \n";
    $stSql .= "   AND registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro                                                                 \n";
    $stSql .= "   AND registro_evento_ferias.timestamp  = ultimo_registro_evento_ferias.timestamp                                                                      \n";
    $stSql .= "   AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento                                                               \n";
    $stSql .= "   AND registro_evento_ferias.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao                                             \n";
    $stSql .= "   AND registro_evento_ferias.cod_contrato = contrato_servidor_periodo.cod_contrato                                                                     \n";
    $stSql .= "   AND contrato_servidor_periodo.cod_contrato = contrato_servidor.cod_contrato                                                                          \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato.cod_contrato                                                                                           \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                         \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                    \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                                      \n";
    $stSql .= "   AND registro_evento_ferias.cod_evento = evento.cod_evento                                                                                            \n";
    $stSql .= "   AND evento.cod_evento = sequencia_calculo_evento.cod_evento                                                                                          \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                                                                         \n";

    return $stSql;
}

function recuperaEventosCalculadosRais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEventosCalculadosRais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEventosCalculadosRais()
{
    $stSql  = "SELECT sum(evento_ferias_calculado.valor) as valor                                                     \n";
    $stSql .= "  FROM folhapagamento.registro_evento_ferias                          \n";
    $stSql .= "     , folhapagamento.evento_ferias_calculado                         \n";
    $stSql .= "     , folhapagamento.periodo_movimentacao                            \n";
    $stSql .= " WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro               \n";
    $stSql .= "   AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                   \n";
    $stSql .= "   AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento             \n";
    $stSql .= "   AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro            \n";
    $stSql .= "   AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao  \n";

    return $stSql;
}

function recuperaValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaValoresAcumuladosCalculo()
{
    $stSql = "select * from recuperaValoresAcumuladosCalculoFerias(
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
    $stSql = "select recuperaRotuloValoresAcumuladosCalculoFerias(
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
    $stSql .= "                         FROM folhapagamento.evento_ferias_calculado                        \n";
    $stSql .= "                   INNER JOIN folhapagamento.registro_evento_ferias                         \n";
    $stSql .= "                           ON registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro             \n";
    $stSql .= "                          AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                 \n";
    $stSql .= "                          AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento           \n";
    $stSql .= "                          AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro          \n";
    $stSql .= "                          AND registro_evento_ferias.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
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
        $stSql .= "                                            WHERE contrato_servidor_orgao.cod_contrato = registro_evento_ferias.cod_contrato)           \n";
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
        $stSql .= "                                            WHERE contrato_servidor_local.cod_contrato = registro_evento_ferias.cod_contrato)           \n";
    }
    $stSql .= "                        WHERE evento_ferias_calculado.cod_evento = evento.cod_evento                                        \n";
    $stSql .= "                        LIMIT 1)                                                                                     \n";

    return $stSql;
}

}
