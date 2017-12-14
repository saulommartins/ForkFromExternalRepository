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
    * Componente IPopUpEmpenho

    * Data de Criação: 08/12/2006

    * @author Analista: Fernando Zank Correa Evangelista
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    $Id: IPopUpOrdemCompra.class.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-03.04.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
require_once( CAM_GP_COM_COMPONENTES . "IPopUpOrdem.class.php");

/**
    * Classe que monta o HTML do IPopUpOrdem
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

*/
class IPopUpOrdemCompra extends IPopUpOrdem
{
    public function IPopUpOrdemCompra(&$obForm)
    {
        parent::IPopUpOrdem($obForm, 'C');
    }

 function setTipo($stTipo='geral') { $this->stTipo = $stTipo; }

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_COM_POPUPS . "ordemCompra/FLProcurarOC.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");
//        $this->setValoresBusca( CAM_GP_COM_POPUPS.'ordemCompra/FLProcurarOC.php?'.Sessao::getId(), $this->obForm->getName(), $this->stTipo );
        parent::montaHTML();
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addHidden( $this->obHdnCodEntidadeOC );
        $obFormulario->addHidden( $this->obHdnExercicio );
        $obFormulario->addHidden( $this->obHdnEmpenhoOC );
        $obFormulario->addComponente ( $this );
        $obFormulario->addComponente ( $this->obTxtExercicioOrdemCompra);
        $obFormulario->addComponente ( $this->obTxtEntidade);

    }

}
?>
