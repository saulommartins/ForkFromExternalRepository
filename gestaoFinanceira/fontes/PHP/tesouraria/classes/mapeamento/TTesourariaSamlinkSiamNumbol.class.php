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
    * Classe de mapeamento da tabela NUMBOL do SIAM
    * Data de Criação: 02/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.17
*/

/*
$Log$
Revision 1.4  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_SIAM );

/**
  * Efetua conexão com a tabela NUMBOL do SIAM
  * Data de Criação: 02/03/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaSamlinkSiamNumbol extends PersistenteSIAM
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaSamlinkSiamNumbol()
{
    parent::Persistente();
    $this->setTabela( "NUMBOL" );

    $this->setCampoCod('k18_data');
    $this->setComplementoChave('');

    $this->AddCampo('k18_data',   'date',   true, '', true,  false );
    $this->AddCampo('k18_numero', 'integer',true, '', false, false );
    $this->AddCampo('k18_liber',  'boolean',true, '', false, false );
    $this->AddCampo('k18_lanca',  'boolean',true, '', false, false );
}

/**
    *
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaNumeroRegistros(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM;
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setDado( "stFiltro", $stCondicao );

        $stSql = $this->montaRecuperaNumeroRegistros().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    }

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaNumeroRegistrosAgrupado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM;
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setDado( "stFiltro", $stCondicao );
        $stGroup = " GROUP BY k18_data ";
        $stSql = $this->montaRecuperaNumeroRegistros().$stCondicao.$stGroup;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    }

    return $obErro;
}

function montaRecuperaNumeroRegistros()
{
    $stSQL  = " SELECT                        \n";
    $stSQL .= "      k18_data AS num_registros \n";
    $stSQL .= " FROM                          \n";
    $stSQL .= "    ".$this->getTabela()."     \n";

    return $stSQL;
}

}
