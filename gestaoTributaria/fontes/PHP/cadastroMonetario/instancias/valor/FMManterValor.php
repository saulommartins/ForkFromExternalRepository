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
    * Pagina de Formulario de Inclusao/Alteracao de VALOR DO INDICADOR ECONOMICO

    * Data de Criacao: 20/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterValor.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.08

*/

/*
$Log$
Revision 1.9  2006/09/15 14:58:08  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );

$obRMONIndicador =  new RMONIndicadorEconomico;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterValor";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

/***********************************************/

include_once ( $pgJs );

//FUNCOES PARA PEGAR OS VALORES NECESSARIOS
if ($stAcao == 'alterar') {
    $obRMONIndicador->DevolveValor ( $rsRecordSet, $inCodIndicador );
    if ( $obRMONIndicador->getValor() ) {
        $x = explode ('-', $obRMONIndicador->getDtVigencia());
        $dia = $x[2].'/'.$x[1].'/'.$x[0];
    }
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $stCtrl );

$obHdnCodIndicador = new Hidden;
$obHdnCodIndicador->setName  ('inCodIndicador');
$obHdnCodIndicador->setValue ( $_REQUEST['inCodIndicador'] );

$obHdnDescricao = new Hidden;
$obHdnDescricao->setName  ('stDescricao');
$obHdnDescricao->setValue ( $_REQUEST['stDescricao'] );

$obHdnDtVigenciaAntes = new Hidden;
$obHdnDtVigenciaAntes->setName  ('dtVigenciaAntes');
$obHdnDtVigenciaAntes->setValue ( $dia );

//------------------------
$obDtVigencia  = new Data;
$obDtVigencia->setName               ( "dtVigencia"                    );
$obDtVigencia->setValue              ( $dia                            );
$obDtVigencia->setRotulo             ( "Data de Vigência"              );
$obDtVigencia->setTitle              ( "Data de Vigência da fórmula de cálculo" );
$obDtVigencia->setMaxLength          ( 20                                );
$obDtVigencia->setSize               ( 10                                );
$obDtVigencia->setNull               ( false                             );
$obDtVigencia->obEvento->setOnChange ( "validaData1500( this );"         );

$obLblDtVigencia = new Label;
$obLblDtVigencia->setName   ( 'LabelDtVigencia' );
$obLblDtVigencia->setTitle  ( 'Data de vigência do Valor referente ao indicador econômico' );
$obLblDtVigencia->setRotulo ( 'Vigência' );
$obLblDtVigencia->setValue  ( $dia );

$obLblCodIndicador = new Label;
$obLblCodIndicador->setName   ( 'LabelCodIndicador' );
$obLblCodIndicador->setTitle  ( 'Código do Indicador' );
$obLblCodIndicador->setRotulo ( 'Código' );
$obLblCodIndicador->setValue  ( $_REQUEST['inCodIndicador'] );

$obLblDescricao = new Label;
$obLblDescricao->setName   ( 'LabelCodIndicador' );
$obLblDescricao->setTitle  ( 'Descrição do Indicador' );
$obLblDescricao->setRotulo ( 'Descrição' );
$obLblDescricao->setValue  ( $_REQUEST['stDescricao'] );

$valor = number_format( $obRMONIndicador->getValor() , 4, ',', '.');
$obTxtValor = new Numerico;
$obTxtValor->setRotulo  ( 'Valor');
$obTxtValor->setTitle   ( 'Valor referente ao Indicador Econômico');
$obTxtValor->setName    ( 'inValor');
$obTxtValor->setValue   ( $valor );
$obTxtValor->setDecimais ( 4 );
$obTxtValor->setMaxValue  ( 99999.9999 );
$obTxtValor->setNull      ( false );
$obTxtValor->setNegativo  ( false );
$obTxtValor->setNaoZero   ( true );
$obTxtValor->setSize    ( 10 );
$obTxtValor->setMaxLength ( 10 );

$obBscFormulaCalculo = new BuscaInner;
$obBscFormulaCalculo->setRotulo ( "*Fórmula de Cálculo" );
$obBscFormulaCalculo->setTitle  ( "Fórmula de cálculo utilizada para converter valores utilizando-se o indicador"  );
$obBscFormulaCalculo->setId     ( "stFormula"  );
$obBscFormulaCalculo->obCampoCod->setName   ( "inCodFuncao" );
$obBscFormulaCalculo->obCampoCod->setValue  ( $inCodFuncao );
$obBscFormulaCalculo->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFormulaCalculo->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','todos','".Sessao::getId()."','800','550');" );

//---- INNER PARA ALTERACAO
$obBscFormulaCalculo2 = new BuscaInner;
$obBscFormulaCalculo2->setRotulo ( "*Fórmula de Cálculo" );
$obBscFormulaCalculo2->setTitle  ( "Fórmula de cálculo utilizada para converter valores utilizando-se o indicador"  );
$obBscFormulaCalculo2->setId     ( "stFormula"  );
$obBscFormulaCalculo2->obCampoCod->setName   ( "inCodFuncao" );
$obBscFormulaCalculo2->obCampoCod->setValue  ( $obRMONIndicador->getCodModulo ().'.'.$obRMONIndicador->getCodBiblioteca ().'.'.$obRMONIndicador->getCodFuncao () );
$obBscFormulaCalculo2->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obBscFormulaCalculo2->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','todos','".Sessao::getId()."','800','550');" );

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ("oculto" );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.08" );
$obFormulario->addTitulo ('Dados para o Valor');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCodIndicador );
$obFormulario->addHidden ( $obHdnDtVigenciaAntes );

$obFormulario->addComponente ( $obLblCodIndicador );
$obFormulario->addComponente ( $obLblDescricao );

if ($stAcao == "alterar") {
$obFormulario->addComponente ( $obLblDtVigencia );
} else {
 $obFormulario->addComponente ( $obDtVigencia );
}

$obFormulario->addComponente ( $obTxtValor );

if ($stAcao == "incluir") {
    $obFormulario->ok       ();
} else {
    $obFormulario->cancelar ();
}

$obFormulario->show();

if ($stAcao == 'incluir') {
    $stJs .= 'f.inCodIndicador.focus();';
} else {
    $stJs .= 'f.inValor.focus();';
}
sistemaLegado::executaFrameOculto ( $stJs );
