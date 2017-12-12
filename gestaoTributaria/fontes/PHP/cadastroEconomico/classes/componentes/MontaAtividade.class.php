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

* $Id: MontaAtividade.class.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.13  2007/04/03 16:02:27  rodrigo
Bug #8950#

Revision 1.12  2006/10/02 17:32:27  fernando
Implementação do método setNullBarra()

Revision 1.11  2006/09/15 11:57:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

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
var $inInscricaoEconomica;
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
    * @access Private
    * @var Boolean
*/
var $boNullBarra;
/**
    *@access Private
    *@var String
*/
var $stDefinicao;
/**
    *@access Private
    *@var Array
*/
var $arCmbAtividade;
/**
    *@access Private
    *@var Integer
*/
var $inNivelObrigatorio;

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
    * @param Boolean $valor
*/
function setNullBarra($valor) { $this->boNullBarra = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setInscricaoEconomica($valor) { $this->inInscricaoEconomica = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNivelObrigatorio($valor) { $this->inNivelObrigatorio= $valor; }

/**
    * @access Public
    * @return Integer
*/
function getTitle() { return $this->stTitle;  }
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
    * @access Public
    * @return Boolean
*/
function getInscricaoEconomica() { return $this->inInscricaoEconomica; }
/**
    * @access Public
    * @return Boolean
*/
function getNullBarra() { return $this->boNullBarra; }
/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao; }
/**
    * @access Public
    * @return Array
*/
function getCmbAtividade() { return $this->arCmbAtividade; }

/**
     * Método construtor
     * @access Private
*/
function MontaAtividade()
{
     include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
    $this->obRCEMAtividade = new RCEMAtividade;
    $this->stMascara = "";
    $this->boCadastroAtividade = true;
    $this->boRetornaJs = false;
    $this->setDefinicao("MONTAATIVIDADE");
    $this->arCmbAtividade = array();
    $this->inNivelObrigatorio=0;
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
        if ($this->getNullBarra()) {
            $obCmbAtividade->setNullBarra(true);
        }
        $obCmbAtividade->setRotulo    ( "Atividade"     );
        $obCmbAtividade->addOption    ( "", "Selecione $stNomeNivel[$inNumNivel]"   );
        $obCmbAtividade->setCampoId   ( "[cod_nivel]§[cod_atividade]§[valor]§[valor_reduzido]" );
        $obCmbAtividade->setCampoDesc ( "nom_atividade" );
        $obCmbAtividade->setStyle     ( "width:250px"     );
        if ($this->boCadastroAtividade) {
            $obCmbAtividade->setNull      ( false             );
        }
        if ($inContNomeCombo > $this->inNivelObrigatorio AND $this->inNivelObrigatorio > 0) {
            $obCmbAtividade->setNull(true);
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

    $obHdnMascara = new Hidden;
    $obHdnMascara->setName ( 'stMascara' );
    $obHdnMascara->setValue ( $this->stMascara );

    //MONTA O FORMULÁRIO DOS NIVEIS DE ATIVIDADE:
    if ( count( $arCombosAtividade ) ) {

        //CAMPO TEXT PARA A CHAVE DA ATIVIDADE
        $obTxtChaveAtividade = new TextBox;
        $obTxtChaveAtividade->setId("stChaveAtividade");
        $obTxtChaveAtividade->setName   ( "stChaveAtividade" );
        $obTxtChaveAtividade->setRotulo ( "Atividade" );
        $obTxtChaveAtividade->setTitle($this->stTitle);
        $obTxtChaveAtividade->setMaxLength ( strlen($this->stMascara) );
        $obTxtChaveAtividade->setSize      ( strlen($this->stMascara) + 2 );
        $obTxtChaveAtividade->setMascara ( $this->stMascara );
        if ($this->boCadastroAtividade) {
            $obTxtChaveAtividade->setNull      ( false                    );
        }

        $obTxtChaveAtividade->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event); ");
        $obTxtChaveAtividade->obEvento->setOnChange("preencheCombosAtividade();");

        //GUARDA O NUMERO DE NIVEIS PAA AUXILIAR O METODO PREENCHE PROX. COMBO A LIMPAR OS COMBOS SEGUINTES
        $obHdnNumNiveis = new Hidden;
        $obHdnNumNiveis->setName  ( "inNumNiveis" );
        $obHdnNumNiveis->setValue ( $inContNomeCombo );
        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addHidden    ( $obHdnNumNiveis      );
        $obFormulario->addHidden    ( $obHdnMascara         );
        $obFormulario->addComponente ( $obTxtChaveAtividade );
        foreach ($arCombosAtividade as $obCmbAtividade) {
            $obFormulario->addComponente( $obCmbAtividade );
        }
    }

    $this->arCmbAtividade = $arCombosAtividade;
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
        $obCmbAtividade->setCampoId   ( "[cod_nivel]§[cod_atividade]§[valor]§[valor_reduzido]" );
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
function preencheProxComboCNAE($inPosCombo, $inNumCombos)
{
    //LIMPA OS COMBOS ABAIXO DO NIVEL SELECIONADO
    $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
    $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    for ($inCont = $inPosCombo; $inCont < $inNumCombos; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = "Selecione ".$rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodAtividade_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('".$stSelecione."','', 'selected');\n";
    }

    if ($this->stValorReduzido) {
        $this->obRCEMAtividade->setCodigoNivel      ( $this->inCodigoNivel    );
        $this->obRCEMAtividade->setCodigoVigencia   ( $this->inCodigoVigencia );
        $this->obRCEMAtividade->recuperaProximoNivel(  $rsProximoNivelCnae );
        $this->obRCEMAtividade->setCodigoNivel      (  $rsProximoNivelCnae->getCampo("cod_nivel") );
        $this->obRCEMAtividade->setValorReduzido( $this->stValorReduzido );
        $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzido .= ".";

            while ( !$rsListaCnae->eof() ) {
                $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" )."§";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_atividade")."§";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor")."§";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_estrutural");

                $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";
                $inContador++;
                $rsListaCnae->proximo();
            }
        }
    }

    $js .= "f.stChaveAtividade.value = '".$this->stValorReduzido."';\n";
    $this->obRCEMAtividade->setCodigoAtividade ( "" );
    if ($this->boPopUp) {
        executaIFrameOculto ( $js );
    } else {
        sistemaLegado::executaFrameOculto ( $js );
    }
}

function preencheCombos2()
{
    $this->obRCEMAtividade->setCodigoVigencia ( $this->inCodigoVigencia );
    $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    for ($inCont = 1; $inCont < 6; $inCont++) {
        $rsListaNivel->setCorrente($inCont);
        $stSelecione = "Selecione ".$rsListaNivel->getCampo("nom_nivel");
        $stNomeCombo = "inCodAtividade_".$inCont;
        $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $js .= "f.".$stNomeCombo.".options[0] = new Option('".$stSelecione."','', 'selected');\n";
    }

    $stValorReduzido = $this->stValorReduzido;
    $arValorReduzido = explode( ".", $stValorReduzido );
/*
    $stTMP = $arValorReduzido[2];
    $arValorReduzido[2] = substr( $stTMP, 0, 1 );
    $arValorReduzido[3] = substr( $stTMP, 1, 3 );
    $arValorReduzido[4] = substr( $stTMP, 5, 2 );
*/

    $stValorSelecionar = -1;
    if ($arValorReduzido[0] != "") {
        $this->obRCEMAtividade->setCodigoNivel ( 1 );
        $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0] );
        $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
        $stValorSelecionar = $rsListaCnae->getCampo( "cod_atividade");
    }

    $this->obRCEMAtividade->setCodigoNivel ( 1 );
    $this->obRCEMAtividade->setValorReduzido( "" );
    $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );

    $inContador = 1;
    $inPosCombo = 1;
    while ( !$rsListaCnae->Eof() ) {
        $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" )."§";
        $stChaveCnae .= $rsListaCnae->getCampo( "cod_atividade")."§";
        $stChaveCnae .= $rsListaCnae->getCampo( "valor")."§";
        $stChaveCnae .= $rsListaCnae->getCampo( "cod_estrutural");
        $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
        $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
        if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_atividade") )
            $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
        else
            $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

        $inContador++;
        $rsListaCnae->proximo();
    }

    if ($arValorReduzido[0] != "") {
        $stValorSelecionar = -1;
        if ($arValorReduzido[1] != "") {
            $this->obRCEMAtividade->setCodigoNivel ( 2 );
            $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1] );
            $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
            $stValorSelecionar = $rsListaCnae->getCampo( "cod_atividade");
        }

        $this->obRCEMAtividade->setCodigoNivel ( 2 );
        $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0] );
        $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
        $inContador = 1;
        $inPosCombo = 2;
        while ( !$rsListaCnae->Eof() ) {
            $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" )."§";
            $stChaveCnae .= $rsListaCnae->getCampo( "cod_atividade")."§";
            $stChaveCnae .= $rsListaCnae->getCampo( "valor")."§";
            $stChaveCnae .= $rsListaCnae->getCampo( "cod_estrutural");
            $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
            $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
            if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_atividade") )
                $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
            else
                $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

            $inContador++;
            $rsListaCnae->proximo();
        }

        if ($arValorReduzido[1] != "") {
            $stValorSelecionar = -1;
            if ($arValorReduzido[2] != "") {
                $this->obRCEMAtividade->setCodigoNivel ( 3 );
                $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2] );
                $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
                $stValorSelecionar = $rsListaCnae->getCampo( "cod_atividade");
            }

            $this->obRCEMAtividade->setCodigoNivel ( 3 );
            $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1] );
            $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
            $inContador = 1;
            $inPosCombo = 3;
            while ( !$rsListaCnae->Eof() ) {
                $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" )."§";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_atividade")."§";
                $stChaveCnae .= $rsListaCnae->getCampo( "valor")."§";
                $stChaveCnae .= $rsListaCnae->getCampo( "cod_estrutural");
                $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_atividade") )
                    $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
                else
                    $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

                $inContador++;
                $rsListaCnae->proximo();
            }

            if ($arValorReduzido[2] != "") {
                $stValorSelecionar = -1;
                if ($arValorReduzido[3] != "") {
                    $this->obRCEMAtividade->setCodigoNivel ( 4 );
                    $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2].'.'.$arValorReduzido[3] );
                    $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
                    $stValorSelecionar = $rsListaCnae->getCampo( "cod_atividade");
                }

                $this->obRCEMAtividade->setCodigoNivel ( 4 );
                $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2] );
                $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
            //C.14.22-3/00
                $inContador = 1;
                $inPosCombo = 4;
                while ( !$rsListaCnae->Eof() ) {
                    $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" )."§";
                    $stChaveCnae .= $rsListaCnae->getCampo( "cod_atividade")."§";
                    $stChaveCnae .= $rsListaCnae->getCampo( "valor")."§";
                    $stChaveCnae .= $rsListaCnae->getCampo( "cod_estrutural");
                    $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                    $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                    if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_atividade") )
                        $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
                    else
                        $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

                    $inContador++;
                    $rsListaCnae->proximo();
                }

                if ($arValorReduzido[3] != "") {
                    $stValorSelecionar = -1;
                    if ($arValorReduzido[4] != "") {
                        $this->obRCEMAtividade->setCodigoNivel ( 5 );
                        $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2].'.'.$arValorReduzido[3].'.'.$arValorReduzido[4] );
                        $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
                        $stValorSelecionar = $rsListaCnae->getCampo( "cod_atividade");
                    }

                    $this->obRCEMAtividade->setCodigoNivel ( 5 );
                    $this->obRCEMAtividade->setValorReduzido( $arValorReduzido[0].".".$arValorReduzido[1].'.'.$arValorReduzido[2].'.'.$arValorReduzido[3] );
                    $obErro = $this->obRCEMAtividade->listarAtividadeComboCNAE( $rsListaCnae );
                //C.14.22-3/00
                    $inContador = 1;
                    $inPosCombo = 5;
                    while ( !$rsListaCnae->Eof() ) {
                        $stChaveCnae  = $rsListaCnae->getCampo( "cod_nivel" )."§§";
                        $stChaveCnae .= $rsListaCnae->getCampo( "cod_atividade")."§";
                        $stChaveCnae .= $rsListaCnae->getCampo( "valor")."§";
                        $stChaveCnae .= $rsListaCnae->getCampo( "cod_estrutural");
                        $stNomeCnae = $rsListaCnae->getCampo( "nom_atividade" );
                        $stNomeCnae = substr($stNomeCnae, 0, strlen($stNomeCnae)-1);
                        if ( $stValorSelecionar == $rsListaCnae->getCampo( "cod_atividade") )
                            $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."', 'selected'); \n";
                        else
                            $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = new Option('".$stNomeCnae."','".$stChaveCnae."'); \n";

                        $inContador++;
                        $rsListaCnae->proximo();
                    }
                }
            }
        }
    }

    $js .= "f.stChaveAtividade.value = '".$this->stValorReduzido."';\n";

    sistemaLegado::executaFrameOculto ( $js );
}

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
                $stChaveAtividade  = $rsListaAtividade->getCampo( "cod_nivel" )."§";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "cod_atividade")."§";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "valor")."§";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "valor_reduzido");
                $stNomeAtividade   = $rsListaAtividade->getCampo( "nom_atividade" );

                $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomeAtividade."','".$stChaveAtividade."',''); \n";
                $inContador++;
                $rsListaAtividade->proximo();
            }
        }
    }

    if ($stChaveAtividade == "" or $stChaveAtividade == null) {
      if ($_REQUEST['stMascara'] == 'Z.99.9.9-9.99') {
          if ( strlen($this->stValorReduzido) == 11 ) {
              $this->stValorReduzido = $this->stValorReduzido."0";
              $stFiltro = " WHERE cod_estrutural like '".$this->stValorReduzido."%'"." AND cod_nivel = ".$_REQUEST['inPosicao']  ;
              $this->obRCEMAtividade->obTCEMAtividade->recuperaAtividadeCombo( $rsListaAtividade, $stFiltro);
                $inContador = 1;
                $stChaveAtividade  = $rsListaAtividade->getCampo( "cod_nivel" )."§";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "cod_atividade")."§";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "valor")."§";
                $stChaveAtividade .= $rsListaAtividade->getCampo( "valor_reduzido").".0";
                $stNomeAtividade   = $rsListaAtividade->getCampo( "nom_atividade" );
                $js .= "f.inCodAtividade_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomeAtividade."','".$stChaveAtividade."',''); \n";
                $_REQUEST['stChaveAtividade'] = $rsListaAtividade->getCampo("valor_reduzido")."0";
                $_REQUEST['inCodAtividade_5'] = $stChaveAtividade;
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
        //echo 'ListaNiveisAnteriores<br><br>';
        $this->obRCEMAtividade->setCodigoNivel    ( $this->inCodigoNivel    );
        // $this->obRCEMAtividade->setCodigoNivel    ( 3    );
        $obErro = $this->obRCEMAtividade->listarNiveisAnteriores( $rsListaNivel );
    } else {

        $this->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
        $obErro = $this->obRCEMAtividade->listarNiveis( $rsListaNivel );
    }

    if ($this->stValorReduzido) {
        if ( strrpos($this->stValorReduzido, ".") == strlen( $this->stValorReduzido ) ) {
            $stValorReduzido = substr( $this->stValorReduzido , 0, strlen( $this->stValorReduzido ) - 1 );
        }
    } else {
            $stValorReduzido = $this->stValorReduzido;
    }

    $arValorReduzido = explode( ".", $this->stValorReduzido );

    $stValorReduzido = "";
    $inCont = 1;//CONTADOR DOS COMBOS DOS NIVEIS DE ATIVIDADE
    while ( !$rsListaNivel->eof() and key( $arValorReduzido ) < (count( $arValorReduzido )+1) ) {

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
         $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','');\n";

         sistemaLegado::executaFrameOculto ( $js );

         if ($boMontaCombos) {
             $this->obRCEMAtividade->setCodigoNivel       ( $rsListaNivel->getCampo("cod_nivel") );
             $obErro = $this->obRCEMAtividade->listarAtividade( $rsListaAtividade );
             $this->obRCEMAtividade->setValorReduzido     ( $stValorReduzido );
             $inContador = 1;
             while ( !$rsListaAtividade->eof() ) {
                 $stChaveAtividade  = $rsListaAtividade->getCampo( "cod_nivel" )."§";
                 $stChaveAtividade .= $rsListaAtividade->getCampo( "cod_atividade")."§";
                 $stChaveAtividade .= $rsListaAtividade->getCampo( "valor")."§";
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

   $cont = 0; $stChaveAtividadeTMP = '';
    while ( $cont < ( strlen ( $this->getMascara() ) - strlen ( $_REQUEST['stChaveAtividade']) ) ) {
        $stChaveAtividadeTMP .= '0';
        $cont++;
    }
    $stChaveAtividadeTMP .= $_REQUEST['stChaveAtividade'] ;
    $js .= "f.stChaveAtividade.value='". $stChaveAtividadeTMP . "';\n";

    if ( !$this->getRetornaJs() ) {
        sistemaLegado::executaFrameOculto ( $js );
    } else {
        return $js;
    }
}

function geraLimpaComboAtividade($stName)
{
    $stJs  = "limpaSelect(f.".$stName.",0);\n";
    $stJs .= "f.".$stName.".options[0] = new Option('Selecione Atividade','','selected');\n";

    return $stJs;
}

function geraFormularioRestrito(&$js,$stName)
{
    include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
    $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade ( new RCEMInscricaoEconomica);
    $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $this->getInscricaoEconomica() );
    $obRCEMInscricaoAtividade->listarMontaAtividadesInscricao ( $rsAtividades);
    $js = $this->geraLimpaComboAtividade($stName);
    if ( !$rsAtividades->eof()) {
        $inContador = 1;
        while ( !$rsAtividades->eof() ) {
            $js .= "f.".$stName.".options[$inContador] = ";
            $js .= "new Option('".$rsAtividades->getCampo("cod_estrutural")." - ".$rsAtividades->getCampo("nom_atividade")."','".$rsAtividades->getCampo("cod_atividade")."',''); \n";
            $inContador++;
            $rsAtividades->proximo();
        }
    }
}

} // end of class
?>
