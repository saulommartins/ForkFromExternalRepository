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
    * Arquivo que monta o combo do Unidade/Grandeza de Medida
    * Data de Criação: 26/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso:

*/

require_once ( CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php" );

class IPopUpUnidadeMedida extends Objeto
{
        public $obInnerUnidade;
    public $inCodUnidade;
        private $obTUnidadeMedida;
        private $stMascara;

        function __construct()
        {
            $this->obTUnidadeMedida = new TUnidadeMedida;
            $this->stMascara = "99.99";

            $this->obInnerUnidade = new BuscaInner;
            $this->obInnerUnidade->setNull( false );
            $this->obInnerUnidade->setTitle( "Busca Unidade." );
            $this->obInnerUnidade->setRotulo( "Unidade" );
            $this->obInnerUnidade->setId( "stUnidade"  );
            $this->obInnerUnidade->obCampoCod->setName( "inCodUnidade" );
            $this->obInnerUnidade->obCampoCod->setInteiro( true );
            $this->obInnerUnidade->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."unidade_medida/FLBuscarUnidadeMedida.php','frm','inCodUnidade','stUnidade','todos','".Sessao::getId()."','800','550');" );
    }

    public function setCodUnidade($inValor)
    {
               $this->inCodUnidade = $inValor;
    }

    public function geraFormulario(&$obFormulario)
    {
               if ($this->stMascara) {
                   $this->obInnerUnidade->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$this->stMascara."', this, event);" );
                   $this->obInnerUnidade->obCampoCod->setMinLength( strlen( $this->stMascara ) );
                   $this->obInnerUnidade->obCampoCod->setSize( strlen( $this->stMascara ) );
               }

              if ($this->inCodUnidade) {
            $arCodUnidade = explode( '.', $this->inCodUnidade );
                    $this->obTUnidadeMedida->setDado( "cod_unidade", $arCodUnidade[0] );
                    $this->obTUnidadeMedida->setDado( "cod_grandeza", $arCodUnidade[1] );
                    $this->obTUnidadeMedida->recuperaPorChave( $rsUnidade );
                    if ( !$rsUnidade->Eof() ) {
                        $this->obInnerUnidade->setValue( $rsUnidade->getCampo( "nom_unidade" ) );
                        $this->obInnerUnidade->obCampoCod->setValue( $this->inCodUnidade );
                    }
               }

        $pgOcul = CAM_GA_ADM_POPUPS."unidade_medida/OCBuscarUnidadeMedida.php?".Sessao::getId();
               $stOnChange = "ajaxJavaScript( '".$pgOcul."&inCodUnidade='+this.value,'preencheUnidade' );";
               $this->obInnerUnidade->obCampoCod->obEvento->setOnChange( $stOnChange );
        $obFormulario->addComponente( $this->obInnerUnidade );
    }
}
?>
