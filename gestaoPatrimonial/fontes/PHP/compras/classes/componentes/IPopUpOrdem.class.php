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

* Data de CriaÃ§Ã£o: 08/12/2006

* @author Analista: Fernando Zank Correa Evangelista
* @author Desenvolvedor: Fernando Zank Correa Evangelista

Casos de uso: uc-03.04.29
*/

/*
$Log$
Revision 1.5  2006/12/20 16:33:09  fernando
 UC 03.04.29

Revision 1.4  2006/12/19 12:06:00  fernando
correção ortográfica

Revision 1.3  2006/12/15 14:57:52  thiago
componente de ordem de compra, diferenciando se tem ou não nota de compra.

Revision 1.2  2006/12/12 12:02:38  fernando
IpopUpOrdemCompra

Revision 1.1  2006/12/11 18:28:41  fernando
IpopUpOrdemCompra

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
require_once( CAM_GF_ORC_COMPONENTES . "ITextBoxSelectEntidadeUsuario.class.php");

/**
    * Classe que monta o HTML do IPopUpOrdem
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

*/

class IPopUpOrdem extends BuscaInner
{
    public $obHdnCodEntidadeOrdem ;
    public $obHdnExercicio;
    public $obITextBoxSelectEntidadeUsuario;
    public $obTxtExercicioOrdem;
    public $stTipoOrdem;

    public function IPopUpOrdem(&$obForm, $stTipoOrdem)
    {
        parent::BuscaInner();
        $this->obForm = &$obForm;
        $this->stTipoOrdem = $stTipoOrdem;
        $this->stDescOrdem = ( $stTipoOrdem == 'C' ) ? 'Compra' : 'Serviço';

        // mascara
//      require_once( CAM_GF_EMP_NEGOCIO . 'REmpenhoEmpenho.class.php' );
//      $obEmpenho = new REmpenhoEmpenho();
//      $obEmpenho->buscaProximoCod();

//        $inTamanho = strlen( $obEmpenho->inCodEmpenho  );
        $inTamanho = 4;
        $stMascara = str_pad( $null , $inTamanho , "9");
        $stMascara .= "/9999";
        unset($obEmpenho);

        //$this->setRotulo            ( "Ordem de Compra" );
        //$this->setTitle             ( "Informe a ordem de compra ou selecione" );
        //$this->obCampoCod->setName  ( "inCodOrdemCompra"  );
        //$this->obCampoCod->setId    ( "inCodOrdemCompra"  );

        $this->setRotulo            ( "Ordem de ".$this->stDescOrdem );
        $this->setTitle             ( "Informe a ordem de ".$this->stDescOrdem." ou selecione" );
        $this->obCampoCod->setName  ( "inCodOrdem"  );
        $this->obCampoCod->setId    ( "inCodOrdem"  );

        $this->obCampoCod->setAlign ( "left" );
        $this->obCampoCod->setMascara( $stMascara );
//        $this->setId                ( 'stOrdemCompra' );
        $this->setNull              ( false);
        $this->stTipoBusca          = 'popup';
//        $this->boNotaFiscal         = false;

        $this->obHdnExercicio = new Hidden;
        //$this->obHdnExercicio->setId    ( 'inExercicioOC' );
        //$this->obHdnExercicio->setName  ( 'inExercicioOC' );
        $this->obHdnExercicio->setId    ( 'inExercicioOrdem' );
        $this->obHdnExercicio->setName  ( 'inExercicioOrdem' );

        $this->obHdnCodEntidadeOrdem = new Hidden;
        //$this->obHdnCodEntidadeOrdem->setId   ( 'inCodEntidadeOC' );
        //$this->obHdnCodEntidadeOrdem->setName ( 'inCodEntidadeOC' );
        $this->obHdnCodEntidadeOrdem->setId   ( 'inCodEntidadeOrdem' );
        $this->obHdnCodEntidadeOrdem->setName ( 'inCodEntidadeOrdem' );

        $this->obHdnEmpenhoOrdem = new Hidden;
        //$this->obHdnEmpenhoOrdem->setId   ( 'stEmpenhoOc' );
        //$this->obHdnEmpenhoOrdem->setName ( 'stEmpenhoOc' );
        $this->obHdnEmpenhoOrdem->setId   ( 'stEmpenhoOrdem' );
        $this->obHdnEmpenhoOrdem->setName ( 'stEmpenhoOrdem' );

        //$this->stTipo = 'geral';
        $this->stTipo = $this->stTipoOrdem;

        $this->obTxtExercicioOrdem = new textBox();
        //$this->obTxtExercicioOrdemCompra->setId('stExercicioOrdemCompra');
        //$this->obTxtExercicioOrdemCompra->setName('stExercicioOrdemCompra');
        //$this->obTxtExercicioOrdemCompra->setRotulo('Exercício Ordem Compra');
        $this->obTxtExercicioOrdem->setId('stExercicioOrdem');
        $this->obTxtExercicioOrdem->setName('stExercicioOrdem');
        $this->obTxtExercicioOrdem->setRotulo('Exercício Ordem '.$this->stDescOrdem);
        $this->obTxtExercicioOrdem->setNull(false);

        $this->obTxtEntidade = new textBox();
        //$this->obTxtEntidade->setId('inCodEntidadeOrdemCompra');
        //$this->obTxtEntidade->setName('inCodEntidadeOrdemCompra');
        $this->obTxtEntidade->setId('inCodEntidadeOrdem');
        $this->obTxtEntidade->setName('inCodEntidadeOrdem');
        $this->obTxtEntidade->setRotulo('Entidade');
        $this->obTxtEntidade->setNull(false);

        $stOnChange  = " document.getElementById('" . $this->obCampoCod->getId()  . "').disabled = false; ";
        $stOnChange .= " document.getElementById('" . $this->obHdnCodEntidadeOrdem->getId()  . "').value = this.value; ";

    }

 function setTipo($stTipo='geral') { $this->stTipo = $stTipo; }

    public function montaHTML()
    {
        $sessao = $_SESSION ['sessao'];
        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_COM_POPUPS . "ordemCompra/FLProcurarOC.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . $sessao->id ."','800','550');");
//        $this->setValoresBusca( CAM_GP_COM_POPUPS.'ordemCompra/FLProcurarOC.php?'.$sessao->id, $this->obForm->getName(), $this->stTipo );
        parent::montaHTML();
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addHidden( $this->obHdnCodEntidadeOrdem );
        $obFormulario->addHidden( $this->obHdnExercicio );
        $obFormulario->addHidden( $this->obHdnEmpenhoOrdem );
        $obFormulario->addComponente ( $this );
        $obFormulario->addComponente ( $this->obTxtExercicioOrdem);
        $obFormulario->addComponente ( $this->obTxtEntidade);

    }
}
?>
