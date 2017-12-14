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
    * Arquivo que monta inner do imovel
    * Data de Criação: 12/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpImovel.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09

*/

/*
$Log$
Revision 1.6  2006/10/09 10:09:27  cercato
alterada consultada de busca do imovel.

Revision 1.5  2006/09/27 09:28:48  cercato
adicionada mascara no componente.

Revision 1.4  2006/09/18 09:12:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class IPopUpImovel extends Objeto
{
    public $obInnerImovel;
    public $obTCIMImovel;
    public $inCodImovel;
    public $obTAdministracaoConfiguracao;
    public $stMascaraImovel;

    public function IPopUpImovel()
    {
//        ;
        $this->obTCIMImovel = new TCIMImovel;
        $this->obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", 12 );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , "numero_inscricao");
        $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao );
        if ( $rsConfiguracao->getCampo("valor") == false ) {
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao");
            $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao );

            $this->stMascaraImovel = $rsConfiguracao->getCampo( "valor" );
        } else {
            $this->stMascaraImovel = "";
            $this->obTCIMImovel->recuperaMaxInscricaoImobiliario( $rsConfiguracao );
            for ( $inX=0; $inX< strlen( $rsConfiguracao->getCampo( "total" ) ); $inX++) {
                $this->stMascaraImovel .= "9";
            }
        }

        $this->obInnerImovel = new BuscaInner;
        $this->obInnerImovel->setRotulo    ( "Inscrição Imobiliária"         );
        $this->obInnerImovel->setTitle     ( "Busca imóvel."  );
        $this->obInnerImovel->setId        ( "stImovel"       );
        $this->obInnerImovel->setNull      ( false             );
        $this->obInnerImovel->obCampoCod->setName      ("inCodImovel"             );
        $this->obInnerImovel->obCampoCod->setSize     ( strlen( $this->stMascaraImovel ) );
        $this->obInnerImovel->obCampoCod->setMaxLength( strlen( $this->stMascaraImovel ) );
        $this->obInnerImovel->obCampoCod->setMascara  ( $this->stMascaraImovel );
        $this->obInnerImovel->obCampoCod->setId        ("inCodImovel"             );
        $this->obInnerImovel->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inCodImovel','stImovel','todos','".Sessao::getId()."','800','550');" );

        $pgOcul = CAM_GT_CIM_INSTANCIAS."imovel/OCManterImovel.php?".Sessao::getId();
        $stOnChange = "ajaxJavaScriptSincrono('".$pgOcul."&inCodImovel='+this.value,'PreencheImovel');";
        $this->obInnerImovel->obCampoCod->obEvento->setOnChange( $stOnChange );
   }

   public function setCodImovel($inValor)
   {
        $this->inCodImovel = $inValor;
   }

   public function geraFormulario(&$obFormulario)
   {
       ;
       if ($this->inCodImovel) {
            $stFiltro = " AND I.inscricao_municipal = ".$this->inCodImovel;
            $this->obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $stEnderecoImovel = $rsImoveis->getCampo("logradouro");
                if ( $rsImoveis->getCampo("numero") )
                    $stEnderecoImovel .= ", ".$rsImoveis->getCampo("numero");

                if ( $rsImoveis->getCampo("complemento") )
                    $stEnderecoImovel .= " - ".$rsImoveis->getCampo("complemento");

                $this->obInnerImovel->setValue( $stEnderecoImovel );
                $this->obInnerImovel->obCampoCod->setValue( $this->inCodImovel );
            }
       }

       $obFormulario->addComponente( $this->obInnerImovel );
   }

}
?>
