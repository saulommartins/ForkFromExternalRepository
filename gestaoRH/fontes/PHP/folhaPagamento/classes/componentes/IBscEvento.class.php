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
* Gerar o componente BuscaInner para Evento
* Data de Criação: 09/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package framework
* @subpackage componentes

  $Id: IBscEvento.class.php 66080 2016-07-18 14:46:22Z evandro $

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

/**
    * Cria o componente BuscaInner para Evento
    * @author Desenvolvedor: Andre Almeida

    * @package framework
    * @subpackage componentes
*/
class IBscEvento
{
    /**
        * @access Private
        * @var Object
    */
    public $obBscInnerEvento;
    /**
        * @access Private
        * @var Object
    */
    public $obHdnEvalIBscEvento;
    /**
        * @access Private
        * @var Object
    */
    public $obHdnFixado;
    /**
        * @access Private
        * @var Object
    */
    public $obHdnApresentaParcela;
    /**
        * @access Private
        * @var Object
    */
    public $obSpnDadosEvento;
    /**
        * @access Private
        * @var Array
        * Tipos: P-I-B-D
    */
    public $arNaturezasAceitas;
    /**
        * @access Private
        * @var Array
        * Tipos: P ou I ou B ou D
    */
    public $stNaturezaCheked;
    /**
        * @access Private
        * @var Boolean
    */
    public $boInformarValorQuantidade;
    /**
        * @access Private
        * @var Boolean
    */
    public $boInformarQuantidadeParcelas;
    /**
        * @access Private
        * @var Boolean
    */
    public $boSugerirValorQuantidade;
    /**
        * @access Private
        * @var Integer
    */
    public $inCodigoEvento;
    /**
        * @access Private
        * @var Object
    */
    public $obLblTextoComplementar;
        /**
        * @access Private
        * @var Object
    */
    public $obLblMesAno;

    public $stTodosEventos;
    
    public $stCampoCodEvento;
    
    public $stCampoNomEvento;

    public $stCampoTextoComplementar;

    /**
        * @access Private
        * @var Boolean
    */
    public $boTextoComplementar;

    /**
        * @access Public
        * @param Char $valor
    */
    public function addNaturezasAceitas($valor) { $this->arNaturezasAceitas[] = $valor; }
    /**
        * @access Public
        * @param Array $valor
    */
    public function setNaturezaChecked($valor) { $this->stNaturezaChecked = $valor; }
    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setInformarValorQuantidade($valor) { $this->boInformarValorQuantidade = $valor; }
    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setInformarQuantidadeParcelas($valor) { $this->boInformarQuantidadeParcelas = $valor; }
    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setSugerirValorQuantidade($valor) { $this->boSugerirValorQuantidade = $valor;}
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoEvento($valor) { $this->inCodigoEvento = $valor;}
    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setEventoSistema($valor) { $this->boEventoSistema = $valor;}

    public function setTodosEventos($valor) { $this->stTodosEventos = $valor;}

    /**
        * @access Public
        * @param Boolean $valor
    */

    public function setTextoComplementar($valor) { $this->boTextoComplementar = $valor;}

    /**
        * @access Public
        * @return String
    */
    public function getNaturezasAceitas()
    {
        $stNaturezasAceitas = '';
        for ( $i=0; $i<count($this->arNaturezasAceitas); $i++) {
            if ($stNaturezasAceitas) {
                $stNaturezasAceitas .= "-";
            }
            $stNaturezasAceitas .= $this->arNaturezasAceitas[$i];
        }

        return $stNaturezasAceitas;
    }
    /**
        * @access Public
        * @return String
    */
    public function getNaturezaChecked() { return $this->stNaturezaChecked; }
    /**
        * @access Public
        * @return Boolean
    */
    public function getInformarValorQuantidade() { return $this->boInformarValorQuantidade; }
    /**
        * @access Public
        * @return Boolean
    */
    public function getInformarQuantidadeParcelas() { return $this->boInformarQuantidadeParcelas; }
    /**
        * @access Public
        * @return Boolean
    */
    public function getSugerirValorQuantidade() { return $this->boSugerirValorQuantidade; }
    /**
        * @access Public
        * @return Boolean
    */
    public function getEventoSistema() { return $this->boEventoSistema; }

    public function getTodosEventos() { return $this->stTodosEventos; }

    ###
    public function getCampoCodEvento() { return $this->stCampoCodEvento; }

    public function setCampoCodEvento($var) { $this->stCampoCodEvento = $var; }

    public function getCampoNomEvento() { return $this->stCampoNomEvento; }

    public function setCampoNomEvento($var) { $this->stCampoNomEvento = $var; }

    public function getCampoTextoComplementar() { return $this->stTextoComplementar; }

    public function setCampoTextoComplementar($var) { $this->stTextoComplementar = $var; }

    /**
        * @access Public
        * @return Boolean
    */
    public function getTextoComplementar() { return $this->boTextoComplementar; }

    /**
        * Método Construtor
        * @access Public
    */
    public function IBscEvento($stCampoCodEvento='inCodigoEvento', $stCampoNomEvento='stEvento', $stCampoTextoComplementar='stTextoComplementar')
    {
        include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";

        //Define a mascara do campo Evento
        $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
        $obRFolhaPagamentoConfiguracao->consultar();
        $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

        $this->setCampoCodEvento($stCampoCodEvento);
        $this->setCampoNomEvento($stCampoNomEvento);
        $this->setCampoTextoComplementar($stCampoTextoComplementar);
        $this->setTextoComplementar(true);

        //Define a mascara do campo Evento
        $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
        $obRFolhaPagamentoConfiguracao->consultar();
        $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

        $this->obBscInnerEvento = new BuscaInner;
        $this->obBscInnerEvento->setRotulo              ( "Evento"            );
        $this->obBscInnerEvento->setId                  ( $stCampoNomEvento   );
        $this->obBscInnerEvento->setName                ( $stCampoNomEvento   );
        $this->obBscInnerEvento->setTitle               ( "Informe o evento a ser lançado." );
        $this->obBscInnerEvento->obCampoCod->setName    ( $stCampoCodEvento   );
        $this->obBscInnerEvento->obCampoCod->setId      ( $stCampoCodEvento   );
        $this->obBscInnerEvento->obCampoCod->setValue   ( $inCodigoEvento     );
        $this->obBscInnerEvento->obCampoCod->setPreencheComZeros ( "E"        );
        $this->obBscInnerEvento->obCampoCod->setMascara ( $stMascaraEvento    );
        $this->obBscInnerEvento->obCampoDescrHidden->setName( "hdnDescEvento" );
        $this->obBscInnerEvento->obCampoDescrHidden->setId  ( "hdnDescEvento" );

        $this->obTxtValor = new Numerico;
        $this->obTxtValor->setName      ( "nuValorEvento"                  );
        $this->obTxtValor->setId        ( "nuValorEvento"                  );
        $this->obTxtValor->setTitle     ( "Informe o valor a ser lançado." );
        $this->obTxtValor->setAlign     ( "RIGHT"                          );
        $this->obTxtValor->setRotulo    ( "Valor"                          );        
        $this->obTxtValor->setMaxLength ( 14                              );        
        $this->obTxtValor->setSize      ( 12                               );
        $this->obTxtValor->setDecimais  ( 2                                );
        $this->obTxtValor->setNegativo  ( false                            );
        $this->obTxtValor->setFormatarNumeroBR(true);
        $this->obTxtValor->obEvento->setOnChange( "ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php?".Sessao::getId()."&nuValorEvento='+this.value, 'validarValor' );" );

        $this->obTxtQuantidade = new Numerico;
        $this->obTxtQuantidade->setName      ( "nuQuantidadeEvento"            );
        $this->obTxtQuantidade->setId        ( "nuQuantidadeEvento"            );
        $this->obTxtQuantidade->setTitle     ( "Informe a quantidade a ser lançada." );
        $this->obTxtQuantidade->setAlign     ( "RIGHT"                         );
        $this->obTxtQuantidade->setRotulo    ( "Quantidade"                    );
        $this->obTxtQuantidade->setMaxLength ( 14                              );        
        $this->obTxtQuantidade->setSize      ( 12                              );        
        $this->obTxtQuantidade->setNegativo  ( false                           );
        $this->obTxtQuantidade->setDefinicao ( "NUMERICO" );
        $this->obTxtQuantidade->setFormatarNumeroBR(true);
        $this->obTxtQuantidade->obEvento->setOnChange( "ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php?".Sessao::getId()."&nuQuantidadeEvento='+this.value, 'validarQuantidade' );" );

        $this->obTxtQuantidadeParcelas = new TextBox;
        $this->obTxtQuantidadeParcelas->setName      ( "nuQuantidadeParcelasEvento"    );
        $this->obTxtQuantidadeParcelas->setId        ( "nuQuantidadeParcelasEvento"    );
        $this->obTxtQuantidadeParcelas->setRotulo    ( "Quantidade de Parcelas"        );
        $this->obTxtQuantidadeParcelas->setInteiro   ( true                            );
        $this->obTxtQuantidadeParcelas->setMaxLength ( 10                              );
        $this->obTxtQuantidadeParcelas->setSize      ( 10                              );
        $this->obTxtQuantidadeParcelas->setTitle     ( "Informe a quantidade de parcelas a ser lançada." );
        $this->obTxtQuantidadeParcelas->obEvento->setOnChange( "ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php?".Sessao::getId()."&nuQuantidadeParcelasEvento='+this.value, 'preencherPrevisaoMesAno' );" );

        $this->obHdnEvalIBscEvento = new HiddenEval;
        $this->obHdnEvalIBscEvento->setName  ( "stHdnEvalIBscEvento" );
        $this->obHdnEvalIBscEvento->setValue ( ""       );

        $this->obHdnFixado = new Hidden;
        $this->obHdnFixado->setName  ( "stHdnFixado" );
        $this->obHdnFixado->setId    ( "stHdnFixado" );
        $this->obHdnFixado->setValue ( ""            );

        $this->obHdnApresentaParcela = new Hidden;
        $this->obHdnApresentaParcela->setName  ( "stHdnApresentaParcela" );
        $this->obHdnApresentaParcela->setId    ( "stHdnApresentaParcela" );
        $this->obHdnApresentaParcela->setValue ( ""                      );

        $this->obSpnDadosEvento = new Span;
        $this->obSpnDadosEvento->setId    ( "spnDadosEvento" );
        $this->obSpnDadosEvento->setValue ( ""               );

        $this->obLblTextoComplementar= new Label;
        $this->obLblTextoComplementar->setRotulo ( "Texto Complementar"      );
        $this->obLblTextoComplementar->setName   ( $stCampoTextoComplementar );
        $this->obLblTextoComplementar->setId     ( $stCampoTextoComplementar );

        $this->obLblMesAno = new Label;
        $this->obLblMesAno->setRotulo( "Previsão Mês/Ano Limite" );
        $this->obLblMesAno->setName  ( "stMesAno"                );
        $this->obLblMesAno->setId    ( "stMesAno"                );
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->montaFuncaoBusca();
        $this->montaFuncaoPreenche();

        $obFormulario->addComponente     ( $this->obBscInnerEvento       );

        if ($this->getTextoComplementar()) {
            $obFormulario->addComponente ( $this->obLblTextoComplementar );
        }

        $obFormulario->addSpan  ( $this->obSpnDadosEvento          );
        $obFormulario->addHidden( $this->obHdnEvalIBscEvento, true );
        $obFormulario->addHidden( $this->obHdnFixado               );
        $obFormulario->addHidden( $this->obHdnApresentaParcela     );

        Sessao::write('IBscEvento',$this);
    }

    public function montaFuncaoBusca()
    {
        if ( !$this->getNaturezasAceitas() ) {
            $this->addNaturezasAceitas('P');
            $this->addNaturezasAceitas('I');
            $this->addNaturezasAceitas('B');
            $this->addNaturezasAceitas('D');
        }
        if ( $this->getNaturezaChecked() == "" ) {
            $this->setNaturezaChecked('P');
        }
        $stTipoEvento = "n_evento_sistema";
        if ( $this->getEventoSistema() ) {
            $stTipoEvento = "evento_sistema";
        }
        if ( $this->getTodosEventos() ) {
            $stTipoEvento = "todos_eventos";
        }

        $this->obBscInnerEvento->setFuncaoBusca( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','".$this->stCampoCodEvento."','".$this->stCampoNomEvento."','','".Sessao::getId()."&stNaturezasAceitas=".$this->getNaturezasAceitas()."&stNaturezaChecked=".$this->getNaturezaChecked()."&boInformarValorQuantidade=".$this->getInformarValorQuantidade()."&boInformarQuantidadeParcelas=".$this->getInformarQuantidadeParcelas()."&boSugerirValorQuantidade=".$this->getSugerirValorQuantidade()."&stTipoEvento=".$stTipoEvento."&stTextoComplementar=".$this->getCampoTextoComplementar()."','800','550')" );
    }

    public function montaFuncaoPreenche()
    {
        if ( !$this->getNaturezasAceitas() ) {
            $this->setNaturezasAceitas('P');
            $this->setNaturezasAceitas('I');
            $this->setNaturezasAceitas('B');
            $this->setNaturezasAceitas('D');
        }
        $stOnChange = $this->obBscInnerEvento->obCampoCod->obEvento->getOnChange();
        $this->obBscInnerEvento->obCampoCod->obEvento->setOnChange( "ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php?".Sessao::getId()."&inCodigoEvento='+this.value+'&stCampoNomEvento=".$this->stCampoNomEvento."&stCampoCodEvento=".$this->stCampoCodEvento."&stTextoComplementar=".$this->stTextoComplementar."', 'preencheDescEvento' ); ".$stOnChange );
    }

    public function montaJsPreencheValores()
    {
        Sessao::write('IBscEvento',$this );

        $stJs  = "document.getElementById('inCodigoEvento').value = '".$this->obBscInnerEvento->obCampoCod->getValue()."'\n";
        $stJs .= "ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php?".Sessao::getId()."&inCodigoEvento=".$this->obBscInnerEvento->obCampoCod->getValue()."&stCampoNomEvento=".$this->stCampoNomEvento."&stCampoCodEvento=".$this->stCampoCodEvento."&stTextoComplementar=".$this->stTextoComplementar."', 'preencheDescEvento' );";

        return $stJs;
    }

}
?>
