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
    * Classe de mapeamento da view SAMLINK_VW_SIAM_NUMBOL
    * Data de Criação: 03/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson Wolowski Gonçalves

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a view SAMLINK_VW_SIAM_NUMBOL
  * Data de Criação: 03/01/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Gelson Wolowski Gonçalves

*/

class VSamlinkSiamNumbol extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VSamlinkSiamNumbol()
{
    parent::Persistente();
    $this->setTabela("samlink_vw_siam_numbol");

    $this->setCampoCod('');
    $this->setComplementoChave('data,numero');

    $this->AddCampo( 'data'           ,'date'    );
    $this->AddCampo( 'numero'         ,'integer' );
    $this->AddCampo( 'liberado'       ,'boolean' );
    $this->AddCampo( 'lancado'        ,'boolean' );

}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                          \n";
    $stSQL .= "     TO_CHAR( nb.data,'dd/mm/yyyy' ) AS data_nb, \n";
    $stSQL .= "     nb.*,                                       \n";
    $stSQL .= "     a.*                                         \n";
    $stSQL .= " FROM                                            \n";
    $stSQL .= "     samlink.vw_siam_numbol AS nb,               \n";
    $stSQL .= "     samlink.vw_siam_autent AS a                 \n";
    $stSQL .= " WHERE                                           \n";
    $stSQL .= "     nb.data = a.data                            \n";

    return $stSQL;
}

}
