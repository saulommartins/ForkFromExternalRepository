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

    * $Id: IPopUpDivida.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.2  2007/08/15 15:31:34  dibueno
*** empty log message ***

Revision 1.1  2006/09/29 11:52:54  cercato
*** empty log message ***

*/

include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

class IPopUpDivida extends Objeto
{
    public $obInnerDivida;
    public $obTDATDividaAtiva;
    public $stInscricao;
    public $stTipo;
    public $stMascara;

    public function IPopUpDivida()
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

        $this->obInnerDivida = new BuscaInner;
        $this->obInnerDivida->setNull             ( false );
        $this->obInnerDivida->setTitle            ( "Busca Inscrição em Dívida Ativa." );
        $this->obInnerDivida->setRotulo           ( "Inscrição/Ano" );
        $this->obInnerDivida->obCampoCod->setName ( "inCodInscricao" );
        $this->obInnerDivida->obCampoCod->setInteiro ( false );
   }

   public function setCodInscricao($stValor)
   {
       $this->stInscricao = $stValor;
   }

   public function geraFormulario(&$obFormulario)
   {
        ;

        $this->obInnerDivida->obCampoCod->setSize     ( strlen( $this->stMascara ) );
        $this->obInnerDivida->obCampoCod->setMaxLength( strlen( $this->stMascara ) );
        $this->obInnerDivida->obCampoCod->setMascara  ( $this->stMascara );
        $this->obInnerDivida->setFuncaoBusca ( "abrePopUp('" . CAM_GT_DAT_POPUPS. "inscricao/FLProcurarInscricao.php','frm', '". $this->obInnerDivida->obCampoCod->stName ."','','". $this->stTipo . "','". Sessao::getId() ."','800','550');" );

       if ($this->stInscricao) {
            $arDados = explode( "/", $this->stInscricao );
            $this->obTDATDividaAtiva->setDado('cod_inscricao', $arDados[0] );
            $this->obTDATDividaAtiva->setDado('exercicio', $arDados[1] );
            $this->obTDATDividaAtiva->recuperaPorChave( $rsInscricao );
            if ( !$rsInscricao->Eof() ) {
                $this->obInnerDivida->obCampoCod->setValue( $this->stInscricao );
            }
       }

       $pgOcul = "'".CAM_GT_DAT_INSTANCIAS."inscricao/OCManterInscricao.php?".Sessao::getId()."&".$this->obInnerDivida->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obInnerDivida->obCampoCod->getName()."'";

       $this->obInnerDivida->obCampoCod->obEvento->setOnChange ( "ajaxJavaScriptSincrono(".$pgOcul.",'buscaInscricao' );" );

       $obFormulario->addComponente( $this->obInnerDivida );
   }

}
?>
