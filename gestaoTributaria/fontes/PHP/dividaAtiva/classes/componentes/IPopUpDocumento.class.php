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
    * Arquivo do componente para busca Documento
    * Data de Criação: 26/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.03

*/

/*
$Log$
Revision 1.1  2006/09/29 10:50:05  cercato
*** empty log message ***

*/

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );

class IPopUpDocumento extends Objeto
{
    public $obInnerDocumento;
    public $obTDATDividaDocumento;
    public $stCodDocumento;
    public $stTipo;

    public function IPopUpDocumento()
    {
        $this->obTDATDividaDocumento = new TDATDividaDocumento;
        $this->stTipo = "todos";

        $this->obInnerDocumento = new BuscaInner;
        $this->obInnerDocumento->setNull             ( false );
        $this->obInnerDocumento->setTitle            ( "Busca Documento." );
        $this->obInnerDocumento->setRotulo           ( "Documento" );
        $this->obInnerDocumento->setId               ( "stNomeDocumento" );
        $this->obInnerDocumento->obCampoCod->setName ( "stCodDocumento" );
        $this->obInnerDocumento->obCampoCod->setId   ( "stCodDocumento" );
        $this->obInnerDocumento->obCampoCod->setInteiro ( true );
   }

   public function setCodDocumento($inValor)
   {
       $this->stCodDocumento = $inValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        $this->obInnerDocumento->setFuncaoBusca      (  "abrePopUp('" . CAM_GT_DAT_POPUPS . "emissao/FLProcurarEmissao.php','frm', '". $this->obInnerDocumento->obCampoCod->stName ."','". $this->obInnerDocumento->stId . "','". $this->stTipo . "','". Sessao::getId() ."','800','550');" );

       if ($this->stCodDocumento) {
            $stFiltro = " WHERE ddd.cod_documento = ".$this->stCodDocumento;
            $this->obTDATDividaDocumento->recuperaListaDocumento( $rsRecordSet, $stFiltro );
            if ( !$rsRecordSet->Eof() ) {
                $this->obInnerDocumento->setValue( $rsRecordSet->getCampo("nome_documento") );
                $this->obInnerDocumento->obCampoCod->setValue( $this->stCodDocumento );
            }
       }

       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."emissao/OCManterEmissao.php?".Sessao::getId()."&".$this->obInnerDocumento->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerDocumento->obCampoCod->getName()."&stIdCampoDesc=".$this->obInnerDocumento->getId()."'";

       $this->obInnerDocumento->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaTipoDocumento' );" );

       $obFormulario->addComponente( $this->obInnerDocumento );
   }

}
?>
