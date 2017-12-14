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
* Classe interface para montar os combos de orgao, unidade, departamento e setor
* Data de Criação: 04/10/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RSetor.class.php" );

class MontaOrgUniDepSet
{
/**
    * @access Private
    * @var Object
*/
var $obRSetor;
/**
    * @access Private
    * @var String
*/
var $stMascaraSetor;
/**
    * @access Private
    * @var String
*/
var $stChaveSetor;
/**
    * @access Private
    * @var String
*/
var $stJs;
/**
    * @access Private
    * @var Boolean
*/
var $boObrigatorio;

/**
    * @access Public
    * @param Object $valor
*/
function setRSetor($valor) { $this->obRSetor = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascaraSetor($valor) { $this->stMascaraSetor = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setChaveSetor($valor) { $this->stChaveSetor = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setJs($valor) { $this->stJs = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setObrigatorio($valor) { $this->boObrigatorio = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRSetor() { return $this->obRSetor; }
/**
    * @access Public
    * @return String
*/
function getMascaraSetor() { return $this->stMascaraSetor; }
/**
    * @access Public
    * @return String
*/
function getChaveSetor() { return $this->stChaveSetor; }
/**
    * @access Public
    * @return String
*/
function getJs() { return $this->stJs; }
/**
    * @access Public
    * @return Boolean
*/
function getObrigatorio() { return $this->boObrigatorio; }

/**
     * Método construtor
     * @access Private
*/
function MontaOrgUniDepSet()
{
    $this->setRSetor( new RSetor );
    $this->setMascaraSetor( SistemaLegado::pegaConfiguracao('mascara_setor') );
    $this->setObrigatorio( true );
}

function montaFormulario(&$obFormulario)
{
    GLOBAL $pgOcul;
    GLOBAL $pgProc;

    $pgProx = $pgOcul."?".Sessao::getId()."&ctrl=";
    $this->obRSetor->obDepartamento->obUnidade->obOrgao->listar( $rsOrgao , ' ano_exercicio, nom_orgao');

    $obTxtChaveSetor = new TextBox;
    $obTxtChaveSetor->setName      ( 'stChaveSetorTxt'                  );
    $obTxtChaveSetor->setRotulo    ( 'Setor'                            );
    $obTxtChaveSetor->setNull      ( !( $this->getObrigatorio() )       );
    $obTxtChaveSetor->setSize      ( strlen( $this->getMascaraSetor() ) );
    $obTxtChaveSetor->setMaxLength ( strlen( $this->getMascaraSetor() ) );
    $obTxtChaveSetor->obEvento->setOnKeyUp( "mascaraDinamico('".$this->getMascaraSetor()."', this, event);" );
    $obTxtChaveSetor->obEvento->setOnChange( "buscaValor( 'montarPorChave','$pgOcul','$pgProc','oculto', 'Sessao::getId()' );" );

    $obCmbOrgao = new Select;
    $obCmbOrgao->setName       ( 'stChaveOrgao'                  );
    $obCmbOrgao->setStyle      ( 'width: 450px'                  );
    $obCmbOrgao->setRotulo     ( 'Setor'                         );
    $obCmbOrgao->setCampoID    ( '[cod_orgao]-[ano_exercicio]'   );
    $obCmbOrgao->setCampoDesc  ( '[nom_orgao] - [ano_exercicio]' );
    $obCmbOrgao->addOption     ( "", "Selecione órgão"           );
    $obCmbOrgao->preencheCombo ( $rsOrgao                        );
    $obCmbOrgao->setNull       ( !( $this->getObrigatorio() )    );
    $obCmbOrgao->obEvento->setOnChange( "buscaValor( 'unidade','$pgOcul','$pgProc','oculto', 'Sessao::getId()' );" );

    $obCmbUnidade = new Select;
    $obCmbUnidade->setName   ( 'stChaveUnidade'             );
    $obCmbUnidade->setStyle  ( 'width: 450px'               );
    $obCmbUnidade->setRotulo ( 'Setor'                      );
    $obCmbUnidade->setNull   ( !( $this->getObrigatorio() ) );
    $obCmbUnidade->addOption ( "", "Selecione unidade"      );
    $obCmbUnidade->obEvento->setOnChange( "buscaValor( 'departamento','$pgOcul','$pgProc','oculto', 'Sessao::getId()' );" );

    $obCmbDepartamento = new Select;
    $obCmbDepartamento->setName   ( 'stChaveDepartamento'        );
    $obCmbDepartamento->setStyle  ( 'width: 450px'               );
    $obCmbDepartamento->setRotulo ( 'Setor'                      );
    $obCmbDepartamento->setNull   ( !( $this->getObrigatorio() ) );
    $obCmbDepartamento->addOption ( "", "Selecione departamento" );
    $obCmbDepartamento->obEvento->setOnChange( "buscaValor( 'setor','$pgOcul','$pgProc','oculto', 'Sessao::getId()' );" );

    $obCmbSetor = new Select;
    $obCmbSetor->setName   ( 'stChaveSetor'               );
    $obCmbSetor->setStyle  ( 'width: 450px'               );
    $obCmbSetor->setRotulo ( 'Setor'                      );
    $obCmbSetor->setNull   ( !( $this->getObrigatorio() ) );
    $obCmbSetor->addOption ( "", "Selecione setor"        );
    $obCmbSetor->obEvento->setOnChange( "buscaValor( 'chaveSetor','$pgOcul','$pgProc','oculto', 'Sessao::getId()' );" );

    $obFormulario->addComponente( $obTxtChaveSetor   );
    $obFormulario->addComponente( $obCmbOrgao        );
    $obFormulario->addComponente( $obCmbUnidade      );
    $obFormulario->addComponente( $obCmbDepartamento );
    $obFormulario->addComponente( $obCmbSetor        );
}

function montarUnidade($boExecuta = true)
{
    if ( $this->obRSetor->obDepartamento->obUnidade->obOrgao->getCodOrgao() ) {
        $obErro = $this->obRSetor->obDepartamento->obUnidade->listar( $rsUnidade );
    } else {
        $rsUnidade = new RecordSet;
        $obErro = new Erro;
    }
    $stJs .= "limpaSelect(f.stChaveDepartamento,1); \n";
    $stJs .= "limpaSelect(f.stChaveSetor,1); \n";
    $stJs .= "limpaSelect(f.stChaveUnidade,1); \n";
    if ( !$obErro->ocorreu() ) {
        while ( !$rsUnidade->eof() ) {
            $stJs .= "f.stChaveUnidade.options[".++$i."] = new Option('".$rsUnidade->getCampo('nom_unidade')."','".$rsUnidade->getCampo('cod_unidade')."');\n";
            $rsUnidade->proximo();
        }
    }
    $this->setJs( $stJs );
    if ( $boExecuta and !$obErro->ocorreu() ) {
        $this->montarChave( $this->obRSetor->obDepartamento->obUnidade->obOrgao->getExercicio(), $this->obRSetor->obDepartamento->obUnidade->obOrgao->getCodOrgao() );
        $stJs .= " f.stChaveSetorTxt.value = '".$this->getChaveSetor()."';\n";
        SistemaLegado::executaFrameOculto( $stJs );
    }

    return $obErro;
}

function montarDepartamento($boExecuta = true)
{
    if ( $this->obRSetor->obDepartamento->obUnidade->getCodUnidade() ) {
        $obErro = $this->obRSetor->obDepartamento->listar( $rsDepartamento );
    } else {
        $rsDepartamento = new RecordSet;
        $obErro = new Erro;
    }
    $stJs .= "limpaSelect(f.stChaveDepartamento,1); \n";
    $stJs .= "limpaSelect(f.stChaveSetor,1); \n";
    if ( !$obErro->ocorreu() ) {
        while ( !$rsDepartamento->eof() ) {
            $stJs .= "f.stChaveDepartamento.options[".++$i."] = new Option('".$rsDepartamento->getCampo('nom_departamento')."','".$rsDepartamento->getCampo('cod_departamento')."');\n";
            $rsDepartamento->proximo();
        }
    }
    $this->setJs( $stJs );
    if ( $boExecuta and !$obErro->ocorreu() ) {
        $this->montarChave( $this->obRSetor->obDepartamento->obUnidade->obOrgao->getExercicio(), $this->obRSetor->obDepartamento->obUnidade->obOrgao->getCodOrgao(), $this->obRSetor->obDepartamento->obUnidade->getCodUnidade() );
        $stJs .= " f.stChaveSetorTxt.value = '".$this->getChaveSetor()."';\n";
        SistemaLegado::executaFrameOculto( $stJs );
    }

    return $obErro;
}

function montarSetor($boExecuta = true)
{
    if ( $this->obRSetor->obDepartamento->getCodDepartamento() ) {
        $obErro = $this->obRSetor->listar( $rsSetor );
    } else {
        $rsSetor = new RecordSet;
        $obErro = new Erro;
    }
    $stJs .= "limpaSelect(f.stChaveSetor,1); \n";
    if ( !$obErro->ocorreu() ) {
        while ( !$rsSetor->eof() ) {
            $stJs .= "f.stChaveSetor.options[".++$i."] = new Option('".$rsSetor->getCampo('nom_setor')."','".$rsSetor->getCampo('cod_setor')."');\n";
            $rsSetor->proximo();
        }
    }
    $this->setJs( $stJs );
    if ( $boExecuta and !$obErro->ocorreu() ) {
        $this->montarChave( $this->obRSetor->obDepartamento->obUnidade->obOrgao->getExercicio(), $this->obRSetor->obDepartamento->obUnidade->obOrgao->getCodOrgao(), $this->obRSetor->obDepartamento->obUnidade->getCodUnidade(), $this->obRSetor->obDepartamento->getCodDepartamento() );
        $stJs .= " f.stChaveSetorTxt.value = '".$this->getChaveSetor()."';\n";
        SistemaLegado::executaFrameOculto( $stJs );
    }

    return $obErro;
}

function montarChaveSetor($boExecuta = true)
{
    $this->montarChave( $this->obRSetor->obDepartamento->obUnidade->obOrgao->getExercicio(), $this->obRSetor->obDepartamento->obUnidade->obOrgao->getCodOrgao(), $this->obRSetor->obDepartamento->obUnidade->getCodUnidade(), $this->obRSetor->obDepartamento->getCodDepartamento(), $this->obRSetor->getCodSetor() );
    $stJs .= " f.stChaveSetorTxt.value = '".$this->getChaveSetor()."';\n";
    $this->setJs( $stJs );
    if ($boExecuta) {
        SistemaLegado::executaFrameOculto( $stJs );
    }
}

function montarChave($inAnoExercicio = 0, $inCodOrgao = 0, $inCodUnidade = 0, $inCodDepartamento = 0, $inCodSetor = 0)
{
    $arLocais          = preg_split( "/[^a-zA-Z0-9]/", $this->getMascaraSetor() );
    $arSeparadores     = preg_replace( "/[a-zA-Z0-9]/","", $this->getMascaraSetor() );
    $inCodOrgao        = str_pad( $inCodOrgao,        strlen( $arLocais[0] ), "0", STR_PAD_LEFT );
    $inCodUnidade      = str_pad( $inCodUnidade,      strlen( $arLocais[1] ), "0", STR_PAD_LEFT );
    $inCodDepartamento = str_pad( $inCodDepartamento, strlen( $arLocais[2] ), "0", STR_PAD_LEFT );
    $inCodSetor        = str_pad( $inCodSetor,        strlen( $arLocais[3] ), "0", STR_PAD_LEFT );
    $inAnoExercicio    = str_pad( $inAnoExercicio,    strlen( $arLocais[4] ), "0", STR_PAD_LEFT );
    $stChaveSetor  = $inCodOrgao.$arSeparadores[0].$inCodUnidade.$arSeparadores[1];
    $stChaveSetor .= $inCodDepartamento.$arSeparadores[2].$inCodSetor.$arSeparadores[3].$inAnoExercicio;
    $this->setChaveSetor( $stChaveSetor );
}

function montarPorChave()
{
    $arChave =  preg_split( "/[^a-zA-Z0-9]/", $this->getChaveSetor() );
    $this->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio ( (int) $arChave[4] );
    $this->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao  ( (int) $arChave[0] );
    $this->obRSetor->obDepartamento->obUnidade->setCodUnidade         ( (int) $arChave[1] );
    $this->obRSetor->obDepartamento->setCodDepartamento               ( (int) $arChave[2] );
    $this->obRSetor->setCodSetor                                      ( (int) $arChave[3] );
    $obErro = $this->montarUnidade( false );
    if ( !$obErro->ocorreu() ) {
        $stJs = $this->getJs();
        $obErro = $this->montarDepartamento( false );
        if ( !$obErro->ocorreu() ) {
             $stJs .= $this->getJs();
             $obErro = $this->montarSetor( false );
             if ( !$obErro->ocorreu() ) {
                 $stJs .= $this->getJs();
                 $stJs .= "f.stChaveOrgao.value = '".(int) $arChave[0]."-".(int) $arChave[4]."';\n";
                 $stJs .= "f.stChaveUnidade.value = '".(int) $arChave[1] ."';\n";
                 $stJs .= "f.stChaveDepartamento.value = '".(int) $arChave[2] ."';\n";
                 $stJs .= "f.stChaveSetor.value = '".(int) $arChave[3] ."';\n";
                 $this->montarChaveSetor( false );
                 $stJs .= $this->getJs();
                 SistemaLegado::executaFrameOculto( $stJs );
             }
        }
    }

    return $obErro;
}

}
?>
