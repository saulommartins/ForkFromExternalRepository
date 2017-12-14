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
    * Arquivo do componente para busca Inscricao
    * Data de Criação: 29/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpCobranca.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.04

*/

/*
$Log$
Revision 1.1  2007/04/16 18:07:43  cercato
*** empty log message ***

*/

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );

class IPopUpCobranca extends Objeto
{
    public $obInnerCobranca;
    public $obTDATDividaParcelamento;
    public $stNrParcelamento;
    public $stTipo;
    public $stMascara;

    public function IPopUpCobranca()
    {
        $this->obTDATDividaParcelamento = new TDATDividaParcelamento;
        $this->obTDATDividaParcelamento->recuperaCodigoCobrancaComponente( $rsInscricao );
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

        $this->obInnerCobranca = new BuscaInner;
        $this->obInnerCobranca->setNull             ( false );
        $this->obInnerCobranca->setTitle            ( "Busca Cobrança em Dívida Ativa." );
        $this->obInnerCobranca->setRotulo           ( "Cobrança/Ano" );
        $this->obInnerCobranca->obCampoCod->setName ( "inNrParcelamento" );
        $this->obInnerCobranca->obCampoCod->setInteiro ( false );
   }

   public function setNrParcelamento($stValor)
   {
       $this->stNrParcelamento = $stValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        ;

        $this->obInnerCobranca->obCampoCod->setSize     ( strlen( $this->stMascara ) );
        $this->obInnerCobranca->obCampoCod->setMaxLength( strlen( $this->stMascara ) );
        $this->obInnerCobranca->obCampoCod->setMascara  ( $this->stMascara );
        $this->obInnerCobranca->setFuncaoBusca ( "abrePopUp('" . CAM_GT_DAT_POPUPS. "cobranca/FLProcurarCobranca.php','frm', '". $this->obInnerCobranca->obCampoCod->stName ."','','". $this->stTipo . "','". Sessao::getId() ."','800','550');" );

       if ($this->stNrParcelamento) {
            $arDados = explode( "/", $this->stNrParcelamento );
            $this->obTDATDividaParcelamento->setDado('num_parcelamento', $arDados[0] );
            $this->obTDATDividaParcelamento->setDado('exercicio', $arDados[1] );
            $this->obTDATDividaParcelamento->recuperaPorChave( $rsInscricao );
            if ( !$rsInscricao->Eof() ) {
                $this->obInnerCobranca->obCampoCod->setValue( $this->stNrParcelamento );
            }
       }

       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."cobranca/OCManterCobranca.php?".Sessao::getId()."&".$this->obInnerCobranca->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerCobranca->obCampoCod->getName()."'";

       $this->obInnerCobranca->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaCobranca' );" );

       $obFormulario->addComponente( $this->obInnerCobranca );
   }

}
?>
