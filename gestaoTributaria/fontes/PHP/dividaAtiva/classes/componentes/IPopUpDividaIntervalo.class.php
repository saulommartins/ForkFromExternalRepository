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
    * Arquivo do componente para busca Inscricao Inteervalo
    * Data de Criação: 29/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpDividaIntervalo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.1  2006/09/29 14:16:00  cercato
*** empty log message ***

*/

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

class IPopUpDividaIntervalo extends Objeto
{
    public $obInnerDividaIntervalo;
    public $obTDATDividaAtiva;
    public $stInscricaoInicial;
    public $stInscricaoFinal;
    public $stTipo;
    public $stMascara;
    public $obVerifica = true;

    public function IPopUpDividaIntervalo()
    {
        $this->obTDATDividaAtiva = new TDATDividaAtiva;
        $this->obTDATDividaAtiva->recuperaCodigoInscricaoComponenteMax( $rsInscricao );
        if ( $rsInscricao->Eof() ) {
            $this->stMascara = "9/9999";
        } else {
            $this->stMascara = "";
            for ( $inX=0; $inX<strlen( $rsInscricao->getCampo( "max_inscricao" ) ); $inX++) {
                $this->stMascara .= "9";
            }

            $this->stMascara .= "/9999";
        }

        $this->stTipo = "todos";
        $this->obInnerDividaIntervalo = new BuscaInnerIntervalo;
        $this->obInnerDividaIntervalo->setNull             ( false );
        $this->obInnerDividaIntervalo->setTitle            ( "Busca Inscrição em Dívida Ativa." );
        $this->obInnerDividaIntervalo->setRotulo           ( "Inscrição/Ano" );
        $this->obInnerDividaIntervalo->obLabelIntervalo->setValue ( "até" );
        $this->obInnerDividaIntervalo->obCampoCod->setName ( "inCodInscricaoInicial" );
        $this->obInnerDividaIntervalo->obCampoCod->setInteiro ( false );
        $this->obInnerDividaIntervalo->obCampoCod2->setName ( "inCodInscricaoFinal" );
        $this->obInnerDividaIntervalo->obCampoCod2->setInteiro ( false );
   }

   public function setCodInscricaoInicial($stValor)
   {
       $this->stInscricaoInicial = $stValor;
   }
   public function setVerifica($stValor)
   {
       $this->obVerifica = $stValor;
    }
   public function setCodInscricaoFinal($stValor)
   {
       $this->stInscricaoFinal = $stValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        ;

        $this->obInnerDividaIntervalo->obCampoCod->setSize     ( strlen( $this->stMascara ) );
        $this->obInnerDividaIntervalo->obCampoCod->setMaxLength( strlen( $this->stMascara ) );
        $this->obInnerDividaIntervalo->obCampoCod->setMascara  ( $this->stMascara );
        $this->obInnerDividaIntervalo->setFuncaoBusca ( "abrePopUp('" . CAM_GT_DAT_POPUPS. "inscricao/FLProcurarInscricao.php','frm', '". $this->obInnerDividaIntervalo->obCampoCod->stName ."','stCampo','". $this->stTipo . "','". Sessao::getId() ."','800','550');" );

        $this->obInnerDividaIntervalo->obCampoCod2->setSize     ( strlen( $this->stMascara ) );
        $this->obInnerDividaIntervalo->obCampoCod2->setMaxLength( strlen( $this->stMascara ) );
        $this->obInnerDividaIntervalo->obCampoCod2->setMascara  ( $this->stMascara );
        $this->obInnerDividaIntervalo->setFuncaoBusca2 ( "abrePopUp('" . CAM_GT_DAT_POPUPS. "inscricao/FLProcurarInscricao.php','frm', '". $this->obInnerDividaIntervalo->obCampoCod2->stName ."','stCampo','". $this->stTipo . "','". Sessao::getId() ."','800','550');" );

       if ($this->stInscricaoInicial) {
            $arDados = explode( "/", $this->stInscricaoInicial );
            $this->obTDATDividaAtiva->setDado('cod_inscricao', $arDados[0] );
            $this->obTDATDividaAtiva->setDado('exercicio', $arDados[1] );
            $this->obTDATDividaAtiva->recuperaPorChave( $rsInscricao );
            if ( !$rsInscricao->Eof() ) {
                $this->obInnerDividaIntervalo->obCampoCod->setValue( $this->stInscricaoInicial );
            }
       }

       if ($this->stInscricaoFinal) {
            $arDados = explode( "/", $this->stInscricaoFinal );
            $this->obTDATDividaAtiva->setDado('cod_inscricao', $arDados[0] );
            $this->obTDATDividaAtiva->setDado('exercicio', $arDados[1] );
            $this->obTDATDividaAtiva->recuperaPorChave( $rsInscricao );
            if ( !$rsInscricao->Eof() ) {
                $this->obInnerDividaIntervalo->obCampoCod2->setValue( $this->stInscricaoFinal );
            }
       }

       if ($this->obVerifica == false) {
           $this->obInnerDividaIntervalo->obCampoCod->obEvento->setOnChange ( "" );
           $this->obInnerDividaIntervalo->obCampoCod2->obEvento->setOnChange( "" );
       } else {
       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."inscricao/OCManterInscricao.php?".Sessao::getId()."&".$this->obInnerDividaIntervalo->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerDividaIntervalo->obCampoCod->getName()."'";

       $this->obInnerDividaIntervalo->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaInscricao' );" );

       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."inscricao/OCManterInscricao.php?".Sessao::getId()."&".$this->obInnerDividaIntervalo->obCampoCod2->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerDividaIntervalo->obCampoCod2->getName()."'";

       $this->obInnerDividaIntervalo->obCampoCod2->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaInscricao' );" );
}
       $obFormulario->addComponente( $this->obInnerDividaIntervalo );
   }

}
?>
