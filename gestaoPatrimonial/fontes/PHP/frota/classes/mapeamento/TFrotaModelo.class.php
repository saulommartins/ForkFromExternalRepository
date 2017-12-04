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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 15/03/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 27758 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-28 07:15:55 -0200 (Seg, 28 Jan 2008) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.3  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:17  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaModelo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TFrotaModelo()
{
    parent::Persistente();
    $this->setTabela('frota.modelo');
    $this->setCampoCod('cod_modelo');
    $this->setComplementoChave('cod_marca');
    $this->AddCampo('cod_modelo','integer',true,'',true,false);
    $this->AddCampo('cod_marca','integer',true,'',true,true);
    $this->AddCampo('nom_modelo','varchar',true,'30',false,false);

}

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT marca.cod_marca
                 , marca.nom_marca
                 , modelo.cod_modelo
                 , modelo.nom_modelo
              FROM frota.modelo
        INNER JOIN frota.marca
                ON marca.cod_marca = modelo.cod_marca
             WHERE ";
        if ( $this->getDado( 'cod_marca' )) {
            $stSql .= " modelo.cod_marca = ".$this->getDado('cod_marca')." AND   ";
        }
        if ( $this->getDado( 'cod_modelo' )) {
            $stSql .= " modelo.cod_modelo = ".$this->getDado('cod_modelo')." AND   ";
        }
        if ( $this->getDado( 'nom_modelo' )) {
            $stSql .= " modelo.nom_modelo = ".$this->getDado('nom_modelo')." AND   ";
        }

        return substr($stSql,0,-6);

    }

}
