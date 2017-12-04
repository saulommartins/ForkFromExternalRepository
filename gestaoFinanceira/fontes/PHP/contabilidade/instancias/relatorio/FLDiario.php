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
    * Página de filtro
    * Data de criação : 13/03/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Programador: Diego Barbosa Victoria

    * $Id: FLDiario.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-02.02.23
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once(CAM_GF_CONT_NEGOCIO . "RContabilidadeConfiguracao.class.php");

//include_once("JSDiario.js");
//sessao->tipoConta = "banco";

$obRConfiguracao = new RContabilidadeConfiguracao;
$obRConfiguracao->consultar();

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidade , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCDiario.php" );

$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "Entidade"                 );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a entidade"     );

 if ($rsEntidade->getNumLinhas() > 1) $obCmbEntidade->addOption    ( ""            ,"Selecione" );

$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setValue     ( $inCodEntidade             );
$obCmbEntidade->preencheCombo( $rsEntidade                );
$obCmbEntidade->setNull      ( false                      );

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio        (  Sessao::getExercicio() );
$obPeriodo->setNull             (false );
$obPeriodo->setValidaExercicio  ( true );
$obPeriodo->setValue            ( 2 );

$obTxtUltimaPagina = new TextBox;
$obTxtUltimaPagina->setName      ( "inUltimaPagina"              );
$obTxtUltimaPagina->setRotulo    ( "Última Página Impressa"      );
$obTxtUltimaPagina->setMaxLength ( 10                            );
$obTxtUltimaPagina->setSize      ( 7                             );
$obTxtUltimaPagina->setInteiro   ( true                          );
$obTxtUltimaPagina->setValue     ( $obRConfiguracao->getDiarioUltimaPagina() );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.23');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidade      );
$obFormulario->addComponente( $obPeriodo    );
$obFormulario->addComponente( $obTxtUltimaPagina );
$obFormulario->OK();
$obFormulario->show();
