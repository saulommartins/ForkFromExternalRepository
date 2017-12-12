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
    * Classe de mapeamento da tabela ORCAMENTO.CLASSIFICACAO_RECEITA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    $Id: TOrcamentoClassificacaoReceita.class.php 60010 2014-09-25 14:52:47Z evandro $

    * Casos de uso: uc-02.01.06
                    uc-02.01.27
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.CLASSIFICACAO_RECEITA
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoClassificacaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoClassificacaoReceita()
{
    parent::Persistente();
    $this->setTabela('orcamento.classificacao_receita');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_conta,cod_posicao,cod_tipo');

    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('cod_classificacao','integer',true,'',true,false);
    $this->AddCampo('cod_conta','integer',true,'',true,true);
    $this->AddCampo('cod_posicao','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                                               ".$stQuebra;
    $stSql .= " exercicio, cod_conta, cod_norma, trim(descricao) as descricao, cod_estrutural as mascara_classificacao  ".$stQuebra;
    $stSql .= " , publico.fn_mascarareduzida(cod_estrutural) as mascara_classificacao_reduzida         ".$stQuebra;
    $stSql .= " FROM                                                                                 ".$stQuebra;
    $stSql .= "     orcamento.conta_receita                                                      ".$stQuebra;
    $stSql .= " WHERE                                                                                ".$stQuebra;
    $stSql .= "     exercicio IS NOT NULL                                                            ".$stQuebra;

    return $stSql;
}

function recuperaDescricaoReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDescricaoReceita().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDescricaoReceita()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                     ".$stQuebra;
    $stSql .= "      CLR.cod_classificacao,                                 ".$stQuebra;
    $stSql .= "      CTR.descricao,                                         ".$stQuebra;
    $stSql .= "      CTR.cod_conta                                          ".$stQuebra;
    $stSql .= "  FROM                                                       ".$stQuebra;
    $stSql .= "      orcamento.classificacao_receita AS CLR,            ".$stQuebra;
    $stSql .= "      orcamento.conta_receita         AS CTR             ".$stQuebra;
    $stSql .= "  WHERE                                                      ".$stQuebra;
    $stSql .= "          CTR.cod_conta     = CLR.cod_conta                  ".$stQuebra;
    $stSql .= "      AND CTR.exercicio     = CLR.exercicio                  ".$stQuebra;

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
    $stSql .= "     orcamento.vw_classificacao_receita                                \n";
    $stSql .= " WHERE exercicio = '" . $this->getDado("stExercicio") . "'         \n";

    return $stSql;
}

function recuperaReceitaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaAnalitica().$stCondicao.$stOrdem;        
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaAnalitica()
{
    $stSql  = " SELECT                                                                               
                        exercicio
                        , cod_conta
                        , cod_norma
                        , trim(descricao) as descricao
                        , cod_estrutural as mascara_classificacao  
                        , publico.fn_mascarareduzida(cod_estrutural) as mascara_classificacao_reduzida
                        ,orcamento.fn_tipo_conta_receita(conta_receita.exercicio, conta_receita.cod_estrutural) as tipo_nivel_conta         
                    FROM orcamento.conta_receita 
                    WHERE exercicio IS NOT NULL                   
    ";

    return $stSql;
}


}
