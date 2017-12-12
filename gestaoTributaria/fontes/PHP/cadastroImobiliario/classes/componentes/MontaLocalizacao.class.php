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
    * Classe de regra de interface para Localizacao
    * Data de Criação: 17/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Interface

    * $Id: MontaLocalizacao.class.php 63826 2015-10-21 16:39:23Z arthur $

    * Casos de uso: uc-05.01.03
*/

class MontaLocalizacao extends Objeto
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
var $inNivelCorte;
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
var $stFuncao;
var $boEscondeId; // variavel utilizada para esconder o campo de ID do BuscaInner
var $obBscChaveLocalizacao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setEscondeId($valor) { $this->boEscondeId      = $valor; }
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
    * @param Integer $valor
*/
function setNivelCorte($valor) { $this->inNivelCorte   = $valor; }
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
    * @return Integer
*/
function getNivelCorte() { return $this->inNivelCorte; }
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
    * @access Public
    * @return String
*/
function getEscondeId() { return $this->boEscondeId; }

/**
     * Método construtor
     * @access Private
*/
function MontaLocalizacao()
{
    include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
    $this->obRCIMLocalizacao = new RCIMLocalizacao;
    $this->stMascara = "";
    $this->boCadastroLocalizacao = true;
    $this->boPopUp = false;
    $this->boObrigatorio = true;
    $this->boCadastroLoteamento = false;

    $this->obBscChaveLocalizacao = new BuscaInner;
    $this->obBscChaveLocalizacao->setRotulo ( "Localização" );
    $this->obBscChaveLocalizacao->setTitle  ( "Informe o código da localização" );
    $this->obBscChaveLocalizacao->obCampoCod->setName ( "stChaveLocalizacao" );
}

/**
    * Monta os combos de localização conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario, $boObrigatorio = true)
{
    if (!$this->getCodigoVigencia()) {
        $this->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    }

    $this->obRCIMLocalizacao->setCodigoVigencia( $this->getCodigoVigencia() );
    $obErro = $this->obRCIMLocalizacao->listarNiveis( $rsListaNivel );
    while ( !$rsListaNivel->eof() ) {
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $rsListaNivel->proximo();
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE LOCALIZAÇÃO:

    if ( !$this->getEscondeId() )
        $this->obBscChaveLocalizacao->setId     ( "stNomeChaveLocalizacao" );

    if ($this->inNivelCorte) {

        $arMascara = explode(".",$this->stMascara);
        $i = 0;
        $stMascara = "";
        while ( $i < $this->getNivelCorte() ) {
            $stMascara .= $arMascara[$i].".";
            $i++;
        }

        $this->stMascara = substr($stMascara,0,strlen($stMascara)-1);
    }

    $this->obBscChaveLocalizacao->obCampoCod->setMinLength ( strlen($this->stMascara) );
    $this->obBscChaveLocalizacao->obCampoCod->setMaxLength ( strlen($this->stMascara) );
    $this->obBscChaveLocalizacao->obCampoCod->setSize      ( strlen($this->stMascara) + 2 );

    $this->obBscChaveLocalizacao->obCampoCod->setAlign ("center");
    $this->obBscChaveLocalizacao->obCampoCod->obEvento->setOnBlur("buscaDado('buscaLocalizacao');");
    $this->obBscChaveLocalizacao->obCampoCod->obEvento->setOnChange("buscaDado('buscaLocalizacao');");
    $this->obBscChaveLocalizacao->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
    $stTmpAcao = "nomLocalizacao";
    if ($this->inNivelCorte) {
        $stTmpAcao = "buscaReduzido";
    }
    $this->obBscChaveLocalizacao->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."localizacao/FLBuscaLocalizacao.php','frm','stChaveLocalizacao','stNomeChaveLocalizacao','buscaLocalizacaoInner','".Sessao::getId()."','800','550');");

    if ( $this->getObrigatorio() ) {
        $this->obBscChaveLocalizacao->setNull( false );
    } else {
        $this->obBscChaveLocalizacao->setNull( true  );
    }
    $obFormulario->addComponente ( $this->obBscChaveLocalizacao );
}

function geraFormulario2(&$obFormulario, $boObrigatorio = true, $inCodNvl)
{
    if (!$this->getCodigoVigencia()) {
        $this->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigenciaAtual );
        $this->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    }

    $this->obRCIMLocalizacao->setCodigoVigencia( $this->getCodigoVigencia() );
    $obErro = $this->obRCIMLocalizacao->listarNiveis( $rsListaNivel );
    while ( !$rsListaNivel->eof() ) {
        $this->stMascara .= $rsListaNivel->getCampo("mascara").".";
        $rsListaNivel->proximo();
    }
    $this->stMascara = substr( $this->stMascara, 0 , strlen($this->stMascara) - 1 );
    //MONTA O FORMULÁRIO DOS NIVEIS DE LOCALIZAÇÃO:
    $this->obBscChaveLocalizacao = new BuscaInner;
    $this->obBscChaveLocalizacao->setId     ( "stNomeChaveLocalizacao" );
    $this->obBscChaveLocalizacao->setRotulo ( "Localização" );
    $this->obBscChaveLocalizacao->setTitle  ( "Informe o código da localização" );
    if ($this->inNivelCorte) {

        $arMascara = explode(".",$this->stMascara);
        $i = 0;
        $stMascara = "";
        while ( $i < $this->getNivelCorte() ) {
            $stMascara .= $arMascara[$i].".";
            $i++;
        }

        $this->stMascara = substr($stMascara,0,strlen($stMascara)-1);
    }

    $this->obBscChaveLocalizacao->obCampoCod->setMinLength ( strlen($this->stMascara) );
    $this->obBscChaveLocalizacao->obCampoCod->setMaxLength ( strlen($this->stMascara) );
    $this->obBscChaveLocalizacao->obCampoCod->setSize      ( strlen($this->stMascara) + 2 );

    $this->obBscChaveLocalizacao->obCampoCod->setName ( "stChaveLocalizacao" );
    $this->obBscChaveLocalizacao->obCampoCod->setAlign ("center");
    $this->obBscChaveLocalizacao->obCampoCod->obEvento->setOnChange("buscaDado('buscaLocalizacao');");
    $this->obBscChaveLocalizacao->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$this->stMascara."', this, event);");
    $stTmpAcao = "nomLocalizacao";
    if ($this->inNivelCorte) {
        $stTmpAcao = "buscaReduzido";
    }
    $this->obBscChaveLocalizacao->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."localizacao/FLBuscaLocalizacao.php','frm','stChaveLocalizacao&inCodNvl=".$inCodNvl."','stNomeChaveLocalizacao','buscaLocalizacaoInner','".Sessao::getId()."','800','550');");

    if ( $this->getObrigatorio() ) {
        $this->obBscChaveLocalizacao->setNull( false );
    } else {
        $this->obBscChaveLocalizacao->setNull( true  );
    }
    $obFormulario->addComponente ( $this->obBscChaveLocalizacao );
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
        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel );
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
        $this->obRCIMLocalizacao->setCodigoNivel( $inCodigoNivel );

        //MONTA O VALOR COMBOS CONFORME O NIVEL
        $arValorComposto = explode( ".", $this->stValorComposto );
        $stValorComposto = "";

        for ($inCont = 0; $inCont < $inCodigoNivel; $inCont++) {
            $stValorComposto .= $arValorComposto[$inCont].".";
        }
        $stValorComposto = substr( $stValorComposto , 0, strlen($stValorComposto) - 1 );
        $this->obRCIMLocalizacao->setValorreduzido ( $stValorComposto );

        $obErro = $this->obRCIMLocalizacao->listarLocalizacao( $rsListaLocalizacao );

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
        if (isset($js)) {
            $js .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        } else {
            $js = "limpaSelect(f.".$stNomeCombo.",0); \n";
        }
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','', 'selected');\n";
    }
    if ($this->stValorReduzido) {
        $this->obRCIMLocalizacao->setCodigoNivel    ( $this->inCodigoNivel    );
        $this->obRCIMLocalizacao->setCodigoVigencia ( $this->inCodigoVigencia );
        $this->obRCIMLocalizacao->recuperaProximoNivel(  $rsProximoNivel );
        $this->obRCIMLocalizacao->setCodigoNivel    (  $rsProximoNivel->getCampo("cod_nivel") );
        $this->obRCIMLocalizacao->setValorReduzido( $this->stValorReduzido );
        $obErro = $this->obRCIMLocalizacao->listarLocalizacao( $rsListaLocalizacao );
        $inContador = 1;
        if ($inPosCombo != $inNumCombos) {
            $this->stValorReduzido .= ".";
            while ( !$rsListaLocalizacao->eof() ) {
                $stChaveLocalizacao  = $rsListaLocalizacao->getCampo( "cod_nivel" )."-";
                $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "cod_localizacao")."-";
                $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "valor")."-";
                $stChaveLocalizacao .= $rsListaLocalizacao->getCampo( "valor_reduzido");
                $stNomeLocalizacao   = $rsListaLocalizacao->getCampo( "nom_localizacao" );
                $js .= "f.inCodLocalizacao_".$inPosCombo.".options[$inContador] = ";
                $js .= "new Option('".$stNomeLocalizacao."','".$stChaveLocalizacao."',''); \n";
                $inContador++;
                $rsListaLocalizacao->proximo();
            }
        }
    }
    if (isset($js)) {
        $js .= "f.stChaveLocalizacao.value = '".$this->stValorReduzido."';\n";
    } else {
        $js = "f.stChaveLocalizacao.value = '".$this->stValorReduzido."';\n";
    }
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
    $js = "";
    while ( !$rsListaNivel->eof() and key( $arValorReduzido ) < count( $arValorReduzido ) ) {
        
        if ($inCont == 1) {
            $stValorReduzido .= current( $arValorReduzido );
            $boMontaCombos = true;
        } else {
            if ($this->obRCIMLocalizacao->getValorReduzido() ) {
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
        $js .= "f.".$stNomeCombo.".options[0] = new Option('Selecione $stSelecione','');\n";

        if ($boMontaCombos) {
            $this->obRCIMLocalizacao->setCodigoNivel       ( $rsListaNivel->getCampo("cod_nivel") );
            
            if (substr_count($stValorReduzido,".") == 0)
                $stValorReduzidoSemPonto = str_replace(".","",$stValorReduzido);
            $this->obRCIMLocalizacao->setValorReduzido     ( $stValorReduzidoSemPonto);
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