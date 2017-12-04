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
  * Data de criação : 22/11/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Programador: Henrique Boaventura

  * $Id: TFrotaCombustivelItem.class.php 63650 2015-09-23 21:21:08Z arthur $

    Caso de uso: uc-03.02.12
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaCombustivelItem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('frota.combustivel_item');
        $this->setCampoCod('cod_item');

        $this->AddCampo('cod_item'       ,'integer',true,'',true,true);
        $this->AddCampo('cod_combustivel','integer',true,'',false,true);
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT combustivel_item.cod_item
                 , combustivel_item.cod_combustivel
                 , INITCAP(combustivel.nom_combustivel) AS nom_combustivel
                 , catalogo_item.descricao_resumida
              
              FROM frota.combustivel_item
        
        INNER JOIN frota.combustivel
                ON combustivel.cod_combustivel = combustivel_item.cod_combustivel
        
        INNER JOIN frota.veiculo_combustivel
                ON veiculo_combustivel.cod_combustivel = combustivel.cod_combustivel
        
        INNER JOIN almoxarifado.catalogo_item
                ON catalogo_item.cod_item = combustivel_item.cod_item
             
             WHERE ";
        if ( $this->getDado( 'cod_veiculo' ) ) {
            $stSql .= " veiculo_combustivel.cod_veiculo = ".$this->getDado('cod_veiculo')." AND   ";
        }

        return substr( $stSql,0,-6 );
    }
}

?>