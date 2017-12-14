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
    * Classe de MAPEAMENTO para MONETARIO.FORMULA_INDICADOR
    * Data de Criacao: 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TMONFormulaIndicador.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.07
*/

/*
$Log$
Revision 1.4  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONFormulaIndicador extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONFormulaIndicador()
{
    parent::Persistente();
    $this->setTabela('monetario.formula_indicador');

    $this->setCampoCod('cod_indicador');
    $this->setComplementoChave('inicio_vigencia');

    $this->AddCampo('cod_indicador','integer',true,'',true,false);
    $this->AddCampo('cod_funcao','integer',true,'',true,false);
    $this->AddCampo('cod_modulo','integer',true,'',false,false);
    $this->AddCampo('cod_biblioteca','integer',true,'',false,false);
    $this->AddCampo('inicio_vigencia','date',true,'',false,false);
}

/**
    * conjunto de funcao para recuperar os codigos da Formula referente
    * @access Public
    * @param  INTEGER Codigo do Acrescimo referente
    * @return SQL
*/
function montaRecuperaRelacionamentoDadosDaFormula($codigo)
{
    $stSql  = " SELECT                              \n";
    $stSql .= "     *                               \n";
    $stSql .= " FROM                                \n";
    $stSql .= "     monetario.formula_indicador     \n";
    $stSql .= " WHERE                               \n";
    $stSql .= "     cod_indicador = '$codigo'       \n";
    $stSql .= " ORDER BY                            \n";
    $stSql .= "     inicio_vigencia DESC            \n";
    $stSql .= " LIMIT 1                             \n";

return $stSql;
}
function RecuperaRelacionamentoDadosDaFormula(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $codigo)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoDadosDaFormula( $codigo );

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * conjunto de funcao para recuperar descricao da formula, na tabela da administracao
    * @access Public
    * @param  INTEGER CodModulo, CodBiblioteca e CodFuncao referente à formula
    * @return SQL
*/
function montaRecuperaRelacionamentoDescricaoDaFormula($codMod, $codBib, $codFunc)
{
    $stSql .= " SELECT                          \n";
    $stSql .= "     *                           \n";
    $stSql .= " FROM                            \n";
    $stSql .= "     administracao.funcao        \n";
    $stSql .= " WHERE                           \n";
    $stSql .= "     cod_modulo = '$codMod'      \n";
    $stSql .= " AND                             \n";
    $stSql .= "     cod_biblioteca = '$codBib'  \n";
    $stSql .= " AND                             \n";
    $stSql .= "     cod_funcao = '$codFunc'     \n";

return $stSql;
}
function RecuperaRelacionamentoDescricaoDaFormula(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $codMod, $codBib, $codFunc)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoDescricaoDaFormula( $codMod, $codBib, $codFunc );
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}// fecha classe de mapeamento
