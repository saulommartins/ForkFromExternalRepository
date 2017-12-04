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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

class MontaServico extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoVigenciaServico;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivelServico;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoServico;
/**
    * @access Private
    * @var String
*/
var $stMascaraServico;
/**
    * @access Private
    * @var String
*/
var $stValorCompostoServico;
/**
    * @access Private
    * @var String
*/
var $stValorReduzidoServico;
/**
    * @access Private
    * @var Object
*/
var $obRCEMServico;
/**
    * @access Private
    * @var Boolean
*/
var $boCadastroAtividade;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigenciaServico($valor) { $this->inCodigoVigenciaServico = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivelServico($valor) { $this->inCodigoNivelServico = $valor;    }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoServico($valor) { $this->inCodigoServico = $valor;         }
/**
    * @access Public
    * @param String $valor
*/
function setMascaraServico($valor) { $this->stMascaraServico = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setValorCompostoServico($valor) { $this->stValorCompostoServico = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzidoServico($valor) { $this->stValorReduzidoServico = $valor;  }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroServico($valor) { $this->boCadastroServico = $valor;       }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroAtividade($valor) { $this->boCadastroAtividade = $valor;     }

/**
    * @access Public
    * @return Integer
*/
function getCodigoVigenciaServico() { return $this->inCodigoVigenciaServico;    }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivelServico() { return $this->inCodigoNivelServico;       }
/**
    * @access Public
    * @return Integer
*/
function getCodigoServico() { return $this->inCodigoServico; }
/**
    * @access Public
    * @return String
*/
function getMascaraServico() { return $this->stMascaraServico;           }
/**
    * @access Public
    * @return String
*/
function getValorCompostoServico() { return $this->stValorCompostoServico;     }
/**
    * @access Public
    * @return String
*/
function getValorReduzidoServico() { return $this->stValorCompostoServico;     }
/**
    * @access Public
    * @return Boolean
*/
function getCadastroServico() { return $this->boCadastroServico;   }
/**
    * @access Public
    * @return Boolean
*/
function getCadastroAtividade() { return $this->boCadastroAtividade;   }

/**
     * Método construtor
     * @access Private
*/
function MontaServico()
{
    $this->obRCEMServico = new RCEMServico;
    $this->stMascaraServico = "";
    $this->boCadastroServico = true;
}

/**
    * Monta os combos de serviço conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $this->obRCEMServico->setCodigoVigencia ( $this->inCodigoVigenciaServico );
    if ($this->boCadastroServico || $this->boCadastroAtividade) {
        $this->obRCEMServico->setCodigoNivel    ( $this->inCodigoNivelServico    );
        $obErro = $this->obRCEMServico->listarNiveisAnteriores( $rsListaNivelServico );
        $stRotulo = "Serviço";
    } else {
        $this->obRCEMServico->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigenciaServico( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMServico->listarNiveis( $rsListaNivelServico );
        $stRotulo = "Serviço";
    }
    $arCombosServico = array();
    $boFlagPrimeiroNivelServico = true;
    $inContNomeCombo = 1;
    while ( !$rsListaNivelServico->eof() ) {
        $stNumNivel = $rsListaNivelServico->getCampo( "cod_nivel" );
        $stNomeNivel[$stNumNivel] = $rsListaNivelServico->getCampo( "nom_nivel" );
        //DEFINICAO PADRAO DOS COMBOS DE SERVICO
        $obCmbServico = new Select;
        $obCmbServico->setRotulo    ( $stRotulo   );
        $obCmbServico->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbServico->setCampoId   ( "[cod_nivel]-[cod_servico]-[valor]-[valor_reduzido]" );
        $obCmbServico->setCampoDesc ( "nom_servico" );
        $obCmbServico->setStyle     ( "width:250px"     );
        if ( $this->getCadastroServico() ) {
            $obCmbServico->setNull      ( false             );
        }
        $obCmbServico->setName( "inCodServico_".$inContNomeCombo++ );
        $obCmbServico->obEvento->setOnChange( "preencheProxComboServico( $inContNomeCombo );" );
        //PREENCHE APENAS O PRIMEIRO NIVEL
        if ($boFlagPrimeiroNivelServico) {
           $boFlagPrimeiroNivelServico = false;
           $this->obRCEMServico->setCodigoNivel ( $rsListaNivelServico->getCampo("cod_nivel") );
           $obErro = $this->obRCEMServico->listarServico( $rsListaServico );
           $obCmbServico->preencheCombo( $rsListaServico );
        }
        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascaraServico .= $rsListaNivelServico->getCampo("mascara").".";
        $arCombosServico[] = $obCmbServico;
        $rsListaNivelServico->proximo();
    }
    $this->stMascaraServico = substr( $this->stMascaraServico, 0 , strlen($this->stMascaraServico) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE SERVICO:
    if ( count( $arCombosServico ) ) {
        //CAMPO TEXT PARA A CHAVE DA SERVICO
        $obTxtChaveServico = new TextBox;
        $obTxtChaveServico->setName      ( "stChaveServico" );
        $obTxtChaveServico->setRotulo    ( $stRotulo );
        $obTxtChaveServico->setMaxLength ( strlen($this->stMascaraServico) );
        $obTxtChaveServico->setSize      ( strlen($this->stMascaraServico) + 2 );
        if ($this->boCadastroServico) {
            $obTxtChaveServico->setNull      ( false                    );
        }
        $obTxtChaveServico->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$this->stMascaraServico."&quot;, this, event);");
        $obTxtChaveServico->obEvento->setOnChange("preencheCombosServico();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveisServico" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveServico );
        foreach ($arCombosServico as $obCmbServico) {
            $obFormulario->addComponente( $obCmbServico );
        }
    }
}

/**
    * Monta os combos de serviço preenchidos conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormularioPreenchido(&$obFormulario)
{
    $this->obRCEMServico->setCodigoVigencia ( $this->inCodigoVigenciaServico );
    if ($this->boCadastroServico || $this->boCadastroAtividade) {
        $this->obRCEMServico->setCodigoNivel    ( $this->inCodigoNivelServico    );
        $obErro = $this->obRCEMServico->listarNiveisAnteriores( $rsListaNivelServico );
    } else {
        $this->obRCEMServico->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigenciaServico( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMServico->listarNiveis( $rsListaNivelServico );
    }
    $arCombosServico = array();
    $inContNomeCombo = 1;
    $stValorCompostoServico = "";
    while ( !$rsListaNivelServico->eof() ) {
        $inCodigoNivelServico = $rsListaNivelServico->getCampo("cod_nivel");
        $this->obRCEMServico->setCodigoNivel   ( $inCodigoNivelServico );
        $this->obRCEMServico->setValorreduzido ( $stValorCompostoServico );
        $obErro = $this->obRCEMServico->listarServico( $rsListaServico );
        //MONTA O VALOR COMBOS CONFORME O NIVEL
        $arValorCompostoServico = explode( ".", $this->stValorCompostoServico );
        $stValorCompostoServico = "";
        for ($inCont = 0; $inCont < $inCodigoNivelServico; $inCont++) {
           $stValorCompostoServico .= $arValorCompostoServico[$inCont].".";
        }
        $stValorCompostoServico = substr( $stValorCompostoServico , 0, strlen($stValorCompostoServico) - 1 );
        //RECUPERA O VALOR DO CODIGO DA SERVICO EM RELACAO AO VALOR COMPOSTO
        while ( !$rsListaServico->eof() ) {
            if ( $rsListaServico->getCampo("valor_reduzido") == $stValorCompostoServico ) {
                $inCodigoServico     =  $rsListaServico->getCampo("cod_servico");
                $stValorServico      = $rsListaServico->getCampo("valor");
                break;
            }
            $rsListaServico->proximo();
        }
        $rsListaServico->setPrimeiroElemento();
        //MONTA O VALOR DO COMBO
        $stValorServico  = $rsListaNivelServico->getCampo("cod_nivel")."-";
        $stValorServico .= $inCodigoServico."-";
        $stValorServico .= $stValorServico."-";
        $stValorServico .= $stValorCompostoServico;

        //DEFINICAO PADRAO DOS COMBOS DE SERVICO
        $stNumNivel = $rsListaNivelServico->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivelServico->getCampo("nom_nivel");
        $obCmbServico = new Select;
        $obCmbServico->setRotulo    ( "Serviço"     );
        $obCmbServico->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbServico->setCampoId   ( "[cod_nivel]-[cod_servico]-[valor]-[valor_reduzido]" );
        $obCmbServico->setCampoDesc ( "nom_servico" );
        $obCmbServico->setStyle     ( "width:250px"     );
        if ($this->boCadastroServico) {
            $obCmbServico->setNull      ( false             );
        }
        $obCmbServico->setName      ( "inCodServico_".$inContNomeCombo );
        $inContNomeCombo++;
        $obCmbServico->setValue     ( $stValorServico );
        $obCmbServico->preencheCombo( $rsListaServico );

        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascaraServico .= $rsListaNivelServico->getCampo("mascara").".";
        $rsListaNivelServico->proximo();
        $obCmbServico->obEvento->setOnChange( "preencheProxComboServico( $inContNomeCombo );" );
        $arCombosServico[] = $obCmbServico;
    }
    $this->stMascaraServico = substr( $this->stMascaraServico, 0 , strlen($this->stMascaraServico) - 1 );
    if ( count( $arCombosServico ) ) {
        //CAMPO TEXT PARA A CHAVE DA SERVICO
        $obTxtChaveServico = new TextBox;
        $obTxtChaveServico->setName   ( "stChaveServico" );
        $obTxtChaveServico->setRotulo ( "Serviço" );
        $obTxtChaveServico->setMaxLength ( strlen($this->stMascaraServico) );
        $obTxtChaveServico->setSize      ( strlen($this->stMascaraServico) + 2 );
        $obTxtChaveServico->setNull      ( false                    );
        $obTxtChaveServico->setValue     ( $stValorCompostoServico );
        $obTxtChaveServico->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$this->stMascaraServico."&quot;, this, event);");
        $obTxtChaveServico->obEvento->setOnChange("preencheCombosServico();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveisServico = new Hidden;
        $obHdnNumNiveisServico->setName  ( "inNumNiveisServico" );
        $obHdnNumNiveisServico->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveisServico );
        $obFormulario->addComponente ( $obTxtChaveServico );
        foreach ($arCombosServico as $obCmbServico) {
            $obFormulario->addComponente( $obCmbServico );
        }
    }
}

/**
    * Monta os combos de serviço conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxCombo($inPosCombo, $inNumCombos)
{
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    if (!$this->boCadastroServico) {
        $this->obRCEMServico->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigenciaServico( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    }
    $obErro = $this->obRCEMServico->listarNiveis( $rsListaNivelServico );
    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivelServico->setCorrente($inCont);
        $stSelecione = $rsListaNivelServico->getCampo("nom_nivel");
        $stNomeCombo = "inCodServico_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
    }
    if ($this->stValorReduzidoServico) {
        $this->obRCEMServico->setCodigoNivel    ( $this->inCodigoNivelServico    );
        $this->obRCEMServico->setCodigoVigencia ( $this->inCodigoVigenciaServico );
        $this->obRCEMServico->recuperaProximoNivel(  $rsProximoNivelServico );
        $this->obRCEMServico->setCodigoNivel    (  $rsProximoNivelServico->getCampo("cod_nivel") );
        $this->obRCEMServico->setValorReduzido( $this->stValorReduzidoServico );
        $obErro = $this->obRCEMServico->listarServico( $rsListaServico );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzidoServico .= ".";
            while ( !$rsListaServico->eof() ) {
                $stChaveServico  = $rsListaServico->getCampo( "cod_nivel" )."-";
                $stChaveServico .= $rsListaServico->getCampo( "cod_servico")."-";
                $stChaveServico .= $rsListaServico->getCampo( "valor")."-";
                $stChaveServico .= $rsListaServico->getCampo( "valor_reduzido");
                $stNomeServico   = $rsListaServico->getCampo( "nom_servico" );
                $js .= "f.inCodServico_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomeServico."','".$stChaveServico."',''); \n";
                $inContador++;
                $rsListaServico->proximo();
            }
        }
    }
    $js .= "f.stChaveServico.value = '".$this->stValorReduzidoServico."';\n";
    $this->obRCEMServico->setCodigoServico ( "" );
    sistemaLegado::executaFrameOculto ( $js );
}

/**
    * Preenche os combos a partir da chave da serviço
    * @access Public
*/
function preencheCombos()
{
    $this->obRCEMServico->setCodigoVigencia ( $this->inCodigoVigenciaServico );
    if ($this->boCadastroServico || $this->boCadastroServico) {
        $this->obRCEMServico->setCodigoNivel    ( $this->inCodigoNivelServico    );
        $obErro = $this->obRCEMServico->listarNiveisAnteriores( $rsListaNivelServico );
    } else {
        $this->obRCEMServico->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigenciaServico( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMServico->listarNiveis( $rsListaNivelServico );
    }
    if ( strrpos($this->stValorReduzidoServico, ".") == strlen( $this->stValorReduzidoServico ) ) {
        $stValorReduzidoServico = substr( $this->stValorReduzidoServico , 0, strlen( $this->stValorReduzidoServico ) - 1 );
    } else {
        $stValorReduzidoServico = $this->stValorReduzidoServico;
    }
    $arValorReduzidoServico = explode( ".", $stValorReduzidoServico );
    $stValorReduzidoServico = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE SERVCO
    while ( !$rsListaNivelServico->eof() and key( $arValorReduzidoServico ) < count( $arValorReduzidoServico ) ) {
         if ($inCont == 1) {
             $stValorReduzidoServico .= current( $arValorReduzidoServico );
             $boMontaCombosServico = true;
         } else {
             if ( $this->obRCEMServico->getValorReduzido() ) {
                 $boMontaCombosServico = true;
             } else {
                 $boMontaCombosServico = false;
             }
             $stValorReduzidoServico .= ".".current( $arValorReduzidoServico );
         }
         next( $arValorReduzidoServico );
         $stNomeCombo = "inCodServico_".$inCont++;
         $stSelecione = $rsListaNivelServico->getCampo("nom_nivel");
         $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
         if ($boMontaCombosServico) {
             $this->obRCEMServico->setCodigoNivel       ( $rsListaNivelServico->getCampo("cod_nivel") );
             $obErro = $this->obRCEMServico->listarServico( $rsListaServico );
             $this->obRCEMServico->setValorReduzido     ( $stValorReduzidoServico );
             $inContador = 1;
             while ( !$rsListaServico->eof() ) {
                 $stChaveServico  = $rsListaServico->getCampo( "cod_nivel" )."-";
                 $stChaveServico .= $rsListaServico->getCampo( "cod_servico")."-";
                 $stChaveServico .= $rsListaServico->getCampo( "valor")."-";
                 $stChaveServico .= $rsListaServico->getCampo( "valor_reduzido");
                 $stNomeServico   = $rsListaServico->getCampo( "nom_servico" );
                 if ( $rsListaServico->getCampo( "valor_reduzido") == $stValorReduzidoServico ) {
                     $stSelected = "selected";
                 } else {
                     $stSelected = "";
                 }
                 $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                 $js .= "new Option('".$stNomeServico."','".$stChaveServico."','".$stSelected."'); \n";
                 $inContador++;
                 $rsListaServico->proximo();
             }
         }
         $rsListaNivelServico->proximo();
    }
    sistemaLegado::executaFrameOculto ( $js );
}

}
?>
