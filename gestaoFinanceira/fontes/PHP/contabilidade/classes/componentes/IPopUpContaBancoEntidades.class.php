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
* Arquivo de popup de busca de Recurso
* Data de Criação: 20/06/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

* @package URBEM
* @subpackage

* $Id: IPopUpContaBancoEntidades.class.php 59612 2014-09-02 12:00:51Z gelson $

 Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpContaBancoEntidades extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obCmbEntidades;

    public function IPopUpContaBancoEntidades($obCmbEntidades)
    {

        parent::BuscaInner();

        $this->obCmbEntidades = $obCmbEntidades;

        $this->setRotulo( "Conta Banco" );
        $this->setTitle( "Informe a Conta Banco" );
        $this->setNull( true  );
        $this->setId( "stNomContaBanco" );
        $this->setValue( '' );
        $this->obCampoCod->setName("inCodContaBanco");
        $this->obCampoCod->setSize     ( 10 );
        $this->obCampoCod->setValue( "" );

    }

    public function montaHTML()
    {
        $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCContaBancoEntidades.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2()."),'buscaPopup');" );
        $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoContaEntidade/FLPlanoContaEntidade.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','banco'+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2().")+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."','".Sessao::getId()."','800','');");
        parent::montaHTML();
    }
}
?>
