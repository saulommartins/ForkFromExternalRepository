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
  * Classe de mapeamento da view SAMLINK_VW_SIAM_JULGAM
  * Data de Criação: 07/04/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $
    * Casos de uso uc-03.00.00
    * Casos de uso uc-02.03.15

*/

/*
$Log$
Revision 1.8  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:10  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a view SAMLINK_VW_SIAM_JULGAM
  * Data de Criação: 07/04/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

*/

class VSamlinkSiamJulgam extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VSamlinkSiamJulgam()
{
    parent::Persistente();
    $this->setTabela('samlink.vw_siam_julgam');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'tipo'            ,'varchar' );
    $this->AddCampo( 'numero'          ,'integer' );
    $this->AddCampo( 'num_item'        ,'varchar' );
    $this->AddCampo( 'nom_item'        ,'varchar' );
    $this->AddCampo( 'complemento'     ,'varchar' );
    $this->AddCampo( 'unidade_entrada' ,'varchar' );
    $this->AddCampo( 'unidade_saida'   ,'varchar' );
    $this->AddCampo( 'quantidade'      ,'numeric', false, '14.4' );
    $this->AddCampo( 'vl_total'        ,'numeric', false, '14.2' );
}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT                           \n";
    $stSql .= "    J.numcgm ,                    \n";
    $stSql .= "    C.nom_cgm ,                   \n";
    $stSql .= "    J.dotacao ,                   \n";
    $stSql .= "    J.tipo ,                      \n";
    $stSql .= "    J.numero ,                    \n";
    $stSql .= "    J.num_item ,                  \n";
    $stSql .= "    J.nom_item ,                  \n";
    $stSql .= "    J.complemento ,               \n";
    $stSql .= "    J.unidade_entrada ,           \n";
    $stSql .= "    J.unidade_saida ,             \n";
    $stSql .= "    J.quantidade ,                \n";
    $stSql .= "    J.vl_total                    \n";
    $stSql .= "FROM                              \n";
    $stSql .= "    ".$this->getTabela()." AS J   \n";
    $stSql .= "   ,sw_cgm                AS C   \n";
    $stSql .= "WHERE J.numcgm = C.numcgm         \n";

    return $stSql;
}
}
