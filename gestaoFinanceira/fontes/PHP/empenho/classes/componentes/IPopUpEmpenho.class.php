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

* Data de Criação: 17/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.3  2006/11/21 16:07:03  larocca
Inclusão Ordem de Compra

Revision 1.2  2006/11/16 10:25:18  larocca
Comentada a linha que deixava o campo Código com opção disable igual a true.

Revision 1.1  2006/10/18 13:45:07  domluc
Componente IPopUpEmpenho

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
require_once( CAM_GF_ORC_COMPONENTES . "ITextBoxSelectEntidadeUsuario.class.php");

/**
    * Classe que monta o HTML do IPopUpEditObjeto
    * @author Desenvolvedor: Lucas Teixeira Stephanou

*/

class IPopUpEmpenho extends BuscaInner
{
    public $obHdnCodEntidade ;
    public $obHdnExercicio;
    public $obHdnPreEmpenho;
    public $obITextBoxSelectEntidadeUsuario;

    public function IPopUpEmpenho(&$obForm)
    {
        parent::BuscaInner();
        $this->obForm = &$obForm;

        // mascara
        require_once( CAM_GF_EMP_NEGOCIO . 'REmpenhoEmpenho.class.php' );
        $obEmpenho = new REmpenhoEmpenho();
        $obEmpenho->buscaProximoCod();

        $inTamanho = strlen( $obEmpenho->inCodEmpenho  );
        $stMascara = str_pad( $null , $inTamanho , "9");
        $stMascara .= "/9999";
        unset($obEmpenho);

        $this->setRotulo            ( 'Empenho' );
        $this->setTitle             ( 'Informe o empenho ou selecione' );
        $this->obCampoCod->setName  ( 'inCodEmpenho'  );
        $this->obCampoCod->setId    ( 'inCodEmpenho'  );
        $this->obCampoCod->setAlign ( "left" );
//        $this->obCampoCod->setDisabled ( true );
        $this->obCampoCod->setMascara( $stMascara );
        $this->obCampoCod->setInteiro(false);
        $this->setId                ( 'stEmpenho' );
        $this->setNull              ( true );
        $this->stTipoBusca          = 'popup';

        $this->obHdnExercicio = new Hidden;
        $this->obHdnExercicio->setId    ( 'inExercicioEmpenho' );
        $this->obHdnExercicio->setName  ( 'inExercicioEmpenho' );

        $this->obHdnCodEntidade = new Hidden;
        $this->obHdnCodEntidade->setId   ( 'inCodEntidadeEmpenho' );
        $this->obHdnCodEntidade->setName ( 'inCodEntidadeEmpenho' );

        $this->obHdnPreEmpenho = new Hidden;
        $this->obHdnPreEmpenho->setId    ( 'inCodPreEmpenho' );
        $this->obHdnPreEmpenho->setName  ( 'inCodPreEmpenho' );

        $stOnChange  = " document.getElementById('" . $this->obCampoCod->getId()  . "').disabled = false; ";
        $stOnChange .= " document.getElementById('" . $this->obCampoCod->getId()  . "').value = ''; ";
        $stOnChange .= " document.getElementById('" . $this->getId()  . "').innerHTML = '&nbsp'; ";
        $stOnChange .= " document.getElementById('" . $this->obHdnCodEntidade->getId()  . "').value = this.value; ";

        $this->obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
        $this->obITextBoxSelectEntidadeUsuario->obTextBox->obEvento->setOnChange( $stOnChange );
        $this->obITextBoxSelectEntidadeUsuario->obSelect->obEvento->setOnChange( $stOnChange );

        if (count($this->obITextBoxSelectEntidadeUsuario->obSelect) == 1) {
            $this->obHdnCodEntidade->setValue($this->obITextBoxSelectEntidadeUsuario->obSelect->arOption[0]->getValor());
        }

    }
    public function setTipoBusca($stTipo) { $this->stTipoBusca = $stTipo; }

    public function montaHTML()
    {
        $this->setFuncaoBusca("if ( !document.getElementById('".$this->obHdnCodEntidade->getId()."').value ) { alertaAviso('@Você deve selecionar a Entidade antes de selecionar o Empenho','form','erro','".Sessao::getId()."'); } else { abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','".$this->obForm->getName()."&inCodigoEntidade='+$('".$this->obITextBoxSelectEntidadeUsuario->obTextBox->getId()."').value,'".$this->obCampoCod->getName()."','".$this->getId()."','".$this->stTipoBusca."','".Sessao::getId()."','800','550');}");
        $this->setValoresBusca( CAM_GF_EMP_POPUPS."empenho/OCProcurarEmpenho.php?".Sessao::getId() . '&stCtrl=' . $this->stTipoBusca , $this->obForm->getName(), $this->stTipoBusca );

        parent::montaHTML();
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addHidden( $this->obHdnCodEntidade );
        $obFormulario->addHidden( $this->obHdnExercicio );
        $obFormulario->addHidden( $this->obHdnPreEmpenho );
        $obFormulario->addComponente ( $this->obITextBoxSelectEntidadeUsuario );
        $obFormulario->addComponente ( $this );
    }
}

?>
