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
    * Classe de mapeamento da tabela EMPENHO.ITEM_PRE_EMPENHO_COMPRA
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.03.03, uc-02.03.02
*/

/*
$Log$
Revision 1.11  2006/08/09 12:53:21  jose.eduardo
Bug #6741#

Revision 1.10  2006/07/11 20:27:52  eduardo
Bug #6531#

Revision 1.9  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoItemPreEmpenhoCompra extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoItemPreEmpenhoCompra()
{
    parent::Persistente();
    $this->setTabela('empenho.item_pre_empenho_compra');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_pre_empenho,exercicio,num_item');

    $this->AddCampo('cod_pre_empenho','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('num_item','integer',true,'',true,false);
    $this->AddCampo('cod_item','integer',true,'',false,false);
    $this->AddCampo('cod_licitacao','integer',false,'',false,false);

}

function consultar($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaItemMaterialPorChave();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    if ( !$rsRecordSet->Eof() ) {
        $this->setDado( 'cod_pre_empenho' , $rsRecordSet->getCampo( 'cod_pre_empenho' ) );
        $this->setDado( 'exercicio'       , $rsRecordSet->getCampo( 'exercicio' ) );
        $this->setDado( 'num_item'        , $rsRecordSet->getCampo( 'num_item' ) );
        $this->setDado( 'cod_item'        , $rsRecordSet->getCampo( 'cod_item' ) );
        $this->setDado( 'cod_licitacao'   , $rsRecordSet->getCampo( 'cod_licitacao' ) );
    }

    return $obErro;
}

function montaRecuperaItemMaterialPorChave()
{
    $stSql  = "select itpec.*\n";
    $stSql .= "from empenho.pre_empenho as pe\n";
    $stSql .= "     join empenho.item_pre_empenho as itpe\n";
    $stSql .= "          on (     itpe.cod_pre_empenho = pe.cod_pre_empenho\n";
    $stSql .= "               and itpe.exercicio       = pe.exercicio\n";
    $stSql .= "               and itpe.num_item        = " . $this->getDado( 'num_item' ) . "\n";
    $stSql .= "             )\n";
    $stSql .= "     join empenho.item_pre_empenho_compra as itpec\n";
    $stSql .= "          on (     itpec.cod_pre_empenho = itpe.cod_pre_empenho\n";
    $stSql .= "               and itpec.exercicio       = itpe.exercicio\n";
    $stSql .= "               and itpec.num_item        = itpe.num_item\n";
    $stSql .= "              )\n";
    $stSql .= "where     pe.cod_pre_empenho = ". $this->getDado( 'cod_pre_empenho' ) . "\n";
    $stSql .= "     and pe.exercicio        = '". $this->getDado( 'exercicio' )       . "'\n";

    return $stSql;
}
}
