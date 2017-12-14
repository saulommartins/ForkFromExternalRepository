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
    * Classe de MAPEAMENTO para MONETARIO.REGRA_CONVERSAO_MOEDA
    * Data de Criacao: 16/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TMONRegraConversaoMoeda.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.06
*/

/*
$Log$
Revision 1.3  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONRegraConversaoMoeda extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONRegraConversaoMoeda()
{
    parent::Persistente();
    $this->setTabela('monetario.regra_conversao_moeda');

    $this->setCampoCod('cod_moeda');
    $this->setComplementoChave('');

    $this->AddCampo('cod_moeda','integer',true,'',true,false);
    $this->AddCampo('cod_funcao','integer',true,'',true,false);
    $this->AddCampo('cod_modulo','integer',true,'',false,false);
    $this->AddCampo('cod_biblioteca','integer',true,'',false,false);
}

//--------------------------------------------------------
/**
    * conjunto de funcao para recuperar os codigos da Formula referente à regra de conversao da moeda
    * @access Public
    * @param  INTEGER Codigo da MOEDA referente
    * @return SQL
*/
function montaRecuperaRelacionamentoDadosDaMoeda($codigo)
{
$sql =  "SELECT
         *
         FROM
         monetario.regra_conversao_moeda
         where cod_moeda = '$codigo'";
return $sql;
}
function RecuperaRelacionamentoDadosDaMoeda(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $codigo)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoDadosDaMoeda( $codigo );

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
function montaRecuperaRelacionamentoDescricaoFormula($codMod, $codBib, $codFunc)
{
$sql =  "SELECT *
         FROM
         administracao.funcao
         where cod_modulo = '$codMod' and cod_biblioteca = '$codBib' and cod_funcao = '$codFunc'
         ";

return $sql;
}
function RecuperaRelacionamentoDescricaoFormula(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $codMod, $codBib, $codFunc)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoDescricaoFormula( $codMod, $codBib, $codFunc );
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}// fecha classe de mapeamento
