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

/**
    * Classe de regra de interface para Atividade
    * Data de Criação: 19/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Interface
*/

class MontaAtividade extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoVigencia;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoAtividade;
/**
    * @access Private
    * @var String
*/
var $stMascara;
/**
    * @access Private
    * @var String
*/
var $stValorComposto;
/**
    * @access Private
    * @var String
*/
var $stValorReduzido;
/**
    * @access Private
    * @var Object
*/
var $obRCEMAtividade;
/**
    * @access Private
    * @var Boolean
*/
var $boCadastroAtividade;
/**
    * @access Private
    * @var Boolean
*/
var $boRetornaJs;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia    = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel       = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoAtividade($valor) { $this->inCodigoAtividade   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido     = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroAtividade($valor) { $this->boCadastroAtividade = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setRetornaJs($valor) { $this->boRetornaJs = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoVigencia() { return $this->inCodigoVigencia;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;     }
/**
    * @access Public
    * @return Integer
*/
function getCodigoAtividade() { return $this->inCodigoAtividade; }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;         }
/**
    * @access Public
    * @return String
*/
function getValorComposto() { return $this->stValorComposto;   }
/**
    * @access Public
    * @return String
*/
function getValorReduzido() { return $this->stValorComposto;   }
/**
    * @access Public
    * @return Boolean
*/
function getCadastroAtividade() { return $this->boCadastroAtividade = $valor; }
/**
    * @access Public
    * @return Boolean
*/
function getRetornaJs() { return $this->boRetornaJs; }

/**
     * Método construtor
     * @access Private
*/
function MontaAtividade()
{
    $this->obRCEMAtividade = new RCEMAtividade;
    $this->stMascara = "";
    $this->boCadastroAtividade = true;
    $this->boRetornaJs = false;
}

/**
    * Monta os combos de atividade conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
    if ($this->boCadastroAtividade) {
        $this->obRCEMAtividade->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMAtividade->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $this->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    }
    $arCombosAtividade = array();
    $boFlagPrimeiroNivel = true;
    $inContNomeCombo = 1;
    while ( !$rsListaNivel->eof() ) {
        $inNumNivel = $rsListaNivel->getCampo( "cod_nivel" );
        $stNomeNivel[$inNumNivel] = $rsListaNivel->getCampo( "nom_nivel" );
        //DEFINICAO PADRAO DOS COMBOS DE ATIVIDADE
        $obCmbAtividade = new Select;
        $obCmbAtividade->setRotulo    ( "Atividade"     );
        $obCmbAtividade->addOption    ( "", "Selecione $stNomeNivel[$inNumNivel]"   );
        $obCmbAtividade->setCampoId   ( "[cod_nivel]-[cod_atividade]-[valor]-[valor_reduzido]" );
        $obCmbAtividade->setCampoDesc ( "nom_atividade" );
        $obCmbAtividade->setStyle     ( "width:250px"     );
        if ($this->boCadastroAtividade) {
            $obCmbAtividade->setNull      ( false             );
        }
        $obCmbAtividade->setName( "inCodAtividade_".$inContNomeCombo++ );
        $obCmbAtividade->obEvento->setOnChange( "preencheProxCombo( $inContNomeCombo );" );
        //PREENCHE APENAS O PRIMEIRO NIVEL
        if ($boFlagPrimeiroNivel) {
           $boFlagPrimeiroNivel = false;
           $this->obRCEMAtividade->setCodigoNivel ( $rsListaNivel->getCampo("cod_nivel") );
           $obErro = $this->obRCEMAtividade->listarAtividade( $rsListaAtividade );
           $obCmbAtividade->preencheCombo( $rsListaAtividade );
        }
        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $arCombosAtividade[] = $obCmbAtividade;
        $rsListaNivel->proximo();
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE ATIVIDADE:
    if ( count( $arCombosAtividade ) ) {
        //CAMPO TEXT PARA A CHAVE DA ATIVIDADE
        $obTxtChaveAtividade = new TextBox;
        $obTxtChaveAtividade->setName   ( "stChaveAtividade" );
        $obTxtChaveAtividade->setRotulo ( "Atividade" );
        $obTxtChaveAtividade->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveAtividade->setSize      ( strlen($this->stMascara) + 2 );
        if ($this->boCadastroAtividade) {
            $obTxtChaveAtividade->setNull      ( false                    );
        }
        $obTxtChaveAtividade->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveAtividade->obEvento->setOnChange("preencheCombosAtividade();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveis" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveAtividade );
        foreach ($arCombosAtividade as $obCmbAtividade) {
            $obFormulario->addComponente( $obCmbAtividade );
        }
    }
}

/**
    * Monta os combos de atividade preenchidos conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormularioPreenchido(&$obFormulario)
{
    $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
    if ($this->boCadastroAtividade) {
        $this->obRCEMAtividade->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMAtividade->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $this->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    }
    $arCombosAtividade = array();
    $inContNomeCombo = 1;
    $stValorComposto = "";
    while ( !$rsListaNivel->eof() ) {
        $inCodigoNivel = $rsListaNivel->getCampo("cod_nivel");
        $this->obRCEMAtividade->setCodigoNivel ( $inCodigoNivel );
        $this->obRCEMAtividade->setValorReduzido ( $stValorComposto );
        $obErro = $this->obRCEMAtividade->listarAtividade( $rsListaAtividade );
        //MONTA O VALOR COMBOS CONFORME O NIVEL
        $arValorComposto = explode( ".", $this->stValorComposto );
        $stValorComposto = "";
        for ($inCont = 0; $inCont < $inCodigoNivel; $inCont++) {
           $stValorComposto .= $arValorComposto[$inCont].".";
        }
        $stValorComposto = substr( $stValorComposto , 0, strlen($stValorComposto) - 1 );
        //RECUPERA O VALOR DO CODIGO DA ATIVIDADE EM RELACAO AO VALOR COMPOSTO
        while ( !$rsListaAtividade->eof() ) {
            if ( $rsListaAtividade->getCampo("valor_reduzido") == $stValorComposto ) {
                $inCodigoAtividade = $rsListaAtividade->getCampo("cod_atividade");
                $stValor             = $rsListaAtividade->getCampo("valor");
                break;
            }
            $rsListaAtividade->proximo();
        }
        $rsListaAtividade->setPrimeiroElemento();
        //MONTA O VALOR DO COMBO
        $stValorAtividade  = $rsListaNivel->getCampo("cod_nivel")."-";
        $stValorAtividade .= $inCodigoAtividade."-";
        $stValorAtividade .= $stValor."-";
        $stValorAtividade .= $stValorComposto;

    //DEFINICAO PADRAO DOS COMBOS DE ATIVIDADE
        $stNumNivel = $rsListaNivel->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivel->getCampo("nom_nivel");
        $obCmbAtividade = new Select;
        $obCmbAtividade->setRotulo    ( "Atividade"     );
        $obCmbAtividade->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbAtividade->setCampoId   ( "[cod_nivel]-[cod_atividade]-[valor]-[valor_reduzido]" );
        $obCmbAtividade->setCampoDesc ( "nom_atividade" );
        $obCmbAtividade->setStyle     ( "width:250px"     );
        if ($this->boCadastroAtividade) {
            $obCmbAtividade->setNull      ( false             );
        }
        $obCmbAtividade->setName      ( "inCodAtividade_".$inContNomeCombo );
        $inContNomeCombo++;
        $obCmbAtividade->setValue     ( $stValorAtividade );
        $obCmbAtividade->preencheCombo( $rsListaAtividade );

        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $rsListaNivel->proximo();
        $obCmbAtividade->obEvento->setOnChange( "preencheProxCombo( $inContNomeCombo );" );
        $arCombosAtividade[] = $obCmbAtividade;
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    if ( count( $arCombosAtividade ) ) {
        //CAMPO TEXT PARA A CHAVE DA ATIVIDADE
        $obTxtChaveAtividade = new TextBox;
        $obTxtChaveAtividade->setName   ( "stChaveAtividade" );
        $obTxtChaveAtividade->setRotulo ( "Atividade" );
        $obTxtChaveAtividade->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveAtividade->setSize      ( strlen($this->stMascara) + 2 );
        if ($this->boCadastroAtividade) {
            $obTxtChaveAtividade->setNull      ( false                    );
        }
        $obTxtChaveAtividade->setValue     ( $stValorComposto );
        $obTxtChaveAtividade->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveAtividade->obEvento->setOnChange("preencheCombosAtividade();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveis" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveAtividade );
        foreach ($arCombosAtividade as $obCmbAtividade) {
            $obFormulario->addComponente( $obCmbAtividade );
        }
    }
}

/**
    * Monta os combos de atividade conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxCombo($inPosCombo, $inNumCombos)
{
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    if (!$this->boCadastroAtividade) {
        $this->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    }
    $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
    $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = $rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodAtividade_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
    }
    if ($this->stValorReduzido) {
        $this->obRCEMAtividade->setCodigoNivel    ( $this->inCodigoNivel    );
        $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
        $this->obRCEMAtividade->recuperaProximoNivel(  $rsProximoNivel );
        $this->obRCEMAtividade->setCodigoNivel    (  $rsProximoNivel->getCampo("cod_nivel") );
        $this->obRCEMAtividade->setValorReduzido  ( $this->stValorReduzido );
        $obErro = $this->obRCEMAtividade->listarAtividadeCombo( $rsListaAtividade );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzido .= ".";
            while ( !$rsListaAtividade->eof() ) {
                $stChaveAtividade  = $rsListaAtividade->getCampo( "cod_nivel" )."-";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "cod_atividade")."-";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "valor")."-";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "valor_reduzido");
                $stNomeAtividade   = $rsListaAtividade->getCampo( "nom_atividade" );
                $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomeAtividade."','".$stChaveAtividade."',''); \n";
                $inContador++;
                $rsListaAtividade->proximo();
            }
        }
    }
    $js .= "f.stChaveAtividade.value = '".$this->stValorReduzido."';\n";
    $this->obRCEMAtividade->setCodigoAtividade ( "" );
    if ( !$this->getRetornaJS() ) {
        sistemaLegado::executaFrameOculto ( $js );
    } else {
        return $js;
    }
}

/**
    * Preenche os combos a partir da chave da atividade
    * @access Public
*/
function preencheCombosAtividade()
{
    $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
    if ($this->boCadastroAtividade) {
        $this->obRCEMAtividade->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMAtividade->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $this->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    }
    if ( strrpos($this->stValorReduzido, ".") == strlen( $this->stValorReduzido ) ) {
        $stValorReduzido = substr( $this->stValorReduzido , 0, strlen( $this->stValorReduzido ) - 1 );
    } else {
        $stValorReduzido = $this->stValorReduzido;
    }
    $arValorReduzido = explode( ".", $stValorReduzido );
    $stValorReduzido = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE ATIVIDADE
    while ( !$rsListaNivel->eof() and key( $arValorReduzido ) < count( $arValorReduzido ) ) {
         if ($inCont == 1) {
             $stValorReduzido .= current( $arValorReduzido );
             $boMontaCombos = true;
         } else {
             if ( $this->obRCEMAtividade->getValorReduzido() ) {
                 $boMontaCombos = true;
             } else {
                 $boMontaCombos = false;
             }
             $stValorReduzido .= ".".current( $arValorReduzido );
         }
         next( $arValorReduzido );
         $stNomeCombo = "inCodAtividade_".$inCont++;
         $stSelecione = $rsListaNivel->getCampo("nom_nivel");
         $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
         if ($boMontaCombos) {
             $this->obRCEMAtividade->setCodigoNivel       ( $rsListaNivel->getCampo("cod_nivel") );
             $obErro = $this->obRCEMAtividade->listarAtividade( $rsListaAtividade );
             $this->obRCEMAtividade->setValorReduzido     ( $stValorReduzido );
             $inContador = 1;
             while ( !$rsListaAtividade->eof() ) {
                 $stChaveAtividade  = $rsListaAtividade->getCampo( "cod_nivel" )."-";
                 $stChaveAtividade .= $rsListaAtividade->getCampo( "cod_atividade")."-";
                 $stChaveAtividade .= $rsListaAtividade->getCampo( "valor")."-";
                 $stChaveAtividade .= $rsListaAtividade->getCampo( "valor_reduzido");
                 $stNomeAtividade   = $rsListaAtividade->getCampo( "nom_atividade" );
                 if ( $rsListaAtividade->getCampo( "valor_reduzido") == $stValorReduzido ) {
                     $stSelected = "selected";
                 } else {
                     $stSelected = "";
                 }
                 $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                 $js .= "new Option('".$stNomeAtividade."','".$stChaveAtividade."','".$stSelected."'); \n";
                 $inContador++;
                 $rsListaAtividade->proximo();
             }
         }
         $rsListaNivel->proximo();
    }

    if ( !$this->getRetornaJs() ) {
        sistemaLegado::executaFrameOculto ( $js );
    } else {
        return $js;
    }
}

}
?>
