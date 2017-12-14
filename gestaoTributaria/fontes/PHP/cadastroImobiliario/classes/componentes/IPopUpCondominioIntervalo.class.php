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
    * Arquivo que monta inner do Condominio em Intervalo
    * Data de Criação: 22/02/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage

    * $Id: IPopUpCondominioIntervalo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09

*/

/*
$Log$
Revision 1.1  2007/02/23 12:17:34  dibueno
Bug #8416#

*/

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelCondominio.class.php"           );

class IPopUpCondominioIntervalo extends Objeto
{
    public $obInnerCondominioIntervalo;
    public $obTCIMImovelCondominio;
    public $inCodCondominioInicial;
    public $inCodCondominioFinal;
    public $obHdnCampo2;
    public $obTAdministracaoConfiguracao;
    #var $stMascaraImovel;
    public $boVerificaCondominio;

    public function IPopUpCondominioIntervalo()
    {
        ;
        $this->boVerificaInscricao = true;

        $this->obTCIMImovelCondominio = new TCIMImovelCondominio;

        $this->obInnerCondominioIntervalo = new BuscaInnerIntervalo;
        $this->obInnerCondominioIntervalo->setRotulo           ( "Condomínio" );
        $this->obInnerCondominioIntervalo->setTitle            ( "Intervalo de Valores para Condomínio" );
        $this->obInnerCondominioIntervalo->obLabelIntervalo->setValue ( "até" );
        $this->obInnerCondominioIntervalo->obCampoCod->setName ( "inCodCondominioInicial" );
        $this->obInnerCondominioIntervalo->setFuncaoBusca ( "abrePopUp('".CAM_GT_CIM_POPUPS."condominio/FLProcurarCondominio.php','frm','inCodCondominioInicial','stCampo','todos','".Sessao::getId()."','800','550');" );

        $this->obInnerCondominioIntervalo->obCampoCod2->setName ( "inCodCondominioFinal" );
        $this->obInnerCondominioIntervalo->setFuncaoBusca2 ( "abrePopUp('".CAM_GT_CIM_POPUPS."condominio/FLProcurarCondominio.php','frm','inCodCondominioFinal','stCampo','todos','".Sessao::getId()."','800','550');" );

        $this->obHdnCampo2 =  new Hidden;
        $this->obHdnCampo2->setName   ( "stCampo" );
        $this->obHdnCampo2->setId     ( "stCampo" );
   }

    public function setVerificaCondominio($valor)
    {
        $this->boVerificaCondominio = $valor;
    }

   public function setCodCondominioInicial($inValor)
   {
        $this->inCodCondominioInicial = $inValor;
   }

   public function setCodCondominioFinal($inValor)
   {
        $this->inCodCondominioFinal = $inValor;
   }

   public function geraFormulario(&$obFormulario)
   {
       ;
       if ($this->inCodCondominioInicial) {
            $stFiltro = " AND cod_condominio = ".$this->inCodCondominioInicial;
            $obTCIMImovelCondominio->recuperaTodos( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $this->obInnerCondominioIntervalo->obCampoCod->setValue( $this->inCodCondominioInicial );
            }
       }

       if ($this->inCodCondominioFinal) {
            $stFiltro = " AND I.inscricao_municipal = ".$this->inCodCondominioFinal;
            $obTCIMImovelCondominio->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            $this->obTCIMImovelCondominio->recuperaRelacionamento( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $this->obInnerCondominioIntervalo->obCampoCod2->setValue( $this->inCodCondominioFinal );
            }
       }

       $pgOcul = CAM_GT_CIM_INSTANCIAS."condominio/OCManterCondominio.php?".Sessao::getId();
       $stOnChange = "ajaxJavaScript('".$pgOcul."&inCodCondominio='+this.value,'PreencheCondominioIntervaloInicial');";
       $stOnChange2 = "ajaxJavaScript('".$pgOcul."&inCodCondominio='+this.value,'PreencheCondominioIntervaloFinal');";
        if ($this->boVerificaCondominio) {
            $this->obInnerCondominioIntervalo->obCampoCod->obEvento->setOnChange( $stOnChange );
            $this->obInnerCondominioIntervalo->obCampoCod2->obEvento->setOnChange( $stOnChange2 );
        }

       $obFormulario->addHidden ( $this->obHdnCampo2 );
       $obFormulario->addComponente( $this->obInnerCondominioIntervalo );
   }

}
?>
