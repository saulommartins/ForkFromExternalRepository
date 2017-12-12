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
    * Classe de mapeamento da tabela diarias.tipo_diaria
    * Data de Criação: 11/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.09.01

    $Id: TDiariasTipoDiaria.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TDiariasTipoDiaria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDiariasTipoDiaria()
{
    parent::Persistente();
    $this->setTabela("diarias.tipo_diaria");

    $this->setCampoCod('cod_tipo');
    $this->setComplementoChave('timestamp');

    $this->AddCampo('cod_tipo' ,'sequence'     ,true  ,''    ,true,false);
    $this->AddCampo('vigencia' ,'date'         ,true  ,''    ,false,false);
    $this->AddCampo('nom_tipo' ,'varchar'      ,true  ,'50'  ,false,false);
    $this->AddCampo('valor'    ,'numeric'      ,true  ,'14,2',false,false);
    $this->AddCampo('cod_norma','integer'      ,true  ,''    ,false,'TNormasNorma');
    $this->AddCampo('timestamp','timestamp'    ,false ,''    ,true,true);
}

function montaRecuperaRelacionamento()
{
    $stSql.= "   SELECT tipo_diaria.cod_tipo                                        \n";
    $stSql.= "        , tipo_diaria.nom_tipo                                        \n";
    $stSql.= "        , tipo_diaria.valor                                           \n";
    $stSql.= "        , tipo_diaria.cod_norma                                       \n";
    $stSql.= "        , tipo_diaria.vigencia                                        \n";
    $stSql.= "        , tipo_diaria_despesa.cod_conta                               \n";
    $stSql.= "        , tipo_diaria_despesa.exercicio as exercicio_despesa          \n";
    $stSql.= "        , norma.*                                                     \n";
    $stSql.= "        , to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao_norma\n";
    $stSql.= "        , norma.descricao as norma_descricao                          \n";
    $stSql.= "        , norma.num_norma||'/'||norma.exercicio as num_norma_exercicio\n";
    $stSql.= "        , conta_despesa.cod_estrutural as mascara_classificacao       \n";
    $stSql.= "        , conta_despesa.descricao as descricao_despesa                \n";
    $stSql.= "     FROM diarias".Sessao::getEntidade().".tipo_diaria                \n";
    $stSql.= "LEFT JOIN diarias".Sessao::getEntidade().".tipo_diaria_despesa        \n";
    $stSql.= "       ON tipo_diaria.cod_tipo = tipo_diaria_despesa.cod_tipo         \n";
    $stSql.= "      AND tipo_diaria.timestamp = tipo_diaria_despesa.timestamp       \n";
    $stSql.= "LEFT JOIN orcamento.conta_despesa                                     \n";
    $stSql.= "       ON (tipo_diaria_despesa.cod_conta   = conta_despesa.cod_conta  \n";
    $stSql.= "      AND  tipo_diaria_despesa.exercicio   = conta_despesa.exercicio) \n";
    $stSql.= "        , normas.norma                                                \n";
    $stSql.= "    WHERE tipo_diaria.cod_norma = norma.cod_norma                     \n";
    $stSql.= "      AND tipo_diaria.timestamp = (SELECT MAX(timestamp)              \n";
    $stSql.= "                                     FROM diarias".Sessao::getEntidade().".tipo_diaria tp      \n";
    $stSql.= "                                    WHERE tp.cod_tipo = tipo_diaria.cod_tipo) \n";

    return $stSql;
}

function recuperaTipoDiaria(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTipoDiaria().$stCondicao;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoDiaria()
{
    $stSql = "SELECT cod_tipo
                   , nom_tipo
                   , valor
                   , cod_norma
                   , timestamp
                   , vigencia
                FROM diarias".Sessao::getEntidade().".tipo_diaria
               WHERE timestamp = (SELECT MAX(timestamp)
                                    FROM diarias".Sessao::getEntidade().".tipo_diaria tp
                                   WHERE tp.cod_tipo = tipo_diaria.cod_tipo)";

    return $stSql;
}

function recuperaTipoDiariaEmVigencia(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTipoDiariaEmVigencia().$stCondicao;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoDiariaEmVigencia()
{
    $stSql = "SELECT cod_tipo
                   , nom_tipo
                   , valor
                   , cod_norma
                   , timestamp
                   , vigencia
                FROM diarias".Sessao::getEntidade().".tipo_diaria
               WHERE timestamp = (SELECT MAX(timestamp)
                                    FROM diarias".Sessao::getEntidade().".tipo_diaria tp
                                   WHERE tp.cod_tipo = tipo_diaria.cod_tipo
                                     AND vigencia <= (SELECT dt_final
                                                        FROM folhapagamento.periodo_movimentacao
                                                       WHERE cod_periodo_movimentacao = (SELECT max(cod_periodo_movimentacao) FROM folhapagamento.periodo_movimentacao)))";

    return $stSql;
}
}
?>
