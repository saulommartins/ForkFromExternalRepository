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
    * Classe de mapeamento da view SAMLINK_VW_SIAM_MATER
    * Data de Criação: 22/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.00.00
    * Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.7  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:11:10  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a view SAMLINK_VW_SIAM_MATER
  * Data de Criação: 22/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

*/

class VSamlinkSiamMater extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VSamlinkSiamMater()
{
    parent::Persistente();
    $this->setTabela('samlink.vw_siam_mater');

    $this->setCampoCod('codigo');
    $this->setComplementoChave('');

    $this->AddCampo( 'codigo'                ,'varchar' );
    $this->AddCampo( 'descricao'             ,'varchar' );
    $this->AddCampo( 'complemento'           ,'varchar' );
    $this->AddCampo( 'unidade_aquisicao'     ,'varchar' );
    $this->AddCampo( 'unidade_saida'         ,'varchar' );
    $this->AddCampo( 'referencia_anterior'   ,'varchar' );

}
}
