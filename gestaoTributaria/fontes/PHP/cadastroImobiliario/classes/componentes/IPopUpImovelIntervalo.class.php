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
    * Arquivo que monta inner do imovel com intervalo
    * Data de Criação: 12/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpImovelIntervalo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09

*/

/*
$Log$
Revision 1.6  2007/01/29 12:01:31  dibueno
Bug #7927#

Revision 1.5  2006/10/09 10:09:27  cercato
alterada consultada de busca do imovel.

Revision 1.4  2006/09/27 09:28:48  cercato
adicionada mascara no componente.

Revision 1.3  2006/09/18 09:12:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class IPopUpImovelIntervalo extends Objeto
{
    public $obInnerImovelIntervalo;
    public $obTCIMImovel;
    public $inCodImovelInicial;
    public $inCodImovelFinal;
    public $obHdnCampo2;
    public $obTAdministracaoConfiguracao;
    public $stMascaraImovel;
    public $boVerificaInscricao;

    public function IPopUpImovelIntervalo()
    {
        ;
        $this->boVerificaInscricao = true;

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

        $this->obInnerImovelIntervalo = new BuscaInnerIntervalo;
        $this->obInnerImovelIntervalo->setRotulo           ( "Inscrição Imobiliária" );
        $this->obInnerImovelIntervalo->setTitle            ( "Intervalo de Valores para Inscrição Imobiliária" );
        $this->obInnerImovelIntervalo->obLabelIntervalo->setValue ( "até" );
        $this->obInnerImovelIntervalo->obCampoCod->setName ( "inCodImovelInicial" );
        $this->obInnerImovelIntervalo->obCampoCod->setSize     ( strlen( $this->stMascaraImovel ) );
        $this->obInnerImovelIntervalo->obCampoCod->setMaxLength( strlen( $this->stMascaraImovel ) );
        $this->obInnerImovelIntervalo->obCampoCod->setMascara  ( $this->stMascaraImovel );
        $this->obInnerImovelIntervalo->setFuncaoBusca ( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inCodImovelInicial','stCampo','todos','".Sessao::getId()."','800','550');" );
        $this->obInnerImovelIntervalo->obCampoCod2->setName ( "inCodImovelFinal" );
        $this->obInnerImovelIntervalo->obCampoCod2->setSize     ( strlen( $this->stMascaraImovel ) );
        $this->obInnerImovelIntervalo->obCampoCod2->setMaxLength( strlen( $this->stMascaraImovel ) );
        $this->obInnerImovelIntervalo->obCampoCod2->setMascara  ( $this->stMascaraImovel );
        $this->obInnerImovelIntervalo->setFuncaoBusca2 ( "abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inCodImovelFinal','stCampo','todos','".Sessao::getId()."','800','550');" );

        $this->obHdnCampo2 =  new Hidden;
        $this->obHdnCampo2->setName   ( "stCampo" );
        $this->obHdnCampo2->setId     ( "stCampo" );
   }

    public function setVerificaInscricao($valor)
    {
        $this->boVerificaInscricao = $valor;
    }

   public function setCodImovelInicial($inValor)
   {
        $this->inCodImovelInicial = $inValor;
   }

   public function setCodImovelFinal($inValor)
   {
        $this->inCodImovelFinal = $inValor;
   }

   public function geraFormulario(&$obFormulario)
   {
       ;
       if ($this->inCodImovelInicial) {
            $stFiltro = " AND I.inscricao_municipal = ".$this->inCodImovelInicial;
            $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $this->obInnerImovelIntervalo->obCampoCod->setValue( $this->inCodImovelInicial );
            }
       }

       if ($this->inCodImovelFinal) {
            $stFiltro = " AND I.inscricao_municipal = ".$this->inCodImovelFinal;
            $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            $this->obTCIMImovel->recuperaRelacionamento( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $this->obInnerImovelIntervalo->obCampoCod2->setValue( $this->inCodImovelFinal );
            }
       }

       $pgOcul = CAM_GT_CIM_INSTANCIAS."imovel/OCManterImovel.php?".Sessao::getId();
       $stOnChange = "ajaxJavaScript('".$pgOcul."&inCodImovel='+this.value,'PreencheImovelIntervaloInicial');";
       $stOnChange2 = "ajaxJavaScript('".$pgOcul."&inCodImovel='+this.value,'PreencheImovelIntervaloFinal');";
        if ($this->boVerificaInscricao) {
            $this->obInnerImovelIntervalo->obCampoCod->obEvento->setOnChange( $stOnChange );
            $this->obInnerImovelIntervalo->obCampoCod2->obEvento->setOnChange( $stOnChange2 );
        }

       $obFormulario->addHidden ( $this->obHdnCampo2 );
       $obFormulario->addComponente( $this->obInnerImovelIntervalo );
   }

}
?>
