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
/*
    * Componente de busca de numeração de componente em função do tipo de documento
    * Data de Criação   : 10/07/2009

    * @author Analista      Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );

class IPopUpTipoDocumentoNumeracao extends Objeto
{
    public $obInnerDocumento;
    public $obCmbTipoDocumento;
    public $obTDATDividaDocumento;
    public $stCodDocumento;
    public $inCodTipoDocumento;
    public $stTipo;

    public function IPopUpTipoDocumentoNumeracao()
    {
        $this->obTDATDividaDocumento = new TDATDividaDocumento;
        $this->stTipo = "todos";

        $this->obCmbTipoDocumento = new Select;
        $this->obCmbTipoDocumento->setNull       (false);
        $this->obCmbTipoDocumento->setRotulo     ( "Tipo de Documento" );
        $this->obCmbTipoDocumento->setTitle      ( "Selecione o tipo de documento." );
        $this->obCmbTipoDocumento->setName       ( "inCodTipoDocumento" );
        $this->obCmbTipoDocumento->setId         ( "inCodTipoDocumento" );
        $this->obCmbTipoDocumento->setCampoID    ( "cod_tipo_documento" );
        $this->obCmbTipoDocumento->setCampoDesc  ( "descricao" );
        $this->obCmbTipoDocumento->addOption     ( "", "Selecione" );
        $this->obCmbTipoDocumento->setStyle      ( "width: 200px" );
        $this->obCmbTipoDocumento->obEvento->setOnChange("");

        $this->obInnerDocumento = new BuscaInner;
        $this->obInnerDocumento->setNull             ( false );
        $this->obInnerDocumento->setTitle            ( "Busca Documento." );
        $this->obInnerDocumento->setRotulo           ( "Documento" );
        $this->obInnerDocumento->setId               ( "stNomeDocumento" );
        $this->obInnerDocumento->obCampoCod->setName ( "stCodDocumento" );
        $this->obInnerDocumento->obCampoCod->setId   ( "stCodDocumento" );
        $this->obInnerDocumento->obCampoCod->setInteiro ( true );
        //o campo cod só pode estar habilitado se já existir um tipo de documento informado
        $this->obInnerDocumento->obCampoCod->setDisabled(true);
        $this->obInnerDocumento->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".CAM_GT_DAT_INSTANCIAS."emissao/OCManterEmissao.php?".Sessao::getId()."','verificaHabilitaDocumento' );");

   }

   public function setCodDocumento($inValor)
   {
       $this->stCodDocumento = $inValor;
   }

   public function setCodTipoDocumento($valor)
   {
       $this->inCodTipoDocumento = $valor;
   }

   public function geraFormulario(&$obFormulario)
   {
       $pgOcul ="'".CAM_GT_DAT_INSTANCIAS."emissao/OCManterEmissao.php?".Sessao::getId();
       $pgOcul.="&".$this->obInnerDocumento->obCampoCod->getName()."='+this.value+'";
       $pgOcul.="&".$this->obCmbTipoDocumento->getName()."='+jQuery('#".$this->obCmbTipoDocumento->getName()."').val()+'";
       $pgOcul.="&stNomCampoCombo=".$this->obCmbTipoDocumento->getName();
       $pgOcul.="&stNomCampoCod=".$this->obInnerDocumento->obCampoCod->getName();
       $pgOcul.="&stIdCampoDesc=".$this->obInnerDocumento->getId()."'";

       //busca os tipos de documento no banco
       $obTAdministracaoTipoDocumento = new TAdministracaoTipoDocumento;
       $obTAdministracaoTipoDocumento->recuperaTodos ( $rsTipoDocumento );
       //verifica se foi setado um valor para o tipo de documento
       if ($this->inCodTipoDocumento) {
           $this->obCmbTipoDocumento->setValor($this->inCodTipoDocumento);
           $this->obInnerDocumento->obCampoCod->setDisabled(false);
       }
       $this->obCmbTipoDocumento->preencheCombo ( $rsTipoDocumento );

       $this->obCmbTipoDocumento->obEvento->setOnChange("ajaxJavaScript($pgOcul,'verificaHabilitaDocumento' );");
       $this->obInnerDocumento->obCampoCod->obEvento->setOnChange("ajaxJavaScript($pgOcul,'verificaHabilitaDocumento' );");

       $this->obInnerDocumento->setFuncaoBusca      (  "abrePopUp('" . CAM_GT_DAT_POPUPS . "emissao/FLProcurarEmissao.php','frm', '". $this->obInnerDocumento->obCampoCod->stName ."','". $this->obInnerDocumento->stId . "','". $this->stTipo . "','". Sessao::getId()."&stNomCampoCombo=".$this->obCmbTipoDocumento->getName()."&','800','550');" );
       if ($this->stCodDocumento) {
            $stFiltro = " WHERE ddd.cod_documento = ".$this->stCodDocumento;
            $this->obTDATDividaDocumento->recuperaListaDocumento( $rsRecordSet, $stFiltro );
            if ( !$rsRecordSet->Eof() ) {
                $this->obInnerDocumento->setValue( $rsRecordSet->getCampo("nome_documento") );
                $this->obInnerDocumento->obCampoCod->setValue( $this->stCodDocumento );
            }
       }
       $this->obInnerDocumento->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaNumeroDocumento' );" );

       $obFormulario->addComponente( $this->obCmbTipoDocumento );
       $obFormulario->addComponente( $this->obInnerDocumento );
   }

}
?>
