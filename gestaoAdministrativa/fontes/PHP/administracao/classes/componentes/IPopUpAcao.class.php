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
* Arquivo de popup de busca de Ações
* Data de Criação: 06/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package URBEM
* @subpackage

$Revision: 15594 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 11:14:25 -0300 (Seg, 18 Set 2006) $

* Casos de uso: uc-01.03.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpAcao extends BuscaInner
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

    public function IPopUpAcao($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Ação'         );
        $this->setTitle                  ( ''             );
        $this->setId                     ( 'stNomeAcao'	  );

        $this->obCampoCod->setName       ( 'inCodigoAcao' );
        $this->obCampoCod->setSize       ( 6              );
        $this->obCampoCod->setMaxLength  ( 10             );
        $this->obCampoCod->setAlign      ( "left"         );

    }

    public function montaHTML()
    {

        $this->setFuncaoBusca("abrePopUp('" . CAM_GA_ADM_POPUPS . "acao/LSListarAcao.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','','" . Sessao::getId() .
"','800','550');");

        $stLink  = CAM_GA_ADM_POPUPS.'acao/OCListarAcao.php?'.Sessao::getId();
//        $stLink .= '&campoNum='.$this->obCampoCod->getName().'&campoNom='.$this->getId();
        $this->setValoresBusca( $stLink, $this->obForm->getName(), $this->stTipo );

        parent::montaHTML();
    }
}
?>
