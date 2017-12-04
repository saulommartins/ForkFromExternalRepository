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

$Revision: 25852 $
$Name$
$Author: bruce $
$Date: 2007-10-05 12:36:01 -0300 (Sex, 05 Out 2007) $

* Casos de uso: uc-01.02.92
                uc-03.03.03
                uc-03.03.14
*/

/*
$Log$
Revision 1.11  2007/10/05 15:34:16  bruce
Ticket#10319#

Revision 1.10  2006/07/13 18:45:39  fernando
Alteração de hints

Revision 1.9  2006/07/10 19:39:47  rodrigo
Adicionado nos componentes de itens,marca e centro de custa a função ajax para manipulação dos dados.

Revision 1.8  2006/07/06 14:04:38  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:20  diego

*/

include_once ( CLA_BUSCAINNER );

class  IPopUpMarca extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpMarca($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Marca'              );
        $this->setTitle                  ( 'Informe a marca.'                 );
        $this->setId                     ( 'stNomMarca'  );
        $this->setNull                   ( false              );

        $this->obCampoCod->setName       ( "inCodMarca"            );
        $this->obCampoCod->setSize       ( 10                  );
        $this->obCampoCod->setMaxLength  ( 9                 );
        $this->obCampoCod->setAlign      ( "left"             );
    }

    public function montaHTML()
    {

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_ALM_POPUPS . "marca/FLManterMarca.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() .
"','800','550');");

     $this->obCampoCod->obEvento->setOnChange(  $this->obCampoCod->obEvento->getOnChange() . "ajaxJavaScript( '".CAM_GP_ALM_POPUPS.'marca/OCManterMarca.php?'.Sessao::getId()."&nomCampoUnidade=".$this->obCampoCod->getName()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->stId."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, 'buscaPopup' );");

       parent::montaHTML();
    }
}
?>
