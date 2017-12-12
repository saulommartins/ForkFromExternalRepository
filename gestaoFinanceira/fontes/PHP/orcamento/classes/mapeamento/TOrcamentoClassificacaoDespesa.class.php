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
    * Classe de mapeamento da tabela ORCAMENTO.CLASSIFICACAO_DESPESA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TOrcamentoClassificacaoDespesa.class.php 59612 2014-09-02 12:00:51Z gelson $
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.04
                    uc-02.01.12
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.CLASSIFICACAO_DESPESA
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoClassificacaoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoClassificacaoDespesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.classificacao_despesa');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_conta,cod_posicao');

    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('cod_conta','integer',true,'',true,true);
    $this->AddCampo('cod_classificacao','integer',true,'',false,false);
    $this->AddCampo('cod_posicao','integer',true,'',true,true);
}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                                                  ".$stQuebra;
    $stSql .= "     *,                                                                                  ".$stQuebra;
    $stSql .= "     publico.fn_mascarareduzida(mascara_classificacao) as mascara_classificacao_reduzida   ".$stQuebra;
    $stSql .= " FROM                                                                                    ".$stQuebra;
    $stSql .= "     orcamento.vw_classificacao_despesa                                                        ".$stQuebra;
    $stSql .= " WHERE                                                                                   ".$stQuebra;
    $stSql .= "     exercicio IS NOT NULL                                                               ".$stQuebra;

    return $stSql;
}

function recuperaDescricaoDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDescricaoDespesa().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDescricaoDespesa()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                     ".$stQuebra;
    $stSql .= "      classificacao_despesa.cod_classificacao,                              ".$stQuebra;
    $stSql .= "      conta_despesa.descricao,                                          ".$stQuebra;
    $stSql .= "      conta_despesa.cod_conta                                          ".$stQuebra;
    $stSql .= "  FROM                                                       ".$stQuebra;
    $stSql .= "      orcamento.classificacao_despesa,                    ".$stQuebra;
    $stSql .= "      orcamento.conta_despesa                             ".$stQuebra;
    $stSql .= "  WHERE                                                      ".$stQuebra;
    $stSql .= "          classificacao_despesa.cod_conta     = conta_despesa.cod_conta                     ".$stQuebra;
    $stSql .= "      AND classificacao_despesa.exercicio     = conta_despesa.exercicio                     ".$stQuebra;

    return $stSql;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorio()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     mascara_classificacao,                                      \n";
    $stSql .= "     descricao,                                                  \n";
    $stSql .= " CASE WHEN publico.fn_nivel(mascara_classificacao) > 6 THEN  \n";
    $stSql .= "     6                                                           \n";
    $stSql .= " ELSE                                                            \n";
    $stSql .= "     publico.fn_nivel(mascara_classificacao)                 \n";
    $stSql .= " END as nivel                                                    \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     orcamento.vw_classificacao_despesa                                \n";
    $stSql .= " WHERE exercicio = '" . $this->getDado("stExercicio") . "'         \n";

    return $stSql;
}

}
