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

    * $Id: MontaLocalizacaoCombos.class.php 63781 2015-10-09 20:50:07Z arthur $

* Casos de uso: uc-05.01.03
*/

/**
    * Classe de regra de interface para Localizacao
    * Data de Criação: 17/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Interface
*/

class MontaLocalizacaoCombos extends Objeto
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
var $inCodigoLocalizacao;
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
var $obRCIMLocalizacao;
/**
    * @access Private
    * @var Boolean
*/
var $boCadastroLocalizacao;
/**
    * @access Private
    * @var Boolean
*/
var $boPopUp;
/**
     * @access Private
     * @var Boolean
 */
var $boObrigatorio;
/**
     * @access Private
     * @var Boolean
*/
var $boCadastroLoteamento;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoLocalizacao($valor) { $this->inCodigoLocalizacao   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido       = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroLocalizacao($valor) { $this->boCadastroLocalizacao = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPopUp($valor) { $this->boPopUp               = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setObrigatorio($valor) { $this->boObrigatorio         = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCadastroLoteamento($valor) { $this->boCadastroLoteamento  = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoVigencia() { return $this->inCodigoVigencia;    }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;       }
/**
    * @access Public
    * @return Integer
*/
function getCodigoLocalizacao() { return $this->inCodigoLocalizacao; }
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
    * @return String
*/
function getObrigatorio() { return $this->boObrigatorio;       }
/**
    * @access Public
    * @return String
*/
function getCadastroLoteamento() { return $this->boCadastroLoteamento; }

/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
    $this->obRCIMLocalizacao = new RCIMLocalizacao;
    $this->stMascara = "";
    $this->boCadastroLocalizacao = true;
    $this->boPopUp = false;
    $this->boObrigatorio = true;
    $this->boCadastroLoteamento = false;
}

/**
    * Monta os combos de localização conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario, $boObrigatorio = true)
{
    if ($this->boCadastroLocalizacao) {

        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCIMLocalizacao->listarNiveisAnteriores( $rsListaNivel );
        $this->obRCIMLocalizacao->setCodigoVigencia ( $rsListaNivel->getCampo('cod_vigencia') );

    } else {

        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel    );
        $this->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $this->obRCIMLocalizacao->setCodigoVigencia( $this->getCodigoVigencia() );
        $obErro = $this->obRCIMLocalizacao->listarNiveis( $rsListaNivel );
    }
    
    $arCombosLocalizacao = array();
    $boFlagPrimeiroNivel = true;
    $inContNomeCombo = 1;

    while ( !$rsListaNivel->eof() ) {

        $stNumNivel = $rsListaNivel->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivel->getCampo("nom_nivel");
        //DEFINICAO PADRAO DOS COMBOS DE LOCALIZACAO

        $obCmbLocalizacao = new Select;
        if ( !$this->getCadastroLoteamento() ) {
            $obCmbLocalizacao->setRotulo    ( "Localização"     );
        } else {
            $obCmbLocalizacao->setRotulo    ( "Localização de origem"     );
        }
        $obCmbLocalizacao->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbLocalizacao->setCampoId   ( "[cod_nivel]-[cod_localizacao]-[valor]-[valor_reduzido]" );
        $obCmbLocalizacao->setCampoDesc ( "nom_localizacao" );
        $obCmbLocalizacao->setStyle     ( "width:250px"     );
        if ( $this->getObrigatorio() ) {
            $obCmbLocalizacao->setNull( false );
        } else {
            $obCmbLocalizacao->setNull( true  );
        }
        $obCmbLocalizacao->setName( "inCodLocalizacao_".$inContNomeCombo++ );
        $obCmbLocalizacao->obEvento->setOnChange( "preencheProxCombo( $inContNomeCombo );" );

        //PREENCHE APENAS O PRIMEIRO NIVEL
        if ($boFlagPrimeiroNivel) {
           $rsListaLocalizacao = new RecordSet();
           $boFlagPrimeiroNivel = false;
           $this->obRCIMLocalizacao->setCodigoVigencia( $rsListaNivel->getCampo("cod_vigencia") );
           $this->obRCIMLocalizacao->setCodigoNivel   ( $rsListaNivel->getCampo("cod_nivel") );
           $obErro = $this->obRCIMLocalizacao->listarLocalizacaoPrimeiroNivel( $rsListaLocalizacao );           
           $obCmbLocalizacao->preencheCombo( $rsListaLocalizacao );
        }
        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $arCombosLocalizacao[] = $obCmbLocalizacao;
        $rsListaNivel->proximo();
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE LOCALIZAÇÃO:
    if ( count( $arCombosLocalizacao ) ) {
        //CAMPO TEXT PARA A CHAVE DA LOCALIZACAO
        $obTxtChaveLocalizacao = new TextBox;
        $obTxtChaveLocalizacao->setName     ( "stChaveLocalizacao" );
        $obTxtChaveLocalizacao->setId       ( "stChaveLocalizacao" );
        $obTxtChaveLocalizacao->setRotulo   ( "Localização" );
        $obTxtChaveLocalizacao->setMaxLength( strlen($this->stMascara) );
        $obTxtChaveLocalizacao->setSize     ( strlen($this->stMascara) + 2 );
        $obTxtChaveLocalizacao->setTitle    ( "Informe a localização desejada." );
        if ( $this->getObrigatorio() ) {
            $obTxtChaveLocalizacao->setNull( false );
        } else {
            $obTxtChaveLocalizacao->setNull( true  );
        }
        $obTxtChaveLocalizacao->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveLocalizacao->obEvento->setOnChange("preencheCombos();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveis" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveLocalizacao );
        foreach ($arCombosLocalizacao as $obCmbLocalizacao) {
            $obFormulario->addComponente( $obCmbLocalizacao );
        }
    }
}

/**
    * Monta os combos de localização preenchidos conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormularioPreenchido(&$obFormulario)
{
    $this->obRCIMLocalizacao->setCodigoVigencia ( $this->getCodigoVigencia() );
    if ($this->boCadastroLocalizacao) {
        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCIMLocalizacao->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $this->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCIMLocalizacao->listarNiveis( $rsListaNivel );
    }
    $arCombosLocalizacao = array();
    $inContNomeCombo = 1;
    $stValorComposto = "";
    while ( !$rsListaNivel->eof() ) {
        $inCodigoNivel = $rsListaNivel->getCampo("cod_nivel");
        $this->obRCIMLocalizacao->setCodigoNivel   ( $inCodigoNivel );
        $this->obRCIMLocalizacao->setValorreduzido ( $stValorComposto );
        $obErro = $this->obRCIMLocalizacao->listarLocalizacao( $rsListaLocalizacao );
        //MONTA O VALOR COMBOS CONFORME O NIVEL
        $arValorComposto = explode( ".", $this->stValorComposto );
        $stValorComposto = "";
        for ($inCont = 0; $inCont < $inCodigoNivel; $inCont++) {
           $stValorComposto .= $arValorComposto[$inCont].".";
        }
        $stValorComposto = substr( $stValorComposto , 0, strlen($stValorComposto) - 1 );
        //RECUPERA O VALOR DO CODIGO DA LOCALIZACAO EM RELACAO AO VALOR COMPOSTO
        while ( !$rsListaLocalizacao->eof() ) {
            if ( $rsListaLocalizacao->getCampo("valor_reduzido") == $stValorComposto ) {
                $inCodigoLocalizacao = $rsListaLocalizacao->getCampo("cod_localizacao");
                $stValor             = $rsListaLocalizacao->getCampo("valor");
                break;
            }
            $rsListaLocalizacao->proximo();
        }
        $rsListaLocalizacao->setPrimeiroElemento();
        //MONTA O VALOR DO COMBO
        $stValorLocalizacao  = $rsListaNivel->getCampo("cod_nivel")."-";
        $stValorLocalizacao .= $inCodigoLocalizacao."-";
        $stValorLocalizacao .= $stValor."-";
        $stValorLocalizacao .= $stValorComposto;

    //DEFINICAO PADRAO DOS COMBOS DE LOCALIZACAO
        $stNumNivel = $rsListaNivel->getCampo("cod_nivel");
        $stNomeNivel[$stNumNivel] = $rsListaNivel->getCampo("nom_nivel");
        $obCmbLocalizacao = new Select;
        if (!$this->boCadastroLoteamento) {
            $obCmbLocalizacao->setRotulo    ( "Localização"     );
        } else {
            $obCmbLocalizacao->setRotulo    ( "Localização de origem" );
        }
        $obCmbLocalizacao->addOption    ( "", "Selecione $stNomeNivel[$stNumNivel]"   );
        $obCmbLocalizacao->setCampoId   ( "[cod_nivel]-[cod_localizacao]-[valor]-[valor_reduzido]" );
        $obCmbLocalizacao->setCampoDesc ( "nom_localizacao" );
        $obCmbLocalizacao->setStyle     ( "width:250px"     );
        $obCmbLocalizacao->setNull      ( false             );
        $obCmbLocalizacao->setName      ( "inCodLocalizacao_".$inContNomeCombo );
        $inContNomeCombo++;
        $obCmbLocalizacao->setValue     ( $stValorLocalizacao );
        $obCmbLocalizacao->preencheCombo( $rsListaLocalizacao );

        //MONTA A MASCARA PARA A FUNCAO MASCARADINAMICO
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $rsListaNivel->proximo();
        $obCmbLocalizacao->obEvento->setOnChange( "preencheProxCombo( $inContNomeCombo );" );
        $arCombosLocalizacao[] = $obCmbLocalizacao;
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    if ( count( $arCombosLocalizacao ) ) {
        //CAMPO TEXT PARA A CHAVE DA LOCALIZACAO
        $obTxtChaveLocalizacao = new TextBox;
        $obTxtChaveLocalizacao->setName   ( "stChaveLocalizacao" );
        $obTxtChaveLocalizacao->setId     ( "stChaveLocalizacao" );
        $obTxtChaveLocalizacao->setRotulo ( "Localização" );
        $obTxtChaveLocalizacao->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveLocalizacao->setSize      ( strlen($this->stMascara) + 2 );
        $obTxtChaveLocalizacao->setNull      ( false                    );
        $obTxtChaveLocalizacao->setValue     ( $stValorComposto );
        $obTxtChaveLocalizacao->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
        $obTxtChaveLocalizacao->obEvento->setOnChange("preencheCombos();");
        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveis" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden     ( $obHdnNumNiveis        );
        $obFormulario->addComponente ( $obTxtChaveLocalizacao );
        foreach ($arCombosLocalizacao as $obCmbLocalizacao) {
            $obFormulario->addComponente( $obCmbLocalizacao );
        }
    }
}

/**
    * Monta os combos de localização conforme o nível setado
    * @access Public
    * @param Integer $inPosCombo Posição do combo no formulário
    * @param Integer $inNumCombos Número de combos no formulário
*/
function preencheProxCombo($inPosCombo, $inNumCombos)
{
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    if (!$this->boCadastroLocalizacao) {
        $this->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    }
    $this->obRCIMLocalizacao->setCodigoVigencia ( $this->getCodigoVigencia() );
    $obErro = $this->obRCIMLocalizacao->listarNiveis( $rsListaNivel );

    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = $rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodLocalizacao_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
    }
    if ($this->stValorReduzido) {
        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel    );
        $this->obRCIMLocalizacao->setCodigoVigencia ( $this->inCodigoVigencia );
        $this->obRCIMLocalizacao->recuperaProximoNivel( $rsProximoNivel );
        $this->obRCIMLocalizacao->setCodigoNivel    (  $rsProximoNivel->getCampo("cod_nivel") );
        $this->obRCIMLocalizacao->setValorReduzido( $this->stValorReduzido );
        $obErro = $this->obRCIMLocalizacao->listarLocalizacao( $rsListaLocalizacao );
        $stMascaraTMP = $rsListaLocalizacao->getCampo('mascara');
        $contMascara = 0;
        while ( $contMascara < strlen ($stMascaraTMP)) {
            $stMascaraAtual .= "0";
            $contMascara++;
        }
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzido .= ".";
            while ( !$rsListaLocalizacao->eof() ) {
                if ( $stMascaraAtual != $rsListaLocalizacao->getCampo('valor') ) {
                    $stChaveLocalizacao  = $rsListaLocalizacao->getCampo( "cod_nivel" )."-";
                    $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "cod_localizacao")."-";
                    $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "valor")."-";
                    $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "valor_reduzido");
                    $stNomeLocalizacao   = $rsListaLocalizacao->getCampo( "nom_localizacao" );
                    $js .= "f.inCodLocalizacao_".$inPosCombo.".options[$inContador] = ";
                    $js .= "new Option('".$stNomeLocalizacao."','".$stChaveLocalizacao."',''); \n";
                    $inContador++;
                }
                $rsListaLocalizacao->proximo();
            }
        }
    }
    $js .= "f.stChaveLocalizacao.value = '".$this->stValorReduzido."';\n";
    $this->obRCIMLocalizacao->setCodigoLocalizacao ( "" );
    if ($this->boPopUp) {
        SistemaLegado::executaIFrameOculto ( $js );
    } else {
        if (!$this->boCadastroLoteamento) {
            SistemaLegado::executaFrameOculto ( $js );
        } else {
            return $js;
        }
    }
}

/**
    * Preenche os combos a partir da chave da localização
    * @access Public
*/
function preencheCombos()
{
    if (!$this->boCadastroLocalizacao) {
        $this->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    }
    
    $this->obRCIMLocalizacao->setCodigoVigencia ( $this->getCodigoVigencia() );
    
    if ($this->boCadastroLocalizacao) {
        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel    );
        $obErro = $this->obRCIMLocalizacao->listarNiveisAnteriores( $rsListaNivel );
    } else {
        $obErro = $this->obRCIMLocalizacao->listarNiveis( $rsListaNivel );
    }
    
    if ( strrpos($this->stValorReduzido, ".") == strlen( $this->stValorReduzido ) ) {
        $stValorReduzido = substr( $this->stValorReduzido , 0, strlen( $this->stValorReduzido ) - 1 );
    } else {
        $stValorReduzido = $this->stValorReduzido;
    }
    
    $arValorReduzido = explode( ".", $stValorReduzido );
    $stValorReduzido = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE LOCALIZACAO
    
    while ( !$rsListaNivel->eof() and key( $arValorReduzido ) < count( $arValorReduzido ) ) {
         
         if ($inCont == 1) {
             $stValorReduzido .= current( $arValorReduzido );
             $boMontaCombos = true;
         } else {
             if ( $this->obRCIMLocalizacao->getValorReduzido() ) {
                 $boMontaCombos = true;
             } else {
                 $boMontaCombos = false;
             }
             $stValorReduzido .= ".".current( $arValorReduzido );
         }
         
         next( $arValorReduzido );
         $stNomeCombo = "inCodLocalizacao_".$inCont++;
         $stSelecione = $rsListaNivel->getCampo("nom_nivel");
         $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
         
         if ($boMontaCombos) {
             $this->obRCIMLocalizacao->setCodigoNivel       ( $rsListaNivel->getCampo("cod_nivel") );
             $obErro = $this->obRCIMLocalizacao->listarLocalizacao( $rsListaLocalizacao );
             $this->obRCIMLocalizacao->setValorReduzido     ( $stValorReduzido );
             $inContador = 1;
             while ( !$rsListaLocalizacao->eof() ) {
                 $stChaveLocalizacao  = $rsListaLocalizacao->getCampo( "cod_nivel" )."-";
                 $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "cod_localizacao")."-";
                 $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "valor")."-";
                 $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "valor_reduzido");
                 $stNomeLocalizacao   = $rsListaLocalizacao->getCampo( "nom_localizacao" );
                 
                 if ( $rsListaLocalizacao->getCampo( "valor_reduzido") == $stValorReduzido ) {
                      $stValorSelecionado = $stChaveLocalizacao;
                 }
                 $js .= "f.".$stNomeCombo.".options[$inContador] = ";
                 $js .= "new Option('".$stNomeLocalizacao."','".$stChaveLocalizacao."'); \n";
                 $inContador++;
                 $rsListaLocalizacao->proximo();
             }
             $js .= "f.".$stNomeCombo.".value = '".$stValorSelecionado."';\n";  
         }
         $rsListaNivel->proximo();
    }
    
    if ($this->boPopUp) {
        SistemaLegado::executaIFrameOculto ( $js );
    } else {
        if (!$this->boCadastroLoteamento) {
            SistemaLegado::executaFrameOculto ( $js );
        } else {
            return $js;
        }
    }
}

}

?>