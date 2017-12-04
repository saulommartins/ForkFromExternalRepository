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
* Classe de formulário de interface para Cnae
* Data de Criação: 22/11/2004

* @author Desenvolvedor: Tonismar Régis Bernardo

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once '../../../includes/Constante.inc.php';
include_once '../../../bibliotecas/mascaras.lib.php';
include_once ( CAM_REGRA."RCEMCnae.class.php"          );
include_once ( CAM_REGRA."RCEMNivelCnae.class.php"     );
include_once ( CAM_REGRA."RCEMAtividade.class.php"     );

/**
    * Classe de que monta o HTML do formulario para Cnae

    * @package framework
    * @subpackage componentes
*/
class MontaCnae extends Objeto
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
var $inCodigoCnae;
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
var $obRCEMCnae;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel = $valor;    }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoCnae($valor) { $this->inCodigoCnae = $valor;         }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido = $valor;  }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroCnae($valor) { $this->boCadastroCnae = $valor;       }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPopUp($valor) { $this->boPopUp        = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoVigenciaCnae() { return $this->inCodigoVigenciaCnae;    }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;       }
/**
    * @access Public
    * @return Integer
*/
function getCodigoCnae() { return $this->inCodigoCnae; }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;           }
/**
    * @access Public
    * @return String
*/
function getValorComposto() { return $this->stValorComposto;     }
/**
    * @access Public
    * @return String
*/
function getValorReduzido() { return $this->stValorComposto;     }
/**
    * @access Public
    * @return Boolean
*/
function getCadastroCnae() { return $this->boCadastroCnae;   }
/**
    * @access Public
    * @return Boolean
*/
function getPopUp() { return $this->boPopUp;  }

/**
     * Método construtor
     * @access Public
*/
function MontaCnae()
{
    $this->obRCEMCnae = new RCEMCnae( new RCEMAtividade );
    $this->stMascara = "";
    $this->boCadastroCnae = true;
    $this->boPopUp = false;
}

/**
    * Monta os combos de cnae conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigenciaCnae );
    if ($this->boCadastroCnae) {
        $this->obRCEMCnae->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMCnae->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    }
    $arCombosCnae = array();
    $boFlagPrimeiroNivelCnae = true;
    $inContNomeCombo = 1;
    while ( !$rsListaNivel->eof() ) {
        $stNumNivel = $rsListaNivel->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivel->getCampo("nom_nivel");
        //DEFINICAO PADRAO DOS COMBOS DE cnae
        $obCmbCnae = new Select;
        $obCmbCnae->setRotulo    ( "CNAE"     );
        $obCmbCnae->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbCnae->setCampoId   ( "[cod_nivel],[cod_cnae],[valor],[valor_reduzido]" );
        $obCmbCnae->setCampoDesc ( "nom_atividade" );
        $obCmbCnae->setStyle     ( "width:250px"     );
        if ( $this->getCadastroCnae() ) {
            $obCmbCnae->setNull      ( false             );
        }
        $obCmbCnae->setName( "inCodCnae_".$inContNomeCombo++ );
        $obCmbCnae->obEvento->setOnChange( "preencheProxComboCnae( $inContNomeCombo );" );
        //PREENCHE APENAS O PRIMEIRO NIVEL
        if ($boFlagPrimeiroNivelCnae) {
           $boFlagPrimeiroNivelCnae = false;
           $this->obRCEMCnae->setCodigoNivel ( $rsListaNivel->getCampo("cod_nivel") );
           $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
           $obCmbCnae->preencheCombo( $rsListaCnae );
        }
        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $arCombosCnae[] = $obCmbCnae;
        $rsListaNivel->proximo();
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE cnae:
    if ( count( $arCombosCnae ) ) {
        //CAMPO TEXT PARA A CHAVE DA cnae
        $obTxtChaveCnae = new TextBox;
        $obTxtChaveCnae->setName      ( "stChaveCnae" );
        $obTxtChaveCnae->setRotulo    ( "CNAE" );
        $obTxtChaveCnae->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveCnae->setSize      ( strlen($this->stMascara) + 2 );
        if ($this->boCadastroCnae) {
            $obTxtChaveCnae->setNull      ( false                    );
        }
        $obTxtChaveCnae->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveCnae->obEvento->setOnChange("preencheCombosCnae();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveisCnae" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveCnae );
        foreach ($arCombosCnae as $obCmbCnae) {
            $obFormulario->addComponente( $obCmbCnae );
        }
    }
}

/**
    * Monta os combos de cnae preenchidos conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormularioPreenchido(&$obFormulario)
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigenciaCnae );
    if ($this->boCadastroCnae) {
        $this->obRCEMCnae->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMCnae->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    }
    $arCombosCnae = array();
    $inContNomeCombo = 1;
    $stValorComposto = "";
    while ( !$rsListaNivel->eof() ) {
        $inCodigoNivel = $rsListaNivel->getCampo("cod_nivel");
        $this->obRCEMCnae->setCodigoNivel   ( $inCodigoNivel );
        $this->obRCEMCnae->setValorReduzidoCnae ( $stValorComposto );
        $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
        //MONTA O VALOR COMBOS CONFORME O NIVEL
        $arValorComposto = explode( ".", $this->stValorComposto );
        $stValorComposto = "";
        for ($inCont = 0; $inCont < $inCodigoNivel; $inCont++) {
           $stValorComposto .= $arValorComposto[$inCont].".";
        }
        $stValorComposto = substr( $stValorComposto , 0, strlen($stValorComposto) - 1 );
        //RECUPERA O VALOR DO CODIGO DA cnae EM RELACAO AO VALOR COMPOSTO
        while ( !$rsListaCnae->eof() ) {
            if ( $rsListaCnae->getCampo("valor_reduzido") == $stValorComposto ) {
                $inCodigoCnae     =  $rsListaCnae->getCampo("cod_cnae");
                $stValorCnae      = $rsListaCnae->getCampo("valor");
                break;
            }
            $rsListaCnae->proximo();
        }
        $rsListaCnae->setPrimeiroElemento();
        //MONTA O VALOR DO COMBO
        $stValorCnae  = $rsListaNivel->getCampo("cod_nivel").",";
        $stValorCnae .= $inCodigoCnae.",";
        $stValorCnae .= $stValorCnae.",";
        $stValorCnae .= $stValorComposto;

        //DEFINICAO PADRAO DOS COMBOS DE cnae
        $stNumNivel = $rsListaNivel->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivel->getCampo("nom_nivel");
        $obCmbCnae = new Select;
        $obCmbCnae->setRotulo    ( "CNAE"     );
        $obCmbCnae->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbCnae->setCampoId   ( "[cod_nivel],[cod_cnae],[valor],[valor_reduzido]" );
        $obCmbCnae->setCampoDesc ( "nom_atividade" );
        $obCmbCnae->setStyle     ( "width:250px"     );
        $obCmbCnae->setNull      ( false             );
        $obCmbCnae->setName      ( "inCodCnae_".$inContNomeCombo );
        $inContNomeCombo++;
        $obCmbCnae->setValue     ( $stValorCnae );
        $obCmbCnae->preencheCombo( $rsListaCnae );

        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $rsListaNivel->proximo();
        $obCmbCnae->obEvento->setOnChange( "preencheProxComboCnae( $inContNomeCombo );" );
        $arCombosCnae[] = $obCmbCnae;
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    if ( count( $arCombosCnae ) ) {
        //CAMPO TEXT PARA A CHAVE DA cnae
        $obTxtChaveCnae = new TextBox;
        $obTxtChaveCnae->setName   ( "stChaveCnae" );
        $obTxtChaveCnae->setRotulo ( "CNAE" );
        $obTxtChaveCnae->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveCnae->setSize      ( strlen($this->stMascara) + 2 );
        $obTxtChaveCnae->setNull      ( false                    );
        $obTxtChaveCnae->setValue     ( $stValorComposto );
        $obTxtChaveCnae->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveCnae->obEvento->setOnChange("preencheCombosCnae();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveisCnae = new Hidden;
        $obHdnNumNiveisCnae->setName  ( "inNumNiveisCnae" );
        $obHdnNumNiveisCnae->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveisCnae );
        $obFormulario->addComponente ( $obTxtChaveCnae );
        foreach ($arCombosCnae as $obCmbCnae) {
            $obFormulario->addComponente( $obCmbCnae );
        }
    }
}

/**
    * Monta os combos de cnae conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxCombo($inPosCombo, $inNumCombos)
{
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigencia );
    $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = $rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodCnae_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";
    }
    if ($this->stValorReduzido) {
        $this->obRCEMCnae->setCodigoNivel      ( $this->inCodigoNivel    );
        $this->obRCEMCnae->setCodigoVigencia   ( $this->inCodigoVigenciaCnae );
        $this->obRCEMCnae->recuperaProximoNivel(  $rsProximoNivelCnae );
        $this->obRCEMCnae->setCodigoNivel      (  $rsProximoNivelCnae->getCampo("cod_nivel") );
        $this->obRCEMCnae->setValorReduzidoCnae( $this->stValorReduzido );
        $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzido .= ".";
            while ( !$rsListaCnae->eof() ) {
                $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                $stNomeCnae   = $rsListaCnae->getCampo( "nom_atividade" );
                $js .= "f.inCodCnae_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomeCnae."','".$stChaveCnae."',''); \n";
                $inContador++;
                $rsListaCnae->proximo();
            }
        }
    }
    $js .= "f.stChaveCnae.value = '".$this->stValorReduzido."';\n";
    $this->obRCEMCnae->setCodigoCnae ( "" );
    if ($this->boPopUp) {
        executaIFrameOculto ( $js );
    } else {
        executaFrameOculto ( $js );
    }
}

/**
    * Preenche os combos a partir da chave da cnae
    * @access Public
*/
function preencheCombos()
{
    $this->obRCEMCnae->setCodigoVigencia ( $this->inCodigoVigencia );
    if ($this->boCadastroCnae) {
        $this->obRCEMCnae->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCEMCnae->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $obErro = $this->obRCEMCnae->listarNiveis( $rsListaNivel );
    }
    if ( strrpos($this->stValorReduzido, ".") == strlen( $this->stValorReduzido ) ) {
        $stValorReduzido = substr( $this->stValorReduzido , 0, strlen( $this->stValorReduzido ) - 1 );
    } else {
        $stValorReduzido = $this->stValorReduzido;
    }
    $arValorReduzido = explode( ".", $stValorReduzido );
    $stValorReduzido = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE SERVCO
    while ( !$rsListaNivel->eof() and key( $arValorReduzido ) < count( $arValorReduzido ) ) {
         if ($inCont == 1) {
             $stValorReduzido .= current( $arValorReduzido );
             $boMontaCombosCnae = true;
         } else {
             if ( $this->obRCEMCnae->getValorReduzido() ) {
                 $boMontaCombosCnae = true;
             } else {
                 $boMontaCombosCnae = false;
             }
             $stValorReduzido .= ".".current( $arValorReduzido );
         }
         next( $arValorReduzido );
         $stNomeCombo = "inCodCnae_".$inCont++;
         $stSelecione = $rsListaNivel->getCampo("nom_nivel");
         $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
         if ($boMontaCombosCnae) {
             $this->obRCEMCnae->setCodigoNivel       ( $rsListaNivel->getCampo("cod_nivel") );
             $obErro = $this->obRCEMCnae->listarCnae( $rsListaCnae );
             $this->obRCEMCnae->setValorReduzidoCnae( $stValorReduzido );
             $inContador = 1;
             while ( !$rsListaCnae->eof() ) {
                 $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" ).",";
                 $stChaveCnae .= $rsListaCnae->getCampo( "cod_cnae").",";
                 $stChaveCnae .= $rsListaCnae->getCampo( "valor").",";
                 $stChaveCnae .= $rsListaCnae->getCampo( "valor_reduzido");
                 $stNomeCnae   = $rsListaCnae->getCampo( "nom_atividade" );
                 if ( $rsListaCnae->getCampo( "valor_reduzido") == $stValorReduzido ) {
                     $stSelected = "selected";
                 } else {
                     $stSelected = "";
                 }
                 $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                 $js .= "new Option('".$stNomeCnae."','".$stChaveCnae."','".$stSelected."'); \n";
                 $inContador++;
                 $rsListaCnae->proximo();
             }
         }
         $rsListaNivel->proximo();
    }
    if ($this->boPopUp) {
        executaIFrameOculto( $js );
    } else {
        executaFrameOculto ( $js );
    }
}

}
?>
