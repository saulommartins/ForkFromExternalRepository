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

$Revision: 11002 $
$Name$
$Author: leandro.zis $
$Date: 2006-06-08 13:02:20 -0300 (Qui, 08 Jun 2006) $

* Casos de uso: uc-01.03.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpUsuario extends BuscaInner
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

    public function IPopUpUsuario($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Usuario'              );
        $this->setTitle                  ( ''                 );
        $this->setId                     ( 'stNomUsuario'  );
        $this->setNull                   ( false              );

        $this->obCampoCod->setName       ( "inCGM"            );
        $this->obCampoCod->setSize       ( 6                  );
        $this->obCampoCod->setMaxLength  ( 10                 );
        $this->obCampoCod->setAlign      ( "left"             );

    }

    public function montaHTML()
    {

        $this->setFuncaoBusca("abrePopUp('" . CAM_GA_ADM_POPUPS . "usuario/FLProcurarUsuario.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() .
"','800','550');");

        $this->setValoresBusca( CAM_GA_ADM_POPUPS.'usuario/OCProcurarUsuario.php?'.Sessao::getId(), $this->obForm->getName(), $this->stTipo );

        parent::montaHTML();
    }
}
?>
