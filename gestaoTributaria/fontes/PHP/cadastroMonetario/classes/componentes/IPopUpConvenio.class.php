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
* Padronizar a busca por Convênio, estendendo a classe BuscaInner
* Data de Criação: 09/11/2005

* @author Desenvolvedor: Tonismar Régis Bernardo

* @package URBEM
* @subpackage Componentes

    * $Id: IPopUpConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.04

*/

/*
$Log$
Revision 1.6  2006/09/15 14:46:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once( CLA_BUSCAINNER );

class  IPopUpConvenio extends BuscaInner
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

    public function IPopUpConvenio($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Convênio'         );
        $this->setTitle                  ( ''                 );
        $this->setId                     ( ''                 );
        $this->setNull                   ( false              );

        $this->obCampoCod->setName       ( "inNumConvenio"    );
        $this->obCampoCod->setSize       ( 6                  );
        $this->obCampoCod->setMaxLength  ( 10                 );
        $this->obCampoCod->setAlign      ( "left"             );
        $this->obCampoCod->obEvento->setOnChange( "buscaValor('buscaConvenio');" );

        $this->stTipo = 'geral';
    }

    public function setTipo($stTipo='geral')
    {
        $this->stTipo = $stTipo;
    }

    public function montaHTML()
    {
        ;

        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_MON_POPUPS . "convenio/FLProcurarConvenio.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        //$this->setValoresBusca(CAM_GT_MON_POPUPS.'convenio/OCProcurarConvenio.php?'.Sessao::getId(), $this->obForm->getName(), $this->stTipo );

        parent::montaHTML();
    }
}
?>
