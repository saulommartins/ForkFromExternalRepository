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

/*
    * Classe de mapeamento da tabela tcmgo.nota_fiscal_empenho
    * Data de Criação   : 10/02/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCEPBNotaFiscalEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEPBNotaFiscalEmpenho()
{
    parent::Persistente();
    $this->setTabela("tcepb.nota_fiscal");

    $this->setCampoCod('cod_nota');
    $this->setComplementoChave('');

    $this->AddCampo( 'cod_nota'           , 'integer' , true  , ''     , true  , true   );
    $this->AddCampo( 'cod_nota_liquidacao', 'integer' , true  , ''     , true  , true   );
    $this->AddCampo( 'exercicio'          , 'char'    , true  , '4'    , true  , true   );
    $this->AddCampo( 'cod_entidade'       , 'integer' , true  , ''     , true  , true   );

}

function recuperaTodos(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTodos().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT cod_nota                                               \n";
    $stSql .= "      , exercicio                                              \n";
    $stSql .= "      , cod_entidade                                           \n";
  //  $stSql .= "      , cod_empenho                                            \n";
  //  $stSql .= "      , publico.fn_numeric_br(vl_associado) as vl_associado    \n";
    $stSql .= "   FROM  tcepb.nota_fiscal                                      \n";

    return $stSql;
}

function totalLiquidacaoEmpenho(&$rsRecordSet, $stFiltro ="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaTotalLiquidacaoEmpenho().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaTotalLiquidacaoEmpenho()
{
 $stSql  = " SELECT COALESCE(empenho.fn_consultar_valor_liquidado('". $this->getDado('exercicio')."',
                                                                                ".$this->getDado('cod_empenho').",
                                                                               ".$this->getDado('cod_entidade')."),0)  -
                    COALESCE(empenho.fn_consultar_valor_liquidado_anulado('". $this->getDado('exercicio')."',
                                                                           ".$this->getDado('cod_empenho').",
                                                                           ".$this->getDado('cod_entidade')."),0) as total ";

 return  $stSql;

}

function montaLiquidacaoEmpenho()
{
    $stSql  = "     SELECT empenho.nota_liquidacao.cod_nota                                                  \n";
    $stSql .= "             ,to_char(empenho.nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao    \n";
    $stSql .= "      FROM   empenho.nota_liquidacao                                                          \n";
    $stSql .= "             WHERE NOT EXISTS ( SELECT 1                                                      \n";
    $stSql .= "                                  FROM tcepb.nota_fiscal                                      \n";
    $stSql .= "      WHERE nota_liquidacao.cod_nota     = nota_fiscal.cod_nota_liquidacao                    \n";
    $stSql .= "       AND nota_liquidacao.cod_entidade = nota_fiscal.cod_entidade                            \n";
    $stSql .= "       AND nota_liquidacao.exercicio    = nota_fiscal.exercicio                               \n";
    $stSql .= "                  )                                                                           \n";

    return $stSql;

}

function liquidacaoEmpenho(&$rsRecordSet, $stFiltro, $stOrder)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaLiquidacaoEmpenho().$stFiltro.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

}

?>
