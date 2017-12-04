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
    * Classe de MAPEAMENTO para MONETARIO.FORMULA_ACRESCIMO
    * Data de Criacao: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TMONFormulaAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.11
*/

/*
$Log$
Revision 1.9  2006/11/23 16:39:56  cercato
bug #7613#

Revision 1.8  2006/11/22 17:54:15  cercato
bug #7576#

Revision 1.7  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONFormulaAcrescimo extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONFormulaAcrescimo()
{
    parent::Persistente();
    $this->setTabela('monetario.formula_acrescimo');

    $this->setCampoCod('cod_acrescimo');
    $this->setComplementoChave('cod_tipo,cod_funcao,cod_modulo,cod_biblioteca,timestamp');

    $this->AddCampo('cod_acrescimo','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_funcao','integer',true,'',false,true);
    $this->AddCampo('cod_modulo','integer',true,'',false,true);
    $this->AddCampo('cod_biblioteca','integer',true,'',false,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
}

/**
    * conjunto de funcao para recuperar a data de vigencia mais recente referente ao acrescimo
    * @access Public
    * @param  INTEGER Codigo do Acrescimo referente
    * @return SQL
*/
function montaRecuperaRelacionamentoUltimaVigenciaDoAcrescimo($codigoAcrescimo)
{
    $stSql  = " SELECT                                  \n";
    $stSql .= "     timestamp                     \n";
    $stSql .= " FROM                                    \n";
    $stSql .= "     monetario.formula_acrescimo         \n";
    $stSql .= " WHERE                                   \n";
    $stSql .= "     cod_acrescimo = '$codigoAcrescimo'  \n";
    $stSql .= " ORDER BY                                \n";
    $stSql .= "     timestamp DESC                \n";
    $stSql .= " LIMIT 1                                 \n";

return $stSql;

}
function RecuperaRelacionamentoUltimaVigenciaDoAcrescimo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $codigoAcrescimo)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoUltimaVigenciaDoAcrescimo($codigoAcrescimo);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * conjunto de funcao para recuperar os codigos da Formula referente
    * @access Public
    * @param  INTEGER Codigo do Acrescimo referente
    * @return SQL
*/
function montaRecuperaRelacionamentoDadosDaFormula($codigo)
{
    $stSql .= " SELECT                          \n";
    $stSql .= "     *                           \n";
    $stSql .= " FROM                            \n";
    $stSql .= "     monetario.formula_acrescimo \n";
    $stSql .= " WHERE                           \n";
    $stSql .= "     cod_acrescimo = '$codigo'   \n";
    $stSql .= " ORDER BY                        \n";
    $stSql .= "     timestamp DESC        \n";
    $stSql .= " LIMIT 1                         \n";

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
    $stSql  = " SELECT                              \n";
    $stSql .= "     *                               \n";
    $stSql .= " FROM                                \n";
    $stSql .= "     administracao.funcao            \n";
    $stSql .= " WHERE                               \n";
    $stSql .= "     cod_modulo = '$codMod'          \n";
    $stSql .= " AND                                 \n";
    $stSql .= "     cod_biblioteca = '$codBib'      \n";
    $stSql .= " AND                                 \n";
    $stSql .= "     cod_funcao = '$codFunc'         \n";

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
