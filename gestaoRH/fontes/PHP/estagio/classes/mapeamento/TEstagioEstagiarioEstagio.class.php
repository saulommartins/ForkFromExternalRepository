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
    * Classe de mapeamento da tabela estagio.estagiario_estagio
    * Data de Criação: 05/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.01

    $Id: TEstagioEstagiarioEstagio.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEstagioEstagiarioEstagio extends Persistente
{
function TEstagioEstagiarioEstagio()
{
    parent::Persistente();
    $this->setTabela("estagio.estagiario_estagio");

    $this->setCampoCod('cod_estagio');
    $this->setComplementoChave('cgm_estagiario,cod_curso,cgm_instituicao_ensino');

    $this->AddCampo('cod_estagio'           ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('cgm_estagiario'        ,'integer' ,true  ,''    ,true,'TEstagioEstagiario','numcgm');
    $this->AddCampo('cod_curso'             ,'integer' ,true  ,''    ,true,'TEstagioCursoInstituicaoEnsino');
    $this->AddCampo('cgm_instituicao_ensino','integer' ,true  ,''    ,true,'TEstagioCursoInstituicaoEnsino','numcgm');
    $this->AddCampo('cod_orgao'             ,'integer' ,true  ,''    ,false,'TOrganogramaOrgao');
    $this->AddCampo('cod_grade'             ,'integer' ,true  ,''    ,false,'TPessoalGradeHorario');
    $this->AddCampo('vinculo_estagio'       ,'char'    ,true  ,'1'   ,false,false);
    $this->AddCampo('dt_inicio'             ,'date'    ,true  ,''    ,false,false);
    $this->AddCampo('dt_final'              ,'date'    ,true  ,''    ,false,false);
    $this->AddCampo('dt_renovacao'          ,'date'    ,false ,''    ,false,false);
    $this->AddCampo('funcao'                ,'varchar' ,true  ,'20'  ,false,false);
    $this->AddCampo('objetivos'             ,'text'    ,false ,''    ,false,false);
    $this->AddCampo('ano_semestre'          ,'varchar' ,true  ,'7'   ,false,false);
    $this->AddCampo('numero_estagio'        ,'varchar' ,true  ,'10'  ,false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT estagiario_estagio.*                                                 \n";
    $stSql .= "     , to_char(dt_inicio,'dd/mm/yyyy') as data_inicio                       \n";
    $stSql .= "     , to_char(dt_final,'dd/mm/yyyy') as data_final                         \n";
    $stSql .= "     , to_char(dt_renovacao,'dd/mm/yyyy') as data_renovacao                 \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                       \n";
    $stSql .= "     , ovw.orgao as cod_estrutural                                          \n";
    $stSql .= "     , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                 \n";
    $stSql .= "  FROM estagio.estagiario_estagio                                           \n";
    $stSql .= "     , sw_cgm                                                               \n";
    $stSql .= "     , organograma.orgao                                                    \n";
    $stSql .= "     , organograma.organograma                                              \n";
    $stSql .= "     , organograma.orgao_nivel                                              \n";
    $stSql .= "     , organograma.nivel                                                    \n";
    $stSql .= "     , organograma.vw_orgao_nivel as ovw                                    \n";
    $stSql .= " WHERE estagiario_estagio.cgm_estagiario = sw_cgm.numcgm                    \n";
    $stSql .= "   AND estagiario_estagio.cod_orgao = orgao.cod_orgao                       \n";
    $stSql .= "   AND organograma.cod_organograma = nivel.cod_organograma                  \n";
    $stSql .= "   AND nivel.cod_organograma       = orgao_nivel.cod_organograma            \n";
    $stSql .= "   AND nivel.cod_nivel             = orgao_nivel.cod_nivel                  \n";
    $stSql .= "   AND orgao_nivel.cod_orgao       = orgao.cod_orgao                        \n";
    $stSql .= "   AND orgao.cod_orgao             = ovw.cod_orgao                          \n";
    $stSql .= "   AND orgao_nivel.cod_organograma = ovw.cod_organograma                    \n";
    $stSql .= "   AND nivel.cod_nivel             = ovw.nivel                              \n";

    return $stSql;
}

function recuperaCgmDoCodigoEstagio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY sw_cgm.nom_cgm ";
    $stSql  = $this->montaRecuperaCgmDoCodigoEstagio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCgmDoCodigoEstagio()
{
    $stSql  = "SELECT estagiario_estagio.*                         \n";
    $stSql .= "     , sw_cgm.nom_cgm                               \n";
    $stSql .= "  FROM estagio.estagiario_estagio                   \n";
    $stSql .= "     , sw_cgm                                       \n";
    $stSql .= " WHERE estagiario_estagio.cgm_estagiario = sw_cgm.numcgm    \n";

    return $stSql;
}

function recuperaCursosVinculadosAEstagiario(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroupBy  = " GROUP BY estagiario_estagio.cod_curso ";
    $stGroupBy .= "        , estagiario_estagio.cgm_instituicao_ensino ";
    $stSql  = $this->montaRecuperaCursosVinculadosAEstagiario().$stFiltro.$stGroupBy.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCursosVinculadosAEstagiario()
{
    $stSql .= "SELECT estagiario_estagio.cod_curso                 \n";
    $stSql .= "     , estagiario_estagio.cgm_instituicao_ensino    \n";
    $stSql .= "  FROM estagio.estagiario_estagio                   \n";

    return $stSql;
}

function recuperaRelatorioPagamentoEstagiarios(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm ";
    $stSql  = $this->montaRecuperaRelatorioPagamentoEstagiarios().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioPagamentoEstagiarios()
{
    $stSql  = "   SELECT estagiario_estagio.numero_estagio                                                                                                \n";
    $stSql .= "        , to_char(dt_inicio,'dd/mm/yyyy') as data_inicio                                                                                  \n";
    $stSql .= "        , to_char(dt_final,'dd/mm/yyyy') as data_final                                                                                    \n";
    $stSql .= "        , to_char(dt_renovacao,'dd/mm/yyyy') as data_renovacao                                                                            \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                  \n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cpf                                                                                                            \n";
    $stSql .= "        , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) as cod_estrutural                                                                                                     \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                                                                 \n";
    $stSql .= "        , (SELECT nom_banco FROM monetario.banco WHERE banco.cod_banco = estagiario_estagio_conta.cod_banco) as nom_banco              \n";
    $stSql .= "        , (SELECT num_agencia FROM monetario.agencia WHERE agencia.cod_banco = estagiario_estagio_conta.cod_banco AND agencia.cod_agencia = estagiario_estagio_conta.cod_agencia) as num_agencia           \n";
    $stSql .= "        , (SELECT descricao FROM organograma.local WHERE local.cod_local = estagiario_estagio_local.cod_local) as descricao_local       \n";
    $stSql .= "        , estagiario_estagio_conta.num_conta                                                                                                  \n";
    $stSql .= "        , estagiario_estagio_conta.cod_banco                                                                                                      \n";
    $stSql .= "        , estagiario_estagio_local.cod_local                                                                                                          \n";
    $stSql .= "        , estagiario_estagio_bolsa.vl_bolsa                                                                                                           \n";
    $stSql .= "        , estagiario_estagio_bolsa.faltas                                                                                                             \n";
    $stSql .= "        , estagiario_vale_refeicao.vl_vale \n";
    $stSql .= "        , estagiario_vale_refeicao.vl_desconto \n";
    $stSql .=          $this->getDado("campo_join");
    $stSql .= "     FROM estagio.estagiario_estagio                                                                               \n";
    $stSql .= "LEFT JOIN (SELECT estagiario_estagio_bolsa.*\n";
    $stSql .= "             FROM estagio.estagiario_estagio_bolsa                                                                                                \n";
    $stSql .= "                , (  SELECT cod_estagio                                                                                \n";
    $stSql .= "                          , cod_curso                                                                                  \n";
    $stSql .= "                          , cgm_estagiario                                                                               \n";
    $stSql .= "                          , cgm_instituicao_ensino                                                                     \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                \n";
    $stSql .= "                       FROM estagio.estagiario_estagio_bolsa                                        \n";
    $stSql .= "                   GROUP BY cod_estagio                                                                                \n";
    $stSql .= "                          , cod_curso                                                                                  \n";
    $stSql .= "                          , cgm_estagiario                                                                             \n";
    $stSql .= "                          , cgm_instituicao_ensino) AS max_estagiario_estagio_bolsa                                    \n";
    $stSql .= "            WHERE estagiario_estagio_bolsa.cod_estagio            = max_estagiario_estagio_bolsa.cod_estagio           \n";
    $stSql .= "              AND estagiario_estagio_bolsa.cod_curso              = max_estagiario_estagio_bolsa.cod_curso             \n";
    $stSql .= "              AND estagiario_estagio_bolsa.cgm_estagiario         = max_estagiario_estagio_bolsa.cgm_estagiario        \n";
    $stSql .= "              AND estagiario_estagio_bolsa.cgm_instituicao_ensino = max_estagiario_estagio_bolsa.cgm_instituicao_ensino\n";
    $stSql .= "              AND estagiario_estagio_bolsa.timestamp = max_estagiario_estagio_bolsa.timestamp) AS estagiario_estagio_bolsa\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_bolsa.cod_estagio                                                           \n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario_estagio_bolsa.cgm_estagiario                                                             \n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_bolsa.cod_curso                                                               \n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_bolsa.cgm_instituicao_ensino                                             \n";

    $stSql .= "LEFT JOIN estagio.estagiario_vale_refeicao                                                                                                \n";
    $stSql .= "       ON estagiario_estagio_bolsa.cod_estagio = estagiario_vale_refeicao.cod_estagio                                                           \n";
    $stSql .= "      AND estagiario_estagio_bolsa.cgm_estagiario = estagiario_vale_refeicao.cgm_estagiario                                                             \n";
    $stSql .= "      AND estagiario_estagio_bolsa.cod_curso = estagiario_vale_refeicao.cod_curso                                                               \n";
    $stSql .= "      AND estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_vale_refeicao.cgm_instituicao_ensino                                     \n";

    $stSql .= "LEFT JOIN estagio.estagiario_estagio_conta                                                                                                \n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_conta.cod_estagio                                                           \n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario_estagio_conta.numcgm                                                             \n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_conta.cod_curso                                                               \n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_conta.cgm_instituicao_ensino                                     \n";

    $stSql .= "LEFT JOIN estagio.estagiario_estagio_local                                                                                                \n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_local.cod_estagio                                                           \n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario_estagio_local.numcgm                                                             \n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_local.cod_curso                                                               \n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_local.cgm_instituicao_ensino                                     \n";
    $stSql .=          $this->getDado("join");
    $stSql .= "        , estagio.curso_instituicao_ensino                                                                                                \n";
    $stSql .= "        , sw_cgm                                                                                                                          \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                \n";
    $stSql .= "        , organograma.orgao                                                                                                               \n";
    $stSql .= "        , organograma.orgao_nivel                                                                                                         \n";
    $stSql .= "    WHERE estagiario_estagio.cgm_estagiario = sw_cgm.numcgm                                                                               \n";
    $stSql .= "      AND estagiario_estagio.cod_curso = curso_instituicao_ensino.cod_curso                                                               \n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = curso_instituicao_ensino.numcgm                                                     \n";
    $stSql .= "      AND estagiario_estagio.cod_orgao = orgao.cod_orgao                                                                                  \n";
    $stSql .= "      AND orgao_nivel.cod_orgao       = orgao.cod_orgao                                                                                   \n";
    $stSql .= "      AND orgao_nivel.cod_nivel = publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao))         \n";
    $stSql .= "      AND sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                     \n";
    $stSql .=          $this->getDado("joinSimples");

    return $stSql;
}

#######################################################################################
#                                                                                     #
#           C O N S U L T A S   P A R A   R E M E S S A   B A N C A R I A             #
#                                                                                     #
#######################################################################################

function recuperaRemessaBancos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem=" ORDER BY nom_cgm ";}
    $obErro = $this->executaRecupera("montaRecuperaRemessaBancos",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaRemessaBancos()
{
    $stSql  = "\nSELECT *                                                                                                                                     ";
    $stSql .= "\n  FROM (                                                                                                                                     ";
    $stSql .= "\n               SELECT estagiario_estagio.*                                                                                                   ";
    $stSql .= "\n             , sw_cgm.nom_cgm                       ";
    $stSql .= "\n             , sw_cgm_pessoa_fisica.cpf                                                                                                      ";
    $stSql .= "\n             , (SELECT num_banco FROM monetario.banco where banco.cod_banco = estagiario_estagio_conta.cod_banco) AS num_banco               ";
    $stSql .= "\n             , estagiario_estagio_conta.cod_banco                                                                                            ";
    $stSql .= "\n             , (SELECT num_agencia                                                                                                           ";
    $stSql .= "\n                  FROM monetario.agencia                                                                                                     ";
    $stSql .= "\n                 WHERE agencia.cod_banco = estagiario_estagio_conta.cod_banco                                                                ";
    $stSql .= "\n                   AND agencia.cod_agencia = estagiario_estagio_conta.cod_agencia) AS num_agencia                                            ";
    $stSql .= "\n             , estagiario_estagio_conta.cod_agencia                                                                                          ";
    $stSql .= "\n             , estagiario_estagio_conta.num_conta                                                                                            ";
    $stSql .= "\n             , coalesce(estagiario_estagio_bolsa.vl_bolsa,0) AS proventos                                                                    ";
    $stSql .= "\n             , CASE WHEN estagiario_estagio_bolsa.faltas > 0                                                                                 ";
    $stSql .= "\n                    THEN (coalesce(estagiario_estagio_bolsa.vl_bolsa,0)/30)*estagiario_estagio_bolsa.faltas +                                ";
    $stSql .= "\n                         coalesce(estagiario_vale_refeicao.vl_desconto,0)                                                                    ";
    $stSql .= "\n                    ELSE coalesce(estagiario_vale_refeicao.vl_desconto,0)                                                                                                                   ";
    $stSql .= "\n               END AS descontos                                                                                                              ";

    if ( $this->getDado('nuPercentualPagar') != "") {
        $stSql .= "\n             , CASE WHEN estagiario_estagio_bolsa.faltas > 0                                                                             ";
        $stSql .= "\n                    THEN (                                                                                                               ";
        $stSql .= "\n                          (                                                                                                              ";
        $stSql .= "\n                            (                                                                                                            ";
        $stSql .= "\n                               coalesce(estagiario_estagio_bolsa.vl_bolsa,0)+coalesce(estagiario_vale_refeicao.vl_vale,0) -                                                           ";
        $stSql .= "\n                               ((coalesce(estagiario_estagio_bolsa.vl_bolsa,0)/30)*estagiario_estagio_bolsa.faltas) -                    ";
        $stSql .= "\n                               coalesce(estagiario_vale_refeicao.vl_desconto,0)                                                          ";
        $stSql .= "\n                            )                                                                                                            ";
        $stSql .= "\n                            * ".$this->getDado('nuPercentualPagar')."                                                                    ";
        $stSql .= "\n                          ) / 100                                                                                                        ";
        $stSql .= "\n                         )                                                                                                               ";
        $stSql .= "\n                    ELSE (                                                                                                               ";
        $stSql .= "\n                           (                                                                                                             ";
        $stSql .= "\n                             coalesce(estagiario_estagio_bolsa.vl_bolsa,0)+coalesce(estagiario_vale_refeicao.vl_vale,0) -                                                             ";
        $stSql .= "\n                             coalesce(estagiario_vale_refeicao.vl_desconto,0)                                                            ";
        $stSql .= "\n                           ) * ".$this->getDado('nuPercentualPagar')."                                                                   ";
        $stSql .= "\n                         ) / 100                                                                                                         ";
        $stSql .= "\n               END AS liquido                                                                                                            ";
    } else {
        $stSql .= "\n             , CASE WHEN estagiario_estagio_bolsa.faltas > 0                                                                             ";
        $stSql .= "\n                    THEN coalesce(estagiario_estagio_bolsa.vl_bolsa,0) -                                                                 ";
        $stSql .= "\n                         ((coalesce(estagiario_estagio_bolsa.vl_bolsa,0)/30)*estagiario_estagio_bolsa.faltas) -                          ";
        $stSql .= "\n                         coalesce(estagiario_vale_refeicao.vl_desconto,0)                                                                ";
        $stSql .= "\n                    ELSE coalesce(estagiario_estagio_bolsa.vl_bolsa,0) -                                                                 ";
        $stSql .= "\n                         coalesce(estagiario_vale_refeicao.vl_desconto,0)                                                                ";
        $stSql .= "\n               END AS liquido                                                                                                            ";
    }

    $stSql .= "\n             , estagiario_estagio_bolsa.faltas                                                                                               ";
    $stSql .= "\n             , estagiario_estagio_local.cod_local                                                                                            ";
    $stSql .= "\n          FROM estagio.estagiario_estagio                                                                                                    ";

    if ($this->getDado('stTipoFiltro') == "atributo_estagiario") {

        $arValoresFiltro = explode("#",$this->getDado('stValoresFiltro'));

        $stSql .= "\nINNER JOIN estagio.atributo_estagiario_estagio                                                                                           ";
        $stSql .= "\n        ON estagiario_estagio.cod_estagio = atributo_estagiario_estagio.cod_estagio                                                                 ";
        $stSql .= "\n       AND estagiario_estagio.cgm_estagiario = atributo_estagiario_estagio.numcgm                                                                   ";
        $stSql .= "\n       AND estagiario_estagio.cod_curso = atributo_estagiario_estagio.cod_curso                                                                     ";
        $stSql .= "\n       AND estagiario_estagio.cgm_instituicao_ensino = atributo_estagiario_estagio.cgm_instituicao_ensino                                           ";
        $stSql .= "\n       AND atributo_estagiario_estagio.cod_atributo = ".$arValoresFiltro[1]."                                                            ";

        if ($arValoresFiltro[0] == 1) {
            $stSql .= "\n       AND atributo_estagiario_estagio.valor IN (".$arValoresFiltro[2].")                                                            ";
        } else {
            $stSql .= "\n       AND atributo_estagiario_estagio.valor = '".$arValoresFiltro[2]."'                                                             ";
        }
    }

    $stSql .= "\n     LEFT JOIN estagio.estagiario_estagio_conta                                                                                              ";
    $stSql .= "\n            ON estagiario_estagio.cod_estagio = estagiario_estagio_conta.cod_estagio                                                         ";
    $stSql .= "\n           AND estagiario_estagio.cgm_estagiario = estagiario_estagio_conta.numcgm                                                           ";
    $stSql .= "\n           AND estagiario_estagio.cod_curso = estagiario_estagio_conta.cod_curso                                                             ";
    $stSql .= "\n           AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_conta.cgm_instituicao_ensino                                   ";
    $stSql .= "\n     LEFT JOIN estagio.estagiario_estagio_local                                                                                              ";
    $stSql .= "\n            ON estagiario_estagio.cod_estagio = estagiario_estagio_local.cod_estagio                                                         ";
    $stSql .= "\n           AND estagiario_estagio.cgm_estagiario = estagiario_estagio_local.numcgm                                                           ";
    $stSql .= "\n           AND estagiario_estagio.cod_curso = estagiario_estagio_local.cod_curso                                                             ";
    $stSql .= "\n           AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_local.cgm_instituicao_ensino                                   ";
    $stSql .= "\n     LEFT JOIN (SELECT estagiario_estagio_bolsa.*                                                                                            ";
    $stSql .= "\n                  FROM estagio.estagiario_estagio_bolsa                                                                                      ";
    $stSql .= "\n                     , (  SELECT cod_estagio                                                                                                 ";
    $stSql .= "\n                               , cod_curso                                                                                                   ";
    $stSql .= "\n                               , cgm_estagiario                                                                                              ";
    $stSql .= "\n                               , cgm_instituicao_ensino                                                                                      ";
    $stSql .= "\n                               , max(timestamp) as timestamp                                                                                 ";
    $stSql .= "\n                            FROM estagio.estagiario_estagio_bolsa                                                                            ";
    $stSql .= "\n                        GROUP BY cod_estagio                                                                                                 ";
    $stSql .= "\n                               , cod_curso                                                                                                   ";
    $stSql .= "\n                               , cgm_estagiario                                                                                              ";
    $stSql .= "\n                               , cgm_instituicao_ensino) AS max_estagiario_estagio_bolsa                                                     ";
    $stSql .= "\n                 WHERE estagiario_estagio_bolsa.cod_estagio            = max_estagiario_estagio_bolsa.cod_estagio                            ";
    $stSql .= "\n                   AND estagiario_estagio_bolsa.cod_curso              = max_estagiario_estagio_bolsa.cod_curso                              ";
    $stSql .= "\n                   AND estagiario_estagio_bolsa.cgm_estagiario         = max_estagiario_estagio_bolsa.cgm_estagiario                         ";
    $stSql .= "\n                   AND estagiario_estagio_bolsa.cgm_instituicao_ensino = max_estagiario_estagio_bolsa.cgm_instituicao_ensino                 ";
    $stSql .= "\n                   AND estagiario_estagio_bolsa.timestamp = max_estagiario_estagio_bolsa.timestamp) AS estagiario_estagio_bolsa              ";
    $stSql .= "\n            ON estagiario_estagio.cod_estagio = estagiario_estagio_bolsa.cod_estagio                                                         ";
    $stSql .= "\n           AND estagiario_estagio.cgm_estagiario = estagiario_estagio_bolsa.cgm_estagiario                                                   ";
    $stSql .= "\n           AND estagiario_estagio.cod_curso = estagiario_estagio_bolsa.cod_curso                                                             ";
    $stSql .= "\n           AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_bolsa.cgm_instituicao_ensino                                   ";
    $stSql .= "\n     LEFT JOIN estagio.estagiario_vale_refeicao                                                                                              ";
    $stSql .= "\n            ON estagiario_vale_refeicao.cod_estagio = estagiario_estagio_bolsa.cod_estagio                                                   ";
    $stSql .= "\n           AND estagiario_vale_refeicao.cod_curso = estagiario_estagio_bolsa.cod_curso                                                       ";
    $stSql .= "\n           AND estagiario_vale_refeicao.cgm_estagiario = estagiario_estagio_bolsa.cgm_estagiario                                             ";
    $stSql .= "\n           AND estagiario_vale_refeicao.cgm_instituicao_ensino = estagiario_estagio_bolsa.cgm_instituicao_ensino                             ";
    $stSql .= "\n           AND estagiario_vale_refeicao.timestamp = estagiario_estagio_bolsa.timestamp                                                       ";
    $stSql .= "\n             , estagio.curso_instituicao_ensino                                                                                              ";
    $stSql .= "\n             , sw_cgm                                                                                                                        ";
    $stSql .= "\n             , sw_cgm_pessoa_fisica                                                                                                          ";
    $stSql .= "\n         WHERE estagiario_estagio.cgm_estagiario = sw_cgm.numcgm                                                                             ";
    $stSql .= "\n           AND sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                   ";
    $stSql .= "\n           AND estagiario_estagio.cod_curso = curso_instituicao_ensino.cod_curso                                                             ";
    $stSql .= "\n           AND estagiario_estagio.cgm_instituicao_ensino = curso_instituicao_ensino.numcgm                                                   ";
    $stSql .= "\n           AND (estagiario_estagio.dt_final IS NULL OR estagiario_estagio.dt_final >= ( SELECT dt_inicial                                    ";
    $stSql .= "\n                                                                                          FROM folhapagamento.periodo_movimentacao           ";
    $stSql .= "\n                                                                                         WHERE cod_periodo_movimentacao = ".$this->getDado('inCodPeriodoMovimentacao');
    $stSql .= "\n                                                                                      ))                                                     ";
    $stSql .= "\n      ) AS remessa                                                                                                                           ";

    $stSqlFiltro = "";

    if ($this->getDado('inCodBanco') != "") {
        //Se for passado apenas um ID executa id, senão executa else onde os id's são inseridos dentro do IN
        if (is_numeric($this->getDado('inCodBanco'))) {
            $stSqlFiltro .= " AND remessa.cod_banco = ".$this->getDado('inCodBanco');
        } else {
            $stSqlFiltro .= " AND remessa.cod_banco IN (".$this->getDado('inCodBanco').")";
        }
    }

    if ($this->getDado('nuLiquidoMinimo') != "" && $this->getDado('nuLiquidoMaximo') != "") {
        $stSqlFiltro .= " AND (remessa.proventos - remessa.descontos) BETWEEN ".$this->getDado('nuLiquidoMinimo')." AND ".$this->getDado('nuLiquidoMaximo');
    }

    $stSqlFiltro .= " AND remessa.liquido > 0 ";

    $stSql .= " WHERE ".substr($stSqlFiltro, 4);

    return $stSql;
}


function recuperaRelatorioCadastroEstagiarios(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm ";
    $stSql  = $this->montaRecuperaRelatorioCadastroEstagiarios().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRelatorioCadastroEstagiarios()
{
    $stSql  = "   SELECT estagiario.numcgm as numcgm_estagiario  												 			\n";
    $stSql .= " 	   , estagiario.nom_pai																	 	 			\n";
    $stSql .= "	       , estagiario.nom_mae																		 			\n";
    $stSql .= "	       , sw_cgm_pessoa_fisica.cpf  																 			\n";
    $stSql .= "	       , sw_cgm_pessoa_fisica.rg																 			\n";
    $stSql .= " 	   , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nascimento          	 			\n";
    $stSql .= " 	   , sw_cgm.nom_cgm as nom_cgm_estagiario													 			\n";
    $stSql .= "	       , sw_cgm.logradouro																		 			\n";
    $stSql .= " 	   , sw_cgm.numero																			 			\n";
    $stSql .= "	       , sw_cgm.complemento             														 			\n";
    $stSql .= "	       , sw_cgm.bairro																			 			\n";
    $stSql .= "	       , sw_cgm.cep						 														 			\n";
    $stSql .= "	       , sw_cgm.fone_residencial																 			\n";
    $stSql .= " 	   , sw_cgm.fone_celular																	 			\n";
    $stSql .= " 	   , sw_municipio.nom_municipio    															 			\n";
    $stSql .= " 	   , sw_uf.sigla_uf    																		 			\n";
    $stSql .= " 	   , to_char(estagiario_estagio.dt_inicio,'dd/mm/yyyy') as dt_inicio   						 			\n";
    $stSql .= " 	   , to_char(estagiario_estagio.dt_final,'dd/mm/yyyy') as dt_final   						 			\n";
    $stSql .= " 	   , to_char(estagiario_estagio.dt_renovacao,'dd/mm/yyyy') as dt_renovacao					 			\n";
    $stSql .= " 	   , estagiario_estagio.cod_estagio as codigo_estagio 										 			\n";
    $stSql .= "		   , estagiario_estagio.cod_curso as codigo_curso											 			\n";
    $stSql .= "		   , estagiario_estagio.funcao																 			\n";
    $stSql .= "		   , estagiario_estagio.numero_estagio														 			\n";
    $stSql .= "		   , estagiario_estagio.objetivos															 			\n";
    $stSql .= "		   , estagiario_estagio.ano_semestre  														 			\n";
    $stSql .= "		   , estagiario_estagio.cod_grade															 			\n";
    $stSql .= "		   , estagiario_estagio.cgm_instituicao_ensino															\n";
    $stSql .= "            , estagiario_estagio_bolsa.vl_bolsa                                                                                                                  \n";
    $stSql .= "            , estagiario_estagio_bolsa.faltas                                                                                                                  \n";
    $stSql .= "		   , (SELECT nom_cgm 																		 			\n";
    $stSql .= "   	        FROM sw_cgm 																		 			\n";
    $stSql .= "    		   WHERE numcgm = instituicao_ensino.numcgm) as nom_cgm_instituicao_ensino   			 			\n";
    $stSql .= " 	   , (SELECT descricao 																		 			\n";
    $stSql .= "			    FROM organograma.local 																 			\n";
    $stSql .= "			   WHERE cod_local = estagiario_estagio_local.cod_local) as local						 			\n";
    $stSql .= "		   , (SELECT nom_cgm 																		 			\n";
    $stSql .= "			    FROM sw_cgm 																		 			\n";
    $stSql .= "			   WHERE numcgm = entidade_intermediadora_estagio.cgm_entidade) as nom_cgm_entidade 				\n";
    $stSql .= "		   , (SELECT nom_agencia||' - '||num_agencia 												 			\n";
    $stSql .= "			    FROM monetario.agencia 																 			\n";
    $stSql .= "			   WHERE cod_banco = estagiario_estagio_conta.cod_banco 								 			\n";
    $stSql .= "			     AND cod_agencia = estagiario_estagio_conta.cod_agencia) as num_nom_agencia			 			\n";
    $stSql .= "		   , (SELECT nom_banco 																		 			\n";
    $stSql .= "			    FROM monetario.banco 																 			\n";
    $stSql .= "			   WHERE cod_banco = agencia.cod_banco) as nom_banco 									 			\n";
    $stSql .= " 	   , (SELECT num_conta 																		 			\n";
    $stSql .= "			    FROM estagio.estagiario_estagio_conta 												 			\n";
    $stSql .= "			   WHERE cod_estagio = estagiario_estagio.cod_estagio									 			\n";
    $stSql .= " 		     AND numcgm = estagiario_estagio.cgm_estagiario										 			\n";
    $stSql .= " 		     AND cod_curso = estagiario_estagio.cod_curso										 			\n";
    $stSql .= " 		     AND cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino) as num_conta			\n";
    $stSql .= " 	   , (SELECT descricao 																		 			\n";
    $stSql .= "		  	    FROM organograma.orgao 																 			\n";
    $stSql .= "			   WHERE cod_orgao = estagiario_estagio.cod_orgao) as nom_orgao							 			\n";
    $stSql .= " 	   , (SELECT nom_curso 																		 			\n";
    $stSql .= "			    FROM estagio.curso 																	 			\n";
    $stSql .= "	  		   WHERE cod_curso = curso_instituicao_ensino.cod_curso ) as nom_curso					 			\n";
    $stSql .= " 	   , (SELECT descricao 																		 			\n";
    $stSql .= "			    FROM estagio.grau 																	 			\n";
    $stSql .= "		  	   WHERE cod_grau = curso.cod_grau) as grau_curso 										 			\n";
    $stSql .= "        , (SELECT descricao                                                                       			\n";
    $stSql .= "	            FROM pessoal.grade_horario                                                           			\n";
    $stSql .= "	           WHERE grade_horario.cod_grade = estagiario_estagio.cod_grade) as carga_horaria        			\n";
    $stSql .= "		   , (SELECT descricao                                                                      	 		\n";
    $stSql .= "	            FROM administracao.mes                                                               			\n";
    $stSql .= "	               , estagio.curso_instituicao_ensino_mes                                            			\n";
    $stSql .= "	           WHERE curso_instituicao_ensino_mes.cod_curso = curso_instituicao_ensino.cod_curso     			\n";
    $stSql .= "  			 AND curso_instituicao_ensino_mes.numcgm = curso_instituicao_ensino.numcgm           			\n";
    $stSql .= "  			 AND mes.cod_mes = curso_instituicao_ensino_mes.cod_mes) as mes_avaliacao_estagio    			\n";
    $stSql .= "     FROM estagio.estagiario																					\n";
    $stSql .= "        , sw_cgm																					 			\n";
    $stSql .= "        , sw_cgm_pessoa_fisica  																				\n";
    $stSql .= "        , sw_municipio																						\n";
    $stSql .= "        , sw_uf																								\n";
    $stSql .= "        , estagio.estagiario_estagio																			\n";

    $stSql .= "LEFT JOIN (SELECT estagiario_estagio_bolsa.*\n";
    $stSql .= "             FROM estagio.estagiario_estagio_bolsa                                                                                                \n";
    $stSql .= "                , (  SELECT cod_estagio                                                                                \n";
    $stSql .= "                          , cod_curso                                                                                  \n";
    $stSql .= "                          , cgm_estagiario                                                                               \n";
    $stSql .= "                          , cgm_instituicao_ensino                                                                     \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                \n";
    $stSql .= "                       FROM estagio.estagiario_estagio_bolsa                                        \n";
    $stSql .= "                   GROUP BY cod_estagio                                                                                \n";
    $stSql .= "                          , cod_curso                                                                                  \n";
    $stSql .= "                          , cgm_estagiario                                                                             \n";
    $stSql .= "                          , cgm_instituicao_ensino) AS max_estagiario_estagio_bolsa                                    \n";
    $stSql .= "            WHERE estagiario_estagio_bolsa.cod_estagio            = max_estagiario_estagio_bolsa.cod_estagio           \n";
    $stSql .= "              AND estagiario_estagio_bolsa.cod_curso              = max_estagiario_estagio_bolsa.cod_curso             \n";
    $stSql .= "              AND estagiario_estagio_bolsa.cgm_estagiario         = max_estagiario_estagio_bolsa.cgm_estagiario        \n";
    $stSql .= "              AND estagiario_estagio_bolsa.cgm_instituicao_ensino = max_estagiario_estagio_bolsa.cgm_instituicao_ensino\n";
    $stSql .= "              AND estagiario_estagio_bolsa.timestamp = max_estagiario_estagio_bolsa.timestamp) AS estagiario_estagio_bolsa\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_bolsa.cod_estagio                                                          \n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario  = estagiario_estagio_bolsa.cgm_estagiario                                                           \n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_bolsa.cod_curso                                                                      \n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_bolsa.cgm_instituicao_ensino            \n";

    $stSql .= "LEFT JOIN estagio.estagiario_estagio_local 																	\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_local.cod_estagio 								\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario  = estagiario_estagio_local.numcgm								\n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_local.cod_curso									\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_local.cgm_instituicao_ensino		\n";
    $stSql .= "LEFT JOIN estagio.entidade_intermediadora_estagio															\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = entidade_intermediadora_estagio.cod_estagio						\n";
    $stSql .= "      AND estagiario_estagio.cod_curso = entidade_intermediadora_estagio.cod_curso							\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = entidade_intermediadora_estagio.cgm_estagiario					\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = entidade_intermediadora_estagio.cgm_instituicao_ensino	\n";
    $stSql .= "LEFT JOIN estagio.estagiario_estagio_conta     																\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_conta.cod_estagio								\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario_estagio_conta.numcgm								\n";
    $stSql .= "	     AND estagiario_estagio.cod_curso = estagiario_estagio_conta.cod_curso									\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_conta.cgm_instituicao_ensino		\n";
    $stSql .= "LEFT JOIN monetario.agencia																					\n";
    $stSql .= "	      ON estagiario_estagio_conta.cod_agencia = agencia.cod_agencia 										\n";
    $stSql .= "	     AND estagiario_estagio_conta.cod_banco = agencia.cod_banco												\n";
    $stSql .= "        , estagio.curso_instituicao_ensino																	\n";
    $stSql .= "        , estagio.instituicao_ensino    																		\n";
    $stSql .= "        , estagio.grau																						\n";
    $stSql .= "        , estagio.curso																						\n";
    $stSql .= "    WHERE estagiario.numcgm = sw_cgm.numcgm																	\n";
    $stSql .= "      AND sw_cgm.numcgm  = sw_cgm_pessoa_fisica.numcgm														\n";
    $stSql .= "      AND sw_cgm.cod_municipio = sw_municipio.cod_municipio													\n";
    $stSql .= "      AND sw_cgm.cod_uf = sw_municipio.cod_uf																\n";
    $stSql .= "      AND sw_municipio.cod_uf = sw_uf.cod_uf																	\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario.numcgm												\n";
    $stSql .= "      AND estagiario_estagio.cod_curso = curso_instituicao_ensino.cod_curso									\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = curso_instituicao_ensino.numcgm						\n";
    $stSql .= "      AND curso_instituicao_ensino.numcgm = instituicao_ensino.numcgm										\n";
    $stSql .= "      AND curso.cod_curso = curso_instituicao_ensino.cod_curso												\n";
    $stSql .= "      AND grau.cod_grau = curso.cod_grau																		\n";

    return $stSql;
}

#######################################################################################
#                                                                                     #
#           C O N S U L T A S   P A R A   T R A N S P A R Ê N C I A                   #
#                                                                                     #
#######################################################################################

function recuperaExportacaoTransparencia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaExportacaoTransparencia().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExportacaoTransparencia()
{
    $stSql = "SELECT (select cod_entidade from orcamento.entidade where cod_entidade = ".$this->getDado('inCodEntidade') ." and exercicio = '".$this->getDado('exercicio') ."' ) as numero_entidade
                               , to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('cod_periodo_movimentacao') .")::date), 'mm/yyyy') as mes_ano
                               , estagiario_estagio.numero_estagio
                               , sw_cgm.nom_cgm
                               , to_char (estagiario_estagio.dt_inicio, 'ddmmyyyy') as data_inicio
                               , to_char (estagiario_estagio.dt_final, 'ddmmyyyy') as data_final
                               , to_char (estagiario_estagio.dt_renovacao, 'ddmmyyyy') as data_renovacao
                               , orgao_descricao.descricao AS descricao_lotacao
                               , local.cod_local AS descricao_local

                        FROM estagio.estagiario_estagio

                INNER JOIN organograma.orgao_descricao
                            ON orgao_descricao.cod_orgao = estagiario_estagio.cod_orgao
                          AND orgao_descricao.timestamp = (select max(timestamp) from organograma.orgao_descricao od
                                                                                   where od.cod_orgao = orgao_descricao.cod_orgao
                                                                                      and od.timestamp <= (select ultimotimestampperiodomovimentacao( ".$this->getDado('cod_periodo_movimentacao') ." , '".$this->getDado('entidade') ."'))::timestamp)

                  LEFT JOIN estagio.estagiario_estagio_local
                            ON estagiario_estagio_local.cod_estagio = estagiario_estagio.cod_estagio
                          AND estagiario_estagio_local.numcgm = estagiario_estagio.cgm_estagiario
                          AND estagiario_estagio_local.cod_curso = estagiario_estagio.cod_curso
                          AND estagiario_estagio_local.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino

                  LEFT JOIN organograma.local
                            ON local.cod_local = estagiario_estagio_local.cod_local

                INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = estagiario_estagio.cgm_estagiario

                INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

             WHERE estagiario_estagio.dt_inicio <= (SELECT  pega0DataFinalCompetenciaDoPeriodoMovimento(".$this->getDado("cod_periodo_movimentacao")."))::date

                  AND (estagiario_estagio.dt_final >= (SELECT pega0DataFinalCompetenciaDoPeriodoMovimento(".$this->getDado("cod_periodo_movimentacao")."))::date
                    OR estagiario_estagio.dt_final IS NULL)
         ORDER BY mes_ano";

    return $stSql;
}

}

?>
