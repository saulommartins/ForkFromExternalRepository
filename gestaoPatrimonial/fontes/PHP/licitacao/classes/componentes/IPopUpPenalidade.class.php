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
* Componente IPopUpPenalidade

* Data de Criação: 16/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

Casos de uso: uc-03.05.28
*/

/*
$Log$
Revision 1.1  2006/10/17 11:58:52  domluc
Criação do Componente de Penalidade

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

/**
    * Classe que monta o HTML do IPopUpEditObjeto
    * @author Desenvolvedor: Diego Barbosa Victoria

*/

class IPopUpPenalidade extends BuscaInner
{
    public function IPopUpPenalidade(&$obForm)
    {
        parent::BuscaInner();
        $this->obForm = &$obForm;

        $this->setRotulo            ( 'Penalidade' );
        $this->setTitle             ( 'Informe a penalidade ou selecione' );
        $this->obCampoCod->setName  ( 'inCodPenalidade'  );
        $this->obCampoCod->setId    ( 'inCodPenalidade'  );
        $this->obCampoCod->setAlign ( "left" );
        $this->setId                ( 'stPenalidade' );
        $this->setNull              ( true );
        $this->stTipoBusca          = 'popup';

    }
    public function setTipoBusca($stTipo) { $this->stTipoBusca = $stTipo; }

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('".CAM_GP_LIC_POPUPS."penalidade/FLProcurarPenalidade.php','".$this->obForm->getName()."','".$this->obCampoCod->getName()."','".$this->getId()."','".$this->stTipoBusca."','".Sessao::getId()."','800','550');");

        $this->setValoresBusca( CAM_GP_LIC_POPUPS."penalidade/OCProcuraPenalidade.php?".Sessao::getId(), $this->obForm->getName() );

        parent::montaHTML();
    }
}

?>
