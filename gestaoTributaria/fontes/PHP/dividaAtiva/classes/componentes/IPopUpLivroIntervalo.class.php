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

    * $Id: IPopUpDividaIntervalo.class.php 29252 2008-04-16 14:25:51Z fabio $

    * Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.1  2006/09/29 14:16:00  cercato
*** empty log message ***

*/

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

class IPopUpLivroIntervalo extends Objeto
{
    public $obInnerLivroIntervalo;
    public $obTDATDividaAtiva;
    public $stLivroInicial;
    public $stLivroFinal;
    public $stTipo;
    public $stMascara;

    public function IPopUpLivroIntervalo()
    {
        $this->obTDATDividaAtiva = new TDATDividaAtiva;
        $this->obTDATDividaAtiva->recuperaLivroMax( $rsLivro );

        if ( $rsLivro->Eof() ) {
            $this->stMascara = "9/9999";
        } else {
            $this->stMascara = "";
            for ( $inX=0; $inX<strlen( $rsLivro->getCampo( "max_livro" ) ); $inX++) {
                $this->stMascara .= "9";
            }

            $this->stMascara .= "/9999";
        }

        $this->stTipo = "todos";
        $this->obInnerLivroIntervalo = new BuscaInnerIntervalo;
        $this->obInnerLivroIntervalo->setNull                    ( false );
        $this->obInnerLivroIntervalo->setTitle                   ( "Busca Livro" );
        $this->obInnerLivroIntervalo->setRotulo                  ( "Livro" );
        $this->obInnerLivroIntervalo->obLabelIntervalo->setValue ( "até" );
        $this->obInnerLivroIntervalo->obCampoCod->setName ( "inLivroFolhaInicial" );
        $this->obInnerLivroIntervalo->obCampoCod->setInteiro ( false );
        $this->obInnerLivroIntervalo->obCampoCod2->setName ( "inLivroFolhaFinal" );
        $this->obInnerLivroIntervalo->obCampoCod2->setInteiro ( false );
   }

   public function setLivroInicial($stValor)
   {
       $this->stLivroInicial = $stValor;
   }

   public function setLivroFinal($stValor)
   {
       $this->stLivroFinal = $stValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        $this->obInnerLivroIntervalo->obCampoCod->setSize     ( strlen( $this->stMascara ) );
        $this->obInnerLivroIntervalo->obCampoCod->setMaxLength( strlen( $this->stMascara ) );
        $this->obInnerLivroIntervalo->obCampoCod->setMascara  ( $this->stMascara );
        $this->obInnerLivroIntervalo->setFuncaoBusca ( "abrePopUp('" . CAM_GT_DAT_POPUPS. "inscricao/FLProcurarLivro.php','frm', '". $this->obInnerLivroIntervalo->obCampoCod->stName ."','','". $this->stTipo . "','". Sessao::getId() ."','800','550');" );

        $this->obInnerLivroIntervalo->obCampoCod2->setSize     ( strlen( $this->stMascara ) );
        $this->obInnerLivroIntervalo->obCampoCod2->setMaxLength( strlen( $this->stMascara ) );
        $this->obInnerLivroIntervalo->obCampoCod2->setMascara  ( $this->stMascara );
        $this->obInnerLivroIntervalo->setFuncaoBusca2 ( "abrePopUp('" . CAM_GT_DAT_POPUPS. "inscricao/FLProcurarLivro.php','frm', '". $this->obInnerLivroIntervalo->obCampoCod2->stName ."','','". $this->stTipo . "','". Sessao::getId()."','800','550');" );

       if ($this->stLivroInicial) {
            $arDados = explode( "/", $this->stLivroInicial );
            $this->obTDATDividaAtiva->setDado('num_livro', $arDados[0] );
//            $this->obTDATDividaAtiva->setDado('num_folha', $arDados[1] );
            $this->obTDATDividaAtiva->recuperaPorChave( $rsLivro );
            if ( !$rsLivro->Eof() ) {
                $this->obInnerLivroIntervalo->obCampoCod->setValue( $this->stLivroInicial );
            }
       }

       if ($this->stLivroFinal) {
            $arDados = explode( "/", $this->stLivroFinal );
            $this->obTDATDividaAtiva->setDado('num_livro', $arDados[0] );
//            $this->obTDATDividaAtiva->setDado('num_folha', $arDados[1] );
            $this->obTDATDividaAtiva->recuperaPorChave( $rsLivro );
            if ( !$rsLivro->Eof() ) {
                $this->obInnerLivroIntervalo->obCampoCod2->setValue( $this->stLivroFinal );
            }
       }

       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."inscricao/OCManterInscricao.php?".Sessao::getId()."&".$this->obInnerLivroIntervalo->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerLivroIntervalo->obCampoCod->getName()."'";

       $this->obInnerLivroIntervalo->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaLivro' );" );

       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."inscricao/OCManterInscricao.php?".Sessao::getId()."&".$this->obInnerLivroIntervalo->obCampoCod2->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerLivroIntervalo->obCampoCod2->getName()."'";

       $this->obInnerLivroIntervalo->obCampoCod2->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaLivro' );" );

       $obFormulario->addComponente( $this->obInnerLivroIntervalo );
   }

}
?>
