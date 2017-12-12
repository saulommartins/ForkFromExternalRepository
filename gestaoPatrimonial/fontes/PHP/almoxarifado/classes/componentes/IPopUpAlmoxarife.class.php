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
* Arquivo de popup de busca de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 12234 $
$Name$
$Author: diego $
$Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

 Casos de uso: uc-03.03.02
*/

/*
$Log$
Revision 1.5  2006/07/06 14:04:38  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:20  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpAlmoxarife extends BuscaInner
{

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpAlmoxarife($obForm)
    {
        parent::BuscaInner();
        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Almoxarife'           );
        $this->setTitle                  ( 'Informe o almoxarife.');
        $this->setId                     ( 'stNomCGM'             );
        $this->setNull                   ( true                   );
        $this->obCampoCod->setName       ( "inCodCGMAlmoxarife"   );
        $this->obCampoCod->setAlign      ( "left"                 );

    }

    public function montaHTML()
    {

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_ALM_POPUPS . "almoxarife/FLProcurarAlmoxarife.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() .
"','800','550');");

        $this->setValoresBusca( CAM_GP_ALM_POPUPS.'almoxarife/OCProcurarAlmoxarife.php?'.Sessao::getId(), $this->obForm->getName(), $this->stTipo );

       parent::montaHTML();
    }
}
?>
